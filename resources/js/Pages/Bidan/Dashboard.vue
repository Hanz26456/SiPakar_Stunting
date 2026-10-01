<template>
  <BidanLayout title="Dashboard Tenaga Kesehatan" :pending-count="stats.perlu_verifikasi">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-100 flex items-center gap-2.5">
          <span>Panel Verifikasi & Monitoring</span>
          <span v-if="stats.perlu_verifikasi > 0" class="text-xs font-medium px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-400 border border-rose-500/20">
            {{ stats.perlu_verifikasi }} Menunggu
          </span>
        </h2>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
          Validasi hasil diagnosis Certainty Factor dari posyandu dan tindak lanjut balita terindikasi stunting.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <Link
          :href="route('bidan.monitoring')"
          class="inline-flex items-center px-3.5 py-2 rounded-xl bg-orbit-primary hover:bg-orbit-primary-light text-xs font-semibold text-white shadow-sm transition-all"
        >
          Buka Halaman Verifikasi
        </Link>
      </div>
    </div>

    <!-- Stat cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <StatCard 
        label="Perlu Verifikasi" 
        :value="stats.perlu_verifikasi" 
        sub="Hasil diagnosis baru"
      />

      <StatCard 
        label="Stunting Aktif" 
        :value="stats.stunting_aktif" 
        sub="Dalam pemantauan nakes"
      />

      <StatCard 
        label="Sudah Dirujuk" 
        :value="stats.sudah_dirujuk" 
        sub="Puskesmas / RSUD"
      />

      <StatCard 
        label="CF Rata-Rata" 
        :value="stats.cf_rata_rata" 
        sub="Tingkat keyakinan pakar"
      />
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
      <!-- Notifikasi prioritas -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-slate-200">Perlu Diverifikasi Segera</h3>
          <Link :href="route('bidan.monitoring')" class="text-xs text-orbit-primary-light hover:underline font-medium">
            Lihat semua →
          </Link>
        </div>

        <div v-if="!belum_verifikasi || belum_verifikasi.length === 0" class="text-xs text-slate-500 text-center py-10 flex-1 flex items-center justify-center">
          Semua diagnosis posyandu telah diverifikasi
        </div>

        <div v-else class="space-y-2 flex-1 overflow-y-auto max-h-[300px] pr-1">
          <div
            v-for="d in belum_verifikasi" :key="d.id"
            class="flex items-center justify-between gap-3 p-3 rounded-xl bg-orbit-surface2/40 border border-orbit-border hover:border-orbit-border2 hover:bg-white/5 cursor-pointer transition-colors"
            @click="$inertia.visit(route('bidan.verifikasi.show', d.id))"
          >
            <div class="flex-1 min-w-0">
              <div class="text-xs font-semibold text-slate-200 truncate">
                {{ d.nama_balita }}
              </div>
              <div class="text-[11px] text-slate-400 mt-0.5">
                {{ d.usia_format }} · CF <strong class="text-slate-300">{{ d.cf_persen }}</strong> · {{ d.kader }}
              </div>
            </div>

            <div class="flex flex-col items-end gap-1 flex-shrink-0">
              <StatusBadge :status="d.status" :label="d.label_status" />
              <span class="text-[10px] text-slate-500">{{ d.tanggal }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Penanganan aktif -->
      <div class="bg-orbit-surface rounded-2xl border border-orbit-border p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-slate-200">Penanganan Balita Aktif</h3>
          <span class="text-xs text-slate-500">{{ penanganan_aktif.length }} kasus</span>
        </div>

        <div v-if="!penanganan_aktif || penanganan_aktif.length === 0" class="text-xs text-slate-500 text-center py-10 flex-1 flex items-center justify-center">
          Tidak ada pasien dalam penanganan khusus
        </div>

        <div v-else class="space-y-2 flex-1 overflow-y-auto max-h-[300px] pr-1">
          <div
            v-for="d in penanganan_aktif" :key="d.id"
            class="flex items-center justify-between gap-3 p-3 rounded-xl bg-orbit-surface2/40 border border-orbit-border hover:border-orbit-border2 hover:bg-white/5 cursor-pointer transition-colors"
            @click="$inertia.visit(route('bidan.pasien.show', d.balita_id))"
          >
            <div class="flex-1 min-w-0">
              <div class="text-xs font-semibold text-slate-200 truncate">
                {{ d.nama_balita }}
              </div>
              <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                <span class="text-slate-300 font-medium">{{ tindakLanjutLabel(d.tindak_lanjut) }}</span>
                <span v-if="d.jadwal_kontrol" class="text-slate-500">· Kontrol: {{ d.jadwal_kontrol }}</span>
              </div>
            </div>
            <StatusBadge :status="d.status_final" :label="d.label_status" />
          </div>
        </div>
      </div>

      <!-- Distribusi 6 bulan -->
      <div class="lg:col-span-2 bg-orbit-surface rounded-2xl border border-orbit-border p-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-semibold text-slate-200">Distribusi Status Gizi 6 Bulan Terakhir</h3>
            <p class="text-xs text-slate-400 mt-0.5">Tren stunting dan normal di wilayah kerja Puskesmas Pujer</p>
          </div>
        </div>
        <div class="pt-2">
          <BarChart :data="distribusi" />
        </div>
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
  pantau: 'Pantau ketat',
  edukasi_gizi: 'Edukasi gizi',
  pmt: 'PMT Pemulihan',
  rujuk_puskesmas: 'Rujuk Puskesmas',
  rujuk_rsud: 'Rujuk RSUD',
}[t] ?? '-')
</script>