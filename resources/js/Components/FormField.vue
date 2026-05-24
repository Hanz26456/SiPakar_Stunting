<template>
  <div>
    <label class="block text-xs font-medium text-gray-600 mb-1">
      {{ label }} <span v-if="required" class="text-red-400">*</span>
    </label>
    <input
      :type="type || 'text'"
      :step="step"
      :placeholder="placeholder"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
      :class="error ? 'border-red-300 bg-red-50' : 'border-gray-200'"
    >
    <div v-if="error" class="text-xs text-red-500 mt-1">{{ error }}</div>
  </div>
</template>

<script setup>
defineProps({
  label: String, modelValue: [String, Number],
  type: String, step: String, placeholder: String,
  error: String, required: Boolean,
})
defineEmits(['update:modelValue'])
</script>
