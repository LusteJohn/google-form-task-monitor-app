<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Student;
use App\Models\GoogleSheet;

class TaskController extends Controller
{
    public function index()
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $tasks = Task::with('student')->get();

        return view('pages.task-list', [
            'tasks' => $tasks,
        ]);
    }

    public function syncTasks(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $linkedSheet = GoogleSheet::where('user_id', session('user_id'))->first();

        if (!$linkedSheet) {
            return back()->withErrors(['sync' => 'No Google Sheet linked. Please paste a Form URL first.']);
        }

        if (!$linkedSheet->file_url_column) {
            return back()->withErrors(['sync' => 'No file URL column configured. Please update the linked sheet to include a file upload column.']);
        }

        $students = new StudentController();
        $studentData = $students->fetchStudentsFromSheet(
            "https://docs.google.com/spreadsheets/d/{$linkedSheet->spreadsheet_id}",
            [
                'name' => $linkedSheet->name_column,
                'email' => $linkedSheet->email_column,
                'phone' => $linkedSheet->phone_column,
                'address' => $linkedSheet->address_column,
            ]
        );

        $taskData = $students->fetchTasksFromSheet(
            "https://docs.google.com/spreadsheets/d/{$linkedSheet->spreadsheet_id}",
            [
                'file_url' => $linkedSheet->file_url_column,
                'status' => $linkedSheet->status_column,
                'due_date' => $linkedSheet->due_date_column,
            ]
        );

        $imported = 0;
        $updated = 0;

        foreach ($taskData as $index => $task) {
            $student = Student::where('email', $studentData[$index]['email'] ?? null)->first();

            if (!$student) {
                $student = Student::create([
                    'name' => $studentData[$index]['name'] ?? null,
                    'email' => $studentData[$index]['email'] ?? null,
                    'phone_number' => $studentData[$index]['phone_number'] ?? null,
                    'address' => $studentData[$index]['address'] ?? null,
                ]);
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
                $updated++;
            } else {
                Task::create([
                    'student_id' => $student->student_id,
                    'task_name' => $linkedSheet->form_name,
                    'file_url' => $task['file_url'],
                    'status' => $task['status'] ?? 'pending',
                    'due_date' => $task['due_date'] ?? now()->toDateString(),
                ]);
                $imported++;
            }
        }

        $linkedSheet->update(['last_synced_at' => now()]);

        return back()->with('success', "Task sync complete. {$imported} added, {$updated} updated.");
    }
}
