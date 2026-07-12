<template>
  <KaderLayout :title="'Edit — ' + balita.nama">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-5">
      <Link :href="route('kader.balita.index')" class="hover:text-gray-600">Data Balita</Link>
      <span>›</span>
      <Link :href="route('kader.balita.show', balita.id)" class="hover:text-gray-600">{{ balita.nama }}</Link>
      <span>›</span>
      <span class="text-gray-600">Edit</span>
    </div>

    <div class="max-w-3xl mx-auto">
      <form @submit.prevent="submit">

        <!-- SECTION: Identitas Balita -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
          <div class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs flex items-center justify-center font-bold">1</span>
            Identitas Balita
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <FormField
                v-model="form.nama"
                label="Nama lengkap balita"
                required
                :error="form.errors.nama"
              />
            </div>
            <!-- Tanggal lahir dan jenis kelamin tidak bisa diedit -->
            <div class="bg-gray-50 rounded-lg p-3 col-span-2">
              <div class="text-xs text-gray-400 mb-1">Tanggal lahir & jenis kelamin tidak dapat diubah</div>
              <div class="text-sm text-gray-700">
                {{ balita.tanggal_lahir }} · {{ balita.jenis_kelamin }}
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION: Data Orang Tua -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
          <div class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs flex items-center justify-center font-bold">2</span>
            Data Orang Tua
          </div>
          <div class="grid grid-cols-2 gap-4">
            <FormField
              v-model="form.nama_ibu"
              label="Nama ibu"
              required
              :error="form.errors.nama_ibu"
            />
            <FormField
              v-model="form.nama_ayah"
              label="Nama ayah (opsional)"
              :error="form.errors.nama_ayah"
            />
            <FormField
              v-model="form.no_hp_ortu"
              label="No. HP orang tua"
              :error="form.errors.no_hp_ortu"
            />
            <FormField
              v-model="form.desa"
              label="Desa/Kelurahan"
              :error="form.errors.desa"
            />
            <div class="col-span-2">
              <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
              <textarea
                v-model="form.alamat"
                rows="2"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-emerald-300"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Action -->
        <div class="flex justify-between">
          <Link
            :href="route('kader.balita.show', balita.id)"
            class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
          >
            ← Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 font-medium"
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