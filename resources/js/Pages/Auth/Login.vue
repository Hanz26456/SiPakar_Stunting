<template>
  <div class="min-h-screen relative flex flex-col justify-between overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white bg-zinc-50 dark:bg-zinc-950">
    <!-- Health Background Illustration Layer -->
    <div
      class="fixed inset-0 bg-cover bg-bottom bg-no-repeat transition-all duration-300 pointer-events-none z-0 dark:brightness-[0.6] dark:contrast-[1.1] opacity-40 dark:opacity-20"
      style="background-image: url('/images/login-health-bg.jpg');"
    />

    <!-- Ambient Dark Overlay for Dark Mode Contrast -->
    <div class="fixed inset-0 bg-zinc-900/5 dark:bg-zinc-950/70 transition-colors pointer-events-none z-0" />

    <!-- Top Bar with Metis Logo & Theme Toggle -->
    <header class="relative z-20 w-full px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 shadow-xs">
        <MetisLogo :show-text="true" />
      </div>

      <button
        @click="toggleTheme"
        type="button"
        class="p-2 rounded-xl bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 shadow-xs text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors focus:outline-none"
        :title="theme === 'dark' ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
      >
        <Sun v-if="theme === 'dark'" class="w-4.5 h-4.5 text-amber-400" />
        <Moon v-else class="w-4.5 h-4.5 text-blue-600" />
      </button>
    </header>

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-4 sm:py-8 max-w-6xl mx-auto w-full">
      <div class="text-center mb-6 max-w-xl px-4">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900/50 text-xs font-semibold mb-3">
          <span>Sistem Pakar Certainty Factor</span>
        </div>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white uppercase drop-shadow-xs">
          SIPAKAR STUNTING
        </h1>
        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 mt-1.5 leading-relaxed font-medium max-w-md mx-auto">
          Sistem Deteksi Dini & Manajemen Intervensi Tumbuh Kembang Balita Terintegrasi Standar WHO 2006.
        </p>
      </div>

      <!-- Center Floating Card -->
      <div class="w-full max-w-[400px] bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl shadow-zinc-900/5 dark:shadow-black/40 overflow-hidden transition-all">
        <!-- Card Body -->
        <div class="p-6 sm:p-7">
          <div class="mb-5">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
              Masuk ke Akun
            </h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
              Silakan masukkan kredensial Anda untuk melanjutkan
            </p>
          </div>

          <!-- Flash error alert -->
          <div v-if="$page.props.flash?.error" class="bg-red-50 dark:bg-red-500/15 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300 text-xs px-3.5 py-2.5 rounded-xl mb-4 flex items-center gap-2">
            <AlertCircle class="w-4 h-4 text-red-500 flex-shrink-0" />
            <span>{{ $page.props.flash.error }}</span>
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <!-- Email -->
            <div>
              <label class="block text-xs font-semibold text-zinc-800 dark:text-zinc-200 mb-1.5">
                Alamat Email
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="nama@posyandu.id"
                autocomplete="email"
                class="w-full bg-zinc-50/80 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all"
                :class="form.errors.email ? 'border-red-500 bg-red-50/50 dark:bg-red-500/10' : ''"
                required
              >
              <div v-if="form.errors.email" class="text-xs text-red-500 mt-1">
                {{ form.errors.email }}
              </div>
            </div>

            <!-- Password -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                  Kata Sandi
                </label>
              </div>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  autocomplete="current-password"
                  class="w-full bg-zinc-50/80 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 pr-10 transition-all"
                  :class="form.errors.password ? 'border-red-500 bg-red-50/50 dark:bg-red-500/10' : ''"
                  required
                >
                <button
                  type="button"
                  class="absolute right-3 top-2.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors focus:outline-none"
                  @click="showPassword = !showPassword"
                >
                  <EyeOff v-if="showPassword" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
              <div v-if="form.errors.password" class="text-xs text-red-500 mt-1">
                {{ form.errors.password }}
              </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between pt-0.5">
              <label class="flex items-center gap-2 cursor-pointer text-xs text-zinc-600 dark:text-zinc-400">
                <input
                  id="remember"
                  v-model="form.remember"
                  type="checkbox"
                  class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-600/30 dark:bg-zinc-800"
                >
                <span>Ingat saya</span>
              </label>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 sm:py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-xs transition-all active:scale-[0.99] disabled:opacity-50 flex items-center justify-center gap-1.5 mt-2"
            >
              <span v-if="form.processing">Memproses...</span>
              <span v-else class="flex items-center gap-1.5">
                <span>Masuk ke Akun</span>
                <ArrowRight class="w-4 h-4" />
              </span>
            </button>
          </form>
        </div>

        <!-- Card Bottom Section: Demo Quick Login -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4">
          <div class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2.5 text-center">
            Pilihan Akun Demo (1-Klik)
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <button
              v-for="akun in akunDemo"
              :key="akun.role"
              type="button"
              @click="isiAkun(akun)"
              class="px-2.5 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-medium text-center transition-colors shadow-2xs truncate"
            >
              {{ akun.label }}
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="relative z-10 w-full py-3 flex justify-center px-4">
      <div class="px-4 py-1.5 rounded-full bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 font-medium shadow-xs">
        Politeknik Negeri Jember & Posyandu Melati Pujer © 2026
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Sun, Moon, Eye, EyeOff, ArrowRight, AlertCircle } from 'lucide-vue-next'
import { useTheme } from '@/Composables/useTheme'
import MetisLogo from '@/Components/MetisLogo.vue'

const { theme, toggleTheme } = useTheme()
const showPassword = ref(false)

const form = useForm({
  email:    '',
  password: '',
  remember: false,
})

const akunDemo = [
  { role: 'admin', label: 'Admin', email: 'admin@posyandu.id', password: 'password' },
  { role: 'bidan', label: 'Bidan', email: 'bidan@posyandu.id', password: 'password' },
  { role: 'kader', label: 'Kader', email: 'kader@posyandu.id', password: 'password' },
  { role: 'ortu',  label: 'Orang Tua', email: 'ortu@posyandu.id', password: 'password' },
]

const isiAkun = (akun) => {
  form.email    = akun.email
  form.password = akun.password
}

const submit = () => {
  form.post(route('login'))
}
</script>
