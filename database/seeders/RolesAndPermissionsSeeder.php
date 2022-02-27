<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions

        // Site Settings
        $siteSettingsPermissionNames = [
            'site.settings.view',
            'site.settings.libraries.view',
            'site.settings.libraries.edit',
            'site.settings.users.view',
            'site.settings.users.edit',
        ];
        $permissions = collect($siteSettingsPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });
        Permission::insert($permissions->toArray());

        // Library Settings
        $libraryPermissionNames = [
            'library.view',
            'library.import',
            'library.metadata.edit',
            'library.readingHistory.view',
            'library.readingHistory.edit',
            'library.share.library',
            'library.share.author',
            'library.share.series',
            'library.share.category',
            'library.share.book',
        ];
        $permissions = collect($libraryPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });
        Permission::insert($permissions->toArray());

        // User Settings / Profile
        $userSettingsPermissionNames = [
            'user.settings.view',
            'user.settings.edit',
        ];
        $permissions = collect($userSettingsPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });
        Permission::insert($permissions->toArray());

        // Sharing
        $userSettingsPermissionNames = [
            'sharing.view',
            'sharing.edit',
        ];
        $permissions = collect($userSettingsPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });
        Permission::insert($permissions->toArray());


        // create roles and assign created permissions

        $permissionsByRole = [
            'Super Admin' => [],
            'Admin' => [
                'site.settings.view',
                'site.settings.libraries.view',
                'site.settings.libraries.edit',
                'site.settings.users.view',
                'site.settings.users.edit',
                'library.view',
                'library.import',
                'library.metadata.edit',
                'library.readingHistory.view',
                'library.readingHistory.edit',
                'library.share.library',
                'library.share.author',
                'library.share.series',
                'library.share.category',
                'library.share.book',
                'user.settings.view',
                'user.settings.edit',
                'sharing.view',
                'sharing.edit',
            ],
            'Librarian' => [
                'site.settings.view',
                'site.settings.libraries.view',
                'site.settings.users.view',
                'library.view',
                'library.import',
                'library.metadata.edit',
                'library.readingHistory.view',
                'library.readingHistory.edit',
                'library.share.library',
                'library.share.author',
                'library.share.series',
                'library.share.category',
                'library.share.book',
                'user.settings.view',
                'user.settings.edit',
                'sharing.view',
                'sharing.edit',
            ],
            'User' => [
                'library.view',
                'library.readingHistory.view',
                'library.readingHistory.edit',
                'user.settings.view',
                'user.settings.edit',
            ]
        ];

        foreach ($permissionsByRole as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($permissions);
        }
    }
}
