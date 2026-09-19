<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Expenses\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $categories = ExpenseCategory::where('company_id', $companyId)
            ->withCount('expenses')
            ->orderBy('name')
            ->get();

        return Inertia::render('ExpenseCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ExpenseCategories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['company_id'] = $request->user()->company_id;
        $validated['status'] = 'active';

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Category created successfully');
    }

    public function edit(ExpenseCategory $expenseCategory): Response
    {
        return Inertia::render('ExpenseCategories/Edit', [
            'category' => $expenseCategory,
        ]);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Category deleted successfully');
    }
}
