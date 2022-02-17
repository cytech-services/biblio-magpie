<template>
	<div class="min-h-full">
		<navigation :user="user" />

		<div>
			<Head :title="title" />

			<header class="bg-white shadow-sm">
				<div class="w-full mx-auto py-4 px-4 sm:px-6 lg:px-8">
					<h1 class="text-lg leading-6 font-semibold text-gray-900">{{ title }}</h1>
				</div>
			</header>
			<main>
				<slot />
			</main>
		</div>
	</div>
</template>

<script>
import Navigation from '@/Components/Navigation.vue'

const user = {
	name: 'Tom Cook',
	email: 'tom@example.com',
	imageUrl:
		'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
}

export default {
	components: {
		Navigation,
	},
	props: {
		title: {
			type: String,
			default: 'Test',
		},
	},
	setup() {
		return {
			user,
		}
	},
}
</script>
