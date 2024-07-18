<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = get_enum_values(PermissionEnum::cases());
        $permissions = array_map(fn($name) => Permission::firstOrCreate([
            'name' => $name,
        ]), $permissions);
        $permissions = array_column($permissions, 'id', 'name');

        $defaultRolePerms = Constants::ROLE_PERMISSIONS;
        foreach ($defaultRolePerms as $role => $perms) {
            /** @var Role $role */
            $role = Role::firstOrCreate([
                'name' => $role,
            ]);
            $perms = array_map(fn(PermissionEnum $name) => $permissions[$name->value], $perms);
            $role->permissions()->sync($perms);
        }
    }
}
