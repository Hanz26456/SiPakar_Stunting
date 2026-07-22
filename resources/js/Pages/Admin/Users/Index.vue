<template>
  <AdminLayout title="Manajemen Pengguna">
    <div class="space-y-4">

      <!-- Search + Filter + Tambah -->
      <div class="flex items-center gap-3">
        <div class="relative flex-1">
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama atau email..."
            class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white"
            @input="doSearch"
          >
          <span class="absolute left-3 top-2.5 text-gray-400 text-sm">🔍</span>
        </div>
        <select
          v-model="filterRole"
          class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none"
          @change="doSearch"
        >
          <option value="">Semua role</option>
          <option value="admin">Admin</option>
          <option value="bidan">Bidan</option>
          <option value="kader">Kader</option>
          <option value="ortu">Orang Tua</option>
        </select>
        <button
          @click="showModal = true"
          class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 flex items-center gap-1.5 flex-shrink-0"
        >
          + Tambah Pengguna
        </button>
      </div>

      <!-- Statistik pengguna per role -->
      <div class="grid grid-cols-4 gap-3">
        <div
          v-for="r in roleStats" :key="r.role"
          class="bg-white rounded-xl border border-gray-200 p-3 flex items-center gap-3 cursor-pointer hover:border-gray-300 transition-colors"
          @click="filterRole = r.role; doSearch()"
        >
          <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg" :class="r.bg">
            {{ r.icon }}
          </div>
          <div>
            <div class="text-lg font-semibold text-gray-800">{{ jumlahRole(r.role) }}</div>
            <div class="text-xs text-gray-400">{{ r.label }}</div>
          </div>
        </div>
      </div>

      <!-- Tabel pengguna -->
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Nama</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Email</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Role</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">No. HP</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Status</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Terdaftar</th>
              <th class="text-left py-3 px-4 text-xs text-gray-400 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="users.data.length === 0">
              <td colspan="7" class="py-12 text-center text-gray-400 text-sm">
                <div class="text-4xl mb-2">👥</div>
                Tidak ada pengguna ditemukan
              </td>
            </tr>
            <tr
              v-for="u in users.data" :key="u.id"
              class="border-b border-gray-50 hover:bg-gray-50"
            >
              <td class="py-3 px-4">
                <div class="flex items-center gap-2">
                  <div
                    class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                    :class="roleBgAvatar(u.role)"
                  >
                    {{ initials(u.name) }}
                  </div>
                  <span class="font-medium text-gray-800">{{ u.name }}</span>
                </div>
              </td>
              <td class="py-3 px-4 text-gray-500 text-xs">{{ u.email }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="roleBadge(u.role)">
                  {{ roleLabel(u.role) }}
                </span>
              </td>
              <td class="py-3 px-4 text-gray-500 text-xs">{{ u.no_hp ?? '—' }}</td>
              <td class="py-3 px-4">
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="u.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'"
                >
                  {{ u.is_active ? '● Aktif' : '○ Nonaktif' }}
                </span>
              </td>
              <td class="py-3 px-4 text-gray-400 text-xs">{{ u.created_at }}</td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-2">
                  <button
                    @click="editUser(u)"
                    class="text-xs text-blue-600 hover:underline"
                  >
                    Edit
                  </button>
                  <button
                    v-if="u.id !== $page.props.auth.user.id"
                    @click="toggleAktif(u)"
                    class="text-xs hover:underline"
                    :class="u.is_active ? 'text-red-500' : 'text-green-600'"
                  >
                    {{ u.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                  <span v-else class="text-xs text-gray-300">Akun Anda</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="users.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
          <div class="text-xs text-gray-400">
            Menampilkan {{ users.from }}–{{ users.to }} dari {{ users.total }} pengguna
          </div>
          <div class="flex gap-1">
            <Link
              v-for="link in users.links" :key="link.label"
              :href="link.url ?? '#'"
              v-html="link.label"
              :class="[
                'px-3 py-1 text-xs rounded border',
                link.active ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50',
                !link.url ? 'opacity-40 pointer-events-none' : ''
              ]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- ===== MODAL TAMBAH / EDIT ===== -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-5">
        <div class="flex items-center justify-between mb-4">
          <div class="text-base font-semibold text-gray-800">
            {{ editingUser ? 'Edit Pengguna' : 'Tambah Pengguna' }}
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
        </div>

        <form @submit.prevent="submitUser" class="space-y-3">

          <!-- Nama -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap <span class="text-red-400">*</span></label>
            <input
              v-model="form.name"
              type="text"
              placeholder="cth. Ratna Dewi"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
              :class="form.errors.name ? 'border-red-300' : ''"
            >
            <div v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-400">*</span></label>
            <input
              v-model="form.email"
              type="email"
              placeholder="email@posyandu.id"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
              :class="form.errors.email ? 'border-red-300' : ''"
            >
            <div v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">
              Password
              <span v-if="editingUser" class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span>
              <span v-else class="text-red-400">*</span>
            </label>
            <input
              v-model="form.password"
              type="password"
              placeholder="Min. 8 karakter"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
              :class="form.errors.password ? 'border-red-300' : ''"
            >
            <div v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</div>
          </div>

          <!-- Role -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Role <span class="text-red-400">*</span></label>
            <select
              v-model="form.role"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              <option value="admin">🛡️ Administrator</option>
              <option value="bidan">👩‍⚕️ Bidan / Tenaga Kesehatan</option>
              <option value="kader">👩 Kader Posyandu</option>
              <option value="ortu">👨‍👩‍👧 Orang Tua</option>
            </select>
          </div>

          <!-- No HP -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">No. HP (opsional)</label>
            <input
              v-model="form.no_hp"
              type="text"
              placeholder="cth. 08123456789"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
          </div>

          <div class="flex gap-2 pt-2">
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
              {{ form.processing ? 'Menyimpan...' : (editingUser ? 'Simpan Perubahan' : 'Tambah Pengguna') }}
            </button>
          </div>

        </form>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  users: Object,
  filters: Object,
})

const page        = usePage()
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
  { role: 'admin',  label: 'Admin',       icon: '🛡️', bg: 'bg-gray-100' },
  { role: 'bidan',  label: 'Bidan',       icon: '👩‍⚕️', bg: 'bg-blue-100' },
  { role: 'kader',  label: 'Kader',       icon: '👩',  bg: 'bg-emerald-100' },
  { role: 'ortu',   label: 'Orang Tua',   icon: '👨‍👩‍👧', bg: 'bg-purple-100' },
]

const jumlahRole = (role) =>
  props.users.data.filter(u => u.role === role).length

const doSearch = () => {
  router.get(route('admin.users.index'), {
    search: search.value,
    role:   filterRole.value,
  }, { preserveState: true, replace: true })
}

const editUser = (u) => {
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
  admin: 'bg-gray-800 text-white',
  bidan: 'bg-blue-100 text-blue-700',
  kader: 'bg-green-100 text-green-700',
  ortu:  'bg-purple-100 text-purple-700',
}[r] ?? 'bg-gray-100 text-gray-500')

const roleBgAvatar = (r) => ({
  admin: 'bg-gray-700 text-white',
  bidan: 'bg-blue-100 text-blue-700',
  kader: 'bg-emerald-100 text-emerald-700',
  ortu:  'bg-purple-100 text-purple-700',
}[r] ?? 'bg-gray-100 text-gray-600')

const roleLabel = (r) => ({
  admin: 'Admin',
  bidan: 'Bidan',
  kader: 'Kader',
  ortu:  'Orang Tua',
}[r] ?? r)
</script>