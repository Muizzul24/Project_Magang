@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6 mt-6">
  <!-- Header Kalender -->
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center space-x-4">
      <button id="prevMonth" class="text-gray-600 hover:text-gray-900 text-2xl font-bold">&lt;</button>
      <h2 id="monthYear" class="text-xl font-semibold"></h2>
      <button id="nextMonth" class="text-gray-600 hover:text-gray-900 text-2xl font-bold">&gt;</button>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Event</button>
  </div>

  <!-- Grid Hari -->
  <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-700 border-b border-gray-300 pb-2 mb-2">
    <div>Sunday</div>
    <div>Monday</div>
    <div>Tuesday</div>
    <div>Wednesday</div>
    <div>Thursday</div>
    <div>Friday</div>
    <div>Saturday</div>
  </div>

  <!-- Grid Tanggal -->
  <div id="calendarGrid" class="grid grid-cols-7 gap-2 text-center text-gray-800"></div>
</div>

<script>
  // Data event dari backend Laravel, format: { "2025-12-01": [{title: "Event 1"}, ...], ... }
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

    // Nama bulan
    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];
    monthYearEl.textContent = `${monthNames[month]} ${year}`;

    // Hari pertama bulan ini (0=Sunday, 1=Monday, ...)
    const firstDay = new Date(year, month, 1).getDay();

    // Jumlah hari di bulan ini
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Tambahkan kotak kosong untuk hari sebelum tanggal 1
    for (let i = 0; i < firstDay; i++) {
      const emptyCell = document.createElement('div');
      calendarGrid.appendChild(emptyCell);
    }

    // Tambahkan tanggal
    for (let day = 1; day <= daysInMonth; day++) {
      const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const dayCell = document.createElement('div');
      dayCell.classList.add('border', 'rounded', 'p-2', 'min-h-[80px]', 'flex', 'flex-col', 'justify-start', 'items-start', 'cursor-pointer', 'hover:bg-gray-100');

      // Nomor tanggal
      const dayNumber = document.createElement('div');
      dayNumber.textContent = day;
      dayNumber.classList.add('font-semibold', 'mb-1');
      dayCell.appendChild(dayNumber);

      // Event (jika ada)
      if (events[dateStr]) {
        events[dateStr].forEach(event => {
          const eventEl = document.createElement('div');
          eventEl.textContent = event.title;
          eventEl.classList.add('bg-blue-100', 'text-blue-800', 'text-xs', 'rounded', 'px-1', 'mb-1', 'truncate', 'w-full');
          dayCell.appendChild(eventEl);
        });
      }

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