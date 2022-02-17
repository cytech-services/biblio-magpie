<template>
	<default-layout title="Dashboard">
		<div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
			<!-- Replace with your content -->
			<div class="px-4 py-4 sm:px-0">
				<div class="border-4 border-dashed border-gray-200 rounded-lg h-96" />
			</div>
			<!-- /End replace -->
		</div>
	</default-layout>
</template>

<script>
import DefaultLayout from '@/Layouts/Default.vue'

export default {
	components: {
		DefaultLayout,
	},
	props: {
		// user: Object,
	},
}
</script>
