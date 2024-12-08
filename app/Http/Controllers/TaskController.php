<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Task; // Importing the Task model
use App\Http\Requests\Task\StoreRequest; // Importing the StoreRequest form request
use App\Http\Requests\Task\UpdateRequest; // Importing the UpdateRequest form request

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        // Retrieve uncompleted and completed tasks from the database and pass them to the view
        return response()->view('tasks.index', [
            'unCompletedTasks' => Task::where('is_completed', 0)->orderBy('updated_at', 'desc')->get(),
            'completedTasks' => Task::where('is_completed', 1)->orderBy('updated_at', 'desc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        // Return the view for creating a new task
        return response()->view('tasks.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        // Validate the incoming request
        $validated = $request->validated();

        // If a file is uploaded, store it in the public storage
        if ($request->hasFile('info_file')) {
            $filePath = Storage::disk('public')->put('files/tasks/info-files', request()->file('info_file'));
            $validated['info_file'] = $filePath;
        }

        // Create a new task with the validated data
        $create = Task::create($validated);

        if($create) {
            // Flash a success notification and redirect to the task index page
            session()->flash('notif.success', 'Task created successfully!');
            return redirect()->route('tasks.index');
        }

        return abort(500); // Return a server error if the task creation fails
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        // Retrieve and display the specified task
        return response()->view('tasks.show', [
            'task' => Task::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        // Retrieve the task with the specified ID and pass it to the view for editing
        return response()->view('tasks.form', [
            'task' => Task::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id): RedirectResponse
    {
        // Find the task with the specified ID
        $task = Task::findOrFail($id);
        
        // Validate the incoming request
        $validated = $request->validated();

        // If an info file is uploaded, update the file path and delete the old file if exists
        if ($request->hasFile('info_file')) {
            if (isset($task->info_file)) {
                Storage::disk('public')->delete($task->info_file);
            }
            $filePath = Storage::disk('public')->put('files/tasks/info-files', request()->file('info_file'), 'public');
            $validated['info_file'] = $filePath;
        }

        // Update the task with the validated data
        $update = $task->update($validated);

        if($update) {
            // Flash a success notification and redirect to the task index page
            session()->flash('notif.success', 'Task updated successfully!');
            return redirect()->route('tasks.index');
        }

        return abort(500); // Return a server error if the task update fails
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // Find the task with the specified ID
        $task = Task::findOrFail($id);

        // If an info file exists, delete it from storage
        if (isset($task->info_file)) {
            Storage::disk('public')->delete($task->info_file);
        }
        
        // Delete the task
        $delete = $task->delete($id);

        if($delete) {
            // Flash a success notification and redirect to the task index page
            session()->flash('notif.success', 'Task deleted successfully!');
            return redirect()->route('tasks.index');
        }

        return abort(500); // Return a server error if the task deletion fails
    }

    /**
     * Mark the specified task as completed.
     */
    public function markCompleted(string $id): RedirectResponse
    {
        // Find the task with the specified ID and update its completion status
        $task = Task::findOrFail($id);
        $isCompleted = $task->update(['is_completed' => 1]);

        if($isCompleted) {
            // Flash a success notification and redirect to the task index page
            session()->flash('notif.success', 'Task marked as completed!');
            return redirect()->route('tasks.index');
        }

        return abort(500); // Return a server error if updating the task fails
    }

    /**
     * Mark the specified task as uncompleted.
     */
    public function markUncompleted(string $id): RedirectResponse
    {
        // Find the task with the specified ID and update its completion status
        $task = Task::findOrFail($id);
        $isCompleted = $task->update(['is_completed' => 0]);

        if($isCompleted) {
            // Flash a success notification and redirect to the task index page
            session()->flash('notif.success', 'Task marked as uncompleted!');
            return redirect()->route('tasks.index');
        }

        return abort(500); // Return a server error if updating the task fails
    }
}