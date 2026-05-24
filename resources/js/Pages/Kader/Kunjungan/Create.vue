<template>
  <KaderLayout title="Input Kunjungan">
    <div class="max-w-2xl mx-auto">

      <!-- Pilih balita (kalau belum dipilih) -->
      <div v-if="!selectedBalita && daftar_balita" class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
        <div class="text-sm font-medium text-gray-700 mb-3">Pilih balita</div>
        <input
          v-model="cariBalita"
          type="text"
          placeholder="Cari nama balita..."
          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-emerald-300"
        >
        <div class="space-y-1 max-h-60 overflow-y-auto">
          <button
            v-for="b in filteredBalita" :key="b.id"
            @click="selectedBalita = b"
            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-emerald-50 text-left transition-colors"
          >
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-sm">👶</div>
            <div>
              <div class="text-sm font-medium text-gray-800">{{ b.nama }}</div>
              <div class="text-xs text-gray-400">{{ b.usia_format }}</div>
            </div>
          </button>
        </div>
      </div>

      <!-- Info balita terpilih -->
      <div v-if="currentBalita" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-emerald-200 flex items-center justify-center text-lg">👶</div>
          <div>
            <div class="font-medium text-gray-800">{{ currentBalita.nama }}</div>
            <div class="text-sm text-emerald-700">{{ currentBalita.usia_format }} · {{ currentBalita.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
          </div>
        </div>
        <button v-if="daftar_balita" @click="selectedBalita = null" class="text-xs text-gray-400 hover:text-gray-600">Ganti</button>
      </div>

      <!-- Warning kalau sudah kunjungan bulan ini -->
      <div v-if="sudah_kunjungan_bulan_ini" class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
        <div class="text-sm text-amber-700">⚠️ Balita ini sudah memiliki kunjungan bulan ini. Apakah ingin melanjutkan?</div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit">
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
          <div class="text-sm font-medium text-gray-700 mb-4">Tanggal kunjungan</div>
          <input
            v-model="form.tanggal_kunjungan"
            type="date"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
            required
          >
          <div v-if="errors.tanggal_kunjungan" class="text-xs text-red-500 mt-1">{{ errors.tanggal_kunjungan }}</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
          <div class="text-sm font-medium text-gray-700 mb-4">Data antropometri</div>
          <div class="grid grid-cols-2 gap-4">
            <FormField
              v-model="form.berat_badan"
              label="Berat badan (kg)"
              type="number"
              step="0.1"
              placeholder="cth. 8.5"
              :error="errors.berat_badan"
              required
            />
            <FormField
              v-model="form.tinggi_badan"
              label="Tinggi badan (cm)"
              type="number"
              step="0.1"
              placeholder="cth. 72.0"
              :error="errors.tinggi_badan"
              required
            />
            <FormField
              v-model="form.lila"
              label="LILA (cm)"
              type="number"
              step="0.1"
              placeholder="cth. 13.5"
              :error="errors.lila"
            />
            <FormField
              v-model="form.lingkar_kepala"
              label="Lingkar kepala (cm)"
              type="number"
              step="0.1"
              placeholder="cth. 44.0"
              :error="errors.lingkar_kepala"
            />
          </div>

          <!-- Preview kunjungan terakhir -->
          <div v-if="kunjungan_terakhir" class="mt-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-500">
            <div class="font-medium mb-1">Kunjungan terakhir ({{ kunjungan_terakhir.tanggal }})</div>
            <div>BB: {{ kunjungan_terakhir.berat_badan }} kg · TB: {{ kunjungan_terakhir.tinggi_badan }} cm</div>
            <div v-if="form.berat_badan && kunjungan_terakhir.berat_badan" class="mt-1">
              <span
                :class="deltaBB >= 0 ? 'text-green-600' : 'text-red-500'"
                class="font-medium"
              >
                {{ deltaBB >= 0 ? '↑' : '↓' }} {{ Math.abs(deltaBB).toFixed(1) }} kg dari kunjungan terakhir
              </span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
          <label class="text-sm font-medium text-gray-700 mb-2 block">Catatan (opsional)</label>
          <textarea
            v-model="form.catatan"
            rows="3"
            placeholder="Catatan kondisi balita saat kunjungan..."
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-emerald-300"
          ></textarea>
        </div>

        <div class="flex gap-3 justify-end">
          <Link :href="route('kader.kunjungan.index')" class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing || !currentBalita"
            class="px-5 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            {{ form.processing ? 'Menyimpan...' : 'Simpan & Lanjut Diagnosis →' }}
          </button>
        </div>
      </form>
    </div>
  </KaderLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
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
    b.nama.toLowerCase().includes(cariBalita.value.toLowerCase())
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