<template>
  <KaderLayout title="Tambah Balita">
    <div class="max-w-3xl mx-auto">

      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-sm text-gray-400 mb-5">
        <Link :href="route('kader.balita.index')" class="hover:text-gray-600">Data Balita</Link>
        <span>›</span>
        <span class="text-gray-600">Tambah Balita</span>
      </div>

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
                placeholder="cth. Arfa Nugraha"
                required
                :error="form.errors.nama"
              />
            </div>
            <FormField
              v-model="form.tanggal_lahir"
              label="Tanggal lahir"
              type="date"
              required
              :error="form.errors.tanggal_lahir"
            />
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">
                Jenis kelamin <span class="text-red-400">*</span>
              </label>
              <div class="flex gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.jenis_kelamin" value="L" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">♂ Laki-laki</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.jenis_kelamin" value="P" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">♀ Perempuan</span>
                </label>
              </div>
              <div v-if="form.errors.jenis_kelamin" class="text-xs text-red-500 mt-1">{{ form.errors.jenis_kelamin }}</div>
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
              placeholder="cth. Siti Aminah"
              required
              :error="form.errors.nama_ibu"
            />
            <FormField
              v-model="form.nama_ayah"
              label="Nama ayah (opsional)"
              placeholder="cth. Budi Santoso"
              :error="form.errors.nama_ayah"
            />
            <FormField
              v-model="form.no_hp_ortu"
              label="No. HP orang tua"
              placeholder="cth. 08123456789"
              :error="form.errors.no_hp_ortu"
            />
            <FormField
              v-model="form.no_kk"
              label="No. KK (opsional)"
              placeholder="16 digit"
              :error="form.errors.no_kk"
            />
            <div class="col-span-2">
              <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
              <textarea
                v-model="form.alamat"
                rows="2"
                placeholder="Alamat lengkap..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-emerald-300"
              ></textarea>
            </div>
            <FormField
              v-model="form.rt_rw"
              label="RT/RW"
              placeholder="cth. 001/002"
              :error="form.errors.rt_rw"
            />
            <FormField
              v-model="form.desa"
              label="Desa/Kelurahan"
              placeholder="cth. Maskuning Kulon"
              :error="form.errors.desa"
            />
          </div>
        </div>

        <!-- SECTION: Riwayat Medis -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
          <div class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs flex items-center justify-center font-bold">3</span>
            Riwayat Medis & Gizi
          </div>
          <div class="grid grid-cols-2 gap-4">

            <!-- ASI Eksklusif -->
            <div class="col-span-2">
              <label class="block text-xs font-medium text-gray-600 mb-2">ASI Eksklusif (0–6 bulan)</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.asi_eksklusif" :value="true" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">✅ Ya, terpenuhi</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.asi_eksklusif" :value="false" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">❌ Tidak terpenuhi</span>
                </label>
              </div>
            </div>

            <FormField
              v-model="form.mulai_mpasi"
              label="Mulai MPASI (tanggal)"
              type="date"
              :error="form.errors.mulai_mpasi"
            />

            <div>
              <label class="block text-xs font-medium text-gray-600 mb-2">MPASI sesuai usia</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.mpasi_sesuai_usia" :value="true" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">Ya</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.mpasi_sesuai_usia" :value="false" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">Tidak</span>
                </label>
              </div>
            </div>

            <!-- Infeksi berulang -->
            <div class="col-span-2">
              <label class="block text-xs font-medium text-gray-600 mb-2">Riwayat penyakit infeksi berulang (ISPA, diare, cacingan)</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.infeksi_berulang" :value="true" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">⚠️ Ada riwayat infeksi berulang</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.infeksi_berulang" :value="false" class="accent-emerald-600">
                  <span class="text-sm text-gray-700">Tidak ada</span>
                </label>
              </div>
            </div>

            <div class="col-span-2" v-if="form.infeksi_berulang">
              <label class="block text-xs font-medium text-gray-600 mb-1">Detail riwayat penyakit</label>
              <textarea
                v-model="form.detail_penyakit"
                rows="2"
                placeholder="cth. Pernah diare 3x dalam 2 bulan, ISPA berulang..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-emerald-300"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- SECTION: Data Kelahiran -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
          <div class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs flex items-center justify-center font-bold">4</span>
            Data Kelahiran (Opsional)
          </div>
          <div class="grid grid-cols-3 gap-4">
            <FormField
              v-model="form.berat_lahir"
              label="Berat lahir (kg)"
              type="number"
              step="0.01"
              placeholder="cth. 3.2"
              :error="form.errors.berat_lahir"
            />
            <FormField
              v-model="form.panjang_lahir"
              label="Panjang lahir (cm)"
              type="number"
              step="0.1"
              placeholder="cth. 48.5"
              :error="form.errors.panjang_lahir"
            />
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Jenis persalinan</label>
              <select
                v-model="form.jenis_persalinan"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
              >
                <option value="">— Pilih —</option>
                <option value="normal">Normal</option>
                <option value="caesar">Caesar</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Action -->
        <div class="flex justify-end gap-3">
          <Link
            :href="route('kader.balita.index')"
            class="px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 font-medium"
          >
            {{ form.processing ? 'Menyimpan...' : 'Simpan Data Balita' }}
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

const form = useForm({
  nama: '',
  tanggal_lahir: '',
  jenis_kelamin: 'L',
  nama_ibu: '',
  nama_ayah: '',
  no_hp_ortu: '',
  alamat: '',
  rt_rw: '',
  desa: '',
  no_kk: '',
  asi_eksklusif: false,
  mulai_mpasi: '',
  mpasi_sesuai_usia: false,
  infeksi_berulang: false,
  detail_penyakit: '',
  berat_lahir: '',
  panjang_lahir: '',
  jenis_persalinan: '',
})

const submit = () => {
  form.post(route('kader.balita.store'))
}
</script>