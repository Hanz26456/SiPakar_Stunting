<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 font-sans antialiased text-zinc-900 dark:text-zinc-100 flex flex-col transition-colors duration-150">

    <!-- Topbar (Fixed Header 56px / h-14) -->
    <MetisTopbar :title="title" role-label="Administrator" @toggle-sidebar="handleToggle">
      <template #extra>
        <slot name="topbar-extra" />
      </template>
    </MetisTopbar>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="mobileOpen"
        @click="mobileOpen = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-xs z-30 lg:hidden"
      />
    </Transition>

    <div class="flex-1 flex relative">

      <!-- Metis Sidebar -->
      <aside
        :class="[
          'admin-sidebar flex flex-col bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 transition-all duration-200 z-35',
          sidebarOpen ? 'w-60' : 'w-16',
          mobileOpen ? 'sidebar-mobile-open' : ''
        ]"
      >
        <!-- Sidebar Brand / Header -->
        <div class="h-14 px-4 flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 flex-shrink-0">
          <MetisLogo :collapsed="!sidebarOpen" subtitle="Panel Administrator" />

          <button
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            type="button"
            class="hidden lg:flex p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
            title="Kecilkan Sidebar"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
          <!-- Main Menu -->
          <AdminNavItem
            :href="route('admin.dashboard')"
            :active="isActive('admin.dashboard')"
            :open="sidebarOpen"
            label="Dashboard"
          >
            <template #icon>
              <LayoutDashboard class="w-4 h-4" />
            </template>
          </AdminNavItem>

          <!-- Group: Basis Pengetahuan -->
          <div class="pt-4 pb-1">
            <div v-if="sidebarOpen" class="px-3 text-[11px] font-semibold text-zinc-400 uppercase tracking-wider">
              Basis Pengetahuan
            </div>
            <div v-else class="border-t border-zinc-200 dark:border-zinc-800 mx-1 my-2" />
          </div>

          <AdminNavItem
            :href="route('admin.gejala.index')"
            :active="isActive('admin.gejala')"
            :open="sidebarOpen"
            label="Kelola Gejala"
          >
            <template #icon>
              <Activity class="w-4 h-4" />
            </template>
          </AdminNavItem>

          <AdminNavItem
            :href="route('admin.rule-cf.index')"
            :active="isActive('admin.rule-cf')"
            :open="sidebarOpen"
            label="Basis Rule CF"
          >
            <template #icon>
              <SlidersHorizontal class="w-4 h-4" />
            </template>
          </AdminNavItem>

          <!-- Group: Pengguna & Audit -->
          <div class="pt-4 pb-1">
            <div v-if="sidebarOpen" class="px-3 text-[11px] font-semibold text-zinc-400 uppercase tracking-wider">
              Sistem & Akses
            </div>
            <div v-else class="border-t border-zinc-200 dark:border-zinc-800 mx-1 my-2" />
          </div>

          <AdminNavItem
            :href="route('admin.users.index')"
            :active="isActive('admin.users')"
            :open="sidebarOpen"
            label="Data Pengguna"
          >
            <template #icon>
              <Users class="w-4 h-4" />
            </template>
          </AdminNavItem>

          <AdminNavItem
            :href="route('admin.log')"
            :active="isActive('admin.log')"
            :open="sidebarOpen"
            label="Log Audit Sistem"
          >
            <template #icon>
              <FileText class="w-4 h-4" />
            </template>
          </AdminNavItem>

          <AdminNavItem
            :href="route('admin.pengaturan')"
            :active="isActive('admin.pengaturan')"
            :open="sidebarOpen"
            label="Pengaturan"
          >
            <template #icon>
              <Settings class="w-4 h-4" />
            </template>
          </AdminNavItem>
        </nav>

        <!-- Sidebar Footer / Expand Toggle -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 p-3 bg-zinc-50/70 dark:bg-zinc-950/40 flex-shrink-0">
          <button
            v-if="!sidebarOpen"
            @click="sidebarOpen = true"
            type="button"
            class="w-full flex items-center justify-center p-2 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
            title="Perluas Sidebar"
          >
            <ChevronRight class="w-4 h-4" />
          </button>
          <div v-else class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 px-1">
            <span class="font-medium">Metis v3.6</span>
            <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-200/80 dark:bg-zinc-800 font-mono">Laravel 12</span>
          </div>
        </div>
      </aside>

      <!-- Main Content Area -->
      <main
        class="admin-main flex-1 transition-all duration-200 flex flex-col justify-between"
        :style="{ marginLeft: sidebarOpen ? '240px' : '64px' }"
      >
        <div class="space-y-5">
          <!-- Flash Notifications in Metis Alert Style -->
          <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
          >
            <div v-if="$page.props.flash?.success" class="p-3.5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm flex items-center justify-between shadow-2xs font-medium">
              <div class="flex items-center gap-2.5">
                <CheckCircle2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" />
                <span>{{ $page.props.flash.success }}</span>
              </div>
              <button @click="$page.props.flash.success = null" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-md">
                <X class="w-3.5 h-3.5" />
              </button>
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
            <div v-if="$page.props.flash?.error" class="p-3.5 rounded-xl border border-rose-500/20 bg-rose-500/10 text-rose-800 dark:text-rose-200 text-xs sm:text-sm flex items-center justify-between shadow-2xs font-medium">
              <div class="flex items-center gap-2.5">
                <AlertCircle class="w-4 h-4 text-rose-600 dark:text-rose-400 flex-shrink-0" />
                <span>{{ $page.props.flash.error }}</span>
              </div>
              <button @click="$page.props.flash.error = null" class="text-rose-500 hover:text-rose-700 p-1 rounded-md">
                <X class="w-3.5 h-3.5" />
              </button>
            </div>
          </Transition>

          <!-- Slot for Main Page Views -->
          <slot />
        </div>

        <!-- Metis Official Footer -->
        <footer class="mt-10 pt-4 border-t border-zinc-200 dark:border-zinc-800 text-xs text-zinc-500 dark:text-zinc-400 flex flex-col sm:flex-row items-center justify-between gap-2">
          <div>
            &copy; 2026 <strong class="font-semibold text-zinc-700 dark:text-zinc-300">Metis Admin</strong> &mdash; Sistem Pakar Stunting Posyandu Melati Pujer, Bondowoso.
          </div>
          <div class="flex items-center gap-3 text-[11px]">
            <span>Certainty Factor & Standar WHO</span>
            <span>&bull;</span>
            <span>Politeknik Negeri Jember</span>
          </div>
        </footer>
      </main>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import {
  LayoutDashboard,
  Activity,
  SlidersHorizontal,
  Users,
  FileText,
  Settings,
  ChevronLeft,
  ChevronRight,
  CheckCircle2,
  AlertCircle,
  X
} from 'lucide-vue-next'
import AdminNavItem from '@/Components/AdminNavItem.vue'
import MetisLogo from '@/Components/MetisLogo.vue'
import MetisTopbar from '@/Components/MetisTopbar.vue'

defineProps({
  title: { type: String, default: 'Admin' }
})

const sidebarOpen = ref(true)
const mobileOpen = ref(false)

const handleToggle = () => {
  if (typeof window !== 'undefined' && window.innerWidth < 1024) {
    mobileOpen.value = !mobileOpen.value
  } else {
    sidebarOpen.value = !sidebarOpen.value
  }
}

const isActive = (routeName) => route().current(routeName + '*')
</script>
