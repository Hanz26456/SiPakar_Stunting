<template>
  <AdminLayout title="Kelola Rule Certainty Factor">
    <div class="space-y-5">

      <!-- Metis Warning Box -->
      <div class="bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 rounded-xl p-4 text-xs sm:text-sm text-amber-900 dark:text-amber-200 flex items-start gap-3 shadow-2xs">
        <AlertTriangle class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
        <div class="leading-relaxed">
          Parameter nilai MB (<em>Measure of Belief</em>) dan MD (<em>Measure of Disbelief</em>) dirumuskan berdasarkan hasil wawancara pakar gizi/bidan pembina. <strong>Modifikasi aturan rule langsung mempengaruhi akurasi kalkulasi diagnosis</strong> seluruh balita.
        </div>
      </div>

      <!-- Header & Action Button -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 font-medium">
          Total <strong class="text-zinc-900 dark:text-zinc-100">{{ rules.length }}</strong> rule inferensi Certainty Factor terdaftar
        </div>
        <button
          @click="openCreate"
          type="button"
          class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-xs transition-all active:scale-[0.98] flex-shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Rule CF Baru</span>
        </button>
      </div>

      <!-- Metis Filter Pills -->
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="s in ['semua', 'normal', 'berisiko', 'stunting', 'stunting_berat']"
          :key="s"
          type="button"
          @click="filterStatus = s"
          :class="[
            'px-3 py-1.5 text-xs rounded-lg font-medium transition-all shadow-2xs',
            filterStatus === s
              ? 'bg-blue-600 text-white font-semibold'
              : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800'
          ]"
        >
          {{ statusLabel(s) }}
        </button>
      </div>

      <!-- Daftar Rule Cards -->
      <div class="space-y-3">
        <div
          v-for="r in ruleTerfilter" :key="r.id"
          class="bg-white dark:bg-zinc-900 rounded-xl border transition-all shadow-2xs"
          :class="r.is_active ? 'border-zinc-200 dark:border-zinc-800' : 'border-zinc-200 dark:border-zinc-800 opacity-60'"
        >
          <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 p-4 sm:p-5">

            <!-- Identitas Rule & Gejala -->
            <div class="flex items-start sm:items-center gap-3.5 flex-1 min-w-0">
              <div class="px-2.5 py-1 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 text-xs font-mono font-bold border border-blue-200 dark:border-blue-900/50 flex-shrink-0">
                {{ r.kode_rule }}
              </div>

              <div class="flex-1 min-w-0">
                <div class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate mb-1">
                  {{ r.nama_gejala }}
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-xs bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 px-2 py-0.5 rounded-md font-mono">
                    {{ r.kode_gejala }}
                  </span>
                  <span class="text-xs text-zinc-400">&rarr;</span>
                  <StatusBadge :status="r.status_diagnosa" :label="statusLabel(r.status_diagnosa)" />
                </div>
              </div>
            </div>

            <!-- Nilai Komponen CF (MB, MD, CF Pakar) -->
            <div class="flex items-center gap-4 flex-shrink-0 px-1 border-t lg:border-t-0 lg:border-l border-zinc-100 dark:border-zinc-800 pt-2 lg:pt-0">
              <div class="text-center min-w-[36px]">
                <div class="text-xs sm:text-sm font-bold text-blue-600 dark:text-blue-400 font-mono">{{ r.mb }}</div>
                <div class="text-[10px] text-zinc-400 uppercase font-semibold">MB</div>
              </div>

              <span class="text-zinc-400 text-xs">&minus;</span>

              <div class="text-center min-w-[36px]">
                <div class="text-xs sm:text-sm font-bold text-amber-600 dark:text-amber-400 font-mono">{{ r.md }}</div>
                <div class="text-[10px] text-zinc-400 uppercase font-semibold">MD</div>
              </div>

              <span class="text-zinc-400 text-xs">=</span>

              <div class="text-center min-w-[44px]">
                <div class="text-sm sm:text-base font-black font-mono" :class="cfColor(r.cf_pakar)">
                  {{ r.cf_pakar.toFixed(2) }}
                </div>
                <div class="text-[10px] text-zinc-400 uppercase font-semibold">CF</div>
              </div>

              <!-- Bar visual CF -->
              <div class="w-20 hidden sm:block">
                <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all"
                    :class="cfBarColor(r.cf_pakar)"
                    :style="{ width: Math.max(0, r.cf_pakar * 100) + '%' }"
                  />
                </div>
                <div class="text-[10px] text-zinc-400 text-center mt-0.5 font-mono">
                  {{ Math.round(r.cf_pakar * 100) }}%
                </div>
              </div>
            </div>

            <!-- Status & Aksi -->
            <div class="flex items-center gap-2 flex-shrink-0 pt-1 lg:pt-0">
              <span
                class="text-xs px-2.5 py-0.5 rounded-full font-semibold inline-flex items-center gap-1"
                :class="r.is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500'"
              >
                {{ r.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>

              <button
                @click="editRule(r)"
                type="button"
                class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors"
              >
                Edit
              </button>

              <button
                @click="toggleRule(r)"
                type="button"
                class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition-colors"
                :class="r.is_active
                  ? 'border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30'
                  : 'border-emerald-200 dark:border-emerald-900/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30'"
              >
                {{ r.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
              </button>
            </div>
          </div>

          <!-- Kondisi -->
          <div v-if="r.kondisi" class="px-5 pb-3.5 text-xs text-zinc-500 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800/80 pt-2.5">
            <strong class="text-zinc-700 dark:text-zinc-300">Keterangan Kondisi:</strong> {{ r.kondisi }}
          </div>
        </div>
      </div>

    </div>

    <!-- ===== METIS MODAL TAMBAH/EDIT ===== -->
    <div v-if="showModal" @click.self="closeModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
          <div class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-100">
            {{ editingRule ? 'Edit Rule Certainty Factor' : 'Tambah Rule CF Baru' }}
          </div>
          <button @click="closeModal" type="button" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitRule" class="space-y-4">
          <!-- Kode rule (hanya saat tambah) -->
          <div v-if="!editingRule">
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Kode Rule <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.kode_rule"
              type="text"
              placeholder="Contoh: R-12"
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-2xs"
            >
            <div v-if="form.errors.kode_rule" class="text-xs text-rose-500 mt-1">{{ form.errors.kode_rule }}</div>
          </div>

          <!-- Gejala (hanya saat tambah) -->
          <div v-if="!editingRule">
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Parameter Gejala Terkait <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.gejala_id"
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-2xs"
            >
              <option value="">— Pilih parameter gejala —</option>
              <option v-for="g in gejala" :key="g.id" :value="g.id">
                {{ g.kode }} — {{ g.nama_gejala }}
              </option>
            </select>
          </div>

          <!-- Status diagnosa (hanya saat tambah) -->
          <div v-if="!editingRule">
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Klasifikasi Status Output <span class="text-rose-500">*</span>
            </label>
            <select
              v-model="form.status_diagnosa"
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-2xs"
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
              <label class="block text-xs font-semibold text-blue-600 dark:text-blue-400 mb-1.5">
                Nilai MB (Kepercayaan) <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.mb"
                type="number"
                step="0.05"
                min="0"
                max="1"
                placeholder="0.0 – 1.0"
                class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 font-mono shadow-2xs"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-amber-600 dark:text-amber-400 mb-1.5">
                Nilai MD (Ketidakyakinan) <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.md"
                type="number"
                step="0.05"
                min="0"
                max="1"
                placeholder="0.0 – 1.0"
                class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-amber-600 focus:ring-1 focus:ring-amber-600 font-mono shadow-2xs"
              >
            </div>
          </div>

          <!-- Preview CF -->
          <div v-if="form.mb !== '' && form.md !== ''" class="bg-zinc-50 dark:bg-zinc-800/60 rounded-lg p-3 text-center border border-zinc-200 dark:border-zinc-700">
            <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-0.5">CF Pakar = MB − MD</div>
            <div
              class="text-2xl font-black font-mono"
              :class="cfColor(parseFloat(form.mb || 0) - parseFloat(form.md || 0))"
            >
              {{ (parseFloat(form.mb || 0) - parseFloat(form.md || 0)).toFixed(2) }}
            </div>
            <div class="h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden mt-2">
              <div
                class="h-full rounded-full transition-all"
                :class="cfBarColor(parseFloat(form.mb || 0) - parseFloat(form.md || 0))"
                :style="{ width: Math.max(0, (parseFloat(form.mb || 0) - parseFloat(form.md || 0)) * 100) + '%' }"
              />
            </div>
          </div>

          <div v-if="form.errors.mb" class="text-xs text-rose-500">{{ form.errors.mb }}</div>

          <!-- Kondisi -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Catatan Kondisi Rule (Opsional)
            </label>
            <input
              v-model="form.kondisi"
              type="text"
              placeholder="Deskripsi kondisi berlakunya rule..."
              class="w-full bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-2xs"
            >
          </div>

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
              {{ form.processing ? 'Menyimpan...' : (editingRule ? 'Simpan Perubahan' : 'Tambah Rule') }}
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
import { AlertTriangle, Plus, X } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'

const props = defineProps({ rules: Array, gejala: Array })

const showModal    = ref(false)
const editingRule  = ref(null)
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

const openCreate = () => {
  editingRule.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

const editRule = (r) => {
  form.clearErrors()
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
  form.clearErrors()
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

const statusLabel  = (s) => ({ semua: 'Semua Status', normal: 'Normal', berisiko: 'Berisiko', stunting: 'Stunting', stunting_berat: 'Stunting Berat' }[s] ?? s)
const cfColor      = (cf) => cf >= 0.7 ? 'text-rose-600 dark:text-rose-400' : cf >= 0.4 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'
const cfBarColor   = (cf) => cf >= 0.7 ? 'bg-rose-500' : cf >= 0.4 ? 'bg-amber-500' : 'bg-emerald-500'
</script>
