<template>
  <KaderLayout title="Dashboard Kader Posyandu">
    <!-- Header -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
          Pencatatan & Diagnosis Dini
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
          Catat antropometri kunjungan bulanan balita dan lakukan diagnosis stunting dengan metode Certainty Factor.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <Link
          :href="route('kader.kunjungan.create')"
          class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-orbit-primary hover:bg-orbit-primary/90 text-xs font-semibold text-white shadow-md shadow-purple-600/20 transition-all"
        >
          <Plus class="w-3.5 h-3.5" />
          <span>Input Kunjungan Baru</span>
        </Link>
      </div>
    </div>

    <!-- 5 Meja Posyandu Guided Workflow Banner -->
    <div class="bg-gradient-to-r from-purple-500/10 via-indigo-500/5 to-cyan-500/10 border border-purple-500/20 dark:border-purple-500/30 rounded-2xl p-5 mb-6 backdrop-blur-sm">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-purple-500/15 text-purple-700 dark:text-purple-300 text-[11px] font-bold uppercase tracking-wider mb-1.5">
            <ClipboardList class="w-3.5 h-3.5" />
            <span>Alur Pelayanan 5 Meja Posyandu</span>
          </div>
          <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
            Hari Pelayanan Posyandu Melati Pujer
          </h3>
          <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 max-w-xl">
            1. Pendaftaran Balita → 2. Timbang & Ukur (BB & TB) → 3. Hitung Z-Score WHO → 4. Diagnosis Pakar Certainty Factor → 5. Verifikasi Bidan.
          </p>
        </div>
        <div class="flex items-center gap-2.5 flex-shrink-0">
          <Link
            :href="route('kader.balita.create')"
            class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 shadow-2xs transition-all"
          >
            + Balita Baru
          </Link>
          <Link
            :href="route('kader.kunjungan.create')"
            class="px-4 py-2 text-xs font-semibold rounded-xl bg-orbit-primary hover:bg-orbit-primary/90 text-white shadow-md shadow-purple-600/25 transition-all flex items-center gap-1.5"
          >
            <span>Mulai Penimbangan</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Stat cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <StatCard 
        label="Total Balita" 
        :value="stats.total_balita" 
        sub="Terdata di posyandu"
      />

      <StatCard 
        label="Status Normal" 
        :value="stats.normal" 
        :sub="`${Math.round(stats.normal / stats.total_balita * 100) || 0}% dari total anak`"
      />

      <StatCard 
        label="Berisiko" 
        :value="stats.berisiko" 
        :sub="`${Math.round(stats.berisiko / stats.total_balita * 100) || 0}% butuh pantauan`"
      />

      <StatCard 
        label="Terindikasi Stunting" 
        :value="stats.stunting" 
        :sub="`Prevalensi: ${stats.persen_stunting}%`"
      />
    </div>

    <!-- Analytics & Priority Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
      <!-- Distribusi 6 bulan -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-semibold text-slate-200">Distribusi Status Gizi (6 Bulan Terakhir)</h3>
            <p class="text-xs text-slate-400 mt-0.5">Tren hasil diagnosis balita di Posyandu Melati</p>
          </div>
        </div>
        <div class="pt-2">
          <BarChart :data="distribusi" />
        </div>
      </div>

      <!-- Balita perlu perhatian -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-slate-200">Perlu Perhatian Segera</h3>
          <Link :href="route('kader.kunjungan.index')" class="text-xs text-orbit-primary-light hover:underline font-medium">
            Lihat semua →
          </Link>
        </div>

        <div v-if="!balita_perhatian || balita_perhatian.length === 0" class="text-xs text-slate-500 text-center py-10 flex-1 flex items-center justify-center">
          Tidak ada balita yang perlu perhatian khusus
        </div>

        <div v-else class="space-y-2 flex-1 overflow-y-auto max-h-[300px] pr-1">
          <div
            v-for="b in balita_perhatian" :key="b.id"
            class="flex items-center justify-between gap-3 p-3 rounded-xl bg-orbit-surface2/40 border border-orbit-border hover:border-orbit-border2 hover:bg-white/5 cursor-pointer transition-colors"
            @click="$inertia.visit(route('kader.diagnosis.show', b.id))"
          >
            <div class="flex-1 min-w-0">
              <div class="text-xs font-semibold text-slate-200 truncate">
                {{ b.nama_balita }}
              </div>
              <div class="text-[11px] text-slate-400 mt-0.5">
                {{ b.usia_format }} · Nilai CF <strong class="text-slate-300">{{ b.cf_persen }}</strong>
              </div>
            </div>

            <StatusBadge :status="b.label_status.toLowerCase().replace(' ', '_')" :label="b.label_status" />
          </div>
        </div>
      </div>

      <!-- Kunjungan terbaru table -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-semibold text-slate-200">Pencatatan Kunjungan Posyandu Bulan Ini</h3>
            <p class="text-xs text-slate-400 mt-0.5">Data antropometri dan status diagnosis terkini</p>
          </div>
          <Link 
            :href="route('kader.kunjungan.create')" 
            class="text-xs bg-orbit-surface2 hover:bg-white/5 text-slate-300 border border-orbit-border px-3 py-1.5 rounded-xl font-medium transition-colors"
          >
            + Catat Baru
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b border-orbit-border text-slate-400 uppercase tracking-wider font-semibold">
                <th class="text-left py-3 px-3">Nama Balita</th>
                <th class="text-left py-3 px-3">Tanggal</th>
                <th class="text-left py-3 px-3">Berat / Tinggi</th>
                <th class="text-left py-3 px-3">Z-Score TB/U</th>
                <th class="text-left py-3 px-3">Status</th>
                <th class="text-left py-3 px-3">Sistem Pakar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-orbit-border/50">
              <tr
                v-for="k in kunjungan_terbaru" :key="k.id"
                class="hover:bg-white/5 cursor-pointer transition-colors"
                @click="$inertia.visit(route('kader.kunjungan.show', k.id))"
              >
                <td class="py-3 px-3 font-semibold text-slate-200">{{ k.nama_balita }}</td>
                <td class="py-3 px-3 text-slate-400">{{ k.tanggal }}</td>
                <td class="py-3 px-3 text-slate-300">{{ k.berat_badan }} kg / {{ k.tinggi_badan }} cm</td>
                <td class="py-3 px-3 font-medium text-slate-200">{{ k.status }}</td>
                <td class="py-3 px-3">
                  <StatusBadge :status="k.warna === 'red' ? 'stunting' : k.warna === 'amber' ? 'berisiko' : 'normal'" :label="k.status" />
                </td>
                <td class="py-3 px-3">
                  <span v-if="k.sudah_diagnosa" class="text-emerald-400 font-medium">
                    Selesai
                  </span>
                  <Link 
                    v-else 
                    :href="route('kader.diagnosis.create', k.id)" 
                    class="text-orbit-primary-light hover:underline font-semibold" 
                    @click.stop
                  >
                    Diagnosa →
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Plus, ClipboardList, ArrowRight } from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import BarChart from '@/Components/BarChart.vue'

defineProps({
  stats: Object,
  balita_perhatian: Array,
  kunjungan_terbaru: Array,
  distribusi: Array,
})
</script>
