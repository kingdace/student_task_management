<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Task; //
use App\Models\Member; //
use App\Models\TaskMember;
use App\Models\TaskAttachment;
use Illuminate\Support\Facades\Storage; //
use Illuminate\Support\Facades\Log;
use App\Models\TaskDocument;
use App\Models\TaskComment;

class TaskController extends Controller
{
    public function createTask(Request $request){
        return DB::transaction(function() use ($request){
            $fields = $request->all();

            $errors = Validator::make($fields, [
                'name' => 'required',
                'projectId' => 'required|numeric',
                'memberIds' => 'required|array',
                'memberIds.*'=>'numeric',
                'description' => 'nullable|string',
                'due_date' => 'required|date',
                'priority' => 'required|integer|min:0|max:2',
            ]);

            if ($errors->fails()) {
                return response($errors->errors()->all(), 422);
            }

            $task = Task::create([
                'projectId' => $fields['projectId'],
                'name' => $fields['name'],
                'description' => $fields['description'],
                'due_date' => $fields['due_date'],
                'priority' => $fields['priority'],
                'progress' => 0,
                'status' => Task::NOT_STARTED,
            ]);

            $members = $fields['memberIds'];
            for($i=0; $i<count($members);$i++){
                TaskMember::create([
                    'projectId' => $fields['projectId'],
                    'taskId' => $task->id,
                    'memberId' => $members[$i]
                ]);
            }

            return response([
                'message' => 'Task created successfully!',
                'task' => $task // Return the created task
            ]);
        });
    }
    public function TaskToNotStartedToPending(Request $request){

        Task::changeTaskStatus($request->taskId,Task::PENDING);
        Task::handleProjectProgress($request->projectId);
        return response(['message'=>'task move to pending'],200);
    }
    public function TaskToPendingToCompleted(Request $request){
        Task::changeTaskStatus($request->taskId,Task::COMPLETED);
        Task::handleProjectProgress($request->projectId);
        return response(['message'=>'task move to completed'],200);
    }


    public function TaskToNotStartedToCompleted(Request $request){

        Task::changeTaskStatus($request->taskId,Task::COMPLETED);
        return response(['message'=>'task move to completed'],200);
    }



    public function TaskToPendingToNotStarted(Request $request){

        Task::changeTaskStatus($request->taskId,Task::NOT_STARTED);
        return response(['message'=>'task move to not started'],200);
    }

    public function TaskToCompletedToPending(Request $request){

        Task::changeTaskStatus($request->taskId,Task::PENDING);
        Task::handleProjectProgress($request->projectId);
        return response(['message'=>'task move to Pending'],200);
    }

    public function TaskToCompletedToNotStarted(Request $request){

        Task::changeTaskStatus($request->taskId,Task::NOT_STARTED);
        return response(['message'=>'task move to not started'],200);
    }

    public function uploadAttachment(Request $request, $taskId)
    {
        try {
            // Validate based on type of upload
            if ($request->hasFile('file')) {
                $request->validate([
                    'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt|max:10240',
                    'comment' => 'nullable|string'
                ]);
            } else {
                $request->validate([
                    'url' => 'required|url',
                    'comment' => 'nullable|string'
                ]);
            }

            // Get the authenticated user (either User or Member)
            $authenticatedUser = auth()->user();
            $userId = null;
            $memberId = null;

            if ($authenticatedUser instanceof \App\Models\Member) {
                $memberId = $authenticatedUser->id;
            } else {
                $userId = $authenticatedUser->id;
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if (!$file->isValid()) {
                    throw new \Exception('File is not valid');
                }

                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $path = 'task-attachments/' . $fileName;

                if (!copy($file->getRealPath(), public_path($path))) {
                    throw new \Exception('Failed to copy file');
                }

                $attachment = TaskAttachment::create([
                    'taskId' => $taskId,
                    'memberId' => $memberId,
                    'userId' => $userId,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'comment' => $request->comment
                ]);
            } else {
                // Handle URL attachment
                $attachment = TaskAttachment::create([
                    'taskId' => $taskId,
                    'memberId' => $memberId,
                    'userId' => $userId,
                    'file_type' => 'url',
                    'file_path' => $request->url,
                    'file_name' => 'URL Link',
                    'comment' => $request->comment
                ]);
            }

            return response()->json([
                'message' => 'Attachment uploaded successfully',
                'attachment' => $attachment
            ]);
        } catch (\Exception $e) {
            Log::error('Attachment upload error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to upload attachment: ' . $e->getMessage()], 500);
        }
    }

    public function updateProgress(Request $request, $taskId)
    {
        try {
            $request->validate([
                'progress' => 'required|integer|min:0|max:100',
                'projectId' => 'required|exists:projects,id'
            ]);

            $task = Task::findOrFail($taskId);
            $task->progress = $request->progress;
            $task->save();

            // Update project progress
            Task::handleProjectProgress($request->projectId);

            return response()->json(['message' => 'Progress updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getTaskDetails($taskId)
    {
        try {
            $task = Task::with(['attachments.member', 'attachments.user', 'task_members.members'])
                ->findOrFail($taskId);
            return response()->json($task);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Task not found'], 404);
        }
    }

    public function updateTask(Request $request, $taskId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'priority' => 'required|integer|min:0|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        try {
            $task = Task::findOrFail($taskId);
            $task->update($request->all());
            return response()->json(['message' => 'Task updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update task'], 500);
        }
    }

    public function uploadTaskDocument(Request $request, $taskId)
    {
        try {
            $request->validate([
                'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,txt',
                'link_url' => 'nullable|url',
                'comment' => 'nullable|string'
            ]);

            if (!$request->hasFile('file') && !$request->link_url) {
                throw new \Exception('No file or link was provided');
            }

            $authenticatedUser = auth()->user();
            $userId = null;
            $memberId = null;

            if ($authenticatedUser instanceof \App\Models\Member) {
                $memberId = $authenticatedUser->id;
            } else {
                $userId = $authenticatedUser->id;
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if (!$file->isValid()) {
                    throw new \Exception('File is not valid');
                }

                // Create directory if it doesn't exist
                $uploadPath = public_path('task-documents');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Generate a safe filename
                $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $path = 'task-documents/' . $fileName;

                // Use copy instead of move for better reliability on Windows
                if (!copy($file->getRealPath(), public_path($path))) {
                    throw new \Exception('Failed to copy file');
                }

                $document = TaskDocument::create([
                    'taskId' => $taskId,
                    'memberId' => $memberId,
                    'userId' => $userId,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'document_type' => $file->getClientOriginalExtension(),
                    'comment' => $request->comment
                ]);
            } elseif ($request->link_url) {
                $document = TaskDocument::create([
                    'taskId' => $taskId,
                    'memberId' => $memberId,
                    'userId' => $userId,
                    'file_type' => 'link',
                    'link_url' => $request->link_url,
                    'comment' => $request->comment
                ]);
            }

            return response()->json([
                'message' => 'Document uploaded successfully',
                'document' => $document->load(['member', 'user'])
            ]);
        } catch (\Exception $e) {
            Log::error('Document upload error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'upload_path' => public_path('task-documents'),
                'is_writable' => is_writable(public_path('task-documents')),
                'file_name' => $fileName ?? null,
                'file_exists' => $request->hasFile('file'),
                'file_valid' => $request->hasFile('file') ? $request->file('file')->isValid() : false,
                'real_path' => $request->hasFile('file') ? $request->file('file')->getRealPath() : null
            ]);
            return response()->json(['message' => 'Failed to upload document: ' . $e->getMessage()], 500);
        }
    }

    public function addComment(Request $request, $taskId)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $authenticatedUser = auth()->user();
        $userId = null;
        $memberId = null;

        if ($authenticatedUser instanceof \App\Models\Member) {
            $memberId = $authenticatedUser->id;
        } else {
            $userId = $authenticatedUser->id;
        }

        $comment = TaskComment::create([
            'taskId' => $taskId,
            'userId' => $userId,
            'memberId' => $memberId,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load(['user', 'member'])
        ]);
    }

    public function getComments($taskId)
    {
        $comments = TaskComment::where('taskId', $taskId)
            ->with(['user', 'member'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comments);
    }



}
