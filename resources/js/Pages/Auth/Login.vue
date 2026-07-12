<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 flex items-center justify-center p-4">

    <!-- Card login -->
    <div class="w-full max-w-md">

      <!-- Logo + judul -->
      <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-lg">
          <span class="text-white text-2xl font-bold">SP</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">SiPakar Stunting</h1>
        <p class="text-gray-400 text-sm mt-1">Posyandu Melati 3, Pujer — Bondowoso</p>
      </div>

      <!-- Form card -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-6">Masuk ke sistem</h2>

        <!-- Flash error dari Laravel -->
        <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-4">
          {{ $page.props.flash.error }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">

          <!-- Email -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">
              Email
            </label>
            <input
              v-model="form.email"
              type="email"
              placeholder="email@posyandu.id"
              autocomplete="email"
              class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 transition-colors"
              :class="form.errors.email ? 'border-red-300 bg-red-50' : 'border-gray-200'"
              required
            >
            <div v-if="form.errors.email" class="text-xs text-red-500 mt-1">
              {{ form.errors.email }}
            </div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">
              Password
            </label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="current-password"
                class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 pr-10 transition-colors"
                :class="form.errors.password ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                required
              >
              <button
                type="button"
                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 text-sm"
                @click="showPassword = !showPassword"
              >
                {{ showPassword ? '🙈' : '👁️' }}
              </button>
            </div>
            <div v-if="form.errors.password" class="text-xs text-red-500 mt-1">
              {{ form.errors.password }}
            </div>
          </div>

          <!-- Remember me -->
          <div class="flex items-center gap-2">
            <input
              id="remember"
              v-model="form.remember"
              type="checkbox"
              class="rounded accent-emerald-600"
            >
            <label for="remember" class="text-sm text-gray-500 cursor-pointer">
              Ingat saya
            </label>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors mt-2"
          >
            <span v-if="form.processing" class="flex items-center justify-center gap-2">
              <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Memproses...
            </span>
            <span v-else>Masuk →</span>
          </button>

        </form>
      </div>

      <!-- Role info -->
      <div class="mt-6 bg-white rounded-xl border border-gray-100 p-4">
        <div class="text-xs font-medium text-gray-400 mb-3 uppercase tracking-wider">Akun demo</div>
        <div class="space-y-2">
          <div
            v-for="akun in akunDemo" :key="akun.role"
            class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
            @click="isiAkun(akun)"
          >
            <div
              class="w-7 h-7 rounded-lg flex items-center justify-center text-sm flex-shrink-0"
              :class="akun.warna"
            >
              {{ akun.icon }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-xs font-medium text-gray-700">{{ akun.label }}</div>
              <div class="text-xs text-gray-400 truncate">{{ akun.email }}</div>
            </div>
            <span class="text-xs text-emerald-600 flex-shrink-0">Isi otomatis</span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center text-xs text-gray-400 mt-6">
        Sistem Pakar Diagnosis Dini Stunting · Politeknik Negeri Jember © 2025
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const showPassword = ref(false)

const form = useForm({
  email:    '',
  password: '',
  remember: false,
})

const akunDemo = [
  { role: 'admin',  label: 'Administrator', email: 'admin@posyandu.id',  password: 'password', icon: '🛡️', warna: 'bg-gray-100' },
  { role: 'bidan',  label: 'Bidan / Nakes', email: 'bidan@posyandu.id',  password: 'password', icon: '👩‍⚕️', warna: 'bg-blue-100' },
  { role: 'kader',  label: 'Kader Posyandu', email: 'kader@posyandu.id', password: 'password', icon: '👩', warna: 'bg-emerald-100' },
]

const isiAkun = (akun) => {
  form.email    = akun.email
  form.password = akun.password
}

const submit = () => {
  form.post(route('login'))
}
</script>