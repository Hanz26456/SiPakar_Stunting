<template>
  <KaderLayout title="Tambah Balita">
    <div class="max-w-3xl mx-auto space-y-5">

      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-400">
        <Link :href="route('kader.balita.index')" class="hover:text-orbit-primary transition-colors">Data Balita</Link>
        <span>›</span>
        <span class="text-slate-600 dark:text-slate-300 font-medium">Tambah Balita Baru</span>
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
                placeholder="cth. Arfa Nugraha"
                required
                :error="form.errors.nama"
              />
            </div>
            <FormField
              v-model="form.tanggal_lahir"
              label="Tanggal Lahir"
              type="date"
              required
              :error="form.errors.tanggal_lahir"
            />
            <div>
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">
                Jenis Kelamin <span class="text-rose-500">*</span>
              </label>
              <div class="flex items-center gap-4 mt-2">
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.jenis_kelamin" value="L" class="accent-purple-600">
                  <span>Laki-laki (L)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.jenis_kelamin" value="P" class="accent-purple-600">
                  <span>Perempuan (P)</span>
                </label>
              </div>
              <div v-if="form.errors.jenis_kelamin" class="text-xs text-rose-500 mt-1">{{ form.errors.jenis_kelamin }}</div>
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
              placeholder="cth. Siti Aminah"
              required
              :error="form.errors.nama_ibu"
            />
            <FormField
              v-model="form.nama_ayah"
              label="Nama Ayah (Opsional)"
              placeholder="cth. Budi Santoso"
              :error="form.errors.nama_ayah"
            />
            <FormField
              v-model="form.no_hp_ortu"
              label="No. WhatsApp / HP Orang Tua"
              placeholder="cth. 08123456789"
              :error="form.errors.no_hp_ortu"
            />
            <FormField
              v-model="form.no_kk"
              label="No. Kartu Keluarga (Opsional)"
              placeholder="16 digit nomor KK"
              :error="form.errors.no_kk"
            />
            <div class="sm:col-span-2">
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Alamat Domisili</label>
              <textarea
                v-model="form.alamat"
                rows="2"
                placeholder="Alamat lengkap jalan, gang, nomor rumah..."
                class="w-full border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface2 text-slate-800 dark:text-slate-100 rounded-xl px-3 py-2 text-xs sm:text-sm resize-none focus:outline-none focus:ring-2 focus:ring-orbit-primary"
              ></textarea>
            </div>
            <FormField
              v-model="form.rt_rw"
              label="RT / RW"
              placeholder="cth. 001/002"
              :error="form.errors.rt_rw"
            />
            <FormField
              v-model="form.desa"
              label="Desa / Kelurahan"
              placeholder="cth. Maskuning Kulon"
              :error="form.errors.desa"
            />
          </div>
        </div>

        <!-- SECTION: Riwayat Medis -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-orbit-primary/10 text-orbit-primary text-xs flex items-center justify-center font-bold">3</span>
            <span>Riwayat Medis & Gizi Awal</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- ASI Eksklusif -->
            <div class="sm:col-span-2">
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-2">ASI Eksklusif (0–6 bulan)</label>
              <div class="flex items-center gap-5">
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.asi_eksklusif" :value="true" class="accent-purple-600">
                  <span>Ya, terpenuhi 6 bulan penuh</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.asi_eksklusif" :value="false" class="accent-purple-600">
                  <span>Tidak terpenuhi</span>
                </label>
              </div>
            </div>

            <FormField
              v-model="form.mulai_mpasi"
              label="Mulai Diberikan MPASI (Tanggal)"
              type="date"
              :error="form.errors.mulai_mpasi"
            />

            <div>
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-2">MPASI Sesuai Usia & Tekstur</label>
              <div class="flex items-center gap-5 mt-2">
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.mpasi_sesuai_usia" :value="true" class="accent-purple-600">
                  <span>Sesuai</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.mpasi_sesuai_usia" :value="false" class="accent-purple-600">
                  <span>Tidak sesuai</span>
                </label>
              </div>
            </div>

            <!-- Infeksi berulang -->
            <div class="sm:col-span-2">
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-2">Riwayat Penyakit Infeksi Berulang (ISPA, diare berulang, TB, cacingan)</label>
              <div class="flex items-center gap-5">
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.infeksi_berulang" :value="true" class="accent-purple-600">
                  <span>Ada riwayat infeksi berulang</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                  <input type="radio" v-model="form.infeksi_berulang" :value="false" class="accent-purple-600">
                  <span>Tidak ada riwayat</span>
                </label>
              </div>
            </div>

            <div class="sm:col-span-2" v-if="form.infeksi_berulang">
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Keterangan Riwayat Penyakit</label>
              <textarea
                v-model="form.detail_penyakit"
                rows="2"
                placeholder="cth. Pernah diare berulang dalam 2 bulan terakhir, riwayat bronkopneumonia..."
                class="w-full border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface2 text-slate-800 dark:text-slate-100 rounded-xl px-3 py-2 text-xs sm:text-sm resize-none focus:outline-none focus:ring-2 focus:ring-orbit-primary"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- SECTION: Data Kelahiran -->
        <div class="bg-white dark:bg-orbit-surface rounded-2xl border border-slate-200 dark:border-orbit-border p-5 shadow-2xs">
          <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-lg bg-orbit-primary/10 text-orbit-primary text-xs flex items-center justify-center font-bold">4</span>
            <span>Data Kelahiran (Opsional)</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <FormField
              v-model="form.berat_lahir"
              label="Berat Lahir (kg)"
              type="number"
              step="0.01"
              placeholder="cth. 3.2"
              :error="form.errors.berat_lahir"
            />
            <FormField
              v-model="form.panjang_lahir"
              label="Panjang Lahir (cm)"
              type="number"
              step="0.1"
              placeholder="cth. 48.5"
              :error="form.errors.panjang_lahir"
            />
            <div>
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Jenis Persalinan</label>
              <select
                v-model="form.jenis_persalinan"
                class="w-full border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface2 text-slate-800 dark:text-slate-100 rounded-xl px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-orbit-primary"
              >
                <option value="">— Pilih Jenis Persalinan —</option>
                <option value="normal">Normal (Pervaginam)</option>
                <option value="caesar">Sectio Caesarea (SC)</option>
                <option value="lainnya">Lainnya / Bantuan Alat</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Action -->
        <div class="flex items-center justify-end gap-3 pt-2">
          <Link
            :href="route('kader.balita.index')"
            class="px-5 py-2.5 text-xs sm:text-sm font-medium border border-slate-200 dark:border-orbit-border bg-white dark:bg-orbit-surface hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 rounded-xl transition-all"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 text-xs sm:text-sm font-semibold bg-orbit-primary hover:bg-orbit-primary/90 text-white rounded-xl shadow-md shadow-purple-600/20 disabled:opacity-50 transition-all active:scale-[0.98]"
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
