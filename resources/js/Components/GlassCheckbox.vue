<script setup>
defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <input
        v-bind="$attrs"
        type="checkbox"
        :checked="modelValue"
        @change="$emit('update:modelValue', $event.target.checked)"
        class="rounded-lg border-white/70 bg-white/50 text-indigo-600 shadow-sm backdrop-blur-sm focus:ring-indigo-500 w-5 h-5 transition-transform"
    />
</template>