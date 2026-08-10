<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\SalesReportRequest;
use App\Http\Requests\Reports\InventoryReportRequest;
use App\Resources\Reports\SalesReportResource;
use App\Resources\Reports\InventoryReportResource;
use App\Services\Reporting\SalesReportService;
use App\Services\Reporting\InventoryReportService;
use App\Services\Reporting\FinancialReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected SalesReportService $salesReportService,
        protected InventoryReportService $inventoryReportService,
        protected FinancialReportService $financialReportService
    ) {}

    public function sales(SalesReportRequest $request): JsonResponse
    {
        $report = $this->salesReportService->generate($request->validated());

        return $this->success(
            new SalesReportResource($report),
            'Sales report generated successfully'
        );
    }

    public function inventory(InventoryReportRequest $request): JsonResponse
    {
        $report = $this->inventoryReportService->generate($request->validated());

        return $this->success(
            new InventoryReportResource($report),
            'Inventory report generated successfully'
        );
    }

    public function financial(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'sometimes|string|in:day,week,month',
        ]);

        $report = $this->financialReportService->generate($validated);

        return $this->success($report, 'Financial report generated successfully');
    }

    public function salesSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period' => 'sometimes|string|in:today,yesterday,this_week,this_month,this_year,last_month,last_year',
        ]);

        $summary = $this->salesReportService->getSummary($validated['period'] ?? 'this_month');

        return $this->success($summary, 'Sales summary retrieved successfully');
    }

    public function topProducts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $products = $this->salesReportService->getTopProducts(
            $validated['limit'] ?? 10,
            $validated['date_from'] ?? null,
            $validated['date_to'] ?? null
        );

        return $this->success($products, 'Top products retrieved successfully');
    }

    public function topCustomers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $customers = $this->salesReportService->getTopCustomers(
            $validated['limit'] ?? 10,
            $validated['date_from'] ?? null,
            $validated['date_to'] ?? null
        );

        return $this->success($customers, 'Top customers retrieved successfully');
    }
}
