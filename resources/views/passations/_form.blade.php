@php
    $isEdit = isset($passation);
@endphp

<form
  method="POST"
  action="{{ $isEdit ? route('passations.update', $passation) : route('passations.store') }}"
  class="space-y-6"
>
  @csrf
  @if ($isEdit)
    @method('PUT')
  @endif

  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label for="nom_patient" class="block text-sm font-medium text-gray-700">Nom</label>
      <input
        type="text"
        name="nom_patient"
        id="nom_patient"
        value="{{ old('nom_patient', $passation->nom_patient ?? '') }}"
        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
        required
      >
      @error('nom_patient') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
      <input
        type="text"
        name="prenom"
        id="prenom"
        value="{{ old('prenom', $passation->prenom ?? '') }}"
        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
      >
      @error('prenom') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label for="cin" class="block text-sm font-medium text-gray-700">CIN</label>
      <input
        type="text"
        name="cin"
        id="cin"
        value="{{ old('cin', $passation->cin ?? '') }}"
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
        value="{{ old('ip', $passation->ip ?? '') }}"
        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
      >
      @error('ip') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  <div>
    <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
    <input
      type="text"
      name="service"
      id="service"
      value="{{ old('service', $passation->service ?? '') }}"
      class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
      required
    >
    @error('service') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
    <textarea
      name="description"
      id="description"
      rows="4"
      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
    >{{ old('description', $passation->description ?? '') }}</textarea>
    @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="date_passation" class="block text-sm font-medium text-gray-700">Date de la passation</label>
    <input
      type="datetime-local"
      name="date_passation"
      id="date_passation"
      value="{{ old('date_passation', isset($passation) ? \Carbon\Carbon::parse($passation->date_passation)->format('Y-m-d\TH:i') : '') }}"
      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
      required
    >
    @error('date_passation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="salle_id" class="block text-sm font-medium text-gray-700">Salle</label>
    <select
      name="salle_id"
      id="salle_id"
      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
      required
    >
      <option value="">-- Sélectionner une salle --</option>
      @foreach($salles as $salle)
        <option
          value="{{ $salle->id }}"
          {{ (old('salle_id', $passation->salle_id ?? '') == $salle->id) ? 'selected' : '' }}
        >
          {{ $salle->nom }} ({{ $salle->nombre_lits ?? 'N/A' }} lits)
        </option>
      @endforeach
    </select>
    @error('salle_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="flex justify-end space-x-3">
    <button type="button" class="modal-close px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
      {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
    </button>
  </div>
</form>
