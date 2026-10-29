<template>
  <KaderLayout title="Input Kunjungan Posyandu">
    <div class="max-w-2xl mx-auto space-y-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">
            Langkah 2: Penimbangan & Pengukuran
          </div>
          <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">
            Pencatatan Kunjungan Bulanan
          </h1>
        </div>

        <Link
          :href="route('kader.kunjungan.index')"
          class="px-3.5 py-1.5 text-xs font-medium rounded-xl border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
        >
          Kembali
        </Link>
      </div>

      <!-- Pilih Balita (jika belum terpilih dari halaman detail) -->
      <div v-if="!selectedBalita && daftar_balita" class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
          Pilih Balita yang Hadir
        </label>
        <div class="relative mb-3">
          <Search class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
          <input
            v-model="cariBalita"
            type="text"
            placeholder="Ketik nama balita atau nama ibu..."
            class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl pl-9.5 pr-3 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
          >
        </div>

        <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1 divide-y divide-slate-100 dark:divide-orbit-border/50">
          <button
            v-for="b in filteredBalita"
            :key="b.id"
            type="button"
            @click="selectedBalita = b"
            class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-purple-50/70 dark:hover:bg-purple-950/20 text-left transition-colors group"
          >
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                <Baby class="w-5 h-5" />
              </div>
              <div>
                <div class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                  {{ b.nama }}
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                  {{ b.usia_format }} · Ibu: {{ b.nama_ibu }}
                </div>
              </div>
            </div>
            <span class="text-xs text-purple-600 dark:text-purple-400 font-medium group-hover:translate-x-0.5 transition-transform">
              Pilih →
            </span>
          </button>
        </div>
      </div>

      <!-- Info Balita Terpilih -->
      <div v-if="currentBalita" class="bg-purple-50/80 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900/40 rounded-2xl p-4 shadow-2xs flex items-center justify-between">
        <div class="flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-2xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
            <Baby class="w-6 h-6" />
          </div>
          <div>
            <div class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
              {{ currentBalita.nama }}
            </div>
            <div class="text-xs text-purple-700 dark:text-purple-300 font-medium mt-0.5">
              {{ currentBalita.usia_format }} · {{ currentBalita.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
            </div>
          </div>
        </div>

        <button
          v-if="daftar_balita"
          type="button"
          @click="selectedBalita = null"
          class="px-3 py-1.5 rounded-lg border border-purple-200 dark:border-purple-800 text-xs font-semibold text-purple-700 dark:text-purple-300 hover:bg-white dark:hover:bg-purple-900/40 transition-colors"
        >
          Ganti Balita
        </button>
      </div>

      <!-- Alert Jika Sudah Pernah Kunjungan Bulan Ini -->
      <div v-if="sudah_kunjungan_bulan_ini" class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 rounded-2xl p-4 flex items-center gap-3 text-amber-800 dark:text-amber-200 text-xs sm:text-sm">
        <AlertTriangle class="w-5 h-5 text-amber-500 flex-shrink-0" />
        <div>
          <span class="font-bold">Perhatian:</span> Balita ini sudah tercatat memiliki data kunjungan di bulan berjalan. Anda tetap dapat melanjutkan bila ini adalah pengukuran ulang.
        </div>
      </div>

      <!-- Main Form -->
      <form @submit.prevent="submit" class="space-y-5">
        <!-- Tanggal Kunjungan -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
            Tanggal Kunjungan / Pengukuran <span class="text-rose-500">*</span>
          </label>
          <input
            v-model="form.tanggal_kunjungan"
            type="date"
            class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all"
            required
          >
          <div v-if="errors.tanggal_kunjungan" class="text-xs text-rose-500 mt-1">
            {{ errors.tanggal_kunjungan }}
          </div>
        </div>

        <!-- Data Antropometri -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs space-y-4">
          <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
              Data Hasil Pengukuran Fisik (Antropometri)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Gunakan alat ukur terstandar posyandu (timbangan digital & infantometer / stadiometer).
            </p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField
              v-model="form.berat_badan"
              label="Berat Badan (kg)"
              type="number"
              step="0.01"
              placeholder="Contoh: 8.5"
              :error="errors.berat_badan"
              required
            />

            <FormField
              v-model="form.tinggi_badan"
              label="Tinggi / Panjang Badan (cm)"
              type="number"
              step="0.1"
              placeholder="Contoh: 72.5"
              :error="errors.tinggi_badan"
              required
            />

            <FormField
              v-model="form.lila"
              label="Lingkar Lengan Atas / LiLA (cm)"
              type="number"
              step="0.1"
              placeholder="Contoh: 13.5"
              :error="errors.lila"
            />

            <FormField
              v-model="form.lingkar_kepala"
              label="Lingkar Kepala (cm)"
              type="number"
              step="0.1"
              placeholder="Contoh: 44.0"
              :error="errors.lingkar_kepala"
            />
          </div>

          <!-- Delta Pertumbuhan dari Kunjungan Sebelumnya -->
          <div v-if="kunjungan_terakhir" class="p-3.5 bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl border border-slate-200/80 dark:border-orbit-border text-xs text-slate-600 dark:text-slate-300">
            <div class="font-semibold text-slate-800 dark:text-slate-200 mb-1">
              Perbandingan Kunjungan Terakhir ({{ kunjungan_terakhir.tanggal }}):
            </div>
            <div class="flex items-center gap-3">
              <span>BB Lalu: <strong>{{ kunjungan_terakhir.berat_badan }} kg</strong></span>
              <span>·</span>
              <span>TB Lalu: <strong>{{ kunjungan_terakhir.tinggi_badan }} cm</strong></span>
            </div>

            <div v-if="form.berat_badan && kunjungan_terakhir.berat_badan" class="mt-2 pt-2 border-t border-slate-200/60 dark:border-orbit-border flex items-center gap-1.5 font-semibold">
              <TrendingUp v-if="deltaBB >= 0" class="w-4 h-4 text-emerald-600" />
              <TrendingDown v-else class="w-4 h-4 text-rose-600" />
              <span :class="deltaBB >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                {{ deltaBB >= 0 ? 'Naik' : 'Turun' }} {{ Math.abs(deltaBB).toFixed(2) }} kg
              </span>
              <span class="text-slate-400 font-normal">dari penimbangan sebelumnya</span>
            </div>
          </div>
        </div>

        <!-- Catatan Tambahan -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
            Catatan Tambahan Kader (Opsional)
          </label>
          <textarea
            v-model="form.catatan"
            rows="3"
            placeholder="Tuliskan catatan kondisi khusus balita, misal: nafsu makan menurun, baru sembuh batuk pilek..."
            class="w-full bg-slate-50 dark:bg-orbit-surface2 border border-slate-200 dark:border-orbit-border rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 transition-all resize-none"
          />
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-between pt-2">
          <Link
            :href="route('kader.kunjungan.index')"
            class="px-4 py-2.5 text-xs sm:text-sm font-medium rounded-xl border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
          >
            Batal
          </Link>

          <button
            type="submit"
            :disabled="form.processing || !currentBalita"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-orbit-primary hover:bg-orbit-primary/90 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md shadow-purple-600/25 transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan & Lanjut ke Diagnosis CF' }}</span>
            <ArrowRight class="w-4 h-4" />
          </button>
        </div>
      </form>

    </div>
  </KaderLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import {
  Baby,
  Search,
  AlertTriangle,
  TrendingUp,
  TrendingDown,
  ArrowRight,
} from 'lucide-vue-next'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import FormField from '@/Components/FormField.vue'

const props = defineProps({
  balita: Object,
  daftar_balita: Array,
  kunjungan_terakhir: Object,
  sudah_kunjungan_bulan_ini: Boolean,
  tanggal_default: String,
})

const selectedBalita = ref(null)
const cariBalita = ref('')

const currentBalita = computed(() => props.balita || selectedBalita.value)

const filteredBalita = computed(() => {
  if (!props.daftar_balita) return []
  return props.daftar_balita.filter(b =>
    b.nama.toLowerCase().includes(cariBalita.value.toLowerCase()) ||
    b.nama_ibu?.toLowerCase().includes(cariBalita.value.toLowerCase())
  )
})

const form = useForm({
  balita_id: computed(() => currentBalita.value?.id),
  tanggal_kunjungan: props.tanggal_default,
  berat_badan: '',
  tinggi_badan: '',
  lila: '',
  lingkar_kepala: '',
  catatan: '',
})

const errors = computed(() => form.errors)

const deltaBB = computed(() => {
  if (!form.berat_badan || !props.kunjungan_terakhir?.berat_badan) return 0
  return parseFloat(form.berat_badan) - parseFloat(props.kunjungan_terakhir.berat_badan)
})

const submit = () => {
  form.balita_id = currentBalita.value?.id
  form.post(route('kader.kunjungan.store'))
}
</script>