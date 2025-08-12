@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <h1 class="text-3xl font-extrabold text-gray-900">Liste des utilisateurs</h1>
        <button onclick="openAddUserModal()"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg shadow transition duration-200 w-full sm:w-auto">
            + Ajouter un utilisateur
        </button>
    </div>

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full table-auto text-left text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 font-semibold text-gray-700 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-3 font-semibold text-gray-700 uppercase tracking-wider text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize text-gray-700">{{ $user->role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center flex justify-center space-x-3">
                            <!-- Modifier -->
                            <button class="edit-user-btn"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}">
                                <img src="{{ asset('images/edit_man.png') }}" alt="Modifier"
                                    class="w-6 h-6 inline-block hover:scale-110 transition">
                            </button>

                            <!-- Supprimer -->
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">
                                    <img src="{{ asset('images/delete_user.png') }}" alt="Supprimer"
                                        class="w-6 h-6 inline-block hover:scale-110 transition">
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400 italic">Aucun utilisateur trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Reusable modal styles -->
<style>
.user-modal-bg {
    background-image: url("{{ asset('images/user_bg.png') }}");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-color: rgba(255, 255, 255, 0.6); /* cloudy overlay */
    background-blend-mode: lighten;
    color: #000; /* default text black for inputs */
}

.user-modal-bg label {
    color: #f9f9f9; /* titles like Nom, Email, Rôle in white-ish */
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.user-modal-bg input,
.user-modal-bg select {
    background-color: rgba(255, 255, 255, 0.9);
    color: #000;
}
</style>


<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="relative p-6 rounded-lg shadow-lg w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl 
                max-h-[90vh] overflow-y-auto user-modal-bg">
        <h2 class="text-xl font-bold mb-4">Ajouter un utilisateur</h2>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users.form', ['submitLabel' => 'Créer', 'prefix' => 'add'])
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="relative p-6 rounded-lg shadow-lg w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl 
                max-h-[90vh] overflow-y-auto user-modal-bg">
        <h2 class="text-xl font-bold mb-4">Modifier l'utilisateur</h2>
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            @include('admin.users.form', ['submitLabel' => 'Mettre à jour', 'prefix' => 'edit'])
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            const role = this.dataset.role;

            let form = document.getElementById('editUserForm');
            form.action = "{{ route('admin.users.update', ':id') }}".replace(':id', id);

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;

            document.getElementById('editUserModal').classList.remove('hidden');
            document.getElementById('editUserModal').classList.add('flex');
        });
    });
});

function openAddUserModal() {
    document.getElementById('add_name').value = '';
    document.getElementById('add_email').value = '';
    document.getElementById('add_password').value = '';
    document.getElementById('add_password_confirmation').value = '';
    document.getElementById('add_role').value = '';

    document.getElementById('addUserModal').classList.remove('hidden');
    document.getElementById('addUserModal').classList.add('flex');
}
</script>
@endsection
