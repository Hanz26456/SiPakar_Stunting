<template>
  <div>
    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
      {{ label }} <span v-if="required" class="text-rose-500">*</span>
    </label>
    <input
      :type="type || 'text'"
      :step="step"
      :placeholder="placeholder"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full bg-white dark:bg-zinc-800/80 border rounded-lg px-3.5 py-2 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-2xs"
      :class="error
        ? 'border-rose-400 dark:border-rose-500 bg-rose-50/50 dark:bg-rose-950/20'
        : 'border-zinc-300 dark:border-zinc-700'"
    >
    <div v-if="error" class="text-xs text-rose-500 mt-1 font-medium">{{ error }}</div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  modelValue: [String, Number],
  type: String,
  step: String,
  placeholder: String,
  error: String,
  required: Boolean,
})
defineEmits(['update:modelValue'])
</script>
