<template>
  <Link
    :href="href"
    :title="!open ? label : undefined"
    :class="[
      'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group relative',
      active
        ? 'bg-orbit-primary/15 text-orbit-primary-light font-semibold shadow-sm'
        : 'text-slate-400 hover:text-slate-100 hover:bg-white/5'
    ]"
  >
    <div 
      v-if="active" 
      class="absolute left-0 top-2 bottom-2 w-1 bg-orbit-primary rounded-r-full shadow-sm shadow-purple-500/50" 
    />
    <span :class="['flex-shrink-0 transition-colors', active ? 'text-orbit-primary-light' : 'text-slate-400 group-hover:text-slate-200']">
      <slot name="icon">
        <span v-if="icon" class="text-base flex-shrink-0 w-5 text-center">{{ icon }}</span>
      </slot>
    </span>
    <span v-if="open" class="truncate flex-1 tracking-tight">{{ label }}</span>
    <slot name="badge" />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: String,
  active: Boolean,
  open: {
    type: Boolean,
    default: true
  },
  icon: String,
  label: String,
})
</script>