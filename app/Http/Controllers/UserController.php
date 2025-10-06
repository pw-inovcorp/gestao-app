<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::select(['id', 'name', 'email', 'mobile', 'role', 'status', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('User/Index', [
            'users' => $users
        ]);
    }
}
