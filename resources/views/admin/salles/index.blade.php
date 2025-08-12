@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-900">Gestion des salles</h1>

    <!-- Add Salle Button -->
    <div class="mb-6">
        <button onclick="openAddModal()"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow transition duration-200">
            + Ajouter une salle
        </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider">Nombre de lits</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($salles as $salle)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $salle->nom }}</td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $salle->nombre_lits }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap space-x-4 flex justify-center items-center">
                            <!-- Edit -->
                            <button 
                                class="edit-btn inline-block p-1 rounded hover:bg-yellow-100 transition duration-150 cursor-pointer"
                                data-id="{{ $salle->id }}"
                                data-nom="{{ $salle->nom }}"
                                data-nombre-lits="{{ $salle->nombre_lits }}">
                                <img src="{{ asset('images/edit.png') }}" alt="Modifier" class="w-6 h-6" />
                            </button>

                            <!-- Delete -->
                            <form action="{{ route('admin.salles.destroy', $salle) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Supprimer cette salle ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Supprimer"
                                        class="inline-block p-1 rounded hover:bg-red-100 transition duration-150 cursor-pointer">
                                    <img src="{{ asset('images/deleted.png') }}" alt="Supprimer" class="w-6 h-6" />
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-400 italic py-8">Aucune salle enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="salle-modal-bg p-6 rounded-lg shadow-lg w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Ajouter une salle</h2>
        <form method="POST" action="{{ route('admin.salles.store') }}">
            @include('admin.salles.form', ['submitLabel' => 'Créer'])
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="salle-modal-bg p-6 rounded-lg shadow-lg w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Modifier la salle</h2>
        <form id="editForm" method="POST">
            @method('PUT')
            @include('admin.salles.form', ['submitLabel' => 'Mettre à jour'])
        </form>
    </div>
</div>

<style>
.salle-modal-bg {
    background-image: url("{{ asset('images/salles_bg.avif') }}");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-color: rgba(255, 255, 255, 0.6); /* cloudy overlay */
    background-blend-mode: lighten;
}

.salle-modal-bg label {
    color: #f9f9f9; /* default titles in white-ish */
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

/* Make specific labels darker */
.salle-modal-bg label[for*="nom"],
.salle-modal-bg label[for*="nombre_lits"] {
    color: #222; /* darker black for these two */
    text-shadow: none;
}

.salle-modal-bg input,
.salle-modal-bg select {
    background-color: rgba(255, 255, 255, 0.9);
    color: #000;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nom = this.dataset.nom;
            const nombreLits = this.dataset.nombreLits;

            let form = document.getElementById('editForm');
            form.action = "{{ route('admin.salles.update', ':id') }}".replace(':id', id);

            document.getElementById('edit_nom').value = nom;
            document.getElementById('edit_nombre_lits').value = nombreLits;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        });
    });
});

function openAddModal() {
    document.getElementById('add_nom').value = '';
    document.getElementById('add_nombre_lits').value = '';
    document.getElementById('addModal').classList.remove('hidden');
    document.getElementById('addModal').classList.add('flex');
}
</script>
@endsection
