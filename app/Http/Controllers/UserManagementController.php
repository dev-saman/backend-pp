<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * List all users with search and pagination.
     */
    public function index(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;
        $search  = $request->search;

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();

        return view('user-management.index', compact('users', 'perPage', 'search'));
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user,super_admin',
            'phone'    => 'nullable|string|max:30',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
            'phone'    => $request->phone,
            'is_active'=> true,
        ]);

        return back()->with('success', 'User created successfully.');
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,user,super_admin',
            'phone'    => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Toggle the is_active status of a user (AJAX).
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'status'    => 'success',
            'is_active' => $user->is_active,
            'message'   => 'User status updated.',
        ]);
    }

    /**
     * Soft-delete a user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'You cannot delete your own account.'], 403);
            }
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'User deleted.']);
        }

        return back()->with('success', 'User deleted successfully.');
    }
}
