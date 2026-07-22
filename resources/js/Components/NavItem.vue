<template>
  <Link 
    :href="href" 
    :class="[
      'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group relative',
      active 
        ? 'bg-blue-50 text-blue-700 border border-blue-100' 
        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
    ]"
  >
    <span :class="['flex-shrink-0', active ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600']">
      <slot name="icon" />
    </span>
    <span v-if="open" class="truncate flex-1">{{ label }}</span>
    <slot name="badge" />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
  href: String,
  active: Boolean,
  open: Boolean,
  label: String
})
</script>