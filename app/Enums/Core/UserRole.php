<?php

namespace App\Enums\Core;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case SalesRep = 'sales_rep';
    case WarehouseStaff = 'warehouse_staff';
    case Accountant = 'accountant';
    case Driver = 'driver';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::SalesRep => 'Sales Rep',
            self::WarehouseStaff => 'Warehouse Staff',
            self::Accountant => 'Accountant',
            self::Driver => 'Driver',
            self::Viewer => 'Viewer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::Admin => 'primary',
            self::Manager => 'info',
            self::SalesRep => 'success',
            self::WarehouseStaff => 'warning',
            self::Accountant => 'secondary',
            self::Driver => 'dark',
            self::Viewer => 'light',
        };
    }
}
