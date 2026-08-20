<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    error: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <textarea
        v-bind="$attrs"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="w-full bg-white/40 backdrop-blur-sm border border-white/60 rounded-xl px-4 py-3 text-slate-800 font-medium placeholder-slate-400 focus:ring-2 focus:ring-indigo-400 focus:border-transparent focus:bg-white/70 shadow-sm transition-all outline-none resize-none"
        :class="{ 'border-red-400 focus:ring-red-400 bg-red-50/50': error }"
    ></textarea>
</template>