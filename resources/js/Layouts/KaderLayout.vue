<template>
  <div class="flex h-screen bg-slate-50 overflow-hidden font-sans antialiased">
    
    <!-- Sidebar -->
    <aside
      :class="[
        'flex flex-col bg-white border-r border-slate-200 transition-all duration-300 ease-in-out z-30 relative',
        sidebarOpen ? 'w-60' : 'w-[4.5rem]'
      ]"
    >
      <!-- Logo Area -->
      <div class="flex items-center gap-3 px-4 h-16 border-b border-slate-100 flex-shrink-0">
        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div v-if="sidebarOpen" class="overflow-hidden transition-opacity duration-200">
          <div class="text-sm font-semibold text-slate-800 tracking-tight">SiPakar Stunting</div>
          <div class="text-xs text-slate-500 mt-0.5">Posyandu Melati Pujer</div>
        </div>
        
        <!-- Toggle Button -->
        <button
          v-if="sidebarOpen"
          @click="sidebarOpen = false"
          class="ml-auto p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
          title="Tutup sidebar"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
          </svg>
        </button>
      </div>

      <!-- Role Badge -->
      <div v-if="sidebarOpen" class="px-4 pt-4 pb-2">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          Kader
        </span>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
        <NavItem 
          :href="route('kader.dashboard')" 
          :active="isActive('kader.dashboard')" 
          :open="sidebarOpen" 
          label="Dashboard"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
          </template>
        </NavItem>

        <NavItem 
          :href="route('kader.balita.index')" 
          :active="isActive('kader.balita')" 
          :open="sidebarOpen" 
          label="Data Balita"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </template>
        </NavItem>

        <NavItem 
          :href="route('kader.kunjungan.index')" 
          :active="isActive('kader.kunjungan')" 
          :open="sidebarOpen" 
          label="Kunjungan"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
          </template>
        </NavItem>

        <NavItem 
          :href="route('kader.kunjungan.create')" 
          :active="isActive('kader.kunjungan.create')" 
          :open="sidebarOpen" 
          label="Input Kunjungan"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
          </template>
        </NavItem>

        <!-- Divider -->
        <div class="pt-3 pb-1">
          <div v-if="sidebarOpen" class="px-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Riwayat</div>
          <div v-else class="border-t border-slate-100 mx-2 my-2"></div>
        </div>

        <NavItem 
          :href="route('kader.kunjungan.index')" 
          :active="false" 
          :open="sidebarOpen" 
          label="Riwayat Diagnosis"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </template>
        </NavItem>
      </nav>

      <!-- Bottom Area -->
      <div class="border-t border-slate-100 p-3 flex-shrink-0">
        <!-- Expand Button (when collapsed) -->
        <button
          v-if="!sidebarOpen"
          @click="sidebarOpen = true"
          class="w-full flex items-center justify-center p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
          title="Buka sidebar"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
          </svg>
        </button>

        <!-- User Profile -->
        <div v-if="sidebarOpen" class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-slate-50 transition-colors group">
          <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold flex-shrink-0 border border-emerald-200">
            {{ initials }}
          </div>
          <div class="overflow-hidden flex-1 min-w-0">
            <div class="text-xs font-medium text-slate-700 truncate">{{ $page.props.auth.user.name }}</div>
            <Link :href="route('logout')" method="post" as="button" class="text-[11px] text-slate-400 hover:text-red-500 transition-colors">
              Keluar
            </Link>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0 bg-white">
      <!-- Header -->
      <header class="bg-white border-b border-slate-100 px-6 h-14 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
          <h1 class="text-base font-semibold text-slate-800 tracking-tight">{{ title }}</h1>
          <slot name="topbar-extra" />
        </div>
        <div class="flex items-center gap-3">
          <span class="inline-flex items-center gap-1.5 text-xs bg-slate-50 text-slate-600 px-3 py-1.5 rounded-full border border-slate-100">
            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pujer, Bondowoso
          </span>
          <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 text-xs font-bold border border-emerald-100">
            {{ initials }}
          </div>
        </div>
      </header>

      <!-- Flash Message -->
      <Transition 
        enter-active-class="transition ease-out duration-200" 
        enter-from-class="opacity-0 -translate-y-2" 
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150" 
        leave-from-class="opacity-100 translate-y-0" 
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="$page.props.flash?.success" class="mx-6 mt-4">
          <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-lg flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>{{ $page.props.flash.success }}</span>
            </div>
            <button @click="dismissFlash" class="text-emerald-400 hover:text-emerald-600 transition-colors p-0.5 rounded hover:bg-emerald-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </Transition>

      <!-- Content -->
      <main class="flex-1 overflow-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavItem from '@/Components/NavItem.vue'

defineProps({ title: String })

const page = usePage()
const sidebarOpen = ref(true)

const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? ''
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActive = (routeName) => {
  return route().current(routeName + '*')
}

const dismissFlash = () => {
  page.props.flash.success = null
}
</script>