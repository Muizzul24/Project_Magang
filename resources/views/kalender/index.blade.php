@extends('layouts.app')

@section('title', 'Kalender Agenda')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg p-6 mt-6">
    <!-- Header Kalender -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <button id="prevMonth" class="text-gray-600 hover:text-gray-900 text-2xl font-bold">&lt;</button>
            <h2 id="monthYear" class="text-xl font-semibold w-40 text-center"></h2>
            <button id="nextMonth" class="text-gray-600 hover:text-gray-900 text-2xl font-bold">&gt;</button>
        </div>
        @if(in_array(auth()->user()->role, ['admin', 'operator']))
            <a href="{{ route('agendas.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Agenda
            </a>
        @endif
    </div>
    <!-- Grid Hari -->
    <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-700 border-b border-gray-300 pb-2 mb-2">
        <div>Minggu</div>
        <div>Senin</div>
        <div>Selasa</div>
        <div>Rabu</div>
        <div>Kamis</div>
        <div>Jumat</div>
        <div>Sabtu</div>
    </div>

    <!-- Grid Tanggal -->
    <div id="calendarGrid" class="grid grid-cols-7 gap-1"></div>
</div>

<script>
    // Data event dari backend, format: [{title: "...", start: "...", end: "...", url: "...", color: "..."}, ...]
    const events = @json($events ?? []);

    const monthYearEl = document.getElementById('monthYear');
    const calendarGrid = document.getElementById('calendarGrid');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    let currentDate = new Date();

    function renderCalendar(date) {
        calendarGrid.innerHTML = '';
        const year = date.getFullYear();
        const month = date.getMonth();

        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        monthYearEl.textContent = `${monthNames[month]} ${year}`;

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Tambahkan kotak kosong untuk hari sebelum tanggal 1
        for (let i = 0; i < firstDayOfMonth; i++) {
            calendarGrid.innerHTML += '<div></div>';
        }

        // Loop untuk setiap hari dalam sebulan
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.className = 'border rounded p-2 min-h-[100px] flex flex-col justify-start items-start';
            
            const dayNumber = document.createElement('div');
            dayNumber.textContent = day;
            dayNumber.className = 'font-semibold mb-1';
            dayCell.appendChild(dayNumber);

            // =============================================
            // PERBAIKAN: Logika untuk menampilkan event multi-hari
            // =============================================
            const currentDay = new Date(year, month, day);
            currentDay.setHours(0, 0, 0, 0); // Normalisasi waktu

            events.forEach(event => {
                const eventStart = new Date(event.start);
                const eventEnd = new Date(event.end);
                eventStart.setHours(0, 0, 0, 0);
                eventEnd.setHours(0, 0, 0, 0);

                // Cek apakah hari ini berada dalam rentang event
                if (currentDay >= eventStart && currentDay <= eventEnd) {
                    const eventLink = document.createElement('a');
                    eventLink.href = event.url;
                    eventLink.textContent = event.title;
                    // Terapkan warna dari controller
                    eventLink.className = `border ${event.color} text-xs rounded px-1 py-0.5 mb-1 truncate w-full block hover:opacity-75`;
                    dayCell.appendChild(eventLink);
                }
            });

            calendarGrid.appendChild(dayCell);
        }
    }

    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    // Render awal
    renderCalendar(currentDate);
</script>
@endsection
