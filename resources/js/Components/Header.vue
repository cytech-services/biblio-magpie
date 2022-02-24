<template>
	<header class="bg-white dark:bg-gray-700 dark:text-gray-100 shadow-sm">
		<div class="w-full h-10 flex items-center px-4 sm:px-6 lg:px-8">
			<h1 class="text-lg leading-6 font-semibold text-gray-900 dark:text-gray-100">
				{{ title }}
			</h1>
		</div>
	</header>
</template>

<script>
export default {
	props: {
		title: {
			type: String,
			default: 'Test',
		},
	},
}
</script>
