<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\GoogleSheet;
use App\Models\User;
use App\Models\Task;

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

                if ($linkedSheet->file_url_column) {
                    $taskData = $this->fetchTasksFromSheet($spreadsheetUrl, [
                        'file_url' => $linkedSheet->file_url_column,
                        'status' => $linkedSheet->status_column,
                        'due_date' => $linkedSheet->due_date_column,
                    ]);

                    foreach ($taskData as $index => $task) {
                        $student = Student::where('email', $students[$index]['email'] ?? null)->first();

                        if (!$student) {
                            continue;
                        }

                        $existing = Task::where('student_id', $student->student_id)
                            ->where('task_name', $linkedSheet->form_name)
                            ->first();

                        if ($existing) {
                            $existing->update([
                                'file_url' => $task['file_url'],
                                'status' => $task['status'] ?? 'pending',
                                'due_date' => $task['due_date'] ?? now()->toDateString(),
                            ]);
                        } else {
                            Task::create([
                                'student_id' => $student->student_id,
                                'task_name' => $linkedSheet->form_name,
                                'file_url' => $task['file_url'],
                                'status' => $task['status'] ?? 'pending',
                                'due_date' => $task['due_date'] ?? now()->toDateString(),
                            ]);
                        }
                    }
                }

                $linkedSheet->update(['last_synced_at' => now()]);
            } catch (\Exception $e) {
                $syncError = 'Auto-sync failed: ' . $e->getMessage();
            }
        }

        $students = Student::all();
        $tasks = Task::with('student')->get();

        return view('pages.task-list', [
            'linkedSheet' => $linkedSheet,
            'students' => $students,
            'tasks' => $tasks,
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
            $fileUrlIndex = null;
            $statusIndex = null;
            $dueDateIndex = null;

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

                if ($fileUrlIndex === null && (str_contains($headerLower, 'file') || str_contains($headerLower, 'url') || str_contains($headerLower, 'link') || str_contains($headerLower, 'attachment') || str_contains($headerLower, 'upload'))) {
                    $fileUrlIndex = $index;
                }

                if ($statusIndex === null && (str_contains($headerLower, 'status') || str_contains($headerLower, 'state'))) {
                    $statusIndex = $index;
                }

                if ($dueDateIndex === null && (str_contains($headerLower, 'due') || str_contains($headerLower, 'date') || str_contains($headerLower, 'deadline'))) {
                    $dueDateIndex = $index;
                }
            }

            if ($nameIndex === null || $emailIndex === null) {
                throw new \Exception('The spreadsheet is missing required columns (Name and/or Email). Found columns: ' . implode(', ', $headers));
            }

            $formName = $this->extractFormName($url);

            GoogleSheet::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'spreadsheet_id' => $sheetId,
                    'form_url' => $url,
                    'form_name' => $formName,
                    'name_column' => $headers[$nameIndex],
                    'email_column' => $headers[$emailIndex],
                    'phone_column' => $phoneIndex !== null ? $headers[$phoneIndex] : null,
                    'address_column' => $addressIndex !== null ? $headers[$addressIndex] : null,
                    'file_url_column' => $fileUrlIndex !== null ? $headers[$fileUrlIndex] : null,
                    'status_column' => $statusIndex !== null ? $headers[$statusIndex] : null,
                    'due_date_column' => $dueDateIndex !== null ? $headers[$dueDateIndex] : null,
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

            $taskData = [];

            if ($linkedSheet->file_url_column) {
                $taskData = $this->fetchTasksFromSheet($spreadsheetUrl, [
                    'file_url' => $linkedSheet->file_url_column,
                    'status' => $linkedSheet->status_column,
                    'due_date' => $linkedSheet->due_date_column,
                ]);
            }

            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $tasksImported = 0;
            $tasksUpdated = 0;

            foreach ($students as $index => $studentData) {
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
                        'name' => $studentData['name'],
                        'email' => $studentData['email'],
                        'phone_number' => $studentData['phone_number'],
                        'address' => $studentData['address'],
                    ]);
                    $imported++;
                }

                if ($linkedSheet->file_url_column && isset($taskData[$index])) {
                    $student = Student::where('email', $studentData['email'])->first();

                    $existingTask = Task::where('student_id', $student->student_id)
                        ->where('task_name', $linkedSheet->form_name)
                        ->first();

                    if ($existingTask) {
                        $existingTask->update([
                            'file_url' => $taskData[$index]['file_url'],
                            'status' => $taskData[$index]['status'] ?? 'pending',
                            'due_date' => $taskData[$index]['due_date'] ?? now()->toDateString(),
                        ]);
                        $tasksUpdated++;
                    } else {
                        Task::create([
                            'student_id' => $student->student_id,
                            'task_name' => $linkedSheet->form_name,
                            'file_url' => $taskData[$index]['file_url'],
                            'status' => $taskData[$index]['status'] ?? 'pending',
                            'due_date' => $taskData[$index]['due_date'] ?? now()->toDateString(),
                        ]);
                        $tasksImported++;
                    }
                }
            }

            $linkedSheet->update(['last_synced_at' => now()]);

            return back()->with('success', "Sync complete. {$imported} students added, {$updated} students updated, {$tasksImported} tasks added, {$tasksUpdated} tasks updated, {$skipped} skipped.");
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

    private function extractFormName(string $url): string
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';

        if (str_contains($path, '/forms/d/e/')) {
            preg_match('/\/forms\/d\/e\/([^\/]+)/', $path, $matches);
            return $matches[1] ?? 'Google Form Task';
        }

        if (str_contains($path, '/forms/d/')) {
            preg_match('/\/forms\/d\/([^\/]+)/', $path, $matches);
            return $matches[1] ?? 'Google Form Task';
        }

        if (str_contains($path, '/spreadsheets/d/')) {
            preg_match('/\/spreadsheets\/d\/([^\/]+)/', $path, $matches);
            return $matches[1] ?? 'Google Form Task';
        }

        return 'Google Form Task';
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

    public function fetchStudentsFromSheet(string $spreadsheetUrl, array $columnMap): array
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

    public function fetchTasksFromSheet(string $spreadsheetUrl, array $columnMap): array
    {
        $exportUrl = $spreadsheetUrl . '/export?format=csv';

        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])
            ->get($exportUrl);

        if ($response->successful()) {
            return $this->parseTaskCsv($response->body(), $columnMap);
        }

        if ($response->status() === 401 || $response->status() === 403) {
            throw new \Exception('Google Sheet is not publicly accessible. Please set sharing to "Anyone with the link can view".');
        }

        throw new \Exception('Failed to fetch Google Sheet (HTTP ' . $response->status() . ').');
    }

    public function parseTaskCsv(string $csvBody, array $columnMap): array
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

            if ($index !== false) {
                $columnIndexes[$field] = $index;
            }
        }

        $tasks = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $row = str_getcsv($line);
            $fileUrl = $row[$columnIndexes['file_url']] ?? null;

            if (empty($fileUrl)) {
                continue;
            }

            $tasks[] = [
                'file_url' => trim($fileUrl),
                'status' => isset($columnIndexes['status']) ? trim($row[$columnIndexes['status']] ?? 'pending') : 'pending',
                'due_date' => isset($columnIndexes['due_date']) ? trim($row[$columnIndexes['due_date']] ?? now()->toDateString()) : now()->toDateString(),
            ];
        }

        return $tasks;
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
