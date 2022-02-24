<template>
	<div aria-live="assertive" class="flex pointer-events-none">
		<div class="w-full group flex flex-col space-y-4">
			<!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
			<transition
				enter-active-class="transform ease-out duration-300 transition"
				enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
				enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
				leave-active-class="transition ease-in duration-100"
				leave-from-class="opacity-100"
				leave-to-class="opacity-0"
			>
				<div
					v-if="show"
					class="w-full group-hover:bg-gray-50 dark:group-hover:bg-gray-800 shadow-lg pointer-events-auto ring-1 ring-black ring-opacity-5 dark:ring-opacity-20 overflow-hidden"
				>
					<div class="p-4">
						<div class="flex items-start">
							<div class="flex-shrink-0">
								<CheckCircleIcon
									class="h-6 w-6 text-green-400"
									aria-hidden="true"
								/>
							</div>
							<div class="ml-3 w-0 flex-1 pt-0.5">
								<p class="text-sm font-medium text-gray-900 dark:text-gray-100">
									{{ notification.name }}
								</p>
								<p class="mt-1 text-sm text-gray-500">
									{{ notification.description }}
								</p>
							</div>
							<div class="ml-4 flex-shrink-0 flex">
								<button
									@click="closeNotification"
									class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
								>
									<span class="sr-only">Close</span>
									<XIcon class="h-5 w-5" aria-hidden="true" />
								</button>
							</div>
						</div>
					</div>
				</div>
			</transition>
		</div>
	</div>
</template>

<script>
import { ref } from 'vue'
import { CheckCircleIcon } from '@heroicons/vue/outline'
import { XIcon } from '@heroicons/vue/solid'

const actions = [
	{
		name: 'Cancel Task',
		description: 'Cancel the current notifications. This does not revert changes.',
		href: '#',
	},
]

export default {
	props: {
		notification: {
			type: Object,
		},
	},
	components: {
		CheckCircleIcon,
		XIcon,
	},
	emits: ['closeNotification'],
	setup(props, ctx) {
		const show = ref(true)

		const closeNotification = () => {
			show.value = false
			ctx.emit('closeNotification', {
				id: props.notification.id,
			})
		}

		return {
			show,
			actions,
			closeNotification,
		}
	},
}
</script>
