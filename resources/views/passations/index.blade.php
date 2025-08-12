@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Liste des passations</h1>

        @auth
            @if(in_array(auth()->user()->role, ['admin', 'medecin']))
                <button
                    id="openCreateModal"
                    class="mt-4 sm:mt-0 inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-3 rounded-lg shadow-lg transition duration-200"
                    type="button"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter une passation
                </button>
            @endif
        @endauth
    </div>

    {{-- Search bar (search by nom, prénom, or IP) --}}
    <form method="GET" action="{{ route('passations.index') }}" class="flex flex-col sm:flex-row items-center gap-3 mb-8">
        <input
            type="text"
            name="search"
            placeholder="Rechercher par nom, prénom ou IP..."
            value="{{ request('search') }}"
            class="w-full sm:w-1/3 border border-gray-300 rounded-md px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            aria-label="Recherche par nom, prénom ou IP"
        >

        <select
            name="salle_id"
            class="w-full sm:w-1/4 border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200"
            aria-label="Filtrer par salle"
        >
            <option value="">-- Toutes les salles --</option>
            @foreach($salles as $salle)
                <option value="{{ $salle->id }}" {{ request('salle_id') == $salle->id ? 'selected' : '' }}>
                    {{ $salle->nom ?? 'Salle #' . $salle->id }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow transition duration-200 whitespace-nowrap"
        >
            Filtrer
        </button>
    </form>

    <!-- Table with fixed max height and vertical scroll -->
    <div class="overflow-x-auto bg-white shadow-lg rounded-lg max-h-[500px] overflow-y-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider">IP (Identifiant Patient)</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider">Salle</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider">Médecin</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($passations as $passation)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $passation->ip ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">
                            {{ $passation->nom_patient }} {{ $passation->prenom }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $passation->salle->nom ?? 'Non spécifiée' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 relative">
                            <p class="font-medium flex items-center justify-between">
                                {{ $passation->user->name ?? 'Inconnu' }}

                                @if($passation->editLogs->count() > 0 && auth()->user()->role === 'admin')
                                <button
                                    type="button"
                                    aria-label="Voir les modifications"
                                    title="Voir les modifications"
                                    class="ml-2 w-6 h-6 flex-shrink-0 cursor-pointer"
                                    data-passation-id="{{ $passation->id }}"
                                    onclick="showEditLogs(event, {{ $passation->id }})"
                                >
                                    <img src="{{ asset('images/edited.png') }}" alt="Modifié" class="w-full h-full object-contain" draggable="false" />
                                </button>
                                @endif
                            </p>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ \Carbon\Carbon::parse($passation->date_passation)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center space-x-3 flex justify-center items-center">
                            <!-- Voir popup -->
                            <button
                                type="button"
                                class="openShowModalBtn w-7 h-7 sm:w-6 sm:h-6 hover:scale-110 transition-transform duration-150"
                                data-id="{{ $passation->id }}"
                                title="Voir"
                                aria-label="Voir les détails de la passation"
                            >
                                <img src="{{ asset('images/show.png') }}" alt="Voir" class="w-full h-full object-contain">
                            </button>

                            @if(auth()->user()->id === $passation->user_id || auth()->user()->role === 'admin')
                                <!-- Modifier -->
                                <button
                                  type="button"
                                  class="openEditModalBtn w-7 h-7 sm:w-6 sm:h-6 hover:scale-110 transition-transform duration-150"
                                  data-id="{{ $passation->id }}"
                                  title="Modifier"
                                  aria-label="Modifier la passation"
                                >
                                  <img src="{{ asset('images/edit.png') }}" alt="Modifier" class="w-full h-full object-contain">
                                </button>

                                <!-- Supprimer -->
                                <form action="{{ route('passations.destroy', $passation) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Confirmer la suppression ?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Supprimer" aria-label="Supprimer la passation">
                                        <img src="{{ asset('images/deleted.png') }}" alt="Supprimer" class="w-7 h-7 sm:w-6 sm:h-6 hover:scale-110 transition-transform duration-150 object-contain">
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Aucune passation trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div
            id="editLogsPopup"
            tabindex="0"
            class="hidden fixed z-50 max-w-xs max-h-60 overflow-auto rounded-md bg-white border border-gray-300 shadow-lg text-xs text-gray-800 p-3 sm:max-w-sm"
            role="dialog" aria-modal="true" aria-labelledby="editLogsPopupTitle"
            style="box-shadow: 0 5px 15px rgba(0,0,0,0.2);"
        >
            <h3 id="editLogsPopupTitle" class="font-semibold px-3 py-2 border-b border-gray-200 bg-gray-50">Modifications</h3>
            <ul id="editLogsPopupList" class="list-disc list-inside space-y-1"></ul>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div
  id="createModal"
  class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4"
  role="dialog" aria-modal="true" aria-labelledby="createModalTitle"
>
  <div
    class="bg-white rounded-lg shadow-lg max-w-xl w-full p-6 relative max-h-[90vh] overflow-auto"
  >
    <button
      id="closeCreateModal"
      class="absolute top-3 right-3 text-gray-700 hover:text-gray-900 text-2xl font-bold"
      title="Fermer"
      aria-label="Fermer la fenêtre"
      type="button"
    >&times;</button>

    <h2 id="createModalTitle" class="text-xl font-semibold mb-4">Créer une nouvelle passation</h2>

    {{-- Step 1: Search --}}
    <div id="step1">
      <p class="mb-4 font-medium text-gray-700">
        Étape 1 : Rechercher un patient ou créer un nouveau
      </p>

      <div class="mb-4">
        <label for="filter_salle" class="block font-medium text-gray-700 mb-1">Filtrer par salle :</label>
        <select
          id="filter_salle"
          class="mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 w-full max-w-xs"
        >
          <option value="">Toutes les salles</option>
          @foreach($salles as $salle)
            <option value="{{ $salle->id }}">{{ $salle->nom }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label for="search_ip" class="block font-medium text-gray-700 mb-1">Rechercher par IP :</label>
        <input
          type="text"
          id="search_ip"
          class="mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 w-full max-w-xs"
          placeholder="Entrez l'IP du patient..."
          autocomplete="off"
        >
      </div>

      <div
    id="patientSearchResults"
    class="max-h-64 overflow-auto border border-gray-300 rounded p-2 hidden"
    ></div>


      <button
        id="nextToStep2"
        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
        disabled
      >
        Suivant
      </button>
    </div>

    {{-- Step 2: Form --}}
    <form
      id="step2"
      method="POST"
      action="{{ route('passations.store') }}"
      class="space-y-6 hidden"
    >
      @csrf
      <p class="mb-4 font-medium text-gray-700">
        Étape 2 : Compléter les informations du patient et les consignes
      </p>

      <input type="hidden" name="existing_patient" id="existing_patient" value="0">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="nom_patient" class="block text-sm font-medium text-gray-700">Nom</label>
          <input
            type="text"
            name="nom_patient"
            id="nom_patient"
            required
            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
          >
          @error('nom_patient') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
          <input
            type="text"
            name="prenom"
            id="prenom"
            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
          >
          @error('prenom') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="cin" class="block text-sm font-medium text-gray-700">CIN</label>
          <input
            type="text"
            name="cin"
            id="cin"
            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
          >
          @error('cin') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="ip" class="block text-sm font-medium text-gray-700">IP (Identifiant Patient)</label>
          <input
            type="text"
            name="ip"
            id="ip"
            required
            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
          >
          @error('ip') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
          <p id="ipExistsMsg" class="text-red-600 mt-1 hidden"></p>
        </div>
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Consignes</label>
        <textarea
          name="description"
          id="description"
          rows="4"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
        ></textarea>
        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="date_passation" class="block text-sm font-medium text-gray-700">Date de la passation</label>
        <input
          type="datetime-local"
          name="date_passation"
          id="date_passation"
          required
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
        >
        @error('date_passation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="salle_id" class="block text-sm font-medium text-gray-700">Salle</label>
        <select
          name="salle_id"
          id="salle_id"
          required
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
        >
          <option value="">-- Sélectionner une salle --</option>
          @foreach($salles as $salle)
            <option value="{{ $salle->id }}">{{ $salle->nom }} ({{ $salle->nombre_lits ?? 'N/A' }} lits)</option>
          @endforeach
        </select>
        <p id="salleWarning" class="hidden text-red-600 text-sm font-semibold mt-2"></p>
        @error('salle_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex justify-between">
        <button
          type="button"
          id="backToStep1"
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
        >
          Retour
        </button>
        <button
          type="submit"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Modals --}}
@foreach($passations as $passation)

  <div
    id="editModal-{{ $passation->id }}"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4"
    role="dialog" aria-modal="true" aria-labelledby="editModalTitle-{{ $passation->id }}"
  >
    <div class="bg-white rounded-lg shadow-lg max-w-xl w-full p-6 relative max-h-[90vh] overflow-auto">
      <button
        class="closeEditModal absolute top-3 right-3 text-gray-700 hover:text-gray-900 text-2xl font-bold"
        data-id="{{ $passation->id }}"
        title="Fermer"
        aria-label="Fermer la fenêtre"
        type="button"
      >&times;</button>

      <h2 id="editModalTitle-{{ $passation->id }}" class="text-xl font-semibold mb-4">Modifier la passation</h2>

      {{-- Time restriction warning --}}
      @if(auth()->user()->role !== 'admin' && now()->diffInMinutes($passation->created_at) > 30)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
          <strong class="font-bold">Modification interdite!</strong>
          <span class="block sm:inline">Vous ne pouvez plus modifier cette passation (délai de 30 minutes dépassé).</span>
        </div>
      @endif

      {{-- Edit form with Consignes label --}}
      <form method="POST" action="{{ route('passations.update', $passation) }}" class="space-y-6">
        @if ($errors->has('modification'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
          <strong class="font-bold">Erreur!</strong>
          <span class="block sm:inline">{{ $errors->first('modification') }}</span>
        </div>
        @endif
        @csrf
        @method('PUT')

        @php
          $isTimeExpired = auth()->user()->role !== 'admin' && now()->diffInMinutes($passation->created_at) > 30;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="nom_patient_{{ $passation->id }}" class="block text-sm font-medium text-gray-700">Nom</label>
            <input
              type="text"
              name="nom_patient"
              id="nom_patient_{{ $passation->id }}"
              value="{{ old('nom_patient', $passation->nom_patient) }}"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 {{ $isTimeExpired ? 'bg-gray-100' : '' }}"
              {{ $isTimeExpired ? 'disabled' : 'required' }}
            >
          </div>

          <div>
            <label for="prenom_{{ $passation->id }}" class="block text-sm font-medium text-gray-700">Prénom</label>
            <input
              type="text"
              name="prenom"
              id="prenom_{{ $passation->id }}"
              value="{{ old('prenom', $passation->prenom) }}"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 {{ $isTimeExpired ? 'bg-gray-100' : '' }}"
              {{ $isTimeExpired ? 'disabled' : '' }}
            >
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="cin_{{ $passation->id }}" class="block text-sm font-medium text-gray-700">CIN</label>
            <input
              type="text"
              name="cin"
              id="cin_{{ $passation->id }}"
              value="{{ old('cin', $passation->cin) }}"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 {{ $isTimeExpired ? 'bg-gray-100' : '' }}"
              {{ $isTimeExpired ? 'disabled' : '' }}
            >
          </div>

          <div>
            <label for="ip_{{ $passation->id }}" class="block text-sm font-medium text-gray-700">IP (Identifiant Patient)</label>
            <input
              type="text"
              name="ip"
              id="ip_{{ $passation->id }}"
              value="{{ old('ip', $passation->ip) }}"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 {{ $isTimeExpired ? 'bg-gray-100' : '' }}"
              {{ $isTimeExpired ? 'disabled' : '' }}
            >
          </div>
        </div>

        <div>
          <label for="description_{{ $passation->id }}" class="block text-sm font-medium text-gray-700 text-left">Consignes</label>
          <textarea
            name="description"
            id="description_{{ $passation->id }}"
            rows="4"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 {{ $isTimeExpired ? 'bg-gray-100' : '' }}"
            {{ $isTimeExpired ? 'disabled' : '' }}
          >{{ old('description', $passation->description) }}</textarea>
        </div>

        <div>
          <label for="date_passation_{{ $passation->id }}" class="block text-sm font-medium text-gray-700">Date de la passation</label>
          <input
            type="datetime-local"
            name="date_passation"
            id="date_passation_{{ $passation->id }}"
            value="{{ old('date_passation', \Carbon\Carbon::parse($passation->date_passation)->format('Y-m-d\TH:i')) }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 {{ $isTimeExpired ? 'bg-gray-100' : '' }}"
            {{ $isTimeExpired ? 'disabled' : 'required' }}
          >
        </div>

        <div>
          <label for="salle_id_{{ $passation->id }}" class="block text-sm font-medium text-gray-700">Salle</label>
          <select
            name="salle_id"
            id="salle_id_{{ $passation->id }}"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 {{ $isTimeExpired ? 'bg-gray-100' : '' }}"
            {{ $isTimeExpired ? 'disabled' : 'required' }}
          >
            <option value="">-- Sélectionner une salle --</option>
            @foreach($salles as $salle)
              <option value="{{ $salle->id }}" {{ old('salle_id', $passation->salle_id) == $salle->id ? 'selected' : '' }}>
                {{ $salle->nom }} ({{ $salle->nombre_lits ?? 'N/A' }} lits)
              </option>
            @endforeach
          </select>
        </div>

        <div class="flex justify-end space-x-3">
          <button type="button" class="modal-close px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
          <button 
            type="submit" 
            class="px-4 py-2 {{ $isTimeExpired ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }} text-white rounded"
            {{ $isTimeExpired ? 'disabled' : '' }}
          >
            Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
@endforeach

{{-- Show Modals --}}
@foreach($passations as $passation)
  <div
    id="showModal-{{ $passation->id }}"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4"
    role="dialog" aria-modal="true" aria-labelledby="showModalTitle-{{ $passation->id }}"
  >
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative max-h-[80vh] overflow-auto">
      <button
        class="closeShowModal absolute top-3 right-3 text-gray-700 hover:text-gray-900 text-2xl font-bold"
        data-id="{{ $passation->id }}"
        title="Fermer"
        aria-label="Fermer la fenêtre"
        type="button"
      >&times;</button>

      <h2 id="showModalTitle-{{ $passation->id }}" class="text-xl font-semibold mb-4">Détails de la passation</h2>

      <div class="space-y-2 text-gray-700">
        <p><strong>IP (Identifiant Patient) :</strong> {{ $passation->ip ?? 'N/A' }}</p>
        <p><strong>Patient :</strong> {{ $passation->nom_patient }} {{ $passation->prenom }}</p>
        <p><strong>CIN :</strong> {{ $passation->cin ?? 'N/A' }}</p>
        <p><strong>Consignes :</strong></p>
        <p class="whitespace-pre-wrap border-l-4 border-blue-500 pl-4 text-left">{{ $passation->description ?? 'Aucune' }}</p>
        <p><strong>Date de la passation :</strong> {{ \Carbon\Carbon::parse($passation->date_passation)->format('d/m/Y H:i') }}</p>
        <p><strong>Salle :</strong> {{ $passation->salle->nom ?? 'Non spécifiée' }}</p>
        <p><strong>Médecin :</strong> {{ $passation->user->name ?? 'Inconnu' }}</p>
      </div>
    </div>
  </div>
@endforeach

<script>
  // Open Edit modal when clicking "Modifier"
  document.querySelectorAll('.openEditModalBtn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      document.getElementById('editModal-' + id).classList.remove('hidden');
    });
  });

  // Close Edit modals
  document.querySelectorAll('.closeEditModal').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      document.getElementById('editModal-' + id).classList.add('hidden');
    });
  });

  // Close Edit modals when clicking outside modal content
  document.querySelectorAll('[id^=editModal-]').forEach(modal => {
    modal.addEventListener('click', (e) => {
      if(e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  });

  // Open Show modal when clicking "Voir"
  document.querySelectorAll('.openShowModalBtn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      document.getElementById('showModal-' + id).classList.remove('hidden');
    });
  });

  // Close Show modals
  document.querySelectorAll('.closeShowModal').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      document.getElementById('showModal-' + id).classList.add('hidden');
    });
  });

  // Close Show modals when clicking outside modal content
  document.querySelectorAll('[id^=showModal-]').forEach(modal => {
    modal.addEventListener('click', (e) => {
      if(e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  });

  // Open Create modal when clicking the green "Ajouter" button
  document.getElementById('openCreateModal')?.addEventListener('click', () => {
    document.getElementById('createModal').classList.remove('hidden');
  });

  // Close Create modal when clicking close button
  document.getElementById('closeCreateModal')?.addEventListener('click', () => {
    document.getElementById('createModal').classList.add('hidden');
  });

  // Close Create modal when clicking outside modal content
  document.getElementById('createModal')?.addEventListener('click', (e) => {
    if(e.target.id === 'createModal') {
      document.getElementById('createModal').classList.add('hidden');
    }
  });

  // Close modal on cancel button click
  document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.fixed').classList.add('hidden');
    });
  });
</script>
<script>
  const passationEditLogs = {
    @foreach($passations as $passation)
      "{{ $passation->id }}": [
        @foreach($passation->editLogs->sortByDesc('created_at') as $log)
          {
            field: {!! json_encode(ucfirst($log->field)) !!},
            user: {!! json_encode($log->user->name) !!},
            old_value: {!! json_encode($log->old_value ?? 'N/A') !!},
            new_value: {!! json_encode($log->new_value ?? 'N/A') !!},
            date: {!! json_encode($log->created_at->format('d/m/Y H:i')) !!}
          }@if(!$loop->last),@endif
        @endforeach
      ]@if(!$loop->last),@endif
    @endforeach
  };

  const popup = document.getElementById('editLogsPopup');
  const popupList = document.getElementById('editLogsPopupList');

  function showEditLogs(event, id) {
    event.stopPropagation();

    const logs = passationEditLogs[id];
    if (!logs || logs.length === 0) {
      popup.classList.add('hidden');
      return;
    }

    popupList.innerHTML = logs.map(log => `
      <li>
        <strong>${log.field}</strong> modifié par <em>${log.user}</em><br>
        de "<code class='font-mono break-all'>${log.old_value}</code>" à "<code class='font-mono break-all'>${log.new_value}</code>"<br>
        <small class="text-gray-500">${log.date}</small>
      </li>
    `).join('');

    const btnRect = (event.currentTarget || event.target).getBoundingClientRect();
    const popupWidth = popup.offsetWidth || 320;
    const popupHeight = popup.offsetHeight || 240;

    let left = btnRect.right + 8 + window.scrollX;
    if (left + popupWidth > window.scrollX + window.innerWidth) {
      left = btnRect.left - popupWidth - 8 + window.scrollX;
    }

    let top = btnRect.top + window.scrollY;
    if (top + popupHeight > window.scrollY + window.innerHeight) {
      top = window.scrollY + window.innerHeight - popupHeight - 8;
    }

    popup.style.left = `${left}px`;
    popup.style.top = `${top}px`;
    popup.style.position = 'absolute';

    popup.classList.remove('hidden');
    popup.focus();
  }

  // Close popup on outside click
  document.addEventListener('click', () => {
    popup.classList.add('hidden');
  });

  // Prevent closing popup if clicking inside popup
  popup.addEventListener('click', e => {
    e.stopPropagation();
  });
</script>
<script>
// Use AJAX to search all passations instead of only logged-in user's passations
const salles = {
  @foreach($salles as $salle)
    "{{ $salle->id }}": {!! json_encode($salle->nom) !!}@if(!$loop->last),@endif
  @endforeach
};

// Elements references
const createModal = document.getElementById('createModal');
const closeBtn = document.getElementById('closeCreateModal');

// Step 1 elements (filter + search)
const step1 = document.getElementById('step1');
const filterSalle = document.getElementById('filter_salle');
const searchIp = document.getElementById('search_ip');
const patientSearchResults = document.getElementById('patientSearchResults');
const nextToStep2Btn = document.getElementById('nextToStep2');

// Step 2 elements (form)
const step2 = document.getElementById('step2');
const backToStep1Btn = document.getElementById('backToStep1');
const ipExistsMsg = document.getElementById('ipExistsMsg');
const salleWarning = document.getElementById('salleWarning');
const existingPatientInput = document.getElementById('existing_patient');

// Form inputs inside step 2
const nomInput = step2.querySelector('#nom_patient');
const prenomInput = step2.querySelector('#prenom');
const cinInput = step2.querySelector('#cin');
const ipInput = step2.querySelector('#ip');
const salleSelect = step2.querySelector('#salle_id');

let selectedPatient = null;
let allPatientsData = {};

// Search all passations via AJAX
async function searchAllPassations() {
  const salleId = filterSalle.value;
  const ipQuery = searchIp.value.trim();

  try {
    const response = await fetch(`{{ route('passations.searchAll') }}?salle_id=${salleId}&ip=${ipQuery}`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();
    
    if (data.success) {
      // Update allPatientsData with search results
      allPatientsData = {};
      data.passations.forEach(passation => {
        allPatientsData[passation.ip] = {
          nom_patient: passation.nom_patient,
          prenom: passation.prenom,
          cin: passation.cin,
          ip: passation.ip,
          salle_id: passation.salle_id,
          salle_nom: passation.salle ? passation.salle.nom : 'N/A'
        };
      });
      
      updateSearchResults();
    }
  } catch (error) {
    console.error('Error searching passations:', error);
    patientSearchResults.innerHTML = '<p class="text-red-500 text-sm">Erreur lors de la recherche</p>';
    patientSearchResults.classList.remove('hidden');
  }
}

// Filter and search patients and update result list
function updateSearchResults() {
  const salleId = filterSalle.value;
  const ipQuery = searchIp.value.trim().toLowerCase();

  let filtered = Object.entries(allPatientsData);

  if (salleId) {
    filtered = filtered.filter(([ip, patient]) => patient.salle_id == salleId);
  }
  if (ipQuery) {
    filtered = filtered.filter(([ip]) => ip.toLowerCase().includes(ipQuery));
  }

  if (filtered.length === 0) {
    patientSearchResults.innerHTML = '<p class="text-gray-500 text-sm">Aucun patient trouvé</p>';
    patientSearchResults.classList.remove('hidden');
    nextToStep2Btn.disabled = false; // Allow new patient creation
    selectedPatient = null;
    return;
  }

  patientSearchResults.innerHTML = filtered.map(([ip, patient]) => `
    <div class="cursor-pointer p-2 hover:bg-gray-100 border-b last:border-b-0" data-ip="${ip}" role="button" tabindex="0">
      <strong>${patient.nom_patient} ${patient.prenom || ''}</strong> — IP: ${patient.ip} — Salle: ${patient.salle_nom || 'N/A'}
    </div>
  `).join('');
  patientSearchResults.classList.remove('hidden');
  nextToStep2Btn.disabled = true;
  selectedPatient = null;
}

// Debounce function to avoid too many API calls
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

const debouncedSearch = debounce(searchAllPassations, 300);

filterSalle.addEventListener('change', debouncedSearch);
searchIp.addEventListener('input', debouncedSearch);

// Initial search when modal opens
document.getElementById('openCreateModal')?.addEventListener('click', () => {
  document.getElementById('createModal').classList.remove('hidden');
  searchAllPassations(); // Load all passations initially
});

// Keyboard accessibility for search results
patientSearchResults.addEventListener('keydown', e => {
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    e.target.click();
  }
});

// Select patient from search results
patientSearchResults.addEventListener('click', e => {
  const target = e.target.closest('[data-ip]');
  if (!target) return;

  const ip = target.getAttribute('data-ip');
  selectedPatient = allPatientsData[ip];

  // Highlight selected patient
  [...patientSearchResults.children].forEach(child => {
    child.classList.toggle('bg-blue-100', child === target);
  });

  nextToStep2Btn.disabled = false;
});

// Load patient data into form (no description or date_passation)
function loadPatientData(patient) {
  nomInput.value = patient.nom_patient;
  prenomInput.value = patient.prenom || '';
  cinInput.value = patient.cin || '';
  ipInput.value = patient.ip;
  salleSelect.value = patient.salle_id;
}

// Clear patient data except ip and salle
function clearPatientDataExceptIpAndSalle() {
  nomInput.value = '';
  prenomInput.value = '';
  cinInput.value = '';
}

// Move to step 2: load form if patient selected, else blank except IP from search
nextToStep2Btn.addEventListener('click', () => {
  if (selectedPatient) {
    existingPatientInput.value = '1';
    loadPatientData(selectedPatient);
    ipExistsMsg.textContent = '';
    ipExistsMsg.classList.add('hidden');
    salleWarning.textContent = '';
    salleWarning.classList.add('hidden');
  } else {
    existingPatientInput.value = '0';
    clearPatientDataExceptIpAndSalle();
    ipInput.value = searchIp.value.trim() || '';
    salleSelect.value = '';
    ipExistsMsg.textContent = '';
    ipExistsMsg.classList.add('hidden');
    salleWarning.textContent = '';
    salleWarning.classList.add('hidden');
  }
  step1.classList.add('hidden');
  step2.classList.remove('hidden');
  patientSearchResults.classList.add('hidden');
});

// Back to step 1
backToStep1Btn.addEventListener('click', () => {
  step2.classList.add('hidden');
  step1.classList.remove('hidden');
  nextToStep2Btn.disabled = true;
  selectedPatient = null;
  ipExistsMsg.textContent = '';
  ipExistsMsg.classList.add('hidden');
  salleWarning.textContent = '';
  salleWarning.classList.add('hidden');
});

// IP exists warning if IP matches another patient and not editing that patient
ipInput.addEventListener('input', () => {
  const ipVal = ipInput.value.trim();
  if (allPatientsData[ipVal] && existingPatientInput.value !== '1') {
    ipExistsMsg.textContent = `Cette IP existe déjà pour le patient ${allPatientsData[ipVal].nom_patient} en salle ${allPatientsData[ipVal].salle_nom}`;
    ipExistsMsg.classList.remove('hidden');
  } else {
    ipExistsMsg.textContent = '';
    ipExistsMsg.classList.add('hidden');
  }
});

// Warning if salle differs, else auto-fill patient data for matched salle
function checkSalleWarning() {
  const ipVal = ipInput.value.trim();
  const selectedSalleId = salleSelect.value;

  if (allPatientsData[ipVal] && allPatientsData[ipVal].salle_id != selectedSalleId) {
    salleWarning.textContent = `Attention : cette passation existe déjà dans la salle ${salles[allPatientsData[ipVal].salle_id]}`;
    salleWarning.classList.remove('hidden');
  } else {
    salleWarning.textContent = '';
    salleWarning.classList.add('hidden');
    if (allPatientsData[ipVal]) {
      loadPatientData(allPatientsData[ipVal]);
    } else {
      clearPatientDataExceptIpAndSalle();
    }
  }
}

salleSelect.addEventListener('change', checkSalleWarning);

// Close modal and reset everything
closeBtn.addEventListener('click', () => {
  createModal.classList.add('hidden');
  step2.classList.add('hidden');
  step1.classList.remove('hidden');
  nextToStep2Btn.disabled = true;
  selectedPatient = null;
  patientSearchResults.classList.add('hidden');
  ipExistsMsg.textContent = '';
  ipExistsMsg.classList.add('hidden');
  salleWarning.textContent = '';
  salleWarning.classList.add('hidden');
});
</script>


@endsection