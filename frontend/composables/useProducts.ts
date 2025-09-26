export interface Product { id: number; title: string; price?: number | null; image_url?: string | null }
export function useProducts(){
const config = useRuntimeConfig();
const loading = ref(false); const error = ref<string|null>(null); const items = ref<Product[]>([]);
const fetchProducts = async () => {
loading.value = true; error.value = null;
try {
const res = await $fetch<any>(`${config.public.apiBase}/products?with=variants,images`);
items.value = res.data || [];
} catch (e:any) { error.value = e?.message || 'Failed to load products'; }
finally { loading.value = false; }
};
return { items, loading, error, fetchProducts };
}