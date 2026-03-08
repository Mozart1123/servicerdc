<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'super_admin'])->latest()->paginate(20);
        return view('super-admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('super-admin.users.create');
    }

    public function show($id)
    {
        return view('super-admin.users.show', ['id' => $id]);
    }

    public function edit($id)
    {
        return view('super-admin.users.edit', ['id' => $id]);
    }
}
