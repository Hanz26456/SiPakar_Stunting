<template>
  <div class="flex h-screen bg-gray-50 overflow-hidden">

    <!-- Sidebar -->
    <aside
      :class="[
        'flex flex-col bg-white border-r border-gray-200 transition-all duration-300 z-30',
        sidebarOpen ? 'w-56' : 'w-16'
      ]"
    >
      <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
          <span class="text-white text-xs font-bold">SP</span>
        </div>
        <div v-if="sidebarOpen" class="overflow-hidden">
          <div class="text-sm font-semibold text-gray-800 whitespace-nowrap">SiPakar Stunting</div>
          <div class="text-xs text-gray-400 whitespace-nowrap">Posyandu Melati Pujer</div>
        </div>
      </div>

      <div v-if="sidebarOpen" class="mx-3 mt-3 mb-1">
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
          <span>👩‍⚕️</span> Tenaga Kesehatan
        </span>
      </div>

      <nav class="flex-1 px-2 py-2 space-y-1">
        <NavItem :href="route('bidan.dashboard')" :active="isActive('bidan.dashboard')" :open="sidebarOpen" icon="🏠" label="Dashboard" />
        <NavItem :href="route('bidan.monitoring')" :active="isActive('bidan.monitoring')" :open="sidebarOpen" icon="📡" label="Monitoring" />

        <div class="pt-2 pb-1">
          <div v-if="sidebarOpen" class="px-2 text-xs text-gray-400 uppercase tracking-wider">Tindakan</div>
          <div v-else class="border-t border-gray-100 mx-1 my-1"></div>
        </div>

        <NavItem :href="route('bidan.monitoring')" :active="false" :open="sidebarOpen" icon="✅" label="Verifikasi">
          <template #badge>
            <span v-if="pendingCount > 0" class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-5 text-center">
              {{ pendingCount }}
            </span>
          </template>
        </NavItem>
        <NavItem :href="route('bidan.laporan')" :active="isActive('bidan.laporan')" :open="sidebarOpen" icon="📊" label="Laporan" />
      </nav>

      <div class="border-t border-gray-100 p-2">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="w-full flex items-center gap-2 px-2 py-2 rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-colors"
        >
          <span class="text-base">{{ sidebarOpen ? '◀' : '▶' }}</span>
          <span v-if="sidebarOpen" class="text-xs">Sembunyikan</span>
        </button>
        <div v-if="sidebarOpen" class="flex items-center gap-2 px-2 py-2 mt-1">
          <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold flex-shrink-0">
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
      <header class="bg-white border-b border-gray-200 px-5 h-13 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
          <h1 class="text-base font-semibold text-gray-800">{{ title }}</h1>
          <slot name="topbar-extra" />
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-full">
            📍 Puskesmas Pujer
          </span>
          <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold">
            {{ initials }}
          </div>
        </div>
      </header>

      <div v-if="$page.props.flash?.success" class="mx-5 mt-4">
        <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm px-4 py-3 rounded-lg flex items-center justify-between">
          <span>✅ {{ $page.props.flash.success }}</span>
          <button @click="$page.props.flash.success = null" class="text-blue-400 hover:text-blue-600">✕</button>
        </div>
      </div>

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

defineProps({ title: String, pendingCount: { type: Number, default: 0 } })
const page = usePage()
const sidebarOpen = ref(true)
const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})
const isActive = (r) => route().current(r + '*')
</script>