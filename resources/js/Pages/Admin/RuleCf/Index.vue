<template>
  <AdminLayout title="Kelola Rule CF">
    <div class="space-y-4">

      <!-- Warning -->
      <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm text-amber-700 flex items-start gap-2">
        <span class="flex-shrink-0">⚠️</span>
        <div>
          Nilai MB dan MD ditetapkan dari hasil wawancara pakar (Bidan Marini Poeji).
          <strong>Perubahan rule langsung mempengaruhi hasil diagnosis</strong> seluruh balita.
        </div>
      </div>

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">{{ rules.length }} rule terdaftar</div>
        <button
          @click="showModal = true"
          class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 flex items-center gap-1.5"
        >
          + Tambah Rule
        </button>
      </div>

      <!-- Filter status diagnosa -->
      <div class="flex gap-2">
        <button
          v-for="s in ['semua', 'normal', 'berisiko', 'stunting', 'stunting_berat']"
          :key="s"
          @click="filterStatus = s"
          :class="[
            'px-3 py-1.5 text-xs rounded-full font-medium',
            filterStatus === s ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
          ]"
        >
          {{ statusLabel(s) }}
        </button>
      </div>

      <!-- Daftar rule -->
      <div class="space-y-2">
        <div
          v-for="r in ruleTerfilter" :key="r.id"
          class="bg-white rounded-xl border overflow-hidden"
          :class="r.is_active ? 'border-gray-200' : 'border-gray-100 opacity-50'"
        >
          <div class="flex items-center gap-3 p-4">

            <!-- Kode rule -->
            <div class="w-14 text-xs font-mono font-bold text-gray-400 flex-shrink-0">
              {{ r.kode_rule }}
            </div>

            <!-- Nama gejala -->
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-gray-800 truncate mb-0.5">
                {{ r.nama_gejala }}
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono">
                  {{ r.kode_gejala }}
                </span>
                <span class="text-xs text-gray-400">→</span>
                <span class="text-xs font-medium" :class="statusColor(r.status_diagnosa)">
                  {{ statusLabel(r.status_diagnosa) }}
                </span>
              </div>
            </div>

            <!-- Nilai CF -->
            <div class="flex items-center gap-5 flex-shrink-0">
              <div class="text-center">
                <div class="text-sm font-semibold text-blue-600">{{ r.mb }}</div>
                <div class="text-xs text-gray-400">MB</div>
              </div>
              <div class="text-gray-300 text-sm">−</div>
              <div class="text-center">
                <div class="text-sm font-semibold text-orange-500">{{ r.md }}</div>
                <div class="text-xs text-gray-400">MD</div>
              </div>
              <div class="text-gray-300 text-sm">=</div>
              <div class="text-center">
                <div class="text-lg font-bold" :class="cfColor(r.cf_pakar)">
                  {{ r.cf_pakar.toFixed(2) }}
                </div>
                <div class="text-xs text-gray-400">CF</div>
              </div>
            </div>

            <!-- CF Bar -->
            <div class="w-24 flex-shrink-0">
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all"
                  :class="cfBarColor(r.cf_pakar)"
                  :style="{ width: (r.cf_pakar * 100) + '%' }"
                ></div>
              </div>
              <div class="text-xs text-gray-400 text-center mt-0.5">
                {{ Math.round(r.cf_pakar * 100) }}%
              </div>
            </div>

            <!-- Status + aksi -->
            <div class="flex items-center gap-2 flex-shrink-0">
              <span
                class="text-xs px-2 py-0.5 rounded-full font-medium"
                :class="r.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'"
              >
                {{ r.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
              <button
                @click="editRule(r)"
                class="text-xs text-blue-600 hover:underline"
              >
                Edit
              </button>
              <button
                @click="toggleRule(r)"
                class="text-xs px-2 py-1 rounded border"
                :class="r.is_active
                  ? 'border-red-200 text-red-500 hover:bg-red-50'
                  : 'border-green-200 text-green-600 hover:bg-green-50'"
              >
                {{ r.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
              </button>
            </div>
          </div>

          <!-- Kondisi (kalau ada) -->
          <div v-if="r.kondisi" class="px-4 pb-3 text-xs text-gray-400 border-t border-gray-50 pt-2">
            Kondisi: {{ r.kondisi }}
          </div>
        </div>
      </div>

    </div>

    <!-- ===== MODAL TAMBAH/EDIT ===== -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-5">
        <div class="flex items-center justify-between mb-4">
          <div class="text-base font-semibold text-gray-800">
            {{ editingRule ? 'Edit Rule CF' : 'Tambah Rule CF' }}
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
        </div>

        <form @submit.prevent="submitRule" class="space-y-3">

          <!-- Kode rule (hanya tambah) -->
          <div v-if="!editingRule">
            <label class="block text-xs font-medium text-gray-600 mb-1">Kode Rule <span class="text-red-400">*</span></label>
            <input
              v-model="form.kode_rule"
              type="text"
              placeholder="cth. R-11"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
            <div v-if="form.errors.kode_rule" class="text-xs text-red-500 mt-1">{{ form.errors.kode_rule }}</div>
          </div>

          <!-- Gejala (hanya tambah) -->
          <div v-if="!editingRule">
            <label class="block text-xs font-medium text-gray-600 mb-1">Gejala <span class="text-red-400">*</span></label>
            <select
              v-model="form.gejala_id"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              <option value="">— Pilih gejala —</option>
              <option v-for="g in gejala" :key="g.id" :value="g.id">
                {{ g.kode }} — {{ g.nama_gejala }}
              </option>
            </select>
          </div>

          <!-- Status diagnosa (hanya tambah) -->
          <div v-if="!editingRule">
            <label class="block text-xs font-medium text-gray-600 mb-1">Status Diagnosa <span class="text-red-400">*</span></label>
            <select
              v-model="form.status_diagnosa"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              <option value="normal">Normal</option>
              <option value="berisiko">Berisiko</option>
              <option value="stunting">Stunting</option>
              <option value="stunting_berat">Stunting Berat</option>
            </select>
          </div>

          <!-- MB dan MD -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-blue-600 mb-1">
                MB (Measure of Belief) <span class="text-red-400">*</span>
              </label>
              <input
                v-model="form.mb"
                type="number"
                step="0.05"
                min="0"
                max="1"
                placeholder="0.0 – 1.0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
              >
            </div>
            <div>
              <label class="block text-xs font-medium text-orange-500 mb-1">
                MD (Measure of Disbelief) <span class="text-red-400">*</span>
              </label>
              <input
                v-model="form.md"
                type="number"
                step="0.05"
                min="0"
                max="1"
                placeholder="0.0 – 1.0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
              >
            </div>
          </div>

          <!-- Preview CF -->
          <div v-if="form.mb !== '' && form.md !== ''" class="bg-gray-50 rounded-lg p-3 text-center">
            <div class="text-xs text-gray-400 mb-1">CF Pakar = MB − MD</div>
            <div
              class="text-2xl font-bold"
              :class="cfColor(parseFloat(form.mb || 0) - parseFloat(form.md || 0))"
            >
              {{ (parseFloat(form.mb || 0) - parseFloat(form.md || 0)).toFixed(2) }}
            </div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-2">
              <div
                class="h-full rounded-full transition-all"
                :class="cfBarColor(parseFloat(form.mb || 0) - parseFloat(form.md || 0))"
                :style="{ width: ((parseFloat(form.mb || 0) - parseFloat(form.md || 0)) * 100) + '%' }"
              ></div>
            </div>
          </div>

          <div v-if="form.errors.mb" class="text-xs text-red-500">{{ form.errors.mb }}</div>

          <!-- Kondisi -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi (opsional)</label>
            <input
              v-model="form.kondisi"
              type="text"
              placeholder="Deskripsi kondisi berlakunya rule..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
          </div>

          <div class="flex gap-2 pt-1">
            <button type="button" @click="closeModal"
              class="flex-1 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
              Batal
            </button>
            <button type="submit" :disabled="form.processing"
              class="flex-1 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 font-medium">
              {{ form.processing ? 'Menyimpan...' : (editingRule ? 'Simpan' : 'Tambah') }}
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

const props = defineProps({ rules: Array, gejala: Array })

const showModal   = ref(false)
const editingRule = ref(null)
const filterStatus = ref('semua')

const form = useForm({
  kode_rule:       '',
  gejala_id:       '',
  status_diagnosa: 'berisiko',
  mb:              '',
  md:              '',
  kondisi:         '',
})

const ruleTerfilter = computed(() => {
  if (filterStatus.value === 'semua') return props.rules
  return props.rules.filter(r => r.status_diagnosa === filterStatus.value)
})

const editRule = (r) => {
  editingRule.value = r
  form.mb      = r.mb
  form.md      = r.md
  form.kondisi = r.kondisi ?? ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value   = false
  editingRule.value = null
  form.reset()
}

const submitRule = () => {
  if (editingRule.value) {
    form.put(route('admin.rule-cf.update', editingRule.value.id), { onSuccess: closeModal })
  } else {
    form.post(route('admin.rule-cf.store'), { onSuccess: closeModal })
  }
}

const toggleRule = (r) => {
  router.patch(route('admin.rule-cf.toggle', r.id))
}

const statusLabel  = (s) => ({ semua: 'Semua', normal: 'Normal', berisiko: 'Berisiko', stunting: 'Stunting', stunting_berat: 'Stunting Berat' }[s] ?? s)
const statusColor  = (s) => ({ stunting_berat: 'text-red-700', stunting: 'text-red-500', berisiko: 'text-amber-600', normal: 'text-green-600' }[s] ?? 'text-gray-500')
const cfColor      = (cf) => cf >= 0.7 ? 'text-red-600' : cf >= 0.4 ? 'text-amber-600' : 'text-green-600'
const cfBarColor   = (cf) => cf >= 0.7 ? 'bg-red-400' : cf >= 0.4 ? 'bg-amber-400' : 'bg-emerald-400'
</script>