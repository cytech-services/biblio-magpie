<script setup>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/inertia-vue3'
</script>

<template>
	<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
		<div class="p-3 rounded-lg dark:bg-gray-200">
			<Link href="/">
				<BreezeApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
			</Link>
		</div>

		<div
			class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-200 dark:bg-gray-600 shadow-md overflow-hidden sm:rounded-lg"
		>
			<slot />
		</div>
	</div>
</template>
