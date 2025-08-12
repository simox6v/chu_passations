<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/Logo_CHU-final.webp') }}" alt="CHU Fès Logo" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Tableau de bord') }}
                    </x-nav-link>

                    <x-nav-link :href="route('passations.index')" :active="request()->routeIs('passations.*')">
                        {{ __('Passations') }}
                    </x-nav-link>

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <!-- Dropdown for Gestion -->
                            <div x-data="{ openGestion: {{ request()->routeIs('admin.*') ? 'true' : 'false' }} }" class="relative">
                                <button
                                    @click="openGestion = !openGestion"
                                    class="inline-flex items-center h-10 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200"
                                    :class="{
                                        'text-blue-600 bg-blue-50': openGestion || request()->routeIs('admin.*'),
                                        'text-gray-500 hover:text-gray-700 hover:bg-gray-50': !openGestion && !request()->routeIs('admin.*')
                                    }"
                                    type="button"
                                >
                                    {{ __('Gestion') }}
                                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="openGestion"
                                    @click.away="openGestion = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                    style="display: none;"
                                >
                                    <div class="py-1">
                                        <a href="{{ route('admin.users.index') }}"
                                            class="block px-4 py-2 text-sm"
                                            :class="{
                                                'bg-blue-50 text-blue-600': request()->routeIs('admin.users.*'),
                                                'text-gray-700 hover:bg-gray-100': !request()->routeIs('admin.users.*')
                                            }">
                                            {{ __('Gestion des utilisateurs') }}
                                        </a>
                                        <a href="{{ route('admin.salles.index') }}"
                                            class="block px-4 py-2 text-sm"
                                            :class="{
                                                'bg-blue-50 text-blue-600': request()->routeIs('admin.salles.*'),
                                                'text-gray-700 hover:bg-gray-100': !request()->routeIs('admin.salles.*')
                                            }">
                                            {{ __('Gestion des salles') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors duration-200">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Tableau de bord') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('passations.index')" :active="request()->routeIs('passations.*')">
                {{ __('Passations') }}
            </x-responsive-nav-link>

            @auth
                @if(auth()->user()->role === 'admin')
                    <!-- Responsive Dropdown -->
                    <div x-data="{ openGestionMobile: false }" class="border-t border-gray-200 mt-2 pt-2">
                        <button
                            @click="openGestionMobile = !openGestionMobile"
                            class="w-full flex justify-between items-center px-4 py-2 text-sm font-medium rounded-md"
                            :class="{
                                'text-blue-600 bg-blue-50': openGestionMobile || request()->routeIs('admin.*'),
                                'text-gray-500 hover:text-gray-700 hover:bg-gray-50': !openGestionMobile && !request()->routeIs('admin.*')
                            }"
                            type="button"
                        >
                            {{ __('Gestion') }}
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openGestionMobile" x-transition class="pl-4 space-y-1">
                            <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                {{ __('Gestion des utilisateurs') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('admin.salles.index')" :active="request()->routeIs('admin.salles.*')">
                                {{ __('Gestion des salles') }}
                            </x-responsive-nav-link>
                        </div>
                    </div>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>