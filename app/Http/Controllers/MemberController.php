<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('query');
        $user = auth()->user();

        $members = Member::query()
            ->whereNull('deleted_at');

        // If user is a member, get members from their creator
        if ($user instanceof \App\Models\Member) {
            $members->where('user_id', $user->user_id);
        } else {
            // If user is a regular user, get their own members
            $members->where('user_id', $user->id);
        }

        if (!is_null($query) && $query !== '') {
            $members->where(function ($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                    ->orWhere('last_name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%');
            });
        }

        return response(['data' => $members->paginate(100)], 200);
    }

    public function store(Request $request)
    {
        $fields = $request->all();

        $errors = Validator::make($fields, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:members',
            'password' => 'required|min:6',
            'middle_name' => 'nullable|string|max:255',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $member = Member::create([
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'] ?? null,
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'role' => 'member',
            'user_id' => auth()->id(),
        ]);

        return response(['message' => 'Member created successfully'], 200);
    }

    public function update(Request $request)
    {
        $fields = $request->all();

        $errors = Validator::make($fields, [
            'id' => 'required|numeric|exists:members,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:members,email,' . $fields['id'],
            'middle_name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',

        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        Member::where('id', $fields['id'])->update([
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'] ?? null,
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        return response(['message' => 'Member updated'], 200);
    }

    public function destroy($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response(['message' => 'Member not found'], 404);
        }

        $member->update([
            'is_deleted' => true,
            'deleted_at' => now()
        ]);

        return response(['message' => 'Member deleted successfully'], 200);
    }

}
