<template>
  <KaderLayout title="Diagnosis Stunting">
    <div class="max-w-5xl mx-auto space-y-6">

      <!-- Breadcrumbs / Top Navigation -->
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2 text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">
            <Calculator class="w-3.5 h-3.5" />
            <span>Sistem Pakar Certainty Factor</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
            Diagnosis Pertumbuhan & Stunting
          </h1>
        </div>

        <Link
          :href="route('kader.kunjungan.index')"
          class="px-3.5 py-1.5 text-xs font-medium rounded-xl border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
        >
          Kembali
        </Link>
      </div>

      <!-- Balita Info + Real-time CF Meter Header -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Balita & Anthropometry Card -->
        <div class="lg:col-span-2 bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
          <div class="flex items-center gap-3.5 mb-4 pb-4 border-b border-slate-100 dark:border-orbit-border/80">
            <div class="w-11 h-11 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
              <Baby class="w-6 h-6" />
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-900 dark:text-white">
                {{ balita.nama }}
              </h2>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ balita.usia_format }} · {{ balita.jenis_kelamin }} · Ibu: {{ balita.nama_ibu }}
              </p>
            </div>
          </div>

          <!-- Measurements Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
              <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">Berat Badan</span>
              <span class="text-lg font-bold text-slate-900 dark:text-white">{{ kunjungan.berat_badan }}</span>
              <span class="text-[11px] text-slate-400 ml-0.5">kg</span>
            </div>

            <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
              <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">Tinggi Badan</span>
              <span class="text-lg font-bold text-slate-900 dark:text-white">{{ kunjungan.tinggi_badan }}</span>
              <span class="text-[11px] text-slate-400 ml-0.5">cm</span>
            </div>

            <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
              <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">Z-Score TB/U</span>
              <span class="text-lg font-bold" :class="zscoreColor(kunjungan.zscore_tbu)">
                {{ kunjungan.zscore_tbu?.toFixed(2) ?? '-' }}
              </span>
              <span class="text-[11px] text-slate-400 ml-0.5">SD</span>
            </div>

            <div class="p-3 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl text-center border border-slate-100 dark:border-orbit-border/60">
              <span class="text-xs text-slate-500 dark:text-slate-400 block mb-0.5">Status Gizi</span>
              <div class="mt-0.5">
                <StatusBadge
                  :status="kunjungan.status_zscore?.toLowerCase().replace(' ', '_') ?? 'normal'"
                  :label="kunjungan.status_zscore ?? 'Normal'"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Real-Time CF Output Card -->
        <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs flex flex-col justify-between">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
              Tingkat Keyakinan (CF)
            </span>
            <span v-if="loading" class="text-xs text-purple-600 dark:text-purple-400 flex items-center gap-1 font-medium">
              <Loader2 class="w-3.5 h-3.5 animate-spin" />
              <span>Menghitung...</span>
            </span>
          </div>

          <div class="flex-1 flex flex-col items-center justify-center py-2">
            <div class="text-4xl sm:text-5xl font-black mb-1.5" :class="cfColor(preview?.cf_kombinasi ?? 0)">
              {{ preview?.cf_persen ?? '0%' }}
            </div>
            <div class="mt-1">
              <StatusBadge
                :status="preview?.status_stunting ?? 'normal'"
                :label="preview?.label_status ?? 'Normal'"
              />
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">
              {{ preview?.gejala_aktif ?? 0 }} dari {{ preview?.total_gejala ?? 0 }} gejala terkonfirmasi
            </div>
          </div>

          <!-- Progress Meter -->
          <div class="mt-3">
            <div class="h-2.5 bg-slate-100 dark:bg-orbit-surface2 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="cfBarColor(preview?.cf_kombinasi ?? 0)"
                :style="{ width: ((preview?.cf_kombinasi ?? 0) * 100) + '%' }"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Two-Column Symptoms Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Automatic Symptoms from Measurements -->
        <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
          <div class="flex items-center gap-2 mb-1">
            <Cpu class="w-4 h-4 text-purple-600 dark:text-purple-400" />
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
              Gejala Otomatis (Antropometri)
            </h3>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
            Dihitung otomatis oleh sistem berdasarkan standar baku WHO & riwayat KMS balita.
          </p>

          <div class="space-y-2.5">
            <div
              v-for="g in gejalOtomatis"
              :key="g.id"
              class="flex items-start gap-3 p-3 rounded-xl border transition-all"
              :class="g.terdeteksi
                ? 'bg-rose-50/70 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/40 text-rose-900 dark:text-rose-200'
                : 'bg-slate-50/70 dark:bg-orbit-surface2/50 border-slate-200/80 dark:border-orbit-border text-slate-700 dark:text-slate-300'"
            >
              <div class="mt-0.5 flex-shrink-0">
                <AlertTriangle v-if="g.terdeteksi" class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                <CheckCircle2 v-else class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-xs sm:text-sm font-semibold leading-snug">
                  {{ g.nama_gejala }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-2 font-mono">
                  <span>{{ g.kode }}</span>
                  <span>·</span>
                  <span :class="g.terdeteksi ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-emerald-600 dark:text-emerald-400'">
                    {{ g.terdeteksi ? 'Terpenuhi (Aktif)' : 'Normal' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Manual Symptoms Checklist -->
        <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
          <div class="flex items-center gap-2 mb-1">
            <Stethoscope class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
              Gejala Klinis (Observasi Kader)
            </h3>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
            Centang jika ditemukan tanda klinis atau fisik saat memeriksa balita secara langsung.
          </p>

          <div class="space-y-2.5">
            <label
              v-for="g in gejala_manual"
              :key="g.id"
              class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all select-none"
              :class="gejalaManu[g.id]
                ? 'bg-amber-50/80 dark:bg-amber-950/25 border-amber-300 dark:border-amber-700/50 shadow-2xs'
                : 'bg-slate-50/50 dark:bg-orbit-surface2/40 border-slate-200 dark:border-orbit-border hover:bg-slate-50 dark:hover:bg-white/[0.02]'"
            >
              <input
                type="checkbox"
                :checked="gejalaManu[g.id]"
                @change="toggleGejala(g.id)"
                class="mt-0.5 w-4 h-4 rounded text-purple-600 focus:ring-purple-500/30 border-slate-300 dark:border-orbit-border"
              >
              <div class="flex-1">
                <div class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white leading-snug">
                  {{ g.nama_gejala }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-2 font-mono">
                  <span>{{ g.kode }}</span>
                  <span>·</span>
                  <span class="capitalize">{{ g.kategori }}</span>
                </div>
              </div>
            </label>
          </div>
        </div>
      </div>

      <!-- Mathematical CF Breakdown Table -->
      <div class="bg-white dark:bg-orbit-surface border border-slate-200 dark:border-orbit-border rounded-2xl p-5 shadow-2xs">
        <div class="flex items-center gap-2 mb-3">
          <Calculator class="w-4 h-4 text-purple-600 dark:text-purple-400" />
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">
            Detail Perhitungan Matematis Certainty Factor
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="border-b border-slate-100 dark:border-orbit-border/80 text-slate-400 uppercase tracking-wider text-[11px] font-semibold">
                <th class="py-2.5 px-3">Rule</th>
                <th class="py-2.5 px-3">Gejala Teridentifikasi</th>
                <th class="py-2.5 px-3 text-center">MB</th>
                <th class="py-2.5 px-3 text-center">MD</th>
                <th class="py-2.5 px-3 text-center">CF Pakar</th>
                <th class="py-2.5 px-3 text-right">Status Rule</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-orbit-border/50">
              <tr
                v-for="d in preview?.detail_gejala ?? []"
                :key="d.kode_rule"
                class="hover:bg-slate-50/50 dark:hover:bg-white/[0.01]"
              >
                <td class="py-2.5 px-3 font-mono font-semibold text-purple-600 dark:text-purple-400">
                  {{ d.kode_rule }}
                </td>
                <td class="py-2.5 px-3 text-slate-800 dark:text-slate-200 font-medium">
                  {{ d.nama_gejala }}
                </td>
                <td class="py-2.5 px-3 text-center text-blue-600 font-mono">
                  {{ d.mb }}
                </td>
                <td class="py-2.5 px-3 text-center text-amber-600 font-mono">
                  {{ d.md }}
                </td>
                <td class="py-2.5 px-3 text-center font-mono font-semibold" :class="d.gejala_aktif ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400'">
                  {{ d.cf_pakar.toFixed(2) }}
                </td>
                <td class="py-2.5 px-3 text-right">
                  <span
                    v-if="d.gejala_aktif"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50"
                  >
                    <Check class="w-3 h-3" />
                    <span>Dihitung</span>
                  </span>
                  <span v-else class="text-slate-400 text-xs">
                    Tidak aktif
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="preview" class="mt-4 pt-3 border-t border-slate-100 dark:border-orbit-border/80 flex items-center justify-between">
          <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
            Total CF Kombinasi Final:
          </div>
          <div class="text-lg sm:text-xl font-black" :class="cfColor(preview.cf_kombinasi)">
            {{ preview.cf_persen }}
          </div>
        </div>
      </div>

      <!-- Actionable System Recommendation Card -->
      <div
        v-if="preview?.rekomendasi"
        class="p-5 rounded-2xl border transition-all"
        :class="{
          'bg-rose-50/80 dark:bg-rose-950/30 border-rose-200 dark:border-rose-900/40 text-rose-900 dark:text-rose-200': preview.status_stunting === 'stunting_berat',
          'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-900/40 text-amber-900 dark:text-amber-200': preview.status_stunting === 'stunting',
          'bg-yellow-50/80 dark:bg-yellow-950/30 border-yellow-200 dark:border-yellow-900/40 text-yellow-900 dark:text-yellow-200': preview.status_stunting === 'berisiko',
          'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-900/40 text-emerald-900 dark:text-emerald-200': preview.status_stunting === 'normal',
        }"
      >
        <div class="flex items-center gap-2 mb-1.5 font-bold text-sm">
          <Lightbulb class="w-4 h-4" />
          <span>Rekomendasi Tindakan Klinis:</span>
        </div>
        <p class="text-xs sm:text-sm leading-relaxed opacity-95">
          {{ preview.rekomendasi }}
        </p>
      </div>

      <!-- Bottom Action Bar -->
      <div class="flex items-center justify-between pt-2">
        <Link
          :href="route('kader.kunjungan.index')"
          class="px-4 py-2.5 text-xs sm:text-sm font-medium rounded-xl border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
        >
          Batalkan
        </Link>

        <button
          @click="simpanDiagnosis"
          :disabled="simpanLoading"
          class="inline-flex items-center gap-2 px-6 py-2.5 bg-orbit-primary hover:bg-orbit-primary/90 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-purple-600/25 transition-all active:scale-[0.98] disabled:opacity-50"
        >
          <Loader2 v-if="simpanLoading" class="w-4 h-4 animate-spin" />
          <Check v-else class="w-4 h-4" />
          <span>{{ simpanLoading ? 'Menyimpan Hasil...' : 'Simpan & Kirim ke Bidan' }}</span>
        </button>
      </div>

    </div>
  </KaderLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import {
  Baby,
  Calculator,
  Cpu,
  Stethoscope,
  AlertTriangle,
  CheckCircle2,
  Check,
  Lightbulb,
  Loader2,
} from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({
  kunjungan: Object,
  balita: Object,
  gejala_manual: Array,
  semua_gejala: Array,
  gejala_terdeteksi: Object,
})

const gejalaManu = ref({})
const preview = ref(null)
const loading = ref(false)
const simpanLoading = ref(false)

const gejalOtomatis = computed(() =>
  props.semua_gejala?.filter(g => g.sumber === 'otomatis') ?? []
)

const toggleGejala = async (id) => {
  gejalaManu.value[id] = !gejalaManu.value[id]
  await fetchPreview()
}

const fetchPreview = async () => {
  loading.value = true
  try {
    const res = await axios.post(
      route('kader.diagnosis.preview', props.kunjungan.id),
      { gejala: gejalaManu.value }
    )
    preview.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const simpanDiagnosis = () => {
  simpanLoading.value = true
  router.post(route('kader.diagnosis.store', props.kunjungan.id), {
    gejala: gejalaManu.value
  })
}

// Initial preview load
fetchPreview()

// Helpers
const zscoreColor = (z) => {
  if (z == null) return 'text-slate-400'
  if (z < -2) return 'text-rose-600 dark:text-rose-400 font-bold'
  if (z < -1) return 'text-amber-600 dark:text-amber-400 font-semibold'
  return 'text-emerald-600 dark:text-emerald-400'
}

const cfColor = (cf) => {
  if (cf >= 0.7) return 'text-rose-600 dark:text-rose-400'
  if (cf >= 0.4) return 'text-amber-600 dark:text-amber-400'
  return 'text-emerald-600 dark:text-emerald-400'
}

const cfBarColor = (cf) => {
  if (cf >= 0.7) return 'bg-rose-500'
  if (cf >= 0.4) return 'bg-amber-500'
  return 'bg-emerald-500'
}
</script>