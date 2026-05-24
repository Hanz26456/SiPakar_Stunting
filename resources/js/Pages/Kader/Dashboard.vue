<template>
  <KaderLayout title="Dashboard">
    <!-- Stat cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
      <StatCard label="Total Balita" :value="stats.total_balita" color="gray" icon="👶" />
      <StatCard label="Normal" :value="stats.normal" color="green" icon="✅"
        :sub="`${Math.round(stats.normal / stats.total_balita * 100) || 0}%`" />
      <StatCard label="Berisiko" :value="stats.berisiko" color="amber" icon="⚠️"
        :sub="`${Math.round(stats.berisiko / stats.total_balita * 100) || 0}%`" />
      <StatCard label="Stunting" :value="stats.stunting" color="red" icon="🚨"
        :sub="`${stats.persen_stunting}%`" />
    </div>

    <div class="grid grid-cols-2 gap-4">
      <!-- Distribusi 6 bulan -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="text-sm font-medium text-gray-700 mb-4">📊 Distribusi status — 6 bulan terakhir</div>
        <BarChart :data="distribusi" />
      </div>

      <!-- Balita perlu perhatian -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
          <div class="text-sm font-medium text-gray-700">🚨 Perlu perhatian segera</div>
          <Link :href="route('kader.kunjungan.index')" class="text-xs text-emerald-600 hover:underline">Lihat semua →</Link>
        </div>
        <div v-if="balita_perhatian.length === 0" class="text-sm text-gray-400 text-center py-6">
          Tidak ada balita yang perlu perhatian 🎉
        </div>
        <div v-else class="space-y-2">
          <div
            v-for="b in balita_perhatian" :key="b.id"
            class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer"
            @click="$inertia.visit(route('kader.diagnosis.show', b.id))"
          >
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-sm">👦</div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-gray-800 truncate">{{ b.nama_balita }}</div>
              <div class="text-xs text-gray-400">{{ b.usia_format }} · CF {{ b.cf_persen }}</div>
            </div>
            <StatusBadge :status="b.label_status.toLowerCase().replace(' ', '_')" :label="b.label_status" />
          </div>
        </div>
      </div>

      <!-- Kunjungan terbaru -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 col-span-2">
        <div class="flex items-center justify-between mb-3">
          <div class="text-sm font-medium text-gray-700">📋 Kunjungan bulan ini</div>
          <Link :href="route('kader.kunjungan.create')" class="text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700">
            + Input kunjungan
          </Link>
        </div>
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="text-left py-2 px-2 text-xs text-gray-400 font-medium">Nama balita</th>
              <th class="text-left py-2 px-2 text-xs text-gray-400 font-medium">Tanggal</th>
              <th class="text-left py-2 px-2 text-xs text-gray-400 font-medium">BB / TB</th>
              <th class="text-left py-2 px-2 text-xs text-gray-400 font-medium">Z-score</th>
              <th class="text-left py-2 px-2 text-xs text-gray-400 font-medium">Status</th>
              <th class="text-left py-2 px-2 text-xs text-gray-400 font-medium">Diagnosis</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="k in kunjungan_terbaru" :key="k.id"
              class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer"
              @click="$inertia.visit(route('kader.kunjungan.show', k.id))"
            >
              <td class="py-2.5 px-2 font-medium text-gray-800">{{ k.nama_balita }}</td>
              <td class="py-2.5 px-2 text-gray-500">{{ k.tanggal }}</td>
              <td class="py-2.5 px-2 text-gray-600">{{ k.berat_badan }} kg / {{ k.tinggi_badan }} cm</td>
              <td class="py-2.5 px-2 font-medium" :class="zscoreColor(k.status)">{{ k.status }}</td>
              <td class="py-2.5 px-2">
                <StatusBadge :status="k.warna === 'red' ? 'stunting' : k.warna === 'amber' ? 'berisiko' : 'normal'" :label="k.status" />
              </td>
              <td class="py-2.5 px-2">
                <span v-if="k.sudah_diagnosa" class="text-xs text-green-600">✅ Selesai</span>
                <Link v-else :href="route('kader.diagnosis.create', k.id)" class="text-xs text-amber-600 hover:underline" @click.stop>
                  Diagnosa →
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
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

const zscoreColor = (status) => ({
  'Normal':         'text-green-600',
  'Berisiko':       'text-amber-600',
  'Stunting':       'text-red-600',
  'Stunting berat': 'text-red-800',
}[status] ?? 'text-gray-500')
</script>
