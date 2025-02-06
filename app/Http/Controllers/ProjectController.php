<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Task;
use App\Models\Member;

use App\Models\TaskProgress;

use Str;
use App\Events\NewUserCreated;
use App\Events\NewProjectCreated;

class ProjectController extends Controller
{

    public function getProject(Request $request, $slug)
    {
        $project = Project::with(['tasks.task_members.members','task_progress'])
            ->where('projects.slug', $slug)
            ->first();

        return response(['data' => $project]);
    }

    public function index(Request $request)
    {
        $query = $request->get('query');
        $user = auth()->user();

        // Base query
        $projects = Project::with(['task_progress'])
            ->notArchived();

        // If user is a member, get projects from their creator
        if ($user instanceof \App\Models\Member) {
            $projects->where('user_id', $user->user_id);
        } else {
            // If user is a regular user, get their own projects
            $projects->where('user_id', $user->id);
        }

        if (!is_null($query) && $query !== '') {
            $projects->where('title', 'like', '%' . $query . '%')
                ->orderBy('id', 'desc');
        }

        return response(['data' => $projects->paginate(10)], 200);
    }
    public function store(Request $request)
    {

        return DB::transaction(function () use ($request) {
            $fields = $request->all();

            $errors = Validator::make($fields, [
                'title' => 'required',
                // 'status' => 'required',
                'startDate' => 'required',
                'endDate' => 'required',
            ]);

            if ($errors->fails()) {
                return response($errors->errors()->all(), 422);
            }

            $project = Project::create([
                'title' => $fields['title'],
                'startDate' => $fields['startDate'],
                'endDate' => $fields['endDate'],
                'slug' => Project::createSlug($fields['title']),
                'user_id' => auth()->id()
            ]);

            TaskProgress::create([
                'projectId' => $project->id,
                'user_id' => auth()->id(),
                'pinned_on_dashbaord' => TaskProgress::NOT_PINNED_ON_DASHBOARD,
                'progress' => TaskProgress::INITIAL_PROJECT_PERCENT
            ]);

            $count = Project::count();
            NewProjectCreated::dispatch($count);

            return response(['message' => 'project created'], 200);
        });
    }

    public function update(Request $request)
    {

        $fields = $request->all();

        $errors = Validator::make($fields, [
            'id' => 'required',
            'title' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $project = Project::where('id', $fields['id'])->update([
            'title' => $fields['title'],
            'startDate' => $fields['startDate'],
            'endDate' => $fields['endDate'],
            'slug' => Project::createSlug($fields['title'])

        ]);

        return response(['message' => 'project updated'], 200);
    }


    public function pinnedProject(Request $request)
    {

        return DB::transaction(function () use ($request) {


            $fields = $request->all();

            $errors = Validator::make($fields, [
                'projectId' => 'required|numeric',

            ]);

            if ($errors->fails()) {
                return response($errors->errors()->all(), 422);
            }
            TaskProgress::where('pinned_on_dashbaord', TaskProgress::PINNED_ON_DASHBOARD)
                ->update(['pinned_on_dashbaord' => TaskProgress::NOT_PINNED_ON_DASHBOARD]);

            TaskProgress::where('projectId', $fields['projectId'])
                ->update([
                    'pinned_on_dashbaord' => TaskProgress::PINNED_ON_DASHBOARD
                ]);
            return response(['message' => 'project pinned on dashboard !']);
        });
    }


    public function countProject()
    {
        $user = auth()->user();

        // If user is a member, get count from their creator
        if ($user instanceof \App\Models\Member) {
            $count = Project::where('user_id', $user->user_id)->count();
        } else {
            // If user is a regular user, get their own project count
            $count = Project::where('user_id', $user->id)->count();
        }

        return response(['count' => $count]);
    }

    public function getPinnedProject(Request $request)
    {
        $user = auth()->user();

        $query = DB::table('task_progress')
            ->join('projects', 'task_progress.projectId', '=', 'projects.id')
            ->select('projects.id', 'projects.title')
            ->where('task_progress.pinned_on_dashbaord', TaskProgress::PINNED_ON_DASHBOARD);

        // Filter by user
        if ($user instanceof \App\Models\Member) {
            $query->where('projects.user_id', $user->user_id);
        } else {
            $query->where('projects.user_id', $user->id);
        }

        $project = $query->first();

        return response(['data' => $project ?? null]);
    }


    public function getProjectChartData(Request $request)
    {
        $projectId = $request->projectId;
        $user = auth()->user();

        // Verify the project belongs to the user or their creator
        $project = Project::where('id', $projectId);
        if ($user instanceof \App\Models\Member) {
            $project->where('user_id', $user->user_id);
        } else {
            $project->where('user_id', $user->id);
        }

        if (!$project->exists()) {
            return response(['message' => 'Project not found'], 404);
        }

        $taskProgress = TaskProgress::where('projectId', $projectId)
            ->select('progress')
            ->first();

        // Get task counts using the updated method that includes not started tasks
        $taskArray = Task::countCompletedAndPendingTask($projectId);

        return response([
            'tasks' => $taskArray, // This will now contain [notStarted, pending, completed]
            'progress' => intval($taskProgress->progress)
        ]);
    }

    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response(['message' => 'Project not found'], 404);
        }

        // Instead of deleting, mark as archived
        $project->update([
            'is_archived' => true,
            'archived_at' => now()
        ]);

        return response(['message' => 'Project archived successfully'], 200);
    }


}
