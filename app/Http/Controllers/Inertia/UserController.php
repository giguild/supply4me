<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Branches\Branch;
use App\Models\Companies\Company;
use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::where('company_id', $request->user()->company_id)
            ->with('company');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $roles = \Spatie\Permission\Models\Role::all();
        $companies = Company::all();
        $branches = Branch::where('company_id', $companyId)->get();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'companies' => $companies,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'company_id' => 'required|exists:companies,id',
            'status' => 'nullable|string',
            'role' => 'nullable|string|exists:roles,name',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'branches' => 'nullable|array',
            'branches.*' => 'exists:branches,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
            'region' => $validated['region'] ?? null,
            'state' => $validated['state'] ?? null,
            'company_id' => $validated['company_id'],
            'status' => $validated['status'] ?? 'active',
        ]);

        $roles = $validated['roles'] ?? ($validated['role'] ? [$validated['role']] : []);
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        if (!empty($validated['branches'])) {
            $user->branches()->sync($validated['branches']);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function show(Request $request, User $user): Response
    {
        $user->load(['company', 'branches', 'roles']);

        return Inertia::render('Users/Show', [
            'user' => $user,
        ]);
    }

    public function edit(Request $request, User $user): Response
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $companies = Company::all();
        $branches = Branch::where('company_id', $request->user()->company_id)->get();

        $user->load(['branches', 'roles']);

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'companies' => $companies,
            'branches' => $branches,
        ]);
    }

    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'company_id' => 'required|exists:companies,id',
            'status' => 'nullable|string',
            'role' => 'nullable|string|exists:roles,name',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'branches' => 'nullable|array',
            'branches.*' => 'exists:branches,id',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
            'region' => $validated['region'] ?? null,
            'state' => $validated['state'] ?? null,
            'company_id' => $validated['company_id'],
            'status' => $validated['status'] ?? $user->status,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        $roles = $validated['roles'] ?? ($validated['role'] ? [$validated['role']] : []);
        $user->syncRoles($roles);
        $user->branches()->sync($validated['branches'] ?? []);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
