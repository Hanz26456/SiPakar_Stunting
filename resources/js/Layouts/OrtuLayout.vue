<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Top bar -->
    <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
      <div>
        <div class="text-sm font-semibold text-gray-800">SiPakar Stunting</div>
        <div class="text-xs text-gray-400">Posyandu Melati Pujer</div>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">
          {{ initials }}
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="pb-20">
      <slot />
    </main>

    <!-- Bottom nav -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-2 py-2 flex justify-around">
      <Link
        v-for="item in navItems" :key="item.route"
        :href="route(item.route)"
        class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg"
        :class="isActive(item.route) ? 'text-emerald-600' : 'text-gray-400'"
      >
        <span class="text-xl">{{ item.icon }}</span>
        <span class="text-xs">{{ item.label }}</span>
      </Link>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

defineProps({ title: String })
const page = usePage()
const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})
const isActive = (r) => route().current(r + '*')
const navItems = [
  { route: 'ortu.dashboard', icon: '🏠', label: 'Beranda' },
  { route: 'ortu.anak', icon: '📈', label: 'Tumbuh kembang' },
  { route: 'ortu.panduan', icon: '💡', label: 'Panduan' },
]
</script>