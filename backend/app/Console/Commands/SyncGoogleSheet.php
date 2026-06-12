<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\StudentController;
use App\Models\GoogleSheet;
use App\Models\Student;
use App\Models\Task;

class SyncGoogleSheet extends Command
{
    protected $signature = 'sheets:sync {--force : Force sync even if recently synced}';
    protected $description = 'Sync students and tasks from all linked Google Sheets';

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
                $controller = new StudentController();
                $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$sheet->spreadsheet_id}";

                $students = $controller->fetchStudentsFromSheet($spreadsheetUrl, [
                    'name' => $sheet->name_column,
                    'email' => $sheet->email_column,
                    'phone' => $sheet->phone_column,
                    'address' => $sheet->address_column,
                ]);

                $studentImported = 0;
                $studentUpdated = 0;

                foreach ($students as $studentData) {
                    $existing = Student::where('email', $studentData['email'])->first();

                    if ($existing) {
                        $existing->update([
                            'name' => $studentData['name'],
                            'phone_number' => $studentData['phone_number'],
                            'address' => $studentData['address'],
                        ]);
                        $studentUpdated++;
                    } else {
                        Student::create($studentData);
                        $studentImported++;
                    }
                }

                $taskImported = 0;
                $taskUpdated = 0;

                if ($sheet->file_url_column) {
                    $tasks = $controller->fetchTasksFromSheet($spreadsheetUrl, [
                        'file_url' => $sheet->file_url_column,
                        'status' => $sheet->status_column,
                        'due_date' => $sheet->due_date_column,
                    ]);

                    foreach ($tasks as $index => $taskData) {
                        $student = Student::where('email', $students[$index]['email'] ?? null)->first();

                        if (!$student) {
                            continue;
                        }

                        $existing = Task::where('student_id', $student->student_id)
                            ->where('task_name', $sheet->form_name)
                            ->first();

                        if ($existing) {
                            $existing->update([
                                'file_url' => $taskData['file_url'],
                                'status' => $taskData['status'] ?? 'pending',
                                'due_date' => $taskData['due_date'] ?? now()->toDateString(),
                            ]);
                            $taskUpdated++;
                        } else {
                            Task::create([
                                'student_id' => $student->student_id,
                                'task_name' => $sheet->form_name,
                                'file_url' => $taskData['file_url'],
                                'status' => $taskData['status'] ?? 'pending',
                                'due_date' => $taskData['due_date'] ?? now()->toDateString(),
                            ]);
                            $taskImported++;
                        }
                    }
                }

                $sheet->update(['last_synced_at' => now()]);

                $this->info("  Students - Added: {$studentImported}, Updated: {$studentUpdated}");
                $this->info("  Tasks - Added: {$taskImported}, Updated: {$taskUpdated}");
            } catch (\Exception $e) {
                $this->error("  Failed: " . $e->getMessage());
            }
        }

        $this->info('Sync complete.');
    }
}
