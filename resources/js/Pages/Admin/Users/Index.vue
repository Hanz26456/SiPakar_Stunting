<template>
  <AdminLayout title="Manajemen Pengguna">
    <div class="space-y-5">

      <!-- Search + Filter + Tambah Button -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="relative flex-1">
          <Search class="w-4 h-4 text-zinc-400 absolute left-3.5 top-3" />
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama atau email pengguna..."
            class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg pl-9.5 pr-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all shadow-xs"
            @input="doSearch"
          >
        </div>

        <select
          v-model="filterRole"
          class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-700 dark:text-zinc-200 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all shadow-xs"
          @change="doSearch"
        >
          <option value="">Semua Peran (Role)</option>
          <option value="admin">Administrator</option>
          <option value="bidan">Bidan</option>
          <option value="kader">Kader</option>
          <option value="ortu">Orang Tua</option>
        </select>

        <button
          @click="openCreate"
          type="button"
          class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-xs transition-colors flex-shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Pengguna</span>
        </button>
      </div>

      <!-- Statistik pengguna per role -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div
          v-for="r in roleStats" :key="r.role"
          class="bg-white dark:bg-zinc-900 rounded-xl border p-4 flex items-center gap-3.5 cursor-pointer transition-all shadow-xs"
          :class="filterRole === r.role ? 'ring-2 ring-blue-600/20 border-blue-600' : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700'"
          @click="filterRole = (filterRole === r.role ? '' : r.role); doSearch()"
        >
          <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :class="r.bg">
            <component :is="r.icon" class="w-5 h-5" :class="r.iconColor" />
          </div>
          <div>
            <div class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ jumlahRole(r.role) }}</div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">{{ r.label }}</div>
          </div>
        </div>
      </div>

      <!-- Tabel pengguna -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead>
              <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/75 dark:bg-zinc-800/40 text-zinc-500 dark:text-zinc-400 text-[11px] uppercase tracking-wider font-semibold">
                <th class="py-3 px-4">Nama Lengkap</th>
                <th class="py-3 px-4">Email</th>
                <th class="py-3 px-4">Peran (Role)</th>
                <th class="py-3 px-4">No. HP</th>
                <th class="py-3 px-4">Status Akun</th>
                <th class="py-3 px-4">Terdaftar</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/70">
              <tr v-if="users.data.length === 0">
                <td colspan="7" class="py-14 text-center">
                  <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 flex items-center justify-center mx-auto mb-3">
                    <Users class="w-6 h-6" />
                  </div>
                  <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Tidak ada pengguna ditemukan</div>
                  <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm mx-auto">
                    Kriteria pencarian tidak cocok dengan nama atau role yang tersimpan.
                  </p>
                </td>
              </tr>
              <tr
                v-for="u in users.data" :key="u.id"
                class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors"
              >
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-2.5">
                    <div
                      class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0"
                      :class="roleBgAvatar(u.role)"
                    >
                      {{ initials(u.name) }}
                    </div>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ u.name }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-300 font-mono text-xs">{{ u.email }}</td>
                <td class="py-3.5 px-4">
                  <span class="px-2.5 py-0.5 rounded-md text-xs font-medium inline-flex items-center gap-1 border" :class="roleBadge(u.role)">
                    {{ roleLabel(u.role) }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-400 text-xs">{{ u.no_hp ?? '—' }}</td>
                <td class="py-3.5 px-4">
                  <span
                    class="px-2.5 py-0.5 rounded-md text-xs font-medium inline-flex items-center gap-1.5 border"
                    :class="u.is_active ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/50' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700'"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="u.is_active ? 'bg-emerald-500' : 'bg-zinc-400'"></span>
                    <span>{{ u.is_active ? 'Aktif' : 'Nonaktif' }}</span>
                  </span>
                </td>
                <td class="py-3.5 px-4 text-zinc-500 dark:text-zinc-400 text-xs">{{ u.created_at }}</td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="editUser(u)"
                      type="button"
                      class="px-2.5 py-1 text-xs font-medium rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 transition-colors"
                    >
                      Edit
                    </button>
                    <button
                      v-if="u.id !== $page.props.auth.user.id"
                      @click="toggleAktif(u)"
                      type="button"
                      class="px-2.5 py-1 text-xs font-medium rounded-lg transition-colors border"
                      :class="u.is_active
                        ? 'border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30'
                        : 'border-emerald-200 dark:border-emerald-900/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30'"
                    >
                      {{ u.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    <span v-else class="text-xs text-zinc-400 italic px-2">Akun Anda</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="users.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
          <div class="text-xs text-zinc-500 dark:text-zinc-400">
            Menampilkan {{ users.from }}–{{ users.to }} dari {{ users.total }} pengguna
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="link in users.links" :key="link.label"
              :href="link.url ?? '#'"
              v-html="link.label"
              :class="[
                'px-3 py-1 text-xs rounded-lg border transition-colors',
                link.active
                  ? 'bg-blue-600 text-white border-blue-600 font-semibold'
                  : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700',
                !link.url ? 'opacity-40 pointer-events-none' : ''
              ]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- ===== MODAL TAMBAH / EDIT ===== -->
    <div v-if="showModal" @click.self="closeModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
          <div class="text-base font-bold text-zinc-900 dark:text-white">
            {{ editingUser ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}
          </div>
          <button @click="closeModal" type="button" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors p-1 rounded-lg">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitUser" class="space-y-3.5">
          <!-- Nama -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Contoh: Ratna Dewi"
              class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all"
              :class="form.errors.name ? 'border-red-400' : ''"
            >
            <div v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Alamat Email <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.email"
              type="email"
              placeholder="email@posyandu.id"
              class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all"
              :class="form.errors.email ? 'border-red-400' : ''"
            >
            <div v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Kata Sandi (Password)
              <span v-if="editingUser" class="text-zinc-400 font-normal">(kosongkan jika tidak diganti)</span>
              <span v-else class="text-red-500">*</span>
            </label>
            <input
              v-model="form.password"
              type="password"
              placeholder="Minimal 8 karakter"
              class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all"
              :class="form.errors.password ? 'border-red-400' : ''"
            >
            <div v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</div>
          </div>

          <!-- Role -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Peran Hak Akses (Role) <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.role"
              class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all"
            >
              <option value="admin">Administrator Sistem</option>
              <option value="bidan">Bidan / Tenaga Medis</option>
              <option value="kader">Kader Posyandu</option>
              <option value="ortu">Orang Tua Balita</option>
            </select>
          </div>

          <!-- No HP -->
          <div>
            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
              Nomor WhatsApp / HP (Opsional)
            </label>
            <input
              v-model="form.no_hp"
              type="text"
              placeholder="Contoh: 081234567890"
              class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all"
            >
          </div>

          <div class="flex items-center gap-2.5 pt-3 border-t border-zinc-200 dark:border-zinc-800">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-colors disabled:opacity-50"
            >
              {{ form.processing ? 'Menyimpan...' : (editingUser ? 'Simpan Perubahan' : 'Tambah Pengguna') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import {
  Search,
  Plus,
  Users,
  Shield,
  Stethoscope,
  UserCheck,
  HeartHandshake,
  X
} from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  users: Object,
  filters: Object,
})

const showModal   = ref(false)
const editingUser = ref(null)
const search      = ref(props.filters?.search ?? '')
const filterRole  = ref(props.filters?.role ?? '')

const form = useForm({
  name:     '',
  email:    '',
  password: '',
  role:     'kader',
  no_hp:    '',
})

const roleStats = [
  { role: 'admin',  label: 'Admin',       icon: Shield,         bg: 'bg-blue-50 dark:bg-blue-950/40',       iconColor: 'text-blue-600 dark:text-blue-400' },
  { role: 'bidan',  label: 'Bidan',       icon: Stethoscope,    bg: 'bg-cyan-50 dark:bg-cyan-950/40',       iconColor: 'text-cyan-600 dark:text-cyan-400' },
  { role: 'kader',  label: 'Kader',       icon: UserCheck,      bg: 'bg-emerald-50 dark:bg-emerald-950/40', iconColor: 'text-emerald-600 dark:text-emerald-400' },
  { role: 'ortu',   label: 'Orang Tua',   icon: HeartHandshake, bg: 'bg-amber-50 dark:bg-amber-950/40',     iconColor: 'text-amber-600 dark:text-amber-400' },
]

const jumlahRole = (role) =>
  props.users.data.filter(u => u.role === role).length

const doSearch = () => {
  router.get(route('admin.users.index'), {
    search: search.value,
    role:   filterRole.value,
  }, { preserveState: true, replace: true })
}

const openCreate = () => {
  editingUser.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

const editUser = (u) => {
  form.clearErrors()
  editingUser.value = u
  form.name     = u.name
  form.email    = u.email
  form.role     = u.role
  form.no_hp    = u.no_hp ?? ''
  form.password = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value   = false
  editingUser.value = null
  form.reset()
  form.clearErrors()
}

const submitUser = () => {
  if (editingUser.value) {
    form.put(route('admin.users.update', editingUser.value.id), {
      onSuccess: closeModal,
    })
  } else {
    form.post(route('admin.users.store'), {
      onSuccess: closeModal,
    })
  }
}

const toggleAktif = (u) => {
  router.patch(route('admin.users.toggle-active', u.id))
}

const initials = (name) =>
  name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)

const roleBadge = (r) => ({
  admin: 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800/40',
  bidan: 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800/40',
  kader: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/40',
  ortu:  'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/40',
}[r] ?? 'bg-zinc-100 text-zinc-600 border-zinc-200')

const roleBgAvatar = (r) => ({
  admin: 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300',
  bidan: 'bg-cyan-100 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300',
  kader: 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300',
  ortu:  'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300',
}[r] ?? 'bg-zinc-100 text-zinc-700')

const roleLabel = (r) => ({
  admin: 'Admin',
  bidan: 'Bidan',
  kader: 'Kader',
  ortu:  'Orang Tua',
}[r] ?? r)
</script>
