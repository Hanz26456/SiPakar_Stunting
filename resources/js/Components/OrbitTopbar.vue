<template>
  <header class="h-16 border-b border-orbit-border bg-orbit-surface/85 backdrop-blur-xl flex items-center px-4 sm:px-6 gap-3 sm:gap-4 flex-shrink-0 relative z-20">
    <!-- Left: Hamburger Toggle + Breadcrumbs / Title -->
    <div class="flex items-center gap-3 min-w-0">
      <button
        @click="$emit('toggle-sidebar')"
        type="button"
        class="p-2 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-white/5 active:bg-white/10 transition-colors focus:outline-none"
        title="Toggle Sidebar"
      >
        <Menu class="w-5 h-5" />
      </button>

      <div class="flex items-center gap-2 text-sm overflow-hidden whitespace-nowrap">
        <span class="text-slate-500 font-medium hidden sm:inline">SiPakar</span>
        <span class="text-slate-600 hidden sm:inline">/</span>
        <span v-if="roleLabel" class="text-slate-400 text-xs px-2 py-0.5 rounded-full bg-orbit-surface2 border border-orbit-border hidden md:inline">
          {{ roleLabel }}
        </span>
        <span class="text-slate-600 hidden md:inline">/</span>
        <h1 class="text-slate-100 font-semibold text-sm sm:text-base truncate">{{ title }}</h1>
      </div>
    </div>

    <!-- Extra actions slot -->
    <div class="ml-auto flex items-center gap-2 sm:gap-3">
      <slot name="extra" />

      <!-- Dark / Light Mode Toggle Button -->
      <button
        @click="toggleTheme"
        type="button"
        class="p-2 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-colors focus:outline-none relative group"
        :title="theme === 'dark' ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
      >
        <Sun v-if="theme === 'dark'" class="w-4.5 h-4.5 text-amber-400 group-hover:rotate-45 transition-transform duration-300" />
        <Moon v-else class="w-4.5 h-4.5 text-purple-400 group-hover:-rotate-12 transition-transform duration-300" />
      </button>

      <!-- Role Badge -->
      <div class="hidden lg:flex items-center px-2.5 py-1 rounded-lg bg-orbit-surface2 border border-orbit-border text-slate-300 text-xs font-medium">
        {{ roleName }}
      </div>

      <!-- User Dropdown Menu -->
      <div class="relative" ref="dropdownRef">
        <button
          @click="dropdownOpen = !dropdownOpen"
          type="button"
          class="flex items-center gap-2 p-1.5 sm:px-2.5 sm:py-1.5 rounded-xl hover:bg-white/5 border border-transparent hover:border-orbit-border transition-colors focus:outline-none group"
        >
          <div class="w-8 h-8 rounded-lg bg-orbit-surface2 border border-orbit-border flex items-center justify-center text-slate-200 text-xs font-semibold flex-shrink-0">
            {{ initials }}
          </div>
          <div class="hidden sm:block text-left max-w-[120px]">
            <div class="text-xs font-semibold text-slate-200 group-hover:text-white truncate">
              {{ userName }}
            </div>
            <div class="text-[10px] text-slate-500 capitalize leading-none">
              {{ userRole }}
            </div>
          </div>
          <ChevronDown class="w-3.5 h-3.5 text-slate-500 group-hover:text-slate-300 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" />
        </button>

        <!-- Dropdown Popup -->
        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="transform scale-95 opacity-0 -translate-y-1"
          enter-to-class="transform scale-100 opacity-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="transform scale-100 opacity-100 translate-y-0"
          leave-to-class="transform scale-95 opacity-0 -translate-y-1"
        >
          <div
            v-if="dropdownOpen"
            class="absolute right-0 mt-2 w-56 rounded-2xl bg-orbit-surface border border-orbit-border shadow-2xl p-2 z-50 text-slate-200"
          >
            <div class="px-3 py-2 border-b border-orbit-border/80 mb-1">
              <p class="text-xs font-semibold text-slate-100 truncate">{{ userName }}</p>
              <p class="text-[11px] text-slate-400 truncate">{{ userEmail }}</p>
            </div>

            <Link
              v-if="hasRoute('profile.edit')"
              :href="route('profile.edit')"
              @click="dropdownOpen = false"
              class="w-full flex items-center gap-2.5 px-3 py-2 text-xs rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-colors"
            >
              <User class="w-4 h-4 text-slate-400" />
              <span>Profil Saya</span>
            </Link>

            <Link
              :href="route('logout')"
              method="post"
              as="button"
              @click="dropdownOpen = false"
              class="w-full flex items-center gap-2.5 px-3 py-2 text-xs rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors text-left"
            >
              <LogOut class="w-4 h-4 text-red-400" />
              <span>Keluar dari Akun</span>
            </Link>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Menu, Sun, Moon, ChevronDown, User, LogOut } from 'lucide-vue-next'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  },
  roleLabel: {
    type: String,
    default: ''
  }
})

defineEmits(['toggle-sidebar'])

const { theme, toggleTheme } = useTheme()
const page = usePage()
const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const authUser = computed(() => page.props.auth?.user || {})
const userName = computed(() => authUser.value.name || 'Pengguna')
const userEmail = computed(() => authUser.value.email || '')
const userRole = computed(() => authUser.value.role || 'user')

const roleName = computed(() => {
  const r = authUser.value.role
  if (r === 'admin') return 'Administrator'
  if (r === 'bidan') return 'Tenaga Kesehatan (Bidan)'
  if (r === 'kader') return 'Kader Posyandu'
  if (r === 'ortu') return 'Orang Tua Balita'
  return 'Pengguna'
})

const initials = computed(() => {
  const name = userName.value
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const hasRoute = (routeName) => {
  try {
    return !!route().has(routeName)
  } catch (e) {
    return false
  }
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    dropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
