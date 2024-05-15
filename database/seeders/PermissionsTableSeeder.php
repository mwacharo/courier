<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            'admin-list',
            'admin-details',
            'admin-create',
            'admin-edit',
            'admin-delete',
            'admin-import',
            'admin-export',
            'admin-reports',
            'admin-permission',
            'merchant-list',
            'merchant-details',
            'merchant-create',
            'merchant-edit',
            'merchant-delete',
            'merchant-import',
            'merchant-export',
            'merchant-reports',
            'rider-list',
            'rider-details',
            'rider-create',
            'rider-edit',
            'rider-delete',
            'rider-import',
            'rider-export',
            'rider-reports',
            'branch-list',
            'branch-details',
            'branch-create',
            'branch-edit',
            'branch-delete',
            'branch-import',
            'branch-export',
            'branch-reports',
            'country-list',
            'country-details',
            'country-create',
            'country-edit',
            'country-delete',
            'country-import',
            'country-export',
            'country-reports',
            'town-list',
            'town-details',
            'town-create',
            'town-edit',
            'town-delete',
            'town-import',
            'town-export',
            'town-reports',
            'zone-list',
            'zone-details',
            'zone-create',
            'zone-edit',
            'zone-delete',
            'zone-import',
            'zone-export',
            'zone-reports',
            'inventory-list',
            'inventory-details',
            'inventory-create',
            'inventory-edit',
            'inventory-delete',
            'inventory-import',
            'inventory-export',
            'inventory-reports',
            'order-list',
            'order-details',
            'order-create',
            'order-edit',
            'order-delete',
            'order-import',
            'order-export',
            'order-reports',
            'schedule-list',
            'schedule-details',
            'schedule-create',
            'schedule-edit',
            'schedule-delete',
            'schedule-import',
            'schedule-export',
            'schedule-reports',
            'finance-reports',
            'order-list',
            'order-details',
            'order-create',
            'order-edit',
            'order-delete',
            'order-import',
            'order-export',
            'call-centre-menu',
            'call-agent-list',
            'call-agent-details',
            'call-agent-create',
            'call-agent-edit',
            'call-agent-delete',
            'call-agent-reports',
            'call-agent-reports',
            'call-history-list',
            'call-history-details',
            'call-history-create',
            'call-history-edit',
            'call-history-delete',
            'call-history-reports',
            'call-agent-schedule',
            'change-order-status-date',
            'change-order-amount',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}