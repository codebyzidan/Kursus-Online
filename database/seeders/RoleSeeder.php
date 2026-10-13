<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.manage',
            'categories.manage',
            'courses.manage',
            'courses.review',
            'orders.manage',
            'coupons.manage',
            'payouts.manage',
            'reviews.moderate',
            'reports.view',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate(UserRole::Admin->value, 'web');
        $admin->syncPermissions($permissions);

        $instructor = Role::findOrCreate(UserRole::Instructor->value, 'web');
        $instructor->syncPermissions(['reports.view']);

        Role::findOrCreate(UserRole::Student->value, 'web');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
