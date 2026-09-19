<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Expenses\Expense;
use App\Models\Expenses\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $query = Expense::where('company_id', $companyId)
            ->with('category')
            ->latest('expense_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->where('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        $expenses = $query->paginate(15)->withQueryString();

        $categories = ExpenseCategory::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $totalExpenses = Expense::where('company_id', $companyId)->sum('amount');
        $approvedExpenses = Expense::where('company_id', $companyId)->where('status', 'approved')->sum('amount');
        $pendingExpenses = Expense::where('company_id', $companyId)->where('status', 'pending')->sum('amount');

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'summary' => [
                'total' => $totalExpenses,
                'approved' => $approvedExpenses,
                'pending' => $pendingExpenses,
            ],
            'filters' => $request->only(['search', 'status', 'category_id', 'start_date', 'end_date']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $categories = ExpenseCategory::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return Inertia::render('Expenses/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reference' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|max:5120',
        ]);

        $validated['company_id'] = $request->user()->company_id;
        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'pending';

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        unset($validated['receipt']);

        $expense = Expense::create($validated);

        return redirect()->route('expenses.show', $expense)->with('success', 'Expense recorded successfully');
    }

    public function show(Request $request, Expense $expense): Response
    {
        $expense->load(['category', 'creator']);

        return Inertia::render('Expenses/Show', [
            'expense' => $expense,
        ]);
    }

    public function edit(Request $request, Expense $expense): Response
    {
        $companyId = $request->user()->company_id;

        $categories = ExpenseCategory::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reference' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|string|in:pending,approved,rejected',
            'receipt' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        unset($validated['receipt']);

        $expense->update($validated);

        return redirect()->route('expenses.show', $expense)->with('success', 'Expense updated successfully');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully');
    }
}
