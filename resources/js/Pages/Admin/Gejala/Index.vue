<template>
  <AdminLayout title="Kelola Gejala Stunting">
    <div class="space-y-5">

      <!-- Metis Info Box -->
      <div class="bg-blue-50/80 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4 text-xs sm:text-sm text-blue-900 dark:text-blue-200 flex items-start gap-3 shadow-2xs">
        <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
        <div class="leading-relaxed">
          Gejala dengan sumber <strong>Otomatis</strong> dievaluasi langsung oleh sistem inferensi Certainty Factor dari data pengukuran antropometri WHO. Gejala <strong>Manual</strong> diobservasi dan diceklis oleh kader saat penimbangan di posyandu.
        </div>
      </div>

      <!-- Header & Action Button -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 font-medium">
          Total <strong class="text-zinc-900 dark:text-zinc-100">{{ gejala.length }}</strong> parameter gejala terdaftar
        </div>
        <button
          @click="openCreate"
          type="button"
          class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-xs transition-all active:scale-[0.98] flex-shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Gejala Baru</span>
        </button>
      </div>

      <!-- Metis Filter Pills -->
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="kat in ['semua', 'antropometri', 'pertumbuhan', 'klinis', 'riwayat_gizi', 'riwayat_penyakit']"
          :key="kat"
          type="button"
          @click="filterKat = kat"
          :class="[
            'px-3 py-1.5 text-xs rounded-lg font-medium transition-all shadow-2xs',
            filterKat === kat
              ? 'bg-blue-600 text-white font-semibold'
              : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800'
          ]"
        >
          {{ katLabel(kat) }}
          <span class="ml-1 opacity-75">({{ jumlahKat(kat) }})</span>
        </button>
      </div>

      <!-- Daftar Gejala Cards -->
      <div class="space-y-3">
        <div
          v-for="g in gejalaTerfilter" :key="g.id"
          class="bg-white dark:bg-zinc-900 rounded-xl border transition-all shadow-2xs"
          :class="g.is_active ? 'border-zinc-200 dark:border-zinc-800' : 'border-zinc-200 dark:border-zinc-800 opacity-60'"
        >
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 sm:p-5">

            <!-- Kode & Identitas -->
            <div class="flex items-start sm:items-center gap-3.5 flex-1 min-w-0">
              <div class="px-2.5 py-1 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 text-xs font-mono font-bold border border-blue-200 dark:border-blue-900/50 flex-shrink-0">
                {{ g.kode }}
              </div>

              <div class="flex-1 min-w-0">
                <div class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                  {{ g.nama_gejala }}
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[11px] px-2 py-0.5 rounded-full font-medium" :class="katColor(g.kategori)">
                    {{ katLabel(g.kategori) }}
                  </span>
                  <span
                    class="text-[11px] px-2 py-0.5 rounded-full font-semibold inline-flex items-center gap-1"
                    :class="g.sumber === 'otomatis'
                      ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/40'
                      : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/40'"
                  >
                    <component :is="g.sumber === 'otomatis' ? Cpu : Eye" class="w-3 h-3" />
                    <span>{{ g.sumber === 'otomatis' ? 'Deteksi Otomatis' : 'Input Manual Kader' }}</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Detail deteksi otomatis -->
            <div v-if="g.sumber === 'otomatis'" class="text-xs text-zinc-500 dark:text-zinc-400 font-mono text-left sm:text-right px-1">
              <span class="text-zinc-400 block text-[10px] uppercase font-sans font-medium">Kondisi Algoritma</span>
              <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ g.kolom_sumber }}</span> {{ g.operator }} {{ g.nilai_threshold }}
            </div>

            <!-- Jumlah rule relasi -->
            <div class="text-left sm:text-center flex-shrink-0 w-20 px-1 border-t sm:border-t-0 sm:border-l border-zinc-100 dark:border-zinc-800 pt-2 sm:pt-0">
              <div class="text-base sm:text-lg font-bold font-mono text-zinc-900 dark:text-zinc-100">{{ g.jumlah_rule }}</div>
              <div class="text-[11px] text-zinc-400">Rule Terkait</div>
            </div>

            <!-- Status + Aksi -->
            <div class="flex items-center gap-2 flex-shrink-0 pt-1 sm:pt-0">
              <span
                class="text-xs px-2.5 py-0.5 rounded-full font-semibold inline-flex items-center gap-1"
                :class="g.is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500'"
              >
                {{ g.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>

              <button
                @click="editGejala(g)"
                type="button"
                class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors"
              >
                Edit
              </button>

              <button
                v-if="g.jumlah_rule === 0"
                @click="hapusGejala(g)"
                type="button"
                class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors"
              >
                Hapus
              </button>
            </div>
          </div>

          <!-- Deskripsi Gejala -->
          <div v-if="g.deskripsi" class="px-5 pb-3.5 text-xs text-zinc-500 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800/80 pt-2.5">
            {{ g.deskripsi }}
          </div>
        </div>
      </div>

    </div>

    <!-- ===== METIS MODAL TAMBAH/EDIT ===== -->
    <div v-if="showModal" @click.self="closeModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
          <div class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-100">
            {{ editingGejala ? 'Edit Data Gejala' : 'Tambah Gejala Baru' }}
          </div>
          <button @click="closeModal" type="button" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitGejala" class="space-y-4">
          <!-- Kode (hanya saat tambah) -->
          <div v-if="!editingGejala">
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Kode Gejala <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.kode"
              type="text"
              placeholder="Contoh: G-11"
              maxlength="10"
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-2xs"
            >
            <div v-if="form.errors.kode" class="text-xs text-rose-500 mt-1">{{ form.errors.kode }}</div>
          </div>

          <!-- Nama gejala -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Nama / Deskripsi Klinis Gejala <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.nama_gejala"
              type="text"
              placeholder="Contoh: Pertumbuhan gigi balita terlambat"
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-2xs"
            >
            <div v-if="form.errors.nama_gejala" class="text-xs text-rose-500 mt-1">{{ form.errors.nama_gejala }}</div>
          </div>

          <!-- Kategori -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Kategori Gejala <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.kategori"
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-2xs"
            >
              <option value="">— Pilih kategori —</option>
              <option value="antropometri">Antropometri (Ukuran Tubuh)</option>
              <option value="pertumbuhan">Pertumbuhan (Tren Berkala)</option>
              <option value="klinis">Klinis (Fisik / Observasi)</option>
              <option value="riwayat_gizi">Riwayat Gizi (Pola Asuh)</option>
              <option value="riwayat_penyakit">Riwayat Penyakit Infeksi</option>
            </select>
          </div>

          <!-- Sumber (hanya saat tambah) -->
          <div v-if="!editingGejala">
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Sumber Deteksi Gejala <span class="text-rose-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
              <label
                class="flex items-center gap-2.5 p-3 rounded-lg border cursor-pointer transition-colors shadow-2xs"
                :class="form.sumber === 'manual' ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/30' : 'border-zinc-200 dark:border-zinc-800'"
              >
                <input type="radio" v-model="form.sumber" value="manual" class="accent-blue-600">
                <div class="text-xs">
                  <span class="font-bold block text-zinc-900 dark:text-zinc-100">Manual</span>
                  <span class="text-zinc-500 text-[11px]">Diceklis kader</span>
                </div>
              </label>
              <label
                class="flex items-center gap-2.5 p-3 rounded-lg border cursor-pointer transition-colors shadow-2xs"
                :class="form.sumber === 'otomatis' ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/30' : 'border-zinc-200 dark:border-zinc-800'"
              >
                <input type="radio" v-model="form.sumber" value="otomatis" class="accent-blue-600">
                <div class="text-xs">
                  <span class="font-bold block text-zinc-900 dark:text-zinc-100">Otomatis</span>
                  <span class="text-zinc-500 text-[11px]">Dihitung sistem</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Konfigurasi otomatis -->
          <div v-if="form.sumber === 'otomatis' && !editingGejala" class="bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 rounded-lg p-3.5 space-y-3">
            <div class="text-xs font-bold text-blue-900 dark:text-blue-300">Konfigurasi Nilai Ambang (Threshold)</div>
            <div>
              <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Kolom Basis Data</label>
              <select
                v-model="form.kolom_sumber"
                class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none"
              >
                <option value="">— Pilih kolom sumber data —</option>
                <option value="zscore_tbu">zscore_tbu (Z-Score TB/U)</option>
                <option value="zscore_bbu">zscore_bbu (Z-Score BB/U)</option>
                <option value="lila">lila (Lingkar Lengan Atas)</option>
                <option value="berat_badan">berat_badan (Tren Kenaikan BB)</option>
                <option value="asi_eksklusif">asi_eksklusif (Riwayat Gizi)</option>
                <option value="mpasi_sesuai_usia">mpasi_sesuai_usia (Riwayat MPASI)</option>
                <option value="infeksi_berulang">infeksi_berulang (Riwayat Sakit)</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
              <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Operator Logika</label>
                <select
                  v-model="form.operator"
                  class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none"
                >
                  <option value="<">Kurang dari (&lt;)</option>
                  <option value="<=">Kurang dari sama (≤)</option>
                  <option value=">">Lebih dari (&gt;)</option>
                  <option value=">=">Lebih dari sama (≥)</option>
                  <option value="=">Sama dengan (=)</option>
                  <option value="trend_flat">Tren tidak naik</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Nilai Ambang</label>
                <input
                  v-model="form.nilai_threshold"
                  type="number"
                  step="0.1"
                  placeholder="Contoh: -2.0"
                  class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none"
                >
              </div>
            </div>
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Penjelasan Tambahan (Opsional)</label>
            <textarea
              v-model="form.deskripsi"
              rows="2"
              placeholder="Catatan justifikasi atau penjelasan klinis gejala..."
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors resize-none shadow-2xs"
            />
          </div>

          <!-- Tombol Aksi Modal -->
          <div class="flex items-center gap-2.5 pt-3 border-t border-zinc-100 dark:border-zinc-800">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors shadow-2xs"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-all active:scale-[0.98] disabled:opacity-50"
            >
              {{ form.processing ? 'Menyimpan...' : (editingGejala ? 'Simpan Perubahan' : 'Tambah Gejala') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { Info, Plus, Cpu, Eye, X } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ gejala: Array })

const showModal     = ref(false)
const editingGejala = ref(null)
const filterKat     = ref('semua')

const form = useForm({
  kode:            '',
  nama_gejala:     '',
  kategori:        '',
  deskripsi:       '',
  sumber:          'manual',
  kolom_sumber:    '',
  operator:        '<',
  nilai_threshold: '',
})

const gejalaTerfilter = computed(() => {
  if (filterKat.value === 'semua') return props.gejala
  return props.gejala.filter(g => g.kategori === filterKat.value)
})

const jumlahKat = (kat) => {
  if (kat === 'semua') return props.gejala.length
  return props.gejala.filter(g => g.kategori === kat).length
}

const openCreate = () => {
  editingGejala.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

const editGejala = (g) => {
  form.clearErrors()
  editingGejala.value = g
  form.nama_gejala    = g.nama_gejala
  form.kategori       = g.kategori
  form.deskripsi      = g.deskripsi ?? ''
  form.nilai_threshold = g.nilai_threshold ?? ''
  showModal.value     = true
}

const closeModal = () => {
  showModal.value     = false
  editingGejala.value = null
  form.reset()
  form.clearErrors()
}

const submitGejala = () => {
  if (editingGejala.value) {
    form.put(route('admin.gejala.update', editingGejala.value.id), {
      onSuccess: closeModal
    })
  } else {
    form.post(route('admin.gejala.store'), {
      onSuccess: closeModal
    })
  }
}

const hapusGejala = (g) => {
  if (confirm(`Hapus gejala ${g.kode} — ${g.nama_gejala}?`)) {
    router.delete(route('admin.gejala.destroy', g.id))
  }
}

const katLabel = (kat) => ({
  semua:            'Semua',
  antropometri:     'Antropometri',
  pertumbuhan:      'Pertumbuhan',
  klinis:           'Klinis',
  riwayat_gizi:     'Riwayat Gizi',
  riwayat_penyakit: 'Riwayat Penyakit',
}[kat] ?? kat)

const katColor = (kat) => ({
  antropometri:     'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/40',
  pertumbuhan:      'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40',
  klinis:           'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/40',
  riwayat_gizi:     'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/40',
  riwayat_penyakit: 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/40',
}[kat] ?? 'bg-zinc-100 text-zinc-600')
</script>
