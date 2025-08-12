@csrf

<div class="space-y-6">
    <div>
        <label for="{{ $submitLabel === 'Créer' ? 'add_nom' : 'edit_nom' }}" class="block text-sm font-medium text-gray-700">Nom de la salle</label>
        <input type="text" name="nom" id="{{ $submitLabel === 'Créer' ? 'add_nom' : 'edit_nom' }}"
               value="{{ old('nom', $salle->nom ?? '') }}"
               class="mt-1 w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               required>
        @error('nom')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="{{ $submitLabel === 'Créer' ? 'add_nombre_lits' : 'edit_nombre_lits' }}" class="block text-sm font-medium text-gray-700">Nombre de lits</label>
        <input type="number" name="nombre_lits" min="0"
               id="{{ $submitLabel === 'Créer' ? 'add_nombre_lits' : 'edit_nombre_lits' }}"
               value="{{ old('nombre_lits', $salle->nombre_lits ?? '') }}"
               class="mt-1 w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               required>
        @error('nombre_lits')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-8 flex justify-between items-center">
    <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-blue-600 hover:underline font-semibold">← Fermer</button>
    <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded shadow transition duration-200">
        {{ $submitLabel }}
    </button>
</div>
