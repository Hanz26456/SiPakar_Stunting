<template>
  <BidanLayout title="Dashboard" :pending-count="stats.perlu_verifikasi">
    <!-- Stat cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
      <StatCard label="Perlu verifikasi" :value="stats.perlu_verifikasi" color="amber" icon="⏳" />
      <StatCard label="Stunting aktif" :value="stats.stunting_aktif" color="red" icon="🚨" />
      <StatCard label="Sudah dirujuk" :value="stats.sudah_dirujuk" color="gray" icon="🏥" />
      <StatCard label="CF rata-rata" :value="stats.cf_rata_rata" color="gray" icon="📊" />
    </div>

    <div class="grid grid-cols-2 gap-4">

      <!-- Notifikasi prioritas -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
          <div class="text-sm font-medium text-gray-700">🔔 Perlu diverifikasi segera</div>
          <Link :href="route('bidan.monitoring')" class="text-xs text-blue-600 hover:underline">Lihat semua →</Link>
        </div>
        <div v-if="belum_verifikasi.length === 0" class="text-sm text-gray-400 text-center py-6">
          Semua diagnosis sudah diverifikasi ✅
        </div>
        <div v-else class="space-y-2">
          <div
            v-for="d in belum_verifikasi" :key="d.id"
            class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-100"
            @click="$inertia.visit(route('bidan.verifikasi.show', d.id))"
          >
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm flex-shrink-0"
              :class="d.warna_status === 'red' ? 'bg-red-100' : 'bg-amber-100'">
              {{ d.warna_status === 'red' ? '🚨' : '⚠️' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-gray-800 truncate">{{ d.nama_balita }}</div>
              <div class="text-xs text-gray-400">{{ d.usia_format }} · CF {{ d.cf_persen }} · {{ d.kader }}</div>
            </div>
            <div class="flex flex-col items-end gap-1">
              <StatusBadge :status="d.status" :label="d.label_status" />
              <span class="text-xs text-gray-300">{{ d.tanggal }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Penanganan aktif -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-sm font-medium text-gray-700 mb-3">📋 Penanganan aktif</div>
        <div class="space-y-2">
          <div
            v-for="d in penanganan_aktif" :key="d.id"
            class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer"
            @click="$inertia.visit(route('bidan.pasien.show', d.balita_id))"
          >
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-gray-800 truncate">{{ d.nama_balita }}</div>
              <div class="text-xs text-gray-400">
                {{ tindakLanjutLabel(d.tindak_lanjut) }}
                <span v-if="d.jadwal_kontrol"> · Kontrol: {{ d.jadwal_kontrol }}</span>
              </div>
            </div>
            <StatusBadge :status="d.status_final" :label="d.label_status" />
          </div>
        </div>
      </div>

      <!-- Distribusi 6 bulan -->
      <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-sm font-medium text-gray-700 mb-4">📈 Distribusi status 6 bulan terakhir</div>
        <BarChart :data="distribusi" />
      </div>
    </div>
  </BidanLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import BarChart from '@/Components/BarChart.vue'

defineProps({
  stats: Object,
  belum_verifikasi: Array,
  penanganan_aktif: Array,
  distribusi: Array,
})

const tindakLanjutLabel = (t) => ({
  pantau: 'Pantau ketat', edukasi_gizi: 'Edukasi gizi',
  pmt: 'PMT', rujuk_puskesmas: 'Rujuk PKM', rujuk_rsud: 'Rujuk RSUD',
}[t] ?? '-')
</script>