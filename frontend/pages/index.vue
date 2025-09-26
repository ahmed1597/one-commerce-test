<template>
<main class="p-6 max-w-4xl mx-auto">
<h1 class="text-2xl font-bold mb-2">Products</h1>
<button @click="load" class="px-3 py-1 border rounded mb-4">Reload</button>
<p v-if="loading">Loading…</p>
<p v-if="error" class="text-red-600">{{ error }}</p>
<ul v-if="items.length" class="grid gap-3">
<li v-for="p in items" :key="p.id" class="border rounded p-3">
<h2 class="font-semibold">{{ p.title }}</h2>
<div v-if="(p as any).variants?.length">Variants: {{ (p as any).variants.length }}</div>
<img v-if="(p as any).images?.length" :src="(p as any).images[0].src" class="mt-2 w-32 h-32 object-cover" />
</li>
</ul>
<p v-else-if="!loading">No products found.</p>
</main>
</template>
<script setup lang="ts">
const { items, loading, error, fetchProducts } = useProducts();
const load = () => fetchProducts();
onMounted(load);
</script>