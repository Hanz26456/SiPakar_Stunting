<template>
  <KaderLayout :title="'Edit — ' + balita.nama">
    <div class="max-w-3xl mx-auto space-y-5">

      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-400">
        <Link :href="route('kader.balita.index')" class="hover:text-orbit-primary transition-colors">Data Balita</Link>
        <span>›</span>
        <Link :href="route('kader.balita.show', balita.id)" class="hover:text-orbit-primary transition-colors">{{ balita.nama }}</Link>
        <span>›</span>
        <span class="text-slate-600 dark:text-slate-300 font-medium">Edit Data</span>
      </div>

      <form @submit.prevent="submit" class="space-y-5">

        <!-- SECTION: Identitas Balita -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-orbit-primary/10 text-orbit-primary text-xs flex items-center justify-center font-bold">1</span>
            <span>Identitas Balita</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <FormField
                v-model="form.nama"
                label="Nama Lengkap Balita"
                required
                :error="form.errors.nama"
              />
            </div>
            <!-- Tanggal lahir dan jenis kelamin tidak bisa diedit -->
            <div class="bg-slate-50 dark:bg-orbit-surface2/60 rounded-xl border border-slate-100 dark:border-orbit-border/60 p-3.5 sm:col-span-2">
              <div class="text-[11px] text-slate-400 mb-1 font-medium">Tanggal lahir & jenis kelamin terikat dengan kurva WHO dan tidak dapat diubah:</div>
              <div class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200">
                {{ balita.tanggal_lahir }} · {{ balita.jenis_kelamin === 'L' || balita.jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION: Data Orang Tua -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-orbit-primary/10 text-orbit-primary text-xs flex items-center justify-center font-bold">2</span>
            <span>Data Orang Tua & Domisili</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormField
              v-model="form.nama_ibu"
              label="Nama Ibu Kandung"
              required
              :error="form.errors.nama_ibu"
            />
            <FormField
              v-model="form.nama_ayah"
              label="Nama Ayah (Opsional)"
              :error="form.errors.nama_ayah"
            />
            <FormField
              v-model="form.no_hp_ortu"
              label="No. WhatsApp / HP"
              :error="form.errors.no_hp_ortu"
            />
            <FormField
              v-model="form.desa"
              label="Desa / Kelurahan"
              :error="form.errors.desa"
            />
            <div class="sm:col-span-2">
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Alamat Domisili</label>
              <textarea
                v-model="form.alamat"
                rows="2"
                class="w-full border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface2 text-slate-800 dark:text-slate-100 rounded-xl px-3 py-2 text-xs sm:text-sm resize-none focus:outline-none focus:ring-2 focus:ring-orbit-primary"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Action -->
        <div class="flex items-center justify-between pt-2">
          <Link
            :href="route('kader.balita.show', balita.id)"
            class="px-5 py-2.5 text-xs sm:text-sm font-medium border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 rounded-xl transition-all"
          >
            ← Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 text-xs sm:text-sm font-semibold bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 disabled:opacity-50 transition-all active:scale-[0.98]"
          >
            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>

      </form>
    </div>
  </KaderLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import KaderLayout from '@/Layouts/KaderLayout.vue'
import FormField from '@/Components/FormField.vue'

const props = defineProps({
  balita: Object,
  riwayat: Object,
})

const form = useForm({
  nama:       props.balita.nama,
  nama_ibu:   props.balita.nama_ibu,
  nama_ayah:  props.balita.nama_ayah ?? '',
  no_hp_ortu: props.balita.no_hp_ortu ?? '',
  alamat:     props.balita.alamat ?? '',
  desa:       props.balita.desa ?? '',
})

const submit = () => {
  form.put(route('kader.balita.update', props.balita.id))
}
</script>
