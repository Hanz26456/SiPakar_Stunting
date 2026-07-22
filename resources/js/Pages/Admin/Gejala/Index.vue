<template>
  <AdminLayout title="Kelola Gejala Stunting">
    <div class="space-y-4">

      <!-- Info box -->
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-700 flex items-start gap-2">
        <span class="flex-shrink-0 text-base">ℹ️</span>
        <div>
          Gejala dengan sumber <strong>Otomatis</strong> dideteksi sistem dari data kunjungan tanpa input manual kader.
          Gejala <strong>Manual</strong> diceklis kader saat pemeriksaan langsung.
        </div>
      </div>

      <!-- Header + Tambah -->
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">{{ gejala.length }} gejala terdaftar</div>
        <button
          @click="showModal = true"
          class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 flex items-center gap-1.5"
        >
          + Tambah Gejala
        </button>
      </div>

      <!-- Filter kategori -->
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="kat in ['semua', 'antropometri', 'pertumbuhan', 'klinis', 'riwayat_gizi', 'riwayat_penyakit']"
          :key="kat"
          @click="filterKat = kat"
          :class="[
            'px-3 py-1.5 text-xs rounded-full font-medium transition-colors',
            filterKat === kat
              ? 'bg-gray-900 text-white'
              : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
          ]"
        >
          {{ katLabel(kat) }}
          <span class="ml-1 opacity-70">{{ jumlahKat(kat) }}</span>
        </button>
      </div>

      <!-- Daftar gejala -->
      <div class="space-y-2">
        <div
          v-for="g in gejalaTerfilter" :key="g.id"
          class="bg-white rounded-xl border overflow-hidden"
          :class="g.is_active ? 'border-gray-200' : 'border-gray-100 opacity-50'"
        >
          <div class="flex items-center gap-3 p-4">

            <!-- Kode -->
            <div class="w-14 text-xs font-mono font-bold text-gray-400 flex-shrink-0">
              {{ g.kode }}
            </div>

            <!-- Nama + kategori -->
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-gray-800 mb-0.5">{{ g.nama_gejala }}</div>
              <div class="flex items-center gap-2">
                <span class="text-xs px-2 py-0.5 rounded-full" :class="katColor(g.kategori)">
                  {{ katLabel(g.kategori) }}
                </span>
                <span
                  class="text-xs px-2 py-0.5 rounded-full font-medium"
                  :class="g.sumber === 'otomatis'
                    ? 'bg-blue-50 text-blue-700'
                    : 'bg-orange-50 text-orange-700'"
                >
                  {{ g.sumber === 'otomatis' ? '🤖 Otomatis' : '👁️ Manual' }}
                </span>
              </div>
            </div>

            <!-- Detail deteksi otomatis -->
            <div v-if="g.sumber === 'otomatis'" class="text-xs text-gray-400 font-mono flex-shrink-0 text-right">
              <div>{{ g.kolom_sumber }}</div>
              <div>{{ g.operator }} {{ g.nilai_threshold }}</div>
            </div>

            <!-- Jumlah rule -->
            <div class="text-center flex-shrink-0 w-16">
              <div class="text-lg font-semibold text-gray-700">{{ g.jumlah_rule }}</div>
              <div class="text-xs text-gray-400">rule</div>
            </div>

            <!-- Status + aksi -->
            <div class="flex items-center gap-2 flex-shrink-0">
              <span
                class="text-xs px-2 py-0.5 rounded-full font-medium"
                :class="g.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'"
              >
                {{ g.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
              <button
                @click="editGejala(g)"
                class="text-xs text-blue-600 hover:underline px-1"
              >
                Edit
              </button>
              <button
                v-if="g.jumlah_rule === 0"
                @click="hapusGejala(g)"
                class="text-xs text-red-500 hover:underline px-1"
              >
                Hapus
              </button>
            </div>
          </div>

          <!-- Deskripsi -->
          <div v-if="g.deskripsi" class="px-4 pb-3 text-xs text-gray-400 border-t border-gray-50 pt-2">
            {{ g.deskripsi }}
          </div>
        </div>
      </div>

    </div>

    <!-- ===== MODAL TAMBAH/EDIT ===== -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-5 max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <div class="text-base font-semibold text-gray-800">
            {{ editingGejala ? 'Edit Gejala' : 'Tambah Gejala' }}
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
        </div>

        <form @submit.prevent="submitGejala" class="space-y-3">

          <!-- Kode (hanya tambah) -->
          <div v-if="!editingGejala">
            <label class="block text-xs font-medium text-gray-600 mb-1">Kode Gejala <span class="text-red-400">*</span></label>
            <input
              v-model="form.kode"
              type="text"
              placeholder="cth. G18"
              maxlength="10"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 font-mono"
            >
            <div v-if="form.errors.kode" class="text-xs text-red-500 mt-1">{{ form.errors.kode }}</div>
          </div>

          <!-- Nama gejala -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Gejala <span class="text-red-400">*</span></label>
            <input
              v-model="form.nama_gejala"
              type="text"
              placeholder="cth. Pertumbuhan gigi melambat"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
            <div v-if="form.errors.nama_gejala" class="text-xs text-red-500 mt-1">{{ form.errors.nama_gejala }}</div>
          </div>

          <!-- Kategori -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Kategori <span class="text-red-400">*</span></label>
            <select
              v-model="form.kategori"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              <option value="">— Pilih kategori —</option>
              <option value="antropometri">Antropometri</option>
              <option value="pertumbuhan">Pertumbuhan</option>
              <option value="klinis">Klinis</option>
              <option value="riwayat_gizi">Riwayat Gizi</option>
              <option value="riwayat_penyakit">Riwayat Penyakit</option>
            </select>
          </div>

          <!-- Sumber (hanya tambah) -->
          <div v-if="!editingGejala">
            <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Deteksi <span class="text-red-400">*</span></label>
            <div class="flex gap-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.sumber" value="manual" class="accent-gray-700">
                <span class="text-sm">👁️ Manual (kader centang)</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.sumber" value="otomatis" class="accent-gray-700">
                <span class="text-sm">🤖 Otomatis (dari data)</span>
              </label>
            </div>
          </div>

          <!-- Konfigurasi otomatis -->
          <div v-if="form.sumber === 'otomatis' && !editingGejala" class="bg-blue-50 rounded-lg p-3 space-y-3">
            <div class="text-xs font-medium text-blue-700 mb-1">Konfigurasi Deteksi Otomatis</div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">Kolom sumber data</label>
              <select
                v-model="form.kolom_sumber"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white"
              >
                <option value="">— Pilih kolom —</option>
                <option value="zscore_tbu">zscore_tbu (Z-score TB/U)</option>
                <option value="zscore_bbu">zscore_bbu (Z-score BB/U)</option>
                <option value="lila">lila (Lingkar Lengan Atas)</option>
                <option value="berat_badan">berat_badan (tren BB)</option>
                <option value="asi_eksklusif">asi_eksklusif (riwayat)</option>
                <option value="mpasi_sesuai_usia">mpasi_sesuai_usia (riwayat)</option>
                <option value="infeksi_berulang">infeksi_berulang (riwayat)</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-xs text-gray-600 mb-1">Operator</label>
                <select
                  v-model="form.operator"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none bg-white"
                >
                  <option value="<">Kurang dari (&lt;)</option>
                  <option value="<=">Kurang dari atau sama (≤)</option>
                  <option value=">">Lebih dari (&gt;)</option>
                  <option value=">=">Lebih dari atau sama (≥)</option>
                  <option value="=">Sama dengan (=)</option>
                  <option value="trend_flat">Tren tidak naik</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Nilai threshold</label>
                <input
                  v-model="form.nilai_threshold"
                  type="number"
                  step="0.1"
                  placeholder="cth. -2.0"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none bg-white"
                >
              </div>
            </div>
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi (opsional)</label>
            <textarea
              v-model="form.deskripsi"
              rows="2"
              placeholder="Penjelasan singkat gejala ini..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-gray-300"
            ></textarea>
          </div>

          <!-- Tombol -->
          <div class="flex gap-2 pt-1">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 font-medium"
            >
              {{ form.processing ? 'Menyimpan...' : (editingGejala ? 'Simpan' : 'Tambah') }}
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
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ gejala: Array })

const showModal    = ref(false)
const editingGejala = ref(null)
const filterKat    = ref('semua')

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

const editGejala = (g) => {
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
  antropometri:     'bg-blue-50 text-blue-700',
  pertumbuhan:      'bg-green-50 text-green-700',
  klinis:           'bg-purple-50 text-purple-700',
  riwayat_gizi:     'bg-amber-50 text-amber-700',
  riwayat_penyakit: 'bg-red-50 text-red-700',
}[kat] ?? 'bg-gray-100 text-gray-600')
</script>