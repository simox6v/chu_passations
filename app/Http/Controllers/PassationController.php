<?php

namespace App\Http\Controllers;

use App\Models\Passation;
use App\Models\Salle;
use App\Models\PassationEditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PassationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // For normal user: only their own passations
    public function index(Request $request)
    {
        $user = Auth::user();
        $salle_id = $request->input('salle_id');
        $search = $request->input('search');

        $query = Passation::with(['user', 'salle']);

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        if ($salle_id) {
            $query->where('salle_id', $salle_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom_patient', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('ip', 'like', "%{$search}%");
            });
        }

        $passations = $query->latest()->get();
        $salles = Salle::all();

        return view('passations.index', compact('passations', 'salles', 'salle_id', 'search'));
    }

    // New method for admin to see all passations without user filtering
    public function indexAll(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }

        $salle_id = $request->input('salle_id');
        $search = $request->input('search');

        $query = Passation::with(['user', 'salle']);

        if ($salle_id) {
            $query->where('salle_id', $salle_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom_patient', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('ip', 'like', "%{$search}%");
            });
        }

        $passations = $query->latest()->get();
        $salles = Salle::all();

        return view('passations.index', compact('passations', 'salles', 'salle_id', 'search'));
    }

    public function create()
    {
        return redirect()->route('passations.index');
    }

    public function store(Request $request)
    {
        $rules = [
            'nom_patient'    => 'required|string|max:255',
            'prenom'         => 'nullable|string|max:255',
            'cin'            => 'nullable|string|max:255',
            'ip'             => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'date_passation' => 'required|date',
            'salle_id'       => 'required|exists:salles,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('passations.index')
                ->withErrors($validator)
                ->withInput()
                ->with('form', 'create');
        }

        $data = $request->only([
            'nom_patient', 'prenom', 'cin', 'ip',
            'description', 'date_passation', 'salle_id'
        ]);
        $data['user_id'] = Auth::id();

        Passation::create($data);

        return redirect()->route('passations.index')->with('success', 'Passation enregistrée avec succès.');
    }

    public function show(Passation $passation)
    {
        if (Auth::id() !== $passation->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }

        return view('passations.show', compact('passation'));
    }

    public function edit(Passation $passation)
    {
        return redirect()->route('passations.index');
    }

    public function update(Request $request, Passation $passation)
    {
        $user = auth()->user();

        // Check if user has permission to edit this passation
        if (Auth::id() !== $passation->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }

        // Check time restriction for non-admin users
        if ($user->role !== 'admin' && now()->diffInMinutes($passation->created_at) > 30) {
            return redirect()->route('passations.index')
                ->withErrors(['modification' => 'Vous ne pouvez pas modifier cette passation après 30 minutes.'])
                ->with('error', 'Modification interdite: délai de 30 minutes dépassé.');
        }

        $rules = [
            'nom_patient'    => 'required|string|max:255',
            'prenom'         => 'nullable|string|max:255',
            'cin'            => 'nullable|string|max:255',
            'ip'             => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'date_passation' => 'required|date',
            'salle_id'       => 'required|exists:salles,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('passations.index')
                ->withErrors($validator)
                ->withInput()
                ->with('form', 'edit')
                ->with('edit_id', $passation->id);
        }

        $changes = [];
        foreach ($request->only([
            'nom_patient', 'prenom', 'cin', 'ip',
            'description', 'date_passation', 'salle_id'
        ]) as $field => $newValue) {
            $oldValue = $passation->$field;
            if ($oldValue != $newValue) {
                $changes[] = [
                    'passation_id' => $passation->id,
                    'user_id' => Auth::id(),
                    'field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $passation->update($request->only([
            'nom_patient', 'prenom', 'cin', 'ip',
            'description', 'date_passation', 'salle_id'
        ]));

        if (!empty($changes)) {
            PassationEditLog::insert($changes);
        }

        return redirect()->route('passations.index')->with('success', 'Passation mise à jour.');
    }

    public function destroy(Passation $passation)
    {
        if (Auth::id() === $passation->user_id || Auth::user()->role === 'admin') {
            $passation->delete();
            return redirect()->route('passations.index')->with('success', 'Passation supprimée.');
        }

        return redirect()->route('passations.index')->with('error', 'Non autorisé.');
    }

    public function dashboard(Request $request)
    {
        $query = Passation::with(['user', 'salle']);

        $salle_id = $request->input('salle_id');
        $search = $request->input('search');

        if (!is_null($salle_id) && $salle_id !== '') {
            $query->where('salle_id', $salle_id);
        }

        if (!is_null($search) && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nom_patient', 'like', '%' . $search . '%')
                ->orWhere('prenom', 'like', '%' . $search . '%')
                ->orWhere('ip', 'like', '%' . $search . '%');  // <-- added here
            });
        }


        $passations = $query->latest()->get();
        $salles = Salle::all();

        return view('dashboard', compact('passations', 'salles', 'salle_id', 'search'));
    }

    /**
     * Search all passations for the create modal (not filtered by user)
     */
    public function searchAllPassations(Request $request)
    {
        $salle_id = $request->input('salle_id');
        $ip = $request->input('ip');

        $query = Passation::with(['user', 'salle']);

        if ($salle_id) {
            $query->where('salle_id', $salle_id);
        }

        if ($ip) {
            $query->where('ip', 'like', "%{$ip}%");
        }

        $passations = $query->latest()->get();

        // Group by IP and get the most recent passation for each IP
        $groupedPassations = $passations->groupBy('ip')->map(function ($passationsByIp) {
            return $passationsByIp->sortByDesc('date_passation')->first();
        });

        return response()->json([
            'success' => true,
            'passations' => $groupedPassations->values()
        ]);
    }

    
}
