<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js untuk toggle sidebar -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @stack('styles') <!-- Jika Anda ingin menambahkan CSS tambahan di view -->
</head>
<body class="bg-gray-100 min-h-screen flex" 
      x-data="{
        sidebarOpen: window.innerWidth >= 1024, // default open di desktop
        init() {
          const updateSidebar = () => {
            if(window.innerWidth < 1024) {
              this.sidebarOpen = false; // sembunyikan sidebar di layar kecil
            } else {
              this.sidebarOpen = true; // tampilkan sidebar di layar besar
            }
          };

          updateSidebar();

          window.addEventListener('resize', () => {
            updateSidebar();
          });

          window.matchMedia('(orientation: portrait)').addEventListener('change', e => {
            updateSidebar();
          });
        }
      }"
      x-init="init()"
>

    <!-- Sidebar -->
    <aside 
        class="fixed top-0 left-0 h-full bg-white shadow-lg border-r border-gray-200 transition-transform duration-300 ease-in-out
               w-64 z-40"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'"
    >
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Menu</h2>
            <!-- Tombol tutup sidebar -->
            <button 
                class="text-gray-600 hover:text-gray-900 focus:outline-none" 
                @click="sidebarOpen = false" 
                aria-label="Tutup sidebar"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <nav class="p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('dashboard') ? 'bg-blue-200 font-semibold' : '' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('agendas.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('agendas*') ? 'bg-blue-200 font-semibold' : '' }}">
                📅 Data Agenda
            </a>
            <a href="{{ route('agendas.arsip') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('agendas/arsip') ? 'bg-blue-200 font-semibold' : '' }}">
                📂 Arsip Agenda
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'operator')            
                <a href="{{ route('pegawais.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('pegawais*') ? 'bg-blue-200 font-semibold' : '' }}">
                    👥 Data Pegawai
                </a>

                <!-- Menu Baru: Dasar Surat -->
                <a href="{{ route('dasarSurat.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('dasarSurat*') ? 'bg-blue-200 font-semibold' : '' }}">
                    📄 Dasar Surat
                </a>

                <!-- Menu Baru: Paraf Surat -->
                <a href="{{ route('parafSurat.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('parafSurat*') ? 'bg-blue-200 font-semibold' : '' }}">
                    ✍️ Paraf Surat
                </a>

                <!-- Menu Baru: Surat Tugas -->
                <a href="{{ route('surat_tugas.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('surat_tugas*') ? 'bg-blue-200 font-semibold' : '' }}">
                    📝 Surat Tugas
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('users*') ? 'bg-blue-200 font-semibold' : '' }}">
                    👤 Daftar User
                </a>
                <a href="{{ route('substansis.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('substansis*') ? 'bg-blue-200 font-semibold' : '' }}">
                    🏢 Daftar Substansi
                </a>
            @endif

            <a href="{{ route('kalender.index') }}" class="block px-3 py-2 rounded hover:bg-blue-100 {{ request()->is('kalender') ? 'bg-blue-200 font-semibold' : '' }}">
                🗓️ Kalender
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left block px-3 py-2 rounded hover:bg-red-100 text-red-600 font-semibold">
                    🚪 Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Overlay untuk mobile saat sidebar terbuka -->
    <div 
      class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
      x-show="sidebarOpen"
      @click="sidebarOpen = false"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-50"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-50"
      x-transition:leave-end="opacity-0"
      style="display: none;"
    ></div>

    <!-- Konten utama -->
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
         :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">

        <!-- Header -->
        <header class="bg-blue-700 text-white p-4 flex items-center justify-between shadow relative">
            <!-- Tombol hamburger untuk buka sidebar -->
            <div class="flex items-center space-x-3">
                <button 
                    class="text-white text-xl focus:outline-none hover:bg-blue-600 p-2 rounded transition-colors duration-200" 
                    @click="sidebarOpen = !sidebarOpen" 
                    aria-label="Toggle sidebar"
                    x-show="!sidebarOpen || window.innerWidth < 1024"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <h1 class="text-xl font-semibold">@yield('title', 'Dashboard')</h1>
            </div>

            <!-- User info -->
            <div class="hidden sm:block">
                @auth
                    <span class="text-sm">Halo, <span class="font-semibold">{{ auth()->user()->nama }}</span> ({{ ucfirst(auth()->user()->role) }})</span>
                @endauth
            </div>

            <!-- User info untuk mobile -->
            <div class="sm:hidden">
                @auth
                    <span class="text-xs">{{ auth()->user()->nama }}</span>
                @endauth
            </div>
        </header>

        <!-- Isi halaman -->
        <main class="flex-1 p-6 bg-white overflow-auto">
            @yield('content')
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts') <!-- Jika Anda ingin menambahkan script tambahan di view -->
</body>
</html>