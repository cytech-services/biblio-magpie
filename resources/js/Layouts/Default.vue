<template>
	<div class="min-h-full">
		<Head :title="headTitle" />
		<navigation :user="user" />

		<div class="min-h-full">
			<slot />
		</div>
	</div>
</template>

<script>
import { computed } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import Navigation from '@/Components/Navigation.vue'

const user = {
	name: 'Tom Cook',
	email: 'tom@example.com',
	imageUrl:
		'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
}

export default {
	components: {
		Head,
		Navigation,
	},
	props: {
		title: {
			type: String,
			default: 'Test',
		},
	},
	setup(props) {
		const headTitle = computed(() => {
			return props.title + ' - Biblio Magpie'
		})

		return {
			user,
			headTitle,
		}
	},
}
</script>
