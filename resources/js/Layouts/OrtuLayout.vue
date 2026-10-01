<template>
  <div class="min-h-screen bg-orbit-bg relative font-sans antialiased selection:bg-purple-500 selection:text-white">
    <!-- Ambient Orbit Glow -->
    <div class="orbit-glow-bg" />

    <!-- Top bar -->
    <header class="bg-orbit-surface/85 backdrop-blur-xl border-b border-orbit-border px-4 py-3 flex items-center justify-between sticky top-0 z-30">
      <OrbitLogo :collapsed="false" subtitle="Posyandu Melati Pujer" />

      <div class="flex items-center gap-2 sm:gap-3">
        <!-- Theme toggle -->
        <button
          @click="toggleTheme"
          type="button"
          class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-colors focus:outline-none"
          :title="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
        >
          <Sun v-if="theme === 'dark'" class="w-4.5 h-4.5 text-amber-400" />
          <Moon v-else class="w-4.5 h-4.5 text-purple-400" />
        </button>

        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-600 to-purple-600 flex items-center justify-center text-white text-xs font-bold shadow-sm shadow-emerald-500/20">
          {{ initials }}
        </div>

        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="p-2 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors"
          title="Keluar"
        >
          <LogOut class="w-4.5 h-4.5" />
        </Link>
      </div>
    </header>

    <!-- Content -->
    <main class="pb-24 pt-4 px-4 sm:px-6 max-w-4xl mx-auto relative z-10">
      <slot />
    </main>

    <!-- Bottom nav (Mobile/App feel) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-orbit-surface/90 backdrop-blur-2xl border-t border-orbit-border px-4 py-2 flex justify-around z-30 shadow-2xl">
      <Link
        v-for="item in navItems"
        :key="item.route"
        :href="route(item.route)"
        class="flex flex-col items-center gap-1 px-4 py-1.5 rounded-xl transition-all duration-150"
        :class="isActive(item.route) ? 'text-orbit-primary-light font-semibold bg-orbit-primary/10' : 'text-slate-400 hover:text-slate-200'"
      >
        <component :is="item.icon" class="w-5 h-5" />
        <span class="text-[11px] tracking-tight">{{ item.label }}</span>
      </Link>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Home, LineChart, BookOpen, Sun, Moon, LogOut } from 'lucide-vue-next'
import OrbitLogo from '@/Components/OrbitLogo.vue'
import { useTheme } from '@/Composables/useTheme'

defineProps({ title: String })

const page = usePage()
const { theme, toggleTheme } = useTheme()

const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActive = (r) => {
  try {
    return route().current(r + '*')
  } catch (e) {
    return false
  }
}

const navItems = [
  { route: 'ortu.dashboard', icon: Home, label: 'Beranda' },
  { route: 'ortu.anak', icon: LineChart, label: 'Pertumbuhan' },
  { route: 'ortu.panduan', icon: BookOpen, label: 'Panduan Gizi' },
]
</script>