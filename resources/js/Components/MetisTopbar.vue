<template>
  <header class="h-14 border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md flex items-center px-4 sm:px-6 gap-3 sm:gap-4 flex-shrink-0 relative z-30 transition-colors">
    <!-- Left: Hamburger Menu Toggle + Breadcrumb / Title -->
    <div class="flex items-center gap-3 min-w-0">
      <button
        @click="$emit('toggle-sidebar')"
        type="button"
        class="p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
        title="Toggle Sidebar"
      >
        <Menu class="w-5 h-5" />
      </button>

      <div class="flex items-center gap-2 text-xs sm:text-sm overflow-hidden whitespace-nowrap">
        <span class="text-zinc-400 font-semibold hidden sm:inline">Metis</span>
        <span class="text-zinc-300 dark:text-zinc-700 hidden sm:inline">/</span>
        <span v-if="roleLabel" class="text-zinc-500 dark:text-zinc-400 text-xs px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hidden md:inline font-medium">
          {{ roleLabel }}
        </span>
        <span class="text-zinc-300 dark:text-zinc-700 hidden md:inline">/</span>
        <h1 class="text-zinc-900 dark:text-zinc-100 font-bold text-xs sm:text-sm truncate">{{ title }}</h1>
      </div>
    </div>

    <!-- Center: Global Search (Ctrl+K) -->
    <div class="hidden lg:flex flex-1 max-w-md mx-4">
      <div class="relative w-full">
        <Search class="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
        <input
          ref="searchInputRef"
          type="search"
          v-model="searchQuery"
          @keydown.enter="handleSearch"
          placeholder="Cari data di sistem... (Ctrl+K)"
          class="w-full bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 rounded-lg pl-9 pr-12 py-1.5 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors"
        >
        <kbd class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-400">
          ⌘K
        </kbd>
      </div>
    </div>

    <!-- Right: Actions (Theme, Fullscreen, Notifications, User Profile) -->
    <div class="ml-auto flex items-center gap-2 sm:gap-2.5">
      <slot name="extra" />

      <!-- Fullscreen Toggle -->
      <button
        @click="toggleFullscreen"
        type="button"
        class="hidden sm:flex p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
        title="Toggle Fullscreen"
      >
        <Minimize2 v-if="isFullscreen" class="w-4 h-4" />
        <Maximize2 v-else class="w-4 h-4" />
      </button>

      <!-- Theme Switcher (Dark / Light Mode) -->
      <button
        @click="toggleTheme"
        type="button"
        class="p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none relative group"
        :title="theme === 'dark' ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
      >
        <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400 group-hover:rotate-45 transition-transform" />
        <Moon v-else class="w-4 h-4 text-blue-600 group-hover:-rotate-12 transition-transform" />
      </button>

      <!-- Notifications Dropdown -->
      <div class="relative" ref="notifRef">
        <button
          @click="notifOpen = !notifOpen"
          type="button"
          class="p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none relative"
          title="Notifikasi"
        >
          <Bell class="w-4 h-4" />
          <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-blue-600 ring-2 ring-white dark:ring-zinc-900" />
        </button>

        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div
            v-if="notifOpen"
            class="absolute right-0 mt-2 w-72 sm:w-80 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden z-50"
          >
            <div class="p-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Notifikasi Sistem</span>
              <span class="text-[11px] font-semibold text-blue-600 dark:text-blue-400">SiPakar Stunting</span>
            </div>
            <div class="p-3 divide-y divide-zinc-100 dark:divide-zinc-800/80 text-xs">
              <div class="py-2 first:pt-0">
                <div class="font-semibold text-zinc-800 dark:text-zinc-200">Basis Pengetahuan CF Aktif</div>
                <div class="text-zinc-500 dark:text-zinc-400 text-[11px] mt-0.5">Metode Certainty Factor dan Standar WHO terkalibrasi normal.</div>
              </div>
              <div class="py-2 last:pb-0">
                <div class="font-semibold text-zinc-800 dark:text-zinc-200">Sistem Posyandu Melati</div>
                <div class="text-zinc-500 dark:text-zinc-400 text-[11px] mt-0.5">Template modern Metis siap digunakan untuk operasional.</div>
              </div>
            </div>
          </div>
        </Transition>
      </div>

      <!-- User Menu Dropdown -->
      <div class="relative" ref="dropdownRef">
        <button
          @click="dropdownOpen = !dropdownOpen"
          type="button"
          class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 transition-colors focus:outline-none group"
        >
          <img
            src="/assets/images/avatar-placeholder.svg"
            alt="User Avatar"
            class="w-7 h-7 rounded-full flex-shrink-0"
          >
          <div class="hidden sm:block text-left max-w-[120px]">
            <div class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate">
              {{ userName }}
            </div>
            <div class="text-[10px] text-zinc-500 capitalize leading-none font-medium">
              {{ userRole }}
            </div>
          </div>
          <ChevronDown class="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-200 transition-transform" :class="{ 'rotate-180': dropdownOpen }" />
        </button>

        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div
            v-if="dropdownOpen"
            class="absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xl py-1.5 z-50 text-xs"
          >
            <div class="px-3.5 py-2 border-b border-zinc-100 dark:border-zinc-800">
              <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ userName }}</div>
              <div class="text-[11px] text-zinc-500 truncate mt-0.5">{{ userEmail }}</div>
            </div>

            <div class="py-1">
              <Link
                :href="route('profile.edit')"
                class="flex items-center gap-2.5 px-3.5 py-2 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-blue-600 transition-colors font-medium"
              >
                <User class="w-3.5 h-3.5" />
                <span>Pengaturan Profil</span>
              </Link>
            </div>

            <div class="border-t border-zinc-100 dark:border-zinc-800 pt-1">
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors font-medium text-left"
              >
                <LogOut class="w-3.5 h-3.5" />
                <span>Keluar Akun (Logout)</span>
              </Link>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  Menu,
  Search,
  Sun,
  Moon,
  Maximize2,
  Minimize2,
  Bell,
  ChevronDown,
  User,
  LogOut
} from 'lucide-vue-next'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard',
  },
  roleLabel: {
    type: String,
    default: '',
  },
})

defineEmits(['toggle-sidebar'])

const page = usePage()
const { theme, toggleTheme } = useTheme()

const dropdownOpen = ref(false)
const notifOpen = ref(false)
const isFullscreen = ref(false)
const searchQuery = ref('')
const dropdownRef = ref(null)
const notifRef = ref(null)
const searchInputRef = ref(null)

const authUser = computed(() => page.props.auth?.user ?? {})
const userName = computed(() => authUser.value.name || 'User')
const userEmail = computed(() => authUser.value.email || '')
const userRole = computed(() => authUser.value.role || 'user')

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(() => {})
    isFullscreen.value = true
  } else {
    document.exitFullscreen().catch(() => {})
    isFullscreen.value = false
  }
}

const handleSearch = () => {
  if (!searchQuery.value) return
  // Global search trigger or navigate
}

const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    dropdownOpen.value = false
  }
  if (notifRef.value && !notifRef.value.contains(e.target)) {
    notifOpen.value = false
  }
}

const handleKeydown = (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault()
    searchInputRef.value?.focus()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleKeydown)
  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement
  })
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleKeydown)
})
</script>

