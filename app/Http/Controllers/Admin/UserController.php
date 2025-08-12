<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Lister tous les utilisateurs
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Formulaire de création (non utilisé avec le modal, mais laissé si besoin)
    public function create()
    {
        return view('admin.users.create');
    }

    // Enregistrer un nouvel utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => ['required', Rule::in(['admin', 'medecin', 'infirmier', 'ambulancier'])],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur ajouté avec succès.');
    }

    // Ancienne méthode edit remplacée par redirection vers index avec données
    public function edit(User $user)
    {
        return redirect()
            ->route('admin.users.index')
            ->with('edit_user', $user)
            ->with('success', 'Utilisateur prêt à être modifié.');
    }

    // Mettre à jour un utilisateur
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => ['required', Rule::in(['admin', 'medecin', 'infirmier', 'ambulancier'])],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Informations de l\'utilisateur mises à jour avec succès.');
    }

    // Supprimer un utilisateur
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }

    // Changer le mot de passe
    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Le mot de passe a été modifié avec succès.');
    }
}
