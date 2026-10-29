<template>
  <BidanLayout title="Verifikasi Diagnosis Medis">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Header & Status Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <Link
          :href="route('bidan.monitoring')"
          class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 transition-colors"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Kembali ke Pemantauan Pasien</span>
        </Link>

        <div>
          <span
            v-if="diagnosis.sudah_diverifikasi"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40"
          >
            <ShieldCheck class="w-3.5 h-3.5" />
            <span>Telah Diverifikasi Bidan</span>
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/40 animate-pulse"
          >
            <Clock class="w-3.5 h-3.5" />
            <span>Menunggu Validasi Klinis Bidan</span>
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Patient Data, Certainty Factor System Output, Growth Trend -->
        <div class="lg:col-span-2 space-y-5">

          <!-- Patient & Anthropometry Card -->
          <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
            <div class="flex items-center gap-3.5 mb-4 pb-4 border-b border-slate-100 dark:border-orbit-border/80">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                <Baby class="w-6 h-6" />
              </div>
              <div class="flex-1 min-w-0">
                <h2 class="text-base font-bold text-slate-900 dark:text-white truncate">
                  {{ balita.nama }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                  {{ balita.usia_format }} · {{ balita.jenis_kelamin }} · Desa: {{ balita.desa }}
                </p>
              </div>
              <div class="text-right flex-shrink-0">
                <span class="text-[11px] text-slate-400 uppercase tracking-wider block">Tanggal Kunjungan</span>
                <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200">{{ kunjungan.tanggal }}</span>
              </div>
            </div>

            <!-- Measurements Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
              <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">Berat Badan</span>
                <span class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">{{ kunjungan.berat_badan }}</span>
                <span class="text-[11px] text-slate-400 ml-0.5">kg</span>
              </div>

              <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">Tinggi Badan</span>
                <span class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">{{ kunjungan.tinggi_badan }}</span>
                <span class="text-[11px] text-slate-400 ml-0.5">cm</span>
              </div>

              <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">Z-Score TB/U</span>
                <span class="text-base sm:text-lg font-bold" :class="zColor(kunjungan.zscore_tbu)">
                  {{ kunjungan.zscore_tbu?.toFixed(2) ?? '-' }}
                </span>
                <span class="text-[11px] text-slate-400 ml-0.5">SD</span>
              </div>

              <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
                <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">LiLA</span>
                <span class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">{{ kunjungan.lila ?? '-' }}</span>
                <span class="text-[11px] text-slate-400 ml-0.5">cm</span>
              </div>
            </div>
          </div>

          <!-- Certainty Factor Mathematical Calculation Detail -->
          <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100 dark:border-orbit-border/80">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <FileText class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                <span>Detail Perhitungan Certainty Factor Sistem</span>
              </h3>
              <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                Total {{ detail_gejala.length }} Rule
              </span>
            </div>

            <div class="space-y-2">
              <div
                v-for="d in detail_gejala"
                :key="d.kode_rule"
                class="flex items-center justify-between gap-3 p-2.5 rounded-xl border transition-all"
                :class="d.gejala_aktif
                  ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/40 text-slate-900 dark:text-white'
                  : 'bg-slate-50/40 dark:bg-orbit-surface2/30 border-slate-200/60 dark:border-orbit-border/40 text-slate-400 opacity-60'"
              >
                <div class="flex items-center gap-2.5 min-w-0">
                  <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                    {{ d.kode_rule }}
                  </span>
                  <span class="text-xs sm:text-sm font-medium truncate">{{ d.nama_gejala }}</span>
                </div>

                <div class="flex items-center gap-2 text-xs flex-shrink-0 font-mono">
                  <span class="text-blue-600 font-medium">MB: {{ d.mb }}</span>
                  <span class="text-amber-600 font-medium">MD: {{ d.md }}</span>
                  <span class="font-bold w-16 text-right" :class="d.gejala_aktif ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400'">
                    CF {{ d.cf_pakar.toFixed(2) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-orbit-border/80 flex items-center justify-between">
              <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                Nilai Keyakinan CF Kombinasi Final:
              </span>
              <span class="text-xl font-black" :class="cfColor(diagnosis.cf_kombinasi)">
                {{ diagnosis.cf_persen }}
              </span>
            </div>
          </div>

          <!-- 6-Month Growth Trend -->
          <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
              <TrendingUp class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              <span>Tren Pertumbuhan TB/U Balita (6 Bulan Terakhir)</span>
            </h3>

            <div class="space-y-2.5">
              <div
                v-for="t in tren"
                :key="t.bulan"
                class="flex items-center gap-3 text-xs"
              >
                <span class="w-16 font-medium text-slate-500 dark:text-slate-400 flex-shrink-0">
                  {{ t.bulan }}
                </span>

                <div class="flex-1 h-2.5 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all"
                    :class="zBarColor(t.zscore_tbu)"
                    :style="{ width: Math.min(100, Math.max(0, (t.zscore_tbu + 3) / 6 * 100)) + '%' }"
                  />
                </div>

                <span class="w-16 text-right font-semibold flex-shrink-0" :class="zColor(t.zscore_tbu)">
                  {{ t.zscore_tbu?.toFixed(1) }} SD
                </span>

                <span class="w-16 text-right text-slate-400 flex-shrink-0">
                  {{ t.berat_badan }} kg
                </span>
              </div>
            </div>
          </div>

        </div>

        <!-- Right Column: Verification & Action Form -->
        <div class="space-y-5">

          <!-- System Output Summary Card -->
          <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-2">
              Diagnosis Sistem Pakar
            </span>

            <div class="flex items-center justify-between gap-3 mb-3">
              <StatusBadge :status="diagnosis.status_stunting" :label="diagnosis.label_status" />
              <span class="text-base sm:text-lg font-black" :class="cfColor(diagnosis.cf_kombinasi)">
                {{ diagnosis.cf_persen }}
              </span>
            </div>

            <div class="text-xs text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-orbit-border/80 pt-2.5 flex items-center justify-between">
              <span>Diinput oleh Kader:</span>
              <strong class="text-slate-700 dark:text-slate-300">{{ diagnosis.kader }}</strong>
            </div>
          </div>

          <!-- Bidan Verification Form -->
          <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
            <div class="flex items-center gap-2 mb-4">
              <Stethoscope class="w-4 h-4 text-purple-600 dark:text-purple-400" />
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                Validasi Klinis Bidan
              </h3>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
              <!-- Koreksi Status (Override) -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Koreksi Status Medis (Opsional)
                </label>
                <select
                  v-model="form.status_override"
                  class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
                >
                  <option value="">— Ikuti Hasil Sistem Pakar —</option>
                  <option value="normal">Normal</option>
                  <option value="berisiko">Berisiko</option>
                  <option value="stunting">Stunting</option>
                  <option value="stunting_berat">Stunting Berat</option>
                </select>
              </div>

              <!-- Alasan Jika Di-override -->
              <div v-if="form.status_override">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Alasan Koreksi Klinis
                </label>
                <input
                  v-model="form.alasan_override"
                  type="text"
                  placeholder="Catatan pertimbangan medis..."
                  class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
                >
              </div>

              <!-- Tindak Lanjut -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Tindak Lanjut / Intervensi
                </label>
                <select
                  v-model="form.tindak_lanjut"
                  class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
                >
                  <option value="">— Pilih Tindak Lanjut —</option>
                  <option value="pantau">Pantau Ketat di Posyandu</option>
                  <option value="edukasi_gizi">Edukasi Gizi Keluarga</option>
                  <option value="pmt">Pemberian Makanan Tambahan (PMT)</option>
                  <option value="rujuk_puskesmas">Rujuk ke Puskesmas</option>
                  <option value="rujuk_rsud">Rujuk ke RSUD</option>
                </select>
              </div>

              <!-- Jadwal Kontrol -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Jadwal Kontrol Ulang
                </label>
                <input
                  v-model="form.jadwal_kontrol"
                  type="date"
                  class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
                >
              </div>

              <!-- Catatan Klinis Tambahan -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Catatan Klinis Bidan
                </label>
                <textarea
                  v-model="form.catatan_bidan"
                  rows="3"
                  placeholder="Catatan konseling ibu, intervensi medis, atau rekomendasi spesifik..."
                  class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all resize-none"
                />
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center gap-2.5 pt-2">
                <button
                  type="button"
                  @click="$inertia.visit(route('bidan.monitoring'))"
                  class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-xl border border-slate-200 dark:border-orbit-border text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-xl bg-orbit-primary hover:bg-orbit-primary/90 text-white shadow-md shadow-purple-600/25 transition-all active:scale-[0.98] disabled:opacity-50"
                >
                  {{ form.processing ? 'Menyimpan...' : 'Simpan Verifikasi' }}
                </button>
              </div>
            </form>
          </div>

          <!-- Link Riwayat Lengkap -->
          <Link
            :href="route('bidan.pasien.show', balita.id)"
            class="block bg-slate-50 dark:bg-orbit-surface2/50 border border-slate-200 dark:border-orbit-border rounded-2xl p-3.5 text-xs sm:text-sm text-center font-medium text-slate-600 dark:text-slate-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50/50 dark:hover:bg-purple-950/20 transition-all shadow-2xs"
          >
            Lihat Rekam Medis Lengkap Balita →
          </Link>

        </div>
      </div>

    </div>
  </BidanLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import {
  Baby,
  Stethoscope,
  ShieldCheck,
  Clock,
  ArrowLeft,
  FileText,
  TrendingUp,
} from 'lucide-vue-next'
import BidanLayout from '@/Layouts/BidanLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  diagnosis: Object,
  detail_gejala: Array,
  balita: Object,
  kunjungan: Object,
  tren: Array,
})

const form = useForm({
  status_override: props.diagnosis.status_override ?? '',
  alasan_override: props.diagnosis.alasan_override ?? '',
  catatan_bidan:   props.diagnosis.catatan_bidan ?? '',
  tindak_lanjut:   props.diagnosis.tindak_lanjut ?? '',
  jadwal_kontrol:  props.diagnosis.jadwal_kontrol ?? '',
})

const submit = () => {
  form.post(route('bidan.verifikasi.update', props.diagnosis.id))
}

const cfColor  = (cf) => cf >= 0.7 ? 'text-rose-600 dark:text-rose-400' : cf >= 0.4 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'
const zColor   = (z)  => z == null ? 'text-slate-400' : z < -2 ? 'text-rose-600 dark:text-rose-400' : z < -1 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'
const zBarColor = (z) => z == null ? 'bg-slate-300' : z < -2 ? 'bg-rose-500' : z < -1 ? 'bg-amber-500' : 'bg-emerald-500'
</script>