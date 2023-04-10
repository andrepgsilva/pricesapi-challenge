<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";

const props = defineProps({
    price: {
        type: Number,
    },
    sku: {
        type: String,
    },
});
</script>

<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <img
            src="https://dummyimage.com/1080.png"
            alt=""
            class="w-full h-64 object-cover"
        />
        <div class="p-4">
            <h2 class="text-xl font-bold mb-2">{{ sku }}</h2>
            <p class="text-gray-700">
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
            </p>
            <div class="mt-4 flex items-center justify-between">
                <span class="font-bold text-xl">€{{ price }}</span>
                <button
                    class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 focus:outline-none focus:bg-gray-700"
                >
                    Add to cart
                </button>
            </div>
        </div>
    </div>
</template>
