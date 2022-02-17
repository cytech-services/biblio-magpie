<template>
	<header class="bg-white shadow-sm">
		<div class="w-full mx-auto py-3 px-4 sm:px-6 lg:px-8">
			<h1 class="text-lg leading-6 font-semibold text-gray-900">{{ title }}</h1>
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
