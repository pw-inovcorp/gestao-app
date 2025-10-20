<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::with('roles')
            ->select(['id', 'name', 'email', 'mobile', 'status', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $users->getCollection()->transform(function ($user) {
            $user->role_name = $user->roles->first()?->name ?? 'Sem Role';
            return $user;
        });

        return Inertia::render('User/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return Inertia::render('User/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'role_id.required' => 'Deve selecionar um grupo de permissões.',
            'role_id.exists' => 'Grupo de permissões inválido.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);


        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);

        return redirect()->route('users.index')
            ->with('success', 'Utilizador criado com sucesso!');
    }

    public function edit(User $user)
    {
        if ($user->id !== auth()->id() && !auth()->user()->can('users.edit')) {
            abort(403, 'Sem permissão para editar este utilizador.');
        }

        $roles = Role::orderBy('name')->get(['id', 'name']);

        return Inertia::render('User/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role_id' => $user->roles->first()?->id,
                'status' => $user->status,
            ],
            'roles' => $roles,
            'canEditRole' => auth()->user()->can('users.edit'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->id !== auth()->id() && !auth()->user()->can('users.edit')) {
            abort(403, 'Sem permissão para editar este utilizador.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'password_confirmation' => ['nullable'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        if ($request->has('role_id') && auth()->user()->can('users.edit')) {
            $role = Role::findById($validated['role_id']);
            $user->syncRoles([$role]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilizador atualizado com sucesso');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não pode eliminar o seu próprio utilizador');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilizador eliminado com sucesso');
    }
}
