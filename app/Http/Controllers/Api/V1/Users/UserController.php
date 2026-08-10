<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use App\Resources\Core\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->department, fn ($q, $d) => $q->where('department', $d))
            ->paginate($request->get('per_page', 15));

        return $this->paginated($users, UserResource::collection($users->items()));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['company_id'] = $request->user()->company_id;

        $user = User::create($validated);

        return $this->created(
            new UserResource($user),
            'User created successfully'
        );
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(
            new UserResource($user->load(['company', 'branches']))
        );
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $user->update($validated);

        return $this->success(
            new UserResource($user->fresh()),
            'User updated successfully'
        );
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return $this->error('You cannot delete your own account', 422);
        }

        $user->delete();

        return $this->noContent('User deleted successfully');
    }
}
