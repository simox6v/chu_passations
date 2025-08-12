<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salles = Salle::latest()->get();
        return view('admin.salles.index', compact('salles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.salles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'nombre_lits' => 'required|integer|min:0',
        ]);

        Salle::create($request->only('nom', 'nombre_lits'));

        return redirect()->route('admin.salles.index')
                         ->with('success', 'Salle créée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salle $salle)
    {
        return view('admin.salles.edit', compact('salle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salle $salle)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'nombre_lits' => 'required|integer|min:0',
        ]);

        $salle->update($request->only('nom', 'nombre_lits'));

        return redirect()->route('admin.salles.index')
                         ->with('success', 'Salle mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salle $salle)
    {
        $salle->delete();

        return redirect()->route('admin.salles.index')
                         ->with('success', 'Salle supprimée avec succès.');
    }
}
