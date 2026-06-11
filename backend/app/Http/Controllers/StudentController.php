<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\GoogleSheet;
use App\Models\User;

class StudentController extends Controller
{
    public function showTaskList()
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $linkedSheet = GoogleSheet::where('user_id', session('user_id'))->first();
        $syncError = null;

        if ($linkedSheet && (!$linkedSheet->last_synced_at || $linkedSheet->last_synced_at->lessThan(now()->subMinutes(5)))) {
            try {
                $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$linkedSheet->spreadsheet_id}";

                $students = $this->fetchStudentsFromSheet($spreadsheetUrl, [
                    'name' => $linkedSheet->name_column,
                    'email' => $linkedSheet->email_column,
                    'phone' => $linkedSheet->phone_column,
                    'address' => $linkedSheet->address_column,
                ]);

                foreach ($students as $studentData) {
                    $existing = Student::where('email', $studentData['email'])->first();

                    if ($existing) {
                        $existing->update([
                            'name' => $studentData['name'],
                            'phone_number' => $studentData['phone_number'],
                            'address' => $studentData['address'],
                        ]);
                    } else {
                        Student::create([
                            'name' => $studentData['name'],
                            'email' => $studentData['email'],
                            'phone_number' => $studentData['phone_number'],
                            'address' => $studentData['address'],
                        ]);
                    }
                }

                $linkedSheet->update(['last_synced_at' => now()]);
            } catch (\Exception $e) {
                $syncError = 'Auto-sync failed: ' . $e->getMessage();
            }
        }

        $students = Student::all();

        return view('pages.task-list', [
            'linkedSheet' => $linkedSheet,
            'students' => $students,
            'syncError' => $syncError,
        ]);
    }

    public function linkForm(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));

        $validator = Validator::make($request->all(), [
            'google_form_url' => ['required', 'url'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $url = $request->input('google_form_url');

        try {
            $sheetId = $this->extractSheetId($url);

            if (!$sheetId) {
                throw new \Exception('Could not extract Google Sheet ID from this URL. Please provide a Google Form or Sheet link.');
            }

            $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$sheetId}";

            $headers = $this->getSheetHeaders($spreadsheetUrl);

            $nameIndex = null;
            $emailIndex = null;
            $phoneIndex = null;
            $addressIndex = null;

            foreach ($headers as $index => $header) {
                $headerLower = strtolower(trim($header));

                if ($nameIndex === null && (str_contains($headerLower, 'name') || str_contains($headerLower, 'full name') || str_contains($headerLower, 'student name'))) {
                    $nameIndex = $index;
                }

                if ($emailIndex === null && (str_contains($headerLower, 'email') || str_contains($headerLower, 'e-mail') || str_contains($headerLower, 'mail'))) {
                    $emailIndex = $index;
                }

                if ($phoneIndex === null && (str_contains($headerLower, 'phone') || str_contains($headerLower, 'mobile') || str_contains($headerLower, 'contact') || str_contains($headerLower, 'number'))) {
                    $phoneIndex = $index;
                }

                if ($addressIndex === null && (str_contains($headerLower, 'address') || str_contains($headerLower, 'location'))) {
                    $addressIndex = $index;
                }
            }

            if ($nameIndex === null || $emailIndex === null) {
                throw new \Exception('The spreadsheet is missing required columns (Name and/or Email). Found columns: ' . implode(', ', $headers));
            }

            GoogleSheet::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'spreadsheet_id' => $sheetId,
                    'form_url' => $url,
                    'name_column' => $headers[$nameIndex],
                    'email_column' => $headers[$emailIndex],
                    'phone_column' => $phoneIndex !== null ? $headers[$phoneIndex] : null,
                    'address_column' => $addressIndex !== null ? $headers[$addressIndex] : null,
                ]
            );

            return back()->with('success', 'Google Sheet linked successfully! Click "Sync Students" to import.');
        } catch (\Exception $e) {
            return back()->withErrors(['google_form_url' => $e->getMessage()])->withInput();
        }
    }

    public function syncStudents(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));

        $linkedSheet = GoogleSheet::where('user_id', $user->id)->first();

        if (!$linkedSheet) {
            return back()->withErrors(['sync' => 'No Google Sheet linked. Please paste a Form URL first.']);
        }

        try {
            $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$linkedSheet->spreadsheet_id}";

            $students = $this->fetchStudentsFromSheet($spreadsheetUrl, [
                'name' => $linkedSheet->name_column,
                'email' => $linkedSheet->email_column,
                'phone' => $linkedSheet->phone_column,
                'address' => $linkedSheet->address_column,
            ]);

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($students as $studentData) {
                $existing = Student::where('email', $studentData['email'])->first();

                if ($existing) {
                    $existing->update([
                        'name' => $studentData['name'],
                        'phone_number' => $studentData['phone_number'],
                        'address' => $studentData['address'],
                    ]);
                    $updated++;
                } else {
                    Student::create([
                        'user_id' => $user->id,
                        'name' => $studentData['name'],
                        'email' => $studentData['email'],
                        'phone_number' => $studentData['phone_number'],
                        'address' => $studentData['address'],
                    ]);
                    $imported++;
                }
            }

            $linkedSheet->update(['last_synced_at' => now()]);

            return back()->with('success', "Sync complete. {$imported} added, {$updated} updated, {$skipped} skipped.");
        } catch (\Exception $e) {
            return back()->withErrors(['sync' => 'Sync failed: ' . $e->getMessage()]);
        }
    }

    private function extractSheetId(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        $path = $parsedUrl['path'] ?? '';
        $query = $parsedUrl['query'] ?? '';

        if (str_contains($host, 'docs.google.com') && str_contains($path, '/spreadsheets/d/')) {
            preg_match('/\/spreadsheets\/d\/([^\/\?]+)/', $path, $matches);
            return $matches[1] ?? null;
        }

        if (str_contains($host, 'drive.google.com') && str_contains($path, '/open')) {
            parse_str($query, $queryParams);
            return $queryParams['id'] ?? null;
        }

        if (str_contains($host, 'docs.google.com') && str_contains($path, '/forms/d/')) {
            preg_match('/\/forms\/d\/([^\/\?]+)/', $path, $matches);
            $formId = $matches[1] ?? null;

            if ($formId && $formId !== 'e') {
                $exportUrl = "https://docs.google.com/spreadsheets/d/{$formId}/export?format=csv";

                $response = Http::timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    ])
                    ->get($exportUrl);

                if ($response->successful()) {
                    return $formId;
                }
            }
        }

        return null;
    }

    private function getSheetHeaders(string $spreadsheetUrl): array
    {
        $exportUrl = $spreadsheetUrl . '/export?format=csv';

        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])
            ->get($exportUrl);

        if ($response->successful()) {
            $lines = str_getcsv($response->body(), "\n");
            $headers = str_getcsv(array_shift($lines));

            return array_map(function ($h) {
                return trim(str_replace(["\ufeff", "\r"], '', $h));
            }, $headers);
        }

        if ($response->status() === 401 || $response->status() === 403) {
            throw new \Exception('Google Sheet is not publicly accessible. Please set sharing to "Anyone with the link can view".');
        }

        throw new \Exception('Failed to access Google Sheet. Please check the URL and sharing settings.');
    }

    private function fetchStudentsFromSheet(string $spreadsheetUrl, array $columnMap): array
    {
        $exportUrl = $spreadsheetUrl . '/export?format=csv';

        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])
            ->get($exportUrl);

        if ($response->successful()) {
            return $this->parseCsv($response->body(), $columnMap);
        }

        if ($response->status() === 401 || $response->status() === 403) {
            throw new \Exception('Google Sheet is not publicly accessible. Please set sharing to "Anyone with the link can view".');
        }

        throw new \Exception('Failed to fetch Google Sheet (HTTP ' . $response->status() . ').');
    }

    private function parseCsv(string $csvBody, array $columnMap): array
    {
        $lines = str_getcsv($csvBody, "\n");
        $rawHeaders = str_getcsv(array_shift($lines));
        $headers = array_map(function ($h) {
            return trim(str_replace(["\ufeff", "\r"], '', $h));
        }, $rawHeaders);

        $columnIndexes = [];

        foreach ($columnMap as $field => $columnName) {
            if (!$columnName) {
                continue;
            }

            $index = false;

            foreach ($headers as $i => $header) {
                if (strcasecmp(trim($header), trim($columnName)) === 0) {
                    $index = $i;
                    break;
                }
            }

            if ($index === false) {
                $headerLower = array_map('strtolower', $headers);
                $searchLower = strtolower(trim($columnName));

                $index = array_search($searchLower, $headerLower, true);

                if ($index === false) {
                    $keywords = [
                        'name' => ['name', 'full name', 'student name', 'fullname'],
                        'email' => ['email', 'e-mail', 'mail', 'email address'],
                        'phone' => ['phone', 'mobile', 'contact', 'contact number', 'phone number'],
                        'address' => ['address', 'location'],
                    ];

                    if (isset($keywords[$field])) {
                        foreach ($keywords[$field] as $keyword) {
                            foreach ($headerLower as $i => $hl) {
                                if (str_contains($hl, $keyword)) {
                                    $index = $i;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }

            if ($index !== false) {
                $columnIndexes[$field] = $index;
            }
        }

        if (!isset($columnIndexes['name'])) {
            throw new \Exception('Could not find "Name" column in the sheet. Found columns: ' . implode(', ', $headers));
        }

        if (!isset($columnIndexes['email'])) {
            throw new \Exception('Could not find "Email" column in the sheet. Found columns: ' . implode(', ', $headers));
        }

        $students = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $row = str_getcsv($line);
            $email = $row[$columnIndexes['email']] ?? null;

            if (empty($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $name = $row[$columnIndexes['name']] ?? trim($email, ' @');

            $students[] = [
                'user_id' => null,
                'name' => trim($name),
                'email' => trim(strtolower($email)),
                'phone_number' => isset($columnIndexes['phone']) ? trim($row[$columnIndexes['phone']] ?? '') : null,
                'address' => isset($columnIndexes['address']) ? trim($row[$columnIndexes['address']] ?? '') : null,
            ];
        }

        return $students;
    }
}
