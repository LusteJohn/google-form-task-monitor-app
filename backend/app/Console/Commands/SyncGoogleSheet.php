<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GoogleSheet;
use App\Models\Student;
use Illuminate\Support\Facades\Http;

class SyncGoogleSheet extends Command
{
    protected $signature = 'sheets:sync {--force : Force sync even if recently synced}';
    protected $description = 'Sync students from all linked Google Sheets';

    public function handle(): void
    {
        $sheets = GoogleSheet::all();
        $force = $this->option('force');

        if ($sheets->isEmpty()) {
            $this->info('No linked Google Sheets found.');
            return;
        }

        foreach ($sheets as $sheet) {
            if (!$force && $sheet->last_synced_at && $sheet->last_synced_at->greaterThan(now()->subMinutes(5))) {
                $this->info("Skipping sheet {$sheet->spreadsheet_id} (synced recently).");
                continue;
            }

            $this->info("Syncing sheet: {$sheet->spreadsheet_id}");

            try {
                $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$sheet->spreadsheet_id}";

                $students = $this->fetchStudentsFromSheet($spreadsheetUrl, [
                    'name' => $sheet->name_column,
                    'email' => $sheet->email_column,
                    'phone' => $sheet->phone_column,
                    'address' => $sheet->address_column,
                ]);

                $imported = 0;
                $updated = 0;

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
                        Student::create($studentData);
                        $imported++;
                    }
                }

                $sheet->update(['last_synced_at' => now()]);

                $this->info("  Added: {$imported}, Updated: {$updated}");
            } catch (\Exception $e) {
                $this->error("  Failed: " . $e->getMessage());
            }
        }

        $this->info('Sync complete.');
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
        $headers = str_getcsv(array_shift($lines));

        $columnIndexes = [];

        foreach ($columnMap as $field => $columnName) {
            if (!$columnName) {
                continue;
            }

            $index = array_search(trim($columnName), $headers);

            if ($index !== false) {
                $columnIndexes[$field] = $index;
            }
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
