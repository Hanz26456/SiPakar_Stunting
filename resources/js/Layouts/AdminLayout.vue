<template>
  <div class="flex h-screen bg-slate-50 overflow-hidden font-sans antialiased">
    
    <!-- Sidebar -->
    <aside
      :class="[
        'flex flex-col bg-slate-900 transition-all duration-300 ease-in-out z-30 relative flex-shrink-0',
        sidebarOpen ? 'w-60' : 'w-[4.5rem]'
      ]"
    >
      <!-- Logo Area -->
      <div class="flex items-center gap-3 px-4 h-16 border-b border-slate-800 flex-shrink-0">
        <div class="w-9 h-9 rounded-xl bg-slate-700 flex items-center justify-center flex-shrink-0 shadow-sm">
          <svg class="w-5 h-5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <div v-if="sidebarOpen" class="overflow-hidden transition-opacity duration-200">
          <div class="text-sm font-semibold text-slate-100 tracking-tight">SiPakar Stunting</div>
          <div class="text-xs text-slate-500 mt-0.5">Panel Administrator</div>
        </div>
        
        <!-- Toggle Button -->
        <button
          v-if="sidebarOpen"
          @click="sidebarOpen = false"
          class="ml-auto p-1 rounded-md text-slate-500 hover:text-slate-300 hover:bg-slate-800 transition-colors"
          title="Tutup sidebar"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
          </svg>
        </button>
      </div>

      <!-- Role Badge -->
      <div v-if="sidebarOpen" class="px-4 pt-4 pb-2">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 text-xs font-medium border border-slate-700">
          <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          Administrator
        </span>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
        <AdminNavItem
          :href="route('admin.dashboard')"
          :active="isActive('admin.dashboard')"
          :open="sidebarOpen"
          label="Dashboard"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
          </template>
        </AdminNavItem>

        <!-- Divider: Basis Pengetahuan -->
        <div class="pt-3 pb-1">
          <div v-if="sidebarOpen" class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Basis Pengetahuan</div>
          <div v-else class="border-t border-slate-800 mx-2 my-2"></div>
        </div>

        <AdminNavItem
          :href="route('admin.gejala.index')"
          :active="isActive('admin.gejala')"
          :open="sidebarOpen"
          label="Kelola Gejala"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
          </template>
        </AdminNavItem>

        <AdminNavItem
          :href="route('admin.rule-cf.index')"
          :active="isActive('admin.rule-cf')"
          :open="sidebarOpen"
          label="Kelola Rule CF"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </template>
        </AdminNavItem>

        <!-- Divider: Sistem -->
        <div class="pt-3 pb-1">
          <div v-if="sidebarOpen" class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Sistem</div>
          <div v-else class="border-t border-slate-800 mx-2 my-2"></div>
        </div>

        <AdminNavItem
          :href="route('admin.users.index')"
          :active="isActive('admin.users')"
          :open="sidebarOpen"
          label="Pengguna"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </template>
        </AdminNavItem>

        <AdminNavItem
          :href="route('admin.log')"
          :active="isActive('admin.log')"
          :open="sidebarOpen"
          label="Log Aktivitas"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </template>
        </AdminNavItem>

        <AdminNavItem
          :href="route('admin.pengaturan')"
          :active="isActive('admin.pengaturan')"
          :open="sidebarOpen"
          label="Pengaturan"
        >
          <template #icon>
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </template>
        </AdminNavItem>
      </nav>

      <!-- Bottom Area -->
      <div class="border-t border-slate-800 p-3 flex-shrink-0">
        <!-- Expand Button (when collapsed) -->
        <button
          v-if="!sidebarOpen"
          @click="sidebarOpen = true"
          class="w-full flex items-center justify-center p-2 rounded-lg text-slate-500 hover:bg-slate-800 hover:text-slate-300 transition-colors"
          title="Buka sidebar"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
          </svg>
        </button>

        <!-- User Profile -->
        <div v-if="sidebarOpen" class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-slate-800 transition-colors group">
          <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-slate-200 text-xs font-bold flex-shrink-0 border border-slate-600">
            {{ initials }}
          </div>
          <div class="overflow-hidden flex-1 min-w-0">
            <div class="text-xs font-medium text-slate-300 truncate">{{ $page.props.auth.user.name }}</div>
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="text-[11px] text-slate-500 hover:text-red-400 transition-colors"
            >
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
          <span class="inline-flex items-center gap-1.5 text-xs bg-slate-100 text-slate-600 px-3 py-1.5 rounded-full border border-slate-200 font-medium">
            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Admin
          </span>
          <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-200 text-xs font-bold border border-slate-700">
            {{ initials }}
          </div>
        </div>
      </header>

      <!-- Flash Messages -->
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
            <button @click="$page.props.flash.success = null" class="text-emerald-400 hover:text-emerald-600 transition-colors p-0.5 rounded hover:bg-emerald-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </Transition>

      <Transition 
        enter-active-class="transition ease-out duration-200" 
        enter-from-class="opacity-0 -translate-y-2" 
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150" 
        leave-from-class="opacity-100 translate-y-0" 
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="$page.props.flash?.error" class="mx-6 mt-4">
          <div class="bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-lg flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>{{ $page.props.flash.error }}</span>
            </div>
            <button @click="$page.props.flash.error = null" class="text-red-400 hover:text-red-600 transition-colors p-0.5 rounded hover:bg-red-100">
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
import AdminNavItem from '@/Components/AdminNavItem.vue'

defineProps({
  title: { type: String, default: 'Admin' }
})

const page = usePage()
const sidebarOpen = ref(true)

const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? ''
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActive = (routeName) => route().current(routeName + '*')
</script>