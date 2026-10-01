<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: [String, Number],
    required: false,
});

const input = ref(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="rounded-xl border border-orbit-border bg-orbit-surface2 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all shadow-sm px-3.5 py-2.5 text-sm"
        v-model="model"
        ref="input"
    />
</template>
