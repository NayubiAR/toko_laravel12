<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('settings.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('settings.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'phone'     => $validated['phone'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        activity('user')
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['role' => $validated['role']])
            ->log('User baru dibuat');

        return redirect()->route('settings.users.index')
            ->with('success', "User \"{$user->name}\" berhasil dibuat dengan role {$validated['role']}.");
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('settings.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|exists:roles,name',
            'is_active'=> 'boolean',
        ]);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        activity('user')
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties(['role' => $validated['role'], 'is_active' => $user->is_active])
            ->log('User diperbarui');

        return redirect()->route('settings.users.index')
            ->with('success', "User \"{$user->name}\" berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        if ($user->sales()->count() > 0) {
            return back()->with('error', "User \"{$user->name}\" tidak bisa dihapus karena memiliki riwayat transaksi.");
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', "User \"{$name}\" berhasil dihapus.");
    }
}