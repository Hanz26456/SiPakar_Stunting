<template>
  <div class="flex items-center gap-3">
    <div class="relative flex-shrink-0">
      <div class="w-9 h-9 rounded-xl bg-orbit-primary flex items-center justify-center glow-primary shadow-sm shadow-purple-500/30">
        <svg viewBox="0 0 32 32" fill="none" class="w-5 h-5">
          <circle cx="16" cy="16" r="4" fill="white" />
          <ellipse cx="16" cy="16" rx="11" ry="5" stroke="white" stroke-width="1.5" stroke-opacity="0.7" transform="rotate(-30 16 16)" />
          <circle cx="23" cy="11" r="2" fill="#06B6D4" />
        </svg>
      </div>
    </div>
    <div v-if="!collapsed" class="overflow-hidden transition-all duration-200 min-w-0">
      <div class="text-sm font-bold text-slate-100 tracking-tight flex items-center gap-1.5">
        <span>SiPakar Stunting</span>
      </div>
      <div class="text-[11px] text-slate-400 truncate mt-0.5">{{ subtitle || 'Posyandu Melati Pujer' }}</div>
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
