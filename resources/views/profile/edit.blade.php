@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Modifier les informations du profil -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Modifier mes informations</h3>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Changer le mot de passe - only for admin -->
            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Changer mon mot de passe</h3>
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            @endif

            <!-- Supprimer le compte -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-red-600 mb-4">Supprimer mon compte</h3>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
@endsection
