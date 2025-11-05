<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding tenant database...');

        // Create permissions
        $permissions = [
            // User Management
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            
            // Role Management
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
            
            // Blog Category Management
            'view_blog::category',
            'view_any_blog::category',
            'create_blog::category',
            'update_blog::category',
            'delete_blog::category',
            'delete_any_blog::category',
            
            // Blog Management
            'view_blog',
            'view_any_blog',
            'create_blog',
            'update_blog',
            'delete_blog',
            'delete_any_blog',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Create Editor Role
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorRole->givePermissionTo([
            'view_blog::category',
            'view_any_blog::category',
            'create_blog::category',
            'update_blog::category',
            'view_blog',
            'view_any_blog',
            'create_blog',
            'update_blog',
        ]);

        // Create Viewer Role
        $viewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewerRole->givePermissionTo([
            'view_blog::category',
            'view_any_blog::category',
            'view_blog',
            'view_any_blog',
        ]);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@' . tenancy()->tenant->id . '.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('super_admin');

        // Create Editor User
        $editor = User::firstOrCreate(
            ['email' => 'editor@' . tenancy()->tenant->id . '.com'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
            ]
        );
        $editor->assignRole('editor');

        // Create Viewer User
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@' . tenancy()->tenant->id . '.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
            ]
        );
        $viewer->assignRole('viewer');

        // Create Blog Categories
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Latest technology news and updates',
                'is_published' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business insights and strategies',
                'is_published' => true,
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Lifestyle tips and trends',
                'is_published' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = BlogCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            // Create sample blogs for each category
            for ($i = 1; $i <= 3; $i++) {
                Blog::firstOrCreate(
                    ['slug' => $categoryData['slug'] . '-blog-post-' . $i],
                    [
                        'title' => ucfirst($categoryData['name']) . ' Blog Post ' . $i,
                        'content' => '<p>This is a sample blog post about ' . strtolower($categoryData['name']) . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>',
                        'category_id' => $category->id,
                        'is_published' => true,
                    ]
                );
            }
        }

        $this->command->info('Tenant database seeded successfully!');
        $this->command->info('Admin credentials: admin@' . tenancy()->tenant->id . '.com / password');
        $this->command->info('Editor credentials: editor@' . tenancy()->tenant->id . '.com / password');
        $this->command->info('Viewer credentials: viewer@' . tenancy()->tenant->id . '.com / password');
    }
}
