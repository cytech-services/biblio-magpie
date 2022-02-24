<template>
	<TransitionRoot as="template" :show="open">
		<Dialog as="div" class="fixed inset-0 overflow-hidden">
			<div class="absolute inset-0 overflow-hidden">
				<DialogOverlay class="absolute inset-0" />

				<div class="fixed inset-y-0 right-0 pl-10 max-w-full flex sm:pl-16">
					<TransitionChild
						as="template"
						enter="transform transition ease-in-out duration-500 sm:duration-700"
						enter-from="translate-x-full"
						enter-to="translate-x-0"
						leave="transform transition ease-in-out duration-500 sm:duration-700"
						leave-from="translate-x-0"
						leave-to="translate-x-full"
					>
						<div class="w-screen max-w-md">
							<div class="p-6 bg-white dark:bg-stone-800">
								<div class="flex items-start justify-between">
									<DialogTitle
										class="text-lg font-medium text-gray-900 dark:text-gray-100"
									>
										<!-- Team -->
									</DialogTitle>
									<div class="ml-3 h-7 flex items-center">
										<button
											type="button"
											class="bg-white dark:bg-stone-800 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
											@click="$emit('closeNotifications')"
										>
											<span class="sr-only">Close panel</span>
											<XIcon class="h-6 w-6" aria-hidden="true" />
										</button>
									</div>
								</div>
							</div>
							<div
								class="h-full flex flex-col gap-10 p-2 bg-white dark:bg-stone-800 shadow-xl overflow-y-scroll"
							>
								<div>
									<div class="mb-3 border-b border-gray-300 dark:border-gray-500">
										<h1 class="px-3 pb-3 text-xl font-bold dark:text-gray-300">
											Tasks
										</h1>
									</div>
									<ul role="list" class="flex-1 flex flex-col gap-y-1">
										<li v-for="task in tasks" :key="task.id">
											<Task :task="task" />
										</li>
									</ul>
								</div>
								<div class="mt-2">
									<div class="mb-3 border-b border-gray-300 dark:border-gray-500">
										<h1 class="px-3 pb-3 text-xl font-bold dark:text-gray-300">
											Notifications
										</h1>
									</div>
									<ul role="list" class="flex-1 flex flex-col gap-y-1">
										<li
											v-for="notification in notifications"
											:key="notification.id"
										>
											<Notification :notification="notification" />
										</li>
									</ul>
								</div>
							</div>
						</div>
					</TransitionChild>
				</div>
			</div>
		</Dialog>
	</TransitionRoot>
</template>

<script>
import { ref } from 'vue'
import {
	Dialog,
	DialogOverlay,
	DialogTitle,
	TransitionChild,
	TransitionRoot,
} from '@headlessui/vue'
import { XIcon } from '@heroicons/vue/outline'
import Task from '@/Components/Panels/Components/Task.vue'
import Notification from '@/Components/Panels/Components/Notification.vue'

const tabs = [
	{ name: 'Tasks', href: '#', current: true },
	{ name: 'Notifications', href: '#', current: false },
]

const tasks = [
	{
		id: 1,
		name: 'Importing Books',
		progress: 0,
		status: 'parsing book XXXX',
	},
]

const notifications = [
	{
		id: 1,
		name: 'Importing Complete',
		description: 'Successfully imported X books',
		type: 'success',
	},
	{
		id: 2,
		name: 'Importing Complete',
		description: 'Successfully imported X books',
		type: 'success',
	},
	{
		id: 3,
		name: 'Importing Complete',
		description: 'Successfully imported X books',
		type: 'success',
	},
]

export default {
	components: {
		Dialog,
		DialogOverlay,
		DialogTitle,
		TransitionChild,
		TransitionRoot,
		XIcon,
		Task,
		Notification,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
	},
	setup() {
		return {
			tabs,
			tasks,
			notifications,
		}
	},
}
</script>
