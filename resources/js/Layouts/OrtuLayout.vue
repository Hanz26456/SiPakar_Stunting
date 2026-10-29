<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased transition-colors duration-150">

    <!-- Top bar -->
    <header class="bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 px-4 py-3 flex items-center justify-between sticky top-0 z-30 transition-colors">
      <MetisLogo :collapsed="false" subtitle="Orang Tua Balita" />

      <div class="flex items-center gap-2 sm:gap-3">
        <!-- Theme toggle -->
        <button
          @click="toggleTheme"
          type="button"
          class="p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
          :title="theme === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
        >
          <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400" />
          <Moon v-else class="w-4 h-4 text-blue-600" />
        </button>

        <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-700 dark:text-zinc-200 text-xs font-bold">
          {{ initials }}
        </div>

        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="p-2 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors"
          title="Keluar"
        >
          <LogOut class="w-4 h-4" />
        </Link>
      </div>
    </header>

    <!-- Content -->
    <main class="pb-24 pt-4 px-4 sm:px-6 max-w-4xl mx-auto relative z-10">
      <slot />
    </main>

    <!-- Bottom nav (Mobile/App feel) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border-t border-zinc-200 dark:border-zinc-800 px-4 py-2 flex justify-around z-30 shadow-lg">
      <Link
        v-for="item in navItems"
        :key="item.route"
        :href="route(item.route)"
        class="flex flex-col items-center gap-1 px-4 py-1.5 rounded-lg transition-colors"
        :class="isActive(item.route) ? 'text-blue-600 dark:text-blue-400 font-semibold bg-blue-50/70 dark:bg-blue-950/30' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
      >
        <component :is="item.icon" class="w-4 h-4" />
        <span class="text-[11px] tracking-tight font-medium">{{ item.label }}</span>
      </Link>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Home, LineChart, BookOpen, Sun, Moon, LogOut } from 'lucide-vue-next'
import MetisLogo from '@/Components/MetisLogo.vue'
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
