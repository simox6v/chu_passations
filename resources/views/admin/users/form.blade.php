<div class="space-y-6">
    <div>
        <label class="block font-semibold text-gray-700">Nom</label>
        <input type="text" name="name" id="{{ $prefix ?? '' }}_name"
               value="{{ old('name') }}"
               class="mt-1 w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
    </div>

    <div>
        <label class="block font-semibold text-gray-700">Email</label>
        <input type="email" name="email" id="{{ $prefix ?? '' }}_email"
               value="{{ old('email') }}"
               class="mt-1 w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
    </div>

    @if(($prefix ?? '') === 'add')
    <div>
        <label class="block font-semibold text-gray-700">Mot de passe</label>
        <input type="password" name="password" id="{{ $prefix ?? '' }}_password"
               class="mt-1 w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
    </div>

    <div>
        <label class="block font-semibold text-gray-700">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" id="{{ $prefix ?? '' }}_password_confirmation"
               class="mt-1 w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    @endif

    <div>
        <label class="block font-semibold text-gray-700">Rôle</label>
        <select name="role" id="{{ $prefix ?? '' }}_role"
                class="mt-1 w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            <option value="">-- Sélectionner --</option>
            <option value="admin">Administrateur</option>
            <option value="medecin">Médecin</option>
        </select>
    </div>
</div>

<div class="mt-8 flex justify-between items-center">
    <button type="button" onclick="this.closest('.fixed').classList.add('hidden')"
            class="text-blue-600 hover:underline font-semibold">← Fermer</button>
    <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded shadow transition duration-200">
        {{ $submitLabel }}
    </button>
</div>
