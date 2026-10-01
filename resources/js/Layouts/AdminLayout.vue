<template>
  <div class="flex h-screen bg-orbit-bg overflow-hidden font-sans antialiased relative selection:bg-purple-500 selection:text-white">
    <!-- Ambient Orbit Glow -->
    <div class="orbit-glow-bg" />

    <!-- Sidebar Backdrop for Mobile -->
    <Transition
      enter-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="mobileOpen"
        @click="mobileOpen = false"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
      />
    </Transition>

    <!-- Sidebar -->
    <aside
      :class="[
        'flex flex-col bg-orbit-surface/95 backdrop-blur-2xl border-r border-orbit-border transition-all duration-300 ease-in-out z-40 relative flex-shrink-0',
        sidebarOpen ? 'w-64' : 'w-[4.5rem]',
        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 fixed lg:relative h-full'
      ]"
    >
      <!-- Logo Area -->
      <div class="flex items-center justify-between px-4 h-16 border-b border-orbit-border flex-shrink-0">
        <OrbitLogo :collapsed="!sidebarOpen" subtitle="Panel Administrator" />

        <!-- Toggle / Close on mobile -->
        <button
          v-if="sidebarOpen"
          @click="toggleSidebar"
          type="button"
          class="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-colors focus:outline-none"
          title="Tutup / Lipat sidebar"
        >
          <PanelLeftClose class="w-4 h-4" />
        </button>
      </div>

      <!-- Role Badge -->
      <div v-if="sidebarOpen" class="px-4 pt-3 pb-1">
        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-orbit-surface2 text-slate-300 text-[11px] font-medium border border-orbit-border">
          Administrator
        </span>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
        <AdminNavItem
          :href="route('admin.dashboard')"
          :active="isActive('admin.dashboard')"
          :open="sidebarOpen"
          label="Dashboard"
        >
          <template #icon>
            <LayoutDashboard class="w-4.5 h-4.5" />
          </template>
        </AdminNavItem>

        <!-- Divider: Basis Pengetahuan -->
        <div class="pt-4 pb-1">
          <div v-if="sidebarOpen" class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">
            Basis Pengetahuan
          </div>
          <div v-else class="border-t border-orbit-border mx-2 my-2" />
        </div>

        <AdminNavItem
          :href="route('admin.gejala.index')"
          :active="isActive('admin.gejala')"
          :open="sidebarOpen"
          label="Kelola Gejala"
        >
          <template #icon>
            <Activity class="w-4.5 h-4.5" />
          </template>
        </AdminNavItem>

        <AdminNavItem
          :href="route('admin.rule-cf.index')"
          :active="isActive('admin.rule-cf')"
          :open="sidebarOpen"
          label="Kelola Rule CF"
        >
          <template #icon>
            <SlidersHorizontal class="w-4.5 h-4.5" />
          </template>
        </AdminNavItem>

        <!-- Divider: Sistem -->
        <div class="pt-4 pb-1">
          <div v-if="sidebarOpen" class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">
            Sistem & Akses
          </div>
          <div v-else class="border-t border-orbit-border mx-2 my-2" />
        </div>

        <AdminNavItem
          :href="route('admin.users.index')"
          :active="isActive('admin.users')"
          :open="sidebarOpen"
          label="Pengguna"
        >
          <template #icon>
            <Users class="w-4.5 h-4.5" />
          </template>
        </AdminNavItem>

        <AdminNavItem
          :href="route('admin.log')"
          :active="isActive('admin.log')"
          :open="sidebarOpen"
          label="Log Aktivitas"
        >
          <template #icon>
            <FileText class="w-4.5 h-4.5" />
          </template>
        </AdminNavItem>

        <AdminNavItem
          :href="route('admin.pengaturan')"
          :active="isActive('admin.pengaturan')"
          :open="sidebarOpen"
          label="Pengaturan"
        >
          <template #icon>
            <Settings class="w-4.5 h-4.5" />
          </template>
        </AdminNavItem>
      </nav>

      <!-- Bottom User Section -->
      <div class="border-t border-orbit-border p-3 flex-shrink-0 bg-orbit-surface2/30">
        <!-- Expand Button (when collapsed) -->
        <button
          v-if="!sidebarOpen"
          @click="sidebarOpen = true"
          type="button"
          class="w-full flex items-center justify-center p-2 rounded-xl text-slate-400 hover:bg-white/5 hover:text-white transition-colors"
          title="Buka sidebar"
        >
          <PanelLeftOpen class="w-4.5 h-4.5" />
        </button>

        <!-- User Profile Card -->
        <div v-if="sidebarOpen" class="flex items-center gap-3 px-2.5 py-2 rounded-xl hover:bg-white/5 transition-colors group">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-600 to-cyan-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm shadow-purple-500/30">
            {{ initials }}
          </div>
          <div class="overflow-hidden flex-1 min-w-0">
            <div class="text-xs font-semibold text-slate-200 truncate group-hover:text-white">{{ $page.props.auth.user.name }}</div>
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="text-[11px] text-slate-500 hover:text-red-400 transition-colors flex items-center gap-1 mt-0.5"
            >
              <span>Keluar</span>
            </Link>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0 relative z-10">
      <!-- Orbit Topbar -->
      <OrbitTopbar :title="title" role-label="Admin" @toggle-sidebar="handleToggle">
        <template #extra>
          <slot name="topbar-extra" />
        </template>
      </OrbitTopbar>

      <!-- Flash Messages -->
      <Transition 
        enter-active-class="transition ease-out duration-200" 
        enter-from-class="opacity-0 -translate-y-2" 
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150" 
        leave-from-class="opacity-100 translate-y-0" 
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="$page.props.flash?.success" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
          <div class="bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm px-4 py-3 rounded-2xl flex items-center justify-between shadow-lg shadow-emerald-500/5">
            <div class="flex items-center gap-2.5">
              <CheckCircle2 class="w-5 h-5 text-emerald-400 flex-shrink-0" />
              <span>{{ $page.props.flash.success }}</span>
            </div>
            <button @click="$page.props.flash.success = null" class="text-emerald-400 hover:text-emerald-200 transition-colors p-1 rounded-lg hover:bg-emerald-500/20">
              <X class="w-4 h-4" />
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
        <div v-if="$page.props.flash?.error" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
          <div class="bg-rose-500/15 border border-rose-500/30 text-rose-300 text-sm px-4 py-3 rounded-2xl flex items-center justify-between shadow-lg shadow-rose-500/5">
            <div class="flex items-center gap-2.5">
              <AlertCircle class="w-5 h-5 text-rose-400 flex-shrink-0" />
              <span>{{ $page.props.flash.error }}</span>
            </div>
            <button @click="$page.props.flash.error = null" class="text-rose-400 hover:text-rose-200 transition-colors p-1 rounded-lg hover:bg-rose-500/20">
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>
      </Transition>

      <!-- Main Slot Content -->
      <main class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  LayoutDashboard,
  Activity,
  SlidersHorizontal,
  Users,
  FileText,
  Settings,
  ShieldCheck,
  PanelLeftClose,
  PanelLeftOpen,
  CheckCircle2,
  AlertCircle,
  X
} from 'lucide-vue-next'
import AdminNavItem from '@/Components/AdminNavItem.vue'
import OrbitLogo from '@/Components/OrbitLogo.vue'
import OrbitTopbar from '@/Components/OrbitTopbar.vue'

defineProps({
  title: { type: String, default: 'Admin' }
})

const page = usePage()
const sidebarOpen = ref(true)
const mobileOpen = ref(false)

const handleToggle = () => {
  if (typeof window !== 'undefined' && window.innerWidth < 1024) {
    mobileOpen.value = !mobileOpen.value
  } else {
    sidebarOpen.value = !sidebarOpen.value
  }
}

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const initials = computed(() => {
  const name = page.props.auth?.user?.name ?? ''
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActive = (routeName) => route().current(routeName + '*')
</script>