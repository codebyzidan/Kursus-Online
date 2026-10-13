<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(fn ($sub) => $sub->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($request->filled('role'), fn ($query) => $query->role($request->string('role')->toString()))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(UserRole::values())],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user->syncRoles([$validated['role']]);
        $user->forceFill(['is_active' => $request->boolean('is_active')])->save();

        return back()->with('success', 'Data pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->getKey() === $request->user()->getKey(), 403, 'Anda tidak dapat menghapus akun sendiri.');

        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }
}
