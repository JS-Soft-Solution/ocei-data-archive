<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Industry standard: Filter, Search, and Paginate in one query
        $query = User::query();

        // 1. Search Logic
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        // 2. Role Filter
        if ($request->filled('role')) {
            $query->where('admin_type', $request->input('role'));
        }

        // 3. Dynamic Pagination Limit (10, 25, 50, etc)
        $limit = $request->input('limit', 10);

        // Handle "All" case
        $users = $limit === 'all' ? $query->get() : $query->paginate((int)$limit);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        // Handle Image Upload
        if ($request->hasFile('applicant_image')) {
            $path = $request->file('applicant_image')->store('users', 'public');
            $validated['applicant_image'] = $path;
        }

        // Generate User ID automatically
        $lastId = User::max('id') + 1;
        $prefix = strtoupper(substr($validated['admin_type'], 0, 3));
        $validated['user_id'] = $prefix . '-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);

        // Hash Password
        $validated['password'] = Hash::make($validated['password']);
        $validated['otp_status'] = 'verified'; // Admin created users are verified by default

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        // Handle Password only if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Handle Image replacement
        if ($request->hasFile('applicant_image')) {
            // Delete old image if exists
            if ($user->applicant_image) {
                Storage::disk('public')->delete($user->applicant_image);
            }
            $validated['applicant_image'] = $request->file('applicant_image')->store('users', 'public');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Soft delete logic
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User removed successfully');
    }
}
