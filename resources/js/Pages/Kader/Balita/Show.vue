<template>
  <KaderLayout :title="balita.nama">
    <div class="space-y-5">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-400">
        <Link :href="route('kader.balita.index')" class="hover:text-orbit-primary transition-colors">Data Balita</Link>
        <span>›</span>
        <span class="text-slate-600 dark:text-slate-300 font-medium">{{ balita.nama }}</span>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Kolom kiri: Info balita + Riwayat medis -->
        <div class="space-y-5">

          <!-- Kartu profil balita -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-3.5 mb-4 pb-4 border-b border-slate-100 dark:border-orbit-border/60">
              <div
                class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                :class="balita.jenis_kelamin === 'Laki-laki' || balita.jenis_kelamin === 'L' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'bg-pink-500/10 text-pink-600 dark:text-pink-400'"
              >
                <Baby class="w-6 h-6" />
              </div>
              <div class="flex-1 min-w-0">
                <div class="font-bold text-slate-800 dark:text-slate-100 truncate text-base">{{ balita.nama }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ balita.usia_format }} · {{ balita.jenis_kelamin }}</div>
              </div>
              <Link
                :href="route('kader.balita.edit', balita.id)"
                class="inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300 hover:text-orbit-primary dark:hover:text-orbit-primary border border-slate-200 dark:border-orbit-border bg-slate-50 dark:bg-white/5 px-2.5 py-1.5 rounded-xl transition-colors font-medium"
              >
                <Pencil class="w-3.5 h-3.5" />
                <span>Edit</span>
              </Link>
            </div>

            <div class="space-y-2.5 text-xs sm:text-sm">
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Tanggal Lahir</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ balita.tanggal_lahir }}</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Nama Ibu</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ balita.nama_ibu }}</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40" v-if="balita.nama_ayah">
                <span class="text-slate-400">Nama Ayah</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ balita.nama_ayah }}</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40" v-if="balita.no_hp_ortu">
                <span class="text-slate-400">No. WhatsApp / HP</span>
                <span class="font-mono text-orbit-primary">{{ balita.no_hp_ortu }}</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40" v-if="balita.desa">
                <span class="text-slate-400">Desa</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ balita.desa }}</span>
              </div>
              <div class="flex justify-between items-start py-1" v-if="balita.alamat">
                <span class="text-slate-400">Alamat</span>
                <span class="font-medium text-slate-700 dark:text-slate-200 text-right max-w-[180px]">{{ balita.alamat }}</span>
              </div>
            </div>
          </div>

          <!-- Riwayat medis -->
          <div v-if="riwayat" class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">
              <ClipboardList class="w-4 h-4 text-orbit-primary" />
              <span>Riwayat Medis & Gizi</span>
            </div>
            <div class="space-y-2.5 text-xs sm:text-sm">
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">ASI Eksklusif</span>
                <span v-if="riwayat.asi_eksklusif" class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                  <Check class="w-3.5 h-3.5" /> Ya
                </span>
                <span v-else class="inline-flex items-center gap-1 font-semibold text-rose-500">
                  <X class="w-3.5 h-3.5" /> Tidak
                </span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">MPASI Sesuai Usia</span>
                <span v-if="riwayat.mpasi_sesuai_usia" class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                  <Check class="w-3.5 h-3.5" /> Ya
                </span>
                <span v-else class="inline-flex items-center gap-1 font-semibold text-rose-500">
                  <X class="w-3.5 h-3.5" /> Tidak
                </span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Infeksi Berulang</span>
                <span v-if="riwayat.infeksi_berulang" class="inline-flex items-center gap-1 font-semibold text-amber-500">
                  <AlertTriangle class="w-3.5 h-3.5" /> Ada
                </span>
                <span v-else class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                  <Check class="w-3.5 h-3.5" /> Tidak
                </span>
              </div>
              <div v-if="riwayat.mulai_mpasi" class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Mulai MPASI</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ riwayat.mulai_mpasi }}</span>
              </div>
              <div v-if="riwayat.berat_lahir" class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-orbit-border/40">
                <span class="text-slate-400">Berat Lahir</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ riwayat.berat_lahir }} kg</span>
              </div>
              <div v-if="riwayat.panjang_lahir" class="flex justify-between items-center py-1">
                <span class="text-slate-400">Panjang Lahir</span>
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ riwayat.panjang_lahir }} cm</span>
              </div>
            </div>
          </div>

          <!-- Tombol aksi input kunjungan -->
          <Link
            :href="route('kader.kunjungan.create-for-balita', balita.id)"
            class="w-full py-2.5 px-4 text-xs sm:text-sm font-semibold bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 transition-all flex items-center justify-center gap-2 active:scale-[0.98]"
          >
            <Plus class="w-4 h-4" />
            <span>Input Kunjungan Posyandu</span>
          </Link>
        </div>

        <!-- Kolom tengah + kanan: Tren + Riwayat kunjungan -->
        <div class="lg:col-span-2 space-y-5">

          <!-- Grafik tren -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
              <TrendingUp class="w-4 h-4 text-orbit-primary" />
              <span>Tren Pertumbuhan Z-Score</span>
            </div>
            <div v-if="tren.length === 0" class="text-xs sm:text-sm text-slate-400 text-center py-8 italic">
              Belum ada riwayat data kunjungan
            </div>
            <div v-else class="space-y-3.5">
              <div v-for="t in tren" :key="t.bulan" class="flex items-center gap-3">
                <div class="text-xs text-slate-400 w-16 flex-shrink-0 font-medium">{{ t.bulan }}</div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <div class="flex-1 h-2 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full transition-all"
                        :class="zBarColor(t.zscore_tbu)"
                        :style="{ width: Math.min(100, Math.max(5, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                      ></div>
                    </div>
                    <span
                      class="text-xs font-mono font-semibold w-16 text-right flex-shrink-0"
                      :class="zColor(t.zscore_tbu)"
                    >
                      {{ t.zscore_tbu != null ? t.zscore_tbu.toFixed(1) : '—' }} SD
                    </span>
                  </div>
                  <div class="flex gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <span>BB: <strong class="text-slate-700 dark:text-slate-200">{{ t.berat_badan }} kg</strong></span>
                    <span>TB: <strong class="text-slate-700 dark:text-slate-200">{{ t.tinggi_badan }} cm</strong></span>
                    <span v-if="t.lila">LiLA: <strong class="text-slate-700 dark:text-slate-200">{{ t.lila }} cm</strong></span>
                  </div>
                </div>
                <span
                  class="text-[11px] font-semibold px-2.5 py-1 rounded-full flex-shrink-0"
                  :class="{
                    'bg-rose-500/10 text-rose-500 border border-rose-500/20': t.status === 'Stunting berat' || t.status === 'Stunting',
                    'bg-amber-500/10 text-amber-500 border border-amber-500/20': t.status === 'Berisiko',
                    'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20': t.status === 'Normal',
                  }"
                >{{ t.status }}</span>
              </div>
            </div>
          </div>

          <!-- Riwayat kunjungan -->
          <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border shadow-2xs overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-slate-100 dark:border-orbit-border bg-slate-50/50 dark:bg-orbit-surface2/40">
              <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200">
                <Calendar class="w-4 h-4 text-orbit-primary" />
                <span>Riwayat Kunjungan Posyandu</span>
              </div>
              <span class="text-xs text-slate-400 font-medium">{{ riwayat_kunjungan.length }} Kunjungan</span>
            </div>
            <div v-if="riwayat_kunjungan.length === 0" class="p-8 text-center text-slate-400 text-xs sm:text-sm italic">
              Belum ada riwayat kunjungan tercatat
            </div>
            <div v-else class="overflow-x-auto">
              <table class="w-full text-xs sm:text-sm text-left">
                <thead>
                  <tr class="border-b border-slate-100 dark:border-orbit-border text-[11px] uppercase tracking-wider font-semibold text-slate-400 bg-slate-50 dark:bg-orbit-surface2/60">
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Usia</th>
                    <th class="py-3 px-4">BB / TB</th>
                    <th class="py-3 px-4">Z-score</th>
                    <th class="py-3 px-4">CF</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-orbit-border/60">
                  <tr
                    v-for="k in riwayat_kunjungan" :key="k.id"
                    class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors"
                  >
                    <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ k.tanggal }}</td>
                    <td class="py-3 px-4 text-slate-400 text-xs whitespace-nowrap">{{ k.usia_bulan }} bln</td>
                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono text-xs whitespace-nowrap">
                      {{ k.berat_badan }} kg / {{ k.tinggi_badan }} cm
                    </td>
                    <td class="py-3 px-4 text-xs font-mono font-semibold whitespace-nowrap" :class="zColor(k.zscore_tbu)">
                      {{ k.zscore_tbu != null ? k.zscore_tbu.toFixed(2) : '—' }}
                    </td>
                    <td class="py-3 px-4 text-xs font-mono font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                      {{ k.cf ?? '—' }}
                    </td>
                    <td class="py-3 px-4 whitespace-nowrap">
                      <StatusBadge
                        v-if="k.status_cf"
                        :status="k.diagnosis?.status_final ?? ''"
                        :label="k.status_cf"
                      />
                      <span v-else class="text-xs text-slate-400 italic">—</span>
                    </td>
                    <td class="py-3 px-4 text-right whitespace-nowrap">
                      <Link
                        v-if="k.diagnosis_id"
                        :href="route('kader.diagnosis.show', k.diagnosis_id)"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-orbit-primary hover:underline"
                      >
                        <span>Lihat</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                      </Link>
                      <Link
                        v-else
                        :href="route('kader.diagnosis.create', k.id)"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-amber-500 hover:underline"
                      >
                        <span>Diagnosis</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </KaderLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  Baby,
  Pencil,
  ClipboardList,
  Check,
  X,
  AlertTriangle,
  Plus,
  TrendingUp,
  Calendar,
  ArrowRight
} from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

defineProps({
  balita: Object,
  riwayat: Object,
  riwayat_kunjungan: Array,
  tren: Array,
})

const zColor = (z) => {
  if (z == null) return 'text-slate-400'
  if (z < -2) return 'text-rose-500'
  if (z < -1) return 'text-amber-500'
  return 'text-emerald-500'
}

const zBarColor = (z) => {
  if (z == null) return 'bg-slate-300 dark:bg-slate-700'
  if (z < -2) return 'bg-rose-500'
  if (z < -1) return 'bg-amber-500'
  return 'bg-emerald-500'
}
</script>
