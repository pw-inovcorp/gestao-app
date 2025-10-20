<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar Permissões
        $permissions = [
            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Entities
            'entities.view',
            'entities.create',
            'entities.edit',
            'entities.delete',
            'entities.vies',

            // Contacts
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.delete',

            // Articles
            'articles.view',
            'articles.create',
            'articles.edit',
            'articles.delete',

            // Proposals
            'proposals.view',
            'proposals.create',
            'proposals.edit',
            'proposals.delete',
            'proposals.convert',
            'proposals.pdf',

            // Orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.convert',
            'orders.pdf',

            // Supplier Orders
            'supplier-orders.view',
            'supplier-orders.edit',
            'supplier-orders.delete',
            'supplier-orders.pdf',

            // Supplier Invoices
            'supplier-invoices.view',
            'supplier-invoices.create',
            'supplier-invoices.edit',
            'supplier-invoices.delete',
            'supplier-invoices.payment',

            // Settings
            'settings.view',
            'settings.edit',

            // Roles & Permissions Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar Roles e atribuir permissões

        // 1. Super Admin - Todas as permissões
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - Tudo exceto users e roles management
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'entities.view', 'entities.create', 'entities.edit', 'entities.delete', 'entities.vies',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'articles.view', 'articles.create', 'articles.edit', 'articles.delete',
            'proposals.view', 'proposals.create', 'proposals.edit', 'proposals.delete', 'proposals.convert', 'proposals.pdf',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.convert', 'orders.pdf',
            'supplier-orders.view', 'supplier-orders.edit', 'supplier-orders.delete', 'supplier-orders.pdf',
            'supplier-invoices.view', 'supplier-invoices.create', 'supplier-invoices.edit', 'supplier-invoices.delete', 'supplier-invoices.payment',
            'settings.view', 'settings.edit',
        ]);

        // 3. Gestor Comercial
        $gestorComercial = Role::create(['name' => 'Gestor Comercial']);
        $gestorComercial->givePermissionTo([
            'entities.view', 'entities.create', 'entities.edit', 'entities.vies',
            'contacts.view', 'contacts.create', 'contacts.edit',
            'articles.view',
            'proposals.view', 'proposals.create', 'proposals.edit', 'proposals.delete', 'proposals.convert', 'proposals.pdf',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.convert', 'orders.pdf',
        ]);

        // 4. Gestor Financeiro
        $gestorFinanceiro = Role::create(['name' => 'Gestor Financeiro']);
        $gestorFinanceiro->givePermissionTo([
            'entities.view',
            'supplier-orders.view', 'supplier-orders.pdf',
            'supplier-invoices.view', 'supplier-invoices.create', 'supplier-invoices.edit', 'supplier-invoices.delete', 'supplier-invoices.payment',
        ]);

        // 5. Operacional
        $operacional = Role::create(['name' => 'Operacional']);
        $operacional->givePermissionTo([
            'entities.view',
            'contacts.view',
            'articles.view',
            'orders.view',
            'supplier-orders.view',
        ]);

        // 6. Visualizador
        $visualizador = Role::create(['name' => 'Visualizador']);
        $visualizador->givePermissionTo([
            'entities.view',
            'contacts.view',
            'articles.view',
            'proposals.view',
            'orders.view',
            'supplier-orders.view',
            'supplier-invoices.view',
        ]);

        $this->command->info('Roles e Permissões criadas com sucesso!');
    }
}
