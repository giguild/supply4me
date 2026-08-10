<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    public function viewSalesReport(User $user): bool
    {
        return $user->hasPermissionTo('report.view_sales');
    }

    public function viewInventoryReport(User $user): bool
    {
        return $user->hasPermissionTo('report.view_inventory');
    }

    public function viewFinancialReport(User $user): bool
    {
        return $user->hasPermissionTo('report.view_financial');
    }
}
