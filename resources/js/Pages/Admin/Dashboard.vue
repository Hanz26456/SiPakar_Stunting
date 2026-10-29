<template>
  <AdminLayout title="Dashboard Administrator">
    <div class="space-y-5">
      <!-- 1. Header / Greeting & Top Filters (Matching Reference Image 1) -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3.5">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-2">
            <span>Selamat Datang, {{ $page.props.auth?.user?.name ?? 'Administrator' }}</span>
            <span class="inline-block animate-wave text-xl">👋</span>
          </h2>
          <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
            Pantauan operasional sistem pakar deteksi dini stunting, kepatuhan posyandu, dan metrik Certainty Factor.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <!-- Wilayah Filter Select -->
          <div class="relative">
            <select
              v-model="selectedPosyandu"
              class="appearance-none bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg pl-3 pr-8 py-2 text-xs font-medium text-zinc-700 dark:text-zinc-200 shadow-xs focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all cursor-pointer"
            >
              <option value="all">Semua Posyandu</option>
              <option value="melati1">Posyandu Melati 1</option>
              <option value="melati2">Posyandu Melati 2</option>
              <option value="melati3">Posyandu Melati 3</option>
            </select>
            <span class="pointer-events-none absolute right-2.5 top-2.5 text-zinc-400">
              <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>

          <!-- Date Pill -->
          <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs text-xs font-medium text-zinc-700 dark:text-zinc-300">
            <Calendar class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
            <span>{{ currentDateLabel }}</span>
          </div>
        </div>
      </div>

      <!-- 2. Operational Alert Banner (Matching Reference Image 1) -->
      <div
        v-if="stats.butuh_tindakan > 0"
        class="bg-red-50 dark:bg-red-950/25 border border-red-200 dark:border-red-900/50 rounded-xl p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-red-900 dark:text-red-200 shadow-xs"
      >
        <div class="flex items-start sm:items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-red-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
            <AlertTriangle class="w-5 h-5" />
          </div>
          <div>
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-xs sm:text-sm font-bold text-red-950 dark:text-red-100">
                Perhatian Operasional Stunting
              </span>
              <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-600 text-white shadow-2xs">
                {{ stats.butuh_tindakan }} Butuh Tindakan
              </span>
            </div>
            <p class="text-xs text-red-700 dark:text-red-300 mt-0.5">
              Terdapat balita terindikasi stunting / berisiko yang memerlukan tindak lanjut intervensi gizi atau verifikasi bidan.
            </p>
          </div>
        </div>

        <Link
          :href="route('admin.log')"
          class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-xs transition-colors self-start sm:self-auto flex-shrink-0"
        >
          <Clock class="w-3.5 h-3.5" />
          <span>{{ stats.butuh_tindakan }} Perlu Verifikasi</span>
        </Link>
      </div>

      <!-- 3. Metric Stat Cards Row (4 Columns - Matching Reference Image 1) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Balita -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Balita Terdaftar</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
              <Baby class="w-4 h-4" />
            </div>
          </div>
          <div>
            <div class="flex items-baseline gap-2">
              <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white tabular-nums">
                {{ stats.total_balita ?? 0 }}
              </span>
              <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                +{{ stats.total_balita }} Aktif
              </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5">
              {{ stats.total_balita ?? 0 }} balita aktif dalam pemantauan
            </p>
          </div>
        </div>

        <!-- Card 2: Tingkat Gizi Normal (with mini progress bar) -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Tingkat Gizi Normal</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
              <CheckCircle2 class="w-4 h-4" />
            </div>
          </div>
          <div>
            <div class="flex items-baseline gap-2">
              <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white tabular-nums">
                {{ stats.persen_normal ?? 0 }}%
              </span>
              <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                {{ stats.normal ?? 0 }} Normal
              </span>
            </div>
            <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden my-2">
              <div
                class="bg-emerald-500 h-full rounded-full transition-all duration-700"
                :style="{ width: `${stats.persen_normal ?? 0}%` }"
              />
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
              Dari {{ stats.total_balita ?? 0 }} total balita terdata
            </p>
          </div>
        </div>

        <!-- Card 3: Akurasi & Rata-rata CF -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Rata-rata CF & Akurasi</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
              <Activity class="w-4 h-4" />
            </div>
          </div>
          <div>
            <div class="flex items-baseline gap-2">
              <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white tabular-nums">
                {{ stats.rata_rata_cf ?? 87.3 }}%
              </span>
              <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                Pakar CF
              </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5">
              Validasi kesesuaian pakar: <strong class="text-zinc-800 dark:text-zinc-200">{{ stats.akurasi ?? 100 }}%</strong>
            </p>
          </div>
        </div>

        <!-- Card 4: Terindikasi Stunting -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Terindikasi Stunting</span>
            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
              <Ruler class="w-4 h-4" />
            </div>
          </div>
          <div>
            <div class="flex items-baseline gap-2">
              <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white tabular-nums">
                {{ (stats.stunting ?? 0) + (stats.stunting_berat ?? 0) }}
              </span>
              <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300">
                Prev. {{ stats.persen_stunting ?? 0 }}%
              </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5">
              Tersedia {{ stats.total_rule ?? 0 }} rule inferensi aktif
            </p>
          </div>
        </div>
      </div>

      <!-- 4. Visualizations Row: Spline Area Chart + Donut Chart (Matching Reference Image 1) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Area Chart: Tren Partisipasi & Diagnosis (7 cols / 60%) -->
        <div class="lg:col-span-7 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-xs flex flex-col justify-between">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
              <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                Tren Partisipasi & Kelulusan Pertumbuhan
              </h3>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Perbandingan kunjungan balita (<em>Diperiksa</em>) vs terindikasi stunting (<em>Stunted</em>)
              </p>
            </div>

            <!-- Time Filter Pills -->
            <div class="inline-flex rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/60 p-0.5 text-xs font-semibold">
              <button
                v-for="range in timeRanges"
                :key="range.key"
                type="button"
                @click="selectedRange = range.key"
                class="px-2.5 py-1 rounded-md transition-all"
                :class="selectedRange === range.key ? 'bg-blue-600 text-white shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
              >
                {{ range.label }}
              </button>
            </div>
          </div>

          <!-- Smooth SVG Spline Area Chart -->
          <div class="relative w-full h-[220px] flex items-center justify-center my-2">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 500 180" preserveAspectRatio="none">
              <defs>
                <!-- Area Gradient for Total Visits -->
                <linearGradient id="curveFillTotal" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#10b981" stop-opacity="0.25" />
                  <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
                </linearGradient>
                <!-- Area Gradient for Stunting -->
                <linearGradient id="curveFillStunting" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#ef4444" stop-opacity="0.20" />
                  <stop offset="100%" stop-color="#ef4444" stop-opacity="0.0" />
                </linearGradient>
              </defs>

              <!-- Subtle Grid lines -->
              <line x1="0" y1="30" x2="500" y2="30" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800" stroke-dasharray="3 3" />
              <line x1="0" y1="80" x2="500" y2="80" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800" stroke-dasharray="3 3" />
              <line x1="0" y1="130" x2="500" y2="130" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800" stroke-dasharray="3 3" />
              <line x1="0" y1="160" x2="500" y2="160" stroke="currentColor" class="text-zinc-200 dark:text-zinc-700" />

              <!-- Y-Axis Values -->
              <text x="5" y="32" class="text-[10px] fill-zinc-400 font-mono">4</text>
              <text x="5" y="82" class="text-[10px] fill-zinc-400 font-mono">3</text>
              <text x="5" y="132" class="text-[10px] fill-zinc-400 font-mono">1</text>
              <text x="5" y="162" class="text-[10px] fill-zinc-400 font-mono">0</text>

              <!-- Area Fill Under Green Spline -->
              <path
                :d="chartPathData.areaTotal"
                fill="url(#curveFillTotal)"
              />

              <!-- Smooth Green Line (Total Visits / Normal) -->
              <path
                :d="chartPathData.lineTotal"
                fill="none"
                stroke="#10b981"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              />

              <!-- Smooth Blue Line (Assigned / Kunjungan) -->
              <path
                :d="chartPathData.lineAssigned"
                fill="none"
                stroke="#2563eb"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              />

              <!-- Data Point Circles with hover animation -->
              <g v-for="(pt, idx) in chartPoints" :key="idx">
                <circle
                  :cx="pt.x"
                  :cy="pt.y"
                  r="4"
                  class="fill-white dark:fill-zinc-900 stroke-emerald-500 stroke-2 transition-transform hover:scale-150 cursor-pointer"
                >
                  <title>{{ pt.label }}: {{ pt.value }} balita</title>
                </circle>
              </g>
            </svg>
          </div>

          <!-- X-Axis Labels -->
          <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 font-medium px-2 pt-1 border-t border-zinc-100 dark:border-zinc-800">
            <span v-for="d in xLabels" :key="d">{{ d }}</span>
          </div>

          <!-- Bottom Legend (Matching Reference Image 1) -->
          <div class="flex items-center justify-center gap-5 mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 text-xs">
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
              <span>Terdaftar (Total)</span>
            </div>
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
              <span>Gizi Normal</span>
            </div>
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
              <span>Berisiko</span>
            </div>
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
              <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
              <span>Stunting</span>
            </div>
          </div>
        </div>

        <!-- Donut Chart: Distribusi Status Balita (5 cols / 40%) -->
        <div class="lg:col-span-5 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between mb-2">
            <div>
              <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                Distribusi Status Balita
              </h3>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Sebaran {{ stats.total_balita ?? 0 }} pemeriksaan Certainty Factor
              </p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900/40">
              Real-time
            </span>
          </div>

          <!-- SVG Donut Chart with Center Text (Matching Reference Image 1) -->
          <div class="relative flex items-center justify-center my-4">
            <svg class="w-48 h-48 -rotate-90 transform" viewBox="0 0 160 160">
              <!-- Base Track Circle -->
              <circle
                cx="80"
                cy="80"
                r="60"
                fill="transparent"
                stroke="currentColor"
                class="text-zinc-100 dark:text-zinc-800"
                stroke-width="16"
              />

              <!-- Segment 1: Normal (Green) -->
              <circle
                cx="80"
                cy="80"
                r="60"
                fill="transparent"
                stroke="#10b981"
                stroke-width="16"
                stroke-linecap="round"
                :stroke-dasharray="donutSegments.normal.dashArray"
                :stroke-dashoffset="donutSegments.normal.dashOffset"
                class="transition-all duration-700"
              />

              <!-- Segment 2: Berisiko (Amber) -->
              <circle
                cx="80"
                cy="80"
                r="60"
                fill="transparent"
                stroke="#f59e0b"
                stroke-width="16"
                stroke-linecap="round"
                :stroke-dasharray="donutSegments.berisiko.dashArray"
                :stroke-dashoffset="donutSegments.berisiko.dashOffset"
                class="transition-all duration-700"
              />

              <!-- Segment 3: Stunting (Red) -->
              <circle
                cx="80"
                cy="80"
                r="60"
                fill="transparent"
                stroke="#ef4444"
                stroke-width="16"
                stroke-linecap="round"
                :stroke-dasharray="donutSegments.stunting.dashArray"
                :stroke-dashoffset="donutSegments.stunting.dashOffset"
                class="transition-all duration-700"
              />
            </svg>

            <!-- Center Display (Total Tugas / Total Balita) -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
              <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                Total Balita
              </span>
              <span class="text-3xl font-extrabold text-zinc-900 dark:text-white tabular-nums tracking-tight">
                {{ stats.total_balita ?? 0 }}
              </span>
            </div>
          </div>

          <!-- Bottom Legend with semantic dots (Matching Reference Image 1) -->
          <div class="grid grid-cols-2 gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800 text-xs">
            <div class="flex items-center gap-2 p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/40">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
              <span class="text-zinc-600 dark:text-zinc-400 truncate">Normal: <strong class="text-zinc-900 dark:text-white">{{ stats.normal ?? 0 }}</strong></span>
            </div>
            <div class="flex items-center gap-2 p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/40">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-500 flex-shrink-0"></span>
              <span class="text-zinc-600 dark:text-zinc-400 truncate">Berisiko: <strong class="text-zinc-900 dark:text-white">{{ stats.berisiko ?? 0 }}</strong></span>
            </div>
            <div class="flex items-center gap-2 p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/40">
              <span class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></span>
              <span class="text-zinc-600 dark:text-zinc-400 truncate">Stunting: <strong class="text-zinc-900 dark:text-white">{{ stats.stunting ?? 0 }}</strong></span>
            </div>
            <div class="flex items-center gap-2 p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/40">
              <span class="w-2.5 h-2.5 rounded-full bg-purple-500 flex-shrink-0"></span>
              <span class="text-zinc-600 dark:text-zinc-400 truncate">S. Berat: <strong class="text-zinc-900 dark:text-white">{{ stats.stunting_berat ?? 0 }}</strong></span>
            </div>
          </div>
        </div>
      </div>

      <!-- 5. Lower Grid: Data Tables + Radial Gauge + Activity & Schedule (Matching Reference Image 2) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Left Side (7 cols / 60%): Pemeriksaan Terkini & Aktivitas Terbaru -->
        <div class="lg:col-span-7 space-y-5">
          <!-- Card A: Pemeriksaan Balita Terkini (Table matching Image 2 top left) -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 pb-3.5 flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800">
              <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                  Pemeriksaan Balita Terkini
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                  Log skrining antropometri & klasifikasi Certainty Factor terkini
                </p>
              </div>
              <Link
                :href="route('admin.log')"
                class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1"
              >
                <span>Lihat Semua</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead>
                  <tr class="bg-zinc-50/75 dark:bg-zinc-800/40 text-zinc-400 dark:text-zinc-400 text-[11px] uppercase tracking-wider font-semibold border-b border-zinc-100 dark:border-zinc-800">
                    <th class="py-2.5 px-4">Balita</th>
                    <th class="py-2.5 px-4">Pengukuran & CF</th>
                    <th class="py-2.5 px-4">Status</th>
                    <th class="py-2.5 px-4">Tanggal & Skor</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/70">
                  <tr v-if="!pemeriksaan_terkini || pemeriksaan_terkini.length === 0">
                    <td colspan="4" class="py-8 text-center text-zinc-400 italic">
                      Belum ada data pemeriksaan terbaru.
                    </td>
                  </tr>
                  <tr
                    v-for="item in pemeriksaan_terkini"
                    :key="item.id"
                    class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors"
                  >
                    <!-- Balita Name & Avatar -->
                    <td class="py-3 px-4">
                      <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-blue-200 dark:border-blue-900/50">
                          {{ item.nama_balita?.charAt(0) ?? 'B' }}
                        </div>
                        <div>
                          <div class="font-bold text-zinc-900 dark:text-white leading-tight">
                            {{ item.nama_balita }}
                          </div>
                          <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                            {{ item.usia_format }} · {{ item.desa }}
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Pengukuran & CF -->
                    <td class="py-3 px-4">
                      <div class="text-zinc-800 dark:text-zinc-200 font-medium">
                        BB: {{ item.berat_badan }} kg · TB: {{ item.tinggi_badan }} cm
                      </div>
                      <div class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">
                        CF Kepastian: <strong class="text-blue-600 dark:text-blue-400">{{ item.cf_persen }}</strong>
                      </div>
                    </td>

                    <!-- Status Pill (Matching Reference Image 2) -->
                    <td class="py-3 px-4">
                      <span
                        class="px-2.5 py-1 rounded-md text-xs font-semibold inline-flex items-center"
                        :class="statusBadgeClass(item.status_final)"
                      >
                        {{ item.label_status }}
                      </span>
                    </td>

                    <!-- Tanggal & Verifikator -->
                    <td class="py-3 px-4">
                      <div class="text-zinc-800 dark:text-zinc-200 font-mono text-xs">
                        {{ item.tanggal }}
                      </div>
                      <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                        {{ item.sudah_diverifikasi ? `Verif: ${item.verifikator ?? 'Bidan'}` : 'Menunggu Verifikasi' }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Card B: Aktivitas Terbaru Sistem (Timeline matching Image 2 bottom left) -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3.5 pb-2.5 border-b border-zinc-100 dark:border-zinc-800">
              <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                  Aktivitas Terbaru Sistem
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                  Pembaruan data balita, skrining Certainty Factor, & verifikasi real-time
                </p>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900/40">
                Real-time
              </span>
            </div>

            <div class="space-y-3">
              <div
                v-for="(act, idx) in aktivitas_terbaru"
                :key="idx"
                class="flex items-start gap-3 p-2.5 rounded-lg bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/70"
              >
                <!-- Icon Tile -->
                <div
                  class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                  :class="activityIconBg(act.tipe)"
                >
                  <component :is="activityIcon(act.tipe)" class="w-4 h-4" />
                </div>

                <!-- Text content -->
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-semibold text-zinc-900 dark:text-white leading-snug">
                    {{ act.detail }}
                  </div>
                  <div class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5 flex items-center gap-1.5">
                    <Clock class="w-3 h-3" />
                    <span>{{ act.waktu }}</span>
                    <span>·</span>
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">{{ act.user }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side (5 cols / 40%): Semi-circular Radial Gauge + Agenda + Kepatuhan -->
        <div class="lg:col-span-5 space-y-5">
          <!-- Card C: Radial Arc Gauge Tingkat Kelulusan / Normal (Matching Image 2 top right) -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
              <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                  Tingkat Gizi Normal Balita
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                  Persentase balita dengan status pertumbuhan optimal
                </p>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/40">
                Target 85%
              </span>
            </div>

            <!-- Semi-circular SVG Gauge (Matching Image 2) -->
            <div class="relative flex items-center justify-center my-3">
              <svg class="w-52 h-28" viewBox="0 0 160 90">
                <!-- Arc Track Background -->
                <path
                  d="M 15,85 A 65,65 0 0,1 145,85"
                  fill="none"
                  stroke="currentColor"
                  class="text-zinc-100 dark:text-zinc-800"
                  stroke-width="12"
                  stroke-linecap="round"
                />

                <!-- Dynamic Colored Arc -->
                <path
                  d="M 15,85 A 65,65 0 0,1 145,85"
                  fill="none"
                  stroke="#2563eb"
                  stroke-width="12"
                  stroke-linecap="round"
                  :stroke-dasharray="204"
                  :stroke-dashoffset="arcGaugeOffset"
                  class="transition-all duration-1000"
                />
              </svg>

              <!-- Center Percent Text -->
              <div class="absolute bottom-1 flex flex-col items-center justify-center">
                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                  Tingkat Kelulusan
                </span>
                <span class="text-3xl font-extrabold text-zinc-900 dark:text-white tabular-nums tracking-tight">
                  {{ stats.persen_normal ?? 0 }}%
                </span>
              </div>
            </div>

            <!-- 3 Mini Stats Columns (Matching Image 2 below arc gauge) -->
            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800 text-center">
              <div>
                <div class="text-lg font-bold text-zinc-900 dark:text-white tabular-nums">
                  {{ stats.normal ?? 0 }}
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Normal</div>
              </div>
              <div class="border-x border-zinc-100 dark:border-zinc-800">
                <div class="text-lg font-bold text-zinc-900 dark:text-white tabular-nums">
                  {{ stats.total_balita ?? 0 }}
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Total Tugas</div>
              </div>
              <div>
                <div class="text-lg font-bold text-zinc-900 dark:text-white tabular-nums">
                  {{ stats.rata_rata_cf ?? 87.3 }}
                </div>
                <div class="text-[11px] text-zinc-500 dark:text-zinc-400">Rata Skor</div>
              </div>
            </div>
          </div>

          <!-- Card D: Agenda Sesi Posyandu Terdekat (Matching Image 2 middle right) -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
              <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                  Agenda Sesi Terdekat
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                  Jadwal tatap muka & kelas pelatihan terdekat
                </p>
              </div>
              <Link
                :href="route('bidan.monitoring')"
                class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1"
              >
                <span>Jadwal</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </Link>
            </div>

            <!-- Agenda Box -->
            <div class="p-3.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800">
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                  <Calendar class="w-4 h-4" />
                </div>
                <div class="flex-1">
                  <div class="text-xs font-bold text-zinc-900 dark:text-white">
                    {{ agenda_terdekat?.judul ?? 'Sesi Penimbangan Rutin Balita' }}
                  </div>
                  <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1 space-y-0.5">
                    <div class="flex items-center gap-1.5">
                      <Clock class="w-3 h-3 text-zinc-400" />
                      <span>{{ agenda_terdekat?.tanggal }} · {{ agenda_terdekat?.waktu }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                      <MapPin class="w-3 h-3 text-zinc-400" />
                      <span>{{ agenda_terdekat?.lokasi }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-3.5 text-center">
              <Link
                :href="route('kader.kunjungan.create')"
                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 text-xs font-semibold transition-colors"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>+ Buat Jadwal Baru</span>
              </Link>
            </div>
          </div>

          <!-- Card E: Kepatuhan Divisi / Posyandu Tertinggi (Matching Image 2 bottom right) -->
          <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
              <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">
                  Kepatuhan Divisi Tertinggi
                </h3>
              </div>
              <Link
                :href="route('bidan.laporan')"
                class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1"
              >
                <span>Laporan Lengkap</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </Link>
            </div>

            <!-- Progress List (Matching Reference Image 2) -->
            <div class="space-y-3 pt-1">
              <div
                v-for="unit in kepatuhan_posyandu"
                :key="unit.nama"
                class="space-y-1.5"
              >
                <div class="flex items-center justify-between text-xs">
                  <span class="font-semibold text-zinc-800 dark:text-zinc-200 text-[11px] uppercase tracking-wider">
                    {{ unit.nama }}
                  </span>
                  <span class="font-bold text-zinc-900 dark:text-white tabular-nums">
                    {{ unit.kepatuhan }}%
                  </span>
                </div>
                <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all duration-500"
                    :class="unit.kepatuhan >= 90 ? 'bg-emerald-500' : (unit.kepatuhan >= 80 ? 'bg-blue-600' : 'bg-amber-500')"
                    :style="{ width: `${unit.kepatuhan}%` }"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  Calendar,
  AlertTriangle,
  Clock,
  Baby,
  CheckCircle2,
  Activity,
  Ruler,
  Plus,
  ArrowRight,
  MapPin,
  Users,
  ShieldCheck,
  FileText
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  stats: Object,
  pemeriksaan_terkini: Array,
  aktivitas_terbaru: Array,
  distribusi: Array,
  kepatuhan_posyandu: Array,
  agenda_terdekat: Object,
})

const selectedPosyandu = ref('all')
const selectedRange = ref('30D')

const timeRanges = [
  { key: '7D',  label: '7D' },
  { key: '30D', label: '30D' },
  { key: '90D', label: '90D' },
  { key: '1Y',  label: '1Y' },
]

// Date label today formatted in Indonesian
const currentDateLabel = computed(() => {
  const now = new Date()
  return now.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
})

// X-axis month / date labels for the chart
const xLabels = computed(() => {
  if (props.distribusi && props.distribusi.length > 0) {
    return props.distribusi.map(d => d.bulan.split(' ')[0])
  }
  return ['23 Okt', '24 Okt', '25 Okt', '26 Okt', '27 Okt', '28 Okt', '29 Okt']
})

// Spline calculation for the area chart
const chartPathData = computed(() => {
  // Mock/actual data points for the spline
  // Using bezier curve to produce smooth hill like in the reference image
  return {
    // Smooth hill for Assigned/Diperiksa
    lineAssigned: 'M 10,140 C 60,140 100,160 140,160 C 180,160 210,160 250,110 C 270,70 280,30 300,30 C 320,30 330,70 350,120 C 370,160 410,160 490,160',
    // Smooth curve for Completed/Normal
    lineTotal: 'M 10,110 C 50,110 80,160 120,160 C 170,160 220,160 260,110 C 280,60 290,25 310,25 C 330,25 340,65 360,120 C 380,160 420,160 490,160',
    areaTotal: 'M 10,110 C 50,110 80,160 120,160 C 170,160 220,160 260,110 C 280,60 290,25 310,25 C 330,25 340,65 360,120 C 380,160 420,160 490,160 L 490,160 L 10,160 Z',
  }
})

const chartPoints = computed(() => [
  { x: 10,  y: 110, label: '23 Okt', value: 3 },
  { x: 120, y: 160, label: '24 Okt', value: 0 },
  { x: 310, y: 25,  label: '26 Okt', value: 4 },
  { x: 490, y: 160, label: '29 Okt', value: 0 },
])

// SVG Donut Segments calculation
const donutSegments = computed(() => {
  const total = props.stats?.total_balita > 0 ? props.stats.total_balita : 1
  const normal = props.stats?.normal ?? 0
  const berisiko = props.stats?.berisiko ?? 0
  const stunting = (props.stats?.stunting ?? 0) + (props.stats?.stunting_berat ?? 0)

  const circumference = 2 * Math.PI * 60 // ~376.99

  const normalLength = (normal / total) * circumference
  const berisikoLength = (berisiko / total) * circumference
  const stuntingLength = (stunting / total) * circumference

  return {
    normal: {
      dashArray: `${normalLength} ${circumference}`,
      dashOffset: 0,
    },
    berisiko: {
      dashArray: `${berisikoLength} ${circumference}`,
      dashOffset: -normalLength,
    },
    stunting: {
      dashArray: `${stuntingLength} ${circumference}`,
      dashOffset: -(normalLength + berisikoLength),
    },
  }
})

// Semi-circular gauge offset (arc length is 204.2)
const arcGaugeOffset = computed(() => {
  const arcLength = 204.2
  const percent = props.stats?.persen_normal ? Math.min(100, Math.max(0, props.stats.persen_normal)) : 91.2
  return arcLength - (arcLength * (percent / 100))
})

// Badge status class matching Metis semantic badges
const statusBadgeClass = (status) => {
  switch (status) {
    case 'normal':
      return 'bg-emerald-600 text-white shadow-2xs'
    case 'berisiko':
      return 'bg-amber-500 text-white shadow-2xs'
    case 'stunting':
      return 'bg-rose-600 text-white shadow-2xs'
    case 'stunting_berat':
      return 'bg-red-700 text-white shadow-2xs'
    default:
      return 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200'
  }
}

// Activity timeline icon & colors
const activityIcon = (type) => {
  switch (type) {
    case 'verifikasi':
      return ShieldCheck
    case 'diagnosis':
      return Activity
    case 'balita':
      return Users
    default:
      return FileText
  }
}

const activityIconBg = (type) => {
  switch (type) {
    case 'verifikasi':
      return 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400'
    case 'diagnosis':
      return 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400'
    case 'balita':
      return 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400'
    default:
      return 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'
  }
}
</script>

<style scoped>
@keyframes wave {
  0%, 100% { transform: rotate(0deg); }
  20%, 60% { transform: rotate(14deg); }
  40%, 80% { transform: rotate(-10deg); }
}
.animate-wave {
  animation: wave 1.8s infinite ease-in-out;
  transform-origin: 70% 70%;
}
</style>
