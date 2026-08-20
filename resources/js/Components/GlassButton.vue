<script setup>
defineProps({
    variant: {
        type: String,
        default: 'primary',
    },
});
</script>

<template>
    <button
        v-bind="$attrs"
        class="font-bold shadow-lg transition-all duration-200 disabled:cursor-not-allowed disabled:opacity-50"
        :class="variant === 'secondary'
            ? 'bg-white/50 text-slate-700 border border-white/70 hover:bg-white/80 backdrop-blur-md'
            : 'bg-gradient-to-r from-indigo-500 to-blue-500 text-white hover:from-indigo-600 hover:to-blue-600 hover:shadow-xl hover:-translate-y-0.5'"
    >
        <slot />
    </button>
</template>