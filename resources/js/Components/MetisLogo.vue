<template>
  <div class="flex items-center gap-2.5">
    <!-- Metis Official Brand Mark -->
    <div class="relative flex-shrink-0">
      <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 rounded-lg shadow-xs">
        <rect width="32" height="32" rx="7" fill="#2563eb"/>
        <path d="M6 25V7h4l6 7 6-7h4v18h-4V13l-5 6h-2l-5-6v12H6z" fill="white"/>
      </svg>
    </div>

    <!-- Metis Brand Typography -->
    <div v-if="!collapsed" class="overflow-hidden transition-all duration-200 min-w-0">
      <div class="text-sm font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5 leading-tight">
        <span>Metis</span>
        <span class="text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-600 dark:text-blue-400">SiPakar</span>
      </div>
      <div class="text-[11px] text-slate-500 dark:text-zinc-400 truncate mt-0.5 font-medium leading-none">
        {{ subtitle || 'Posyandu Melati Pujer' }}
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  collapsed: {
    type: Boolean,
    default: false,
  },
  subtitle: {
    type: String,
    default: 'Posyandu Melati Pujer',
  },
})
</script>

