<script setup>
import Product from '@/Components/Product.vue';

const props = defineProps(['result']);
</script>
 
<template>
  <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-8">Products</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <Product  
              v-for="product in result.found"
              :price="product.price"
              :sku="product.sku"
            />
        </div>
    </div>
</template>