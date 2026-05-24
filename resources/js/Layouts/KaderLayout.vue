<template>
  <div class="flex h-screen bg-gray-50 overflow-hidden">

    <!-- Sidebar -->
    <aside
      :class="[
        'flex flex-col bg-white border-r border-gray-200 transition-all duration-300 z-30',
        sidebarOpen ? 'w-56' : 'w-16'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center flex-shrink-0">
          <span class="text-white text-xs font-bold">SP</span>
        </div>
        <div v-if="sidebarOpen" class="overflow-hidden">
          <div class="text-sm font-semibold text-gray-800 whitespace-nowrap">SiPakar Stunting</div>
          <div class="text-xs text-gray-400 whitespace-nowrap">Posyandu Melati Pujer</div>
        </div>
      </div>

      <!-- Role badge -->
      <div v-if="sidebarOpen" class="mx-3 mt-3 mb-1">
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium">
          <span>👩</span> Kader
        </span>
      </div>

      <!-- Nav -->
      <nav class="flex-1 px-2 py-2 space-y-1">
        <NavItem :href="route('kader.dashboard')" :active="isActive('kader.dashboard')" :open="sidebarOpen" icon="🏠" label="Dashboard" />
        <NavItem :href="route('kader.balita.index')" :active="isActive('kader.balita')" :open="sidebarOpen" icon="👶" label="Data Balita" />
        <NavItem :href="route('kader.kunjungan.index')" :active="isActive('kader.kunjungan')" :open="sidebarOpen" icon="📋" label="Kunjungan" />
        <NavItem :href="route('kader.kunjungan.create')" :active="isActive('kader.kunjungan.create')" :open="sidebarOpen" icon="➕" label="Input Kunjungan" />

        <div class="pt-2 pb-1">
          <div v-if="sidebarOpen" class="px-2 text-xs text-gray-400 uppercase tracking-wider">Riwayat</div>
          <div v-else class="border-t border-gray-100 mx-1 my-1"></div>
        </div>

        <NavItem :href="route('kader.kunjungan.index')" :active="false" :open="sidebarOpen" icon="📊" label="Riwayat Diagnosis" />
      </nav>

      <!-- Toggle + User -->
      <div class="border-t border-gray-100 p-2">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="w-full flex items-center gap-2 px-2 py-2 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors"
        >
          <span class="text-base">{{ sidebarOpen ? '◀' : '▶' }}</span>
          <span v-if="sidebarOpen" class="text-xs">Sembunyikan</span>
        </button>
        <div v-if="sidebarOpen" class="flex items-center gap-2 px-2 py-2 mt-1">
          <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold flex-shrink-0">
            {{ initials }}
          </div>
          <div class="overflow-hidden flex-1">
            <div class="text-xs font-medium text-gray-700 truncate">{{ $page.props.auth.user.name }}</div>
            <Link :href="route('logout')" method="post" as="button" class="text-xs text-gray-400 hover:text-red-500">Keluar</Link>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

      <!-- Topbar -->
      <header class="bg-white border-b border-gray-200 px-5 h-13 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
          <h1 class="text-base font-semibold text-gray-800">{{ title }}</h1>
          <slot name="topbar-extra" />
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-1 rounded-full">
            📍 Pujer, Bondowoso
          </span>
          <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">
            {{ initials }}
          </div>
        </div>
      </header>

      <!-- Flash message -->
      <div v-if="$page.props.flash?.success" class="mx-5 mt-4">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-lg flex items-center justify-between">
          <span>✅ {{ $page.props.flash.success }}</span>
          <button @click="dismissFlash" class="text-emerald-400 hover:text-emerald-600">✕</button>
        </div>
      </div>

      <!-- Content -->
      <main class="flex-1 overflow-auto p-5">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavItem from '@/Components/NavItem.vue'

const props = defineProps({ title: String })
const page = usePage()
const sidebarOpen = ref(true)

const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActive = (routeName) => {
  return route().current(routeName + '*')
}

const dismissFlash = () => {
  page.props.flash.success = null
}
</script>
