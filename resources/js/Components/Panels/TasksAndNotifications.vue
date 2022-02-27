<template>
	<TransitionRoot as="template" :show="open">
		<Dialog as="div" class="fixed inset-0 overflow-hidden" @close="$emit('closeNotifications')">
			<div class="absolute inset-0 overflow-hidden">
				<TransitionChild
					as="template"
					enter="ease-in-out duration-500"
					enter-from="opacity-0"
					enter-to="opacity-100"
					leave="ease-in-out duration-500"
					leave-from="opacity-100"
					leave-to="opacity-0"
				>
					<DialogOverlay
						class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
					/>
				</TransitionChild>
				<div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
					<TransitionChild
						as="template"
						enter="transform transition ease-in-out duration-500 sm:duration-700"
						enter-from="translate-x-full"
						enter-to="translate-x-0"
						leave="transform transition ease-in-out duration-500 sm:duration-700"
						leave-from="translate-x-0"
						leave-to="translate-x-full"
					>
						<div class="pointer-events-auto relative w-[30rem]">
							<TransitionChild
								as="template"
								enter="ease-in-out duration-500"
								enter-from="opacity-0"
								enter-to="opacity-100"
								leave="ease-in-out duration-500"
								leave-from="opacity-100"
								leave-to="opacity-0"
							>
								<div
									class="absolute top-0 left-0 -ml-8 flex pt-4 pr-2 sm:-ml-10 sm:pr-4"
								>
									<button
										type="button"
										class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
										@click="$emit('closeNotifications')"
									>
										<span class="sr-only">Close panel</span>
										<XIcon class="h-6 w-6" aria-hidden="true" />
									</button>
								</div>
							</TransitionChild>
							<div class="h-full overflow-y-auto bg-white p-6">
								<div class="space-y-6 pb-16">
									<div>
										<div
											class="mb-3 border-b border-gray-300 dark:border-gray-500"
										>
											<h1
												class="px-3 pb-3 text-xl font-bold dark:text-gray-300"
											>
												Tasks
											</h1>
										</div>
										<ul role="list" class="flex-1 flex flex-col gap-y-1">
											<li v-for="task in tasks" :key="task.id">
												<Task :task="task" />
											</li>
										</ul>
									</div>
									<div class="my-2">
										<div
											class="flex justify-between mb-3 border-b border-gray-300 dark:border-gray-500"
										>
											<h1
												class="px-3 pb-3 text-xl font-bold dark:text-gray-300"
											>
												Notifications
											</h1>
											<NotificationsMenu
												@remove-all-notifications="removeAllNotifications"
											/>
										</div>
										<ul role="list" class="flex-1 flex flex-col gap-y-1">
											<li
												v-for="notification in notifications"
												:key="notification.id"
											>
												<component
													:is="notification.component"
													:notification="notification"
													@close-notification="closeNotification"
												/>
											</li>
										</ul>
									</div>
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
import { onMounted, onUnmounted, reactive, ref } from 'vue'
import {
	Dialog,
	DialogOverlay,
	DialogTitle,
	TransitionChild,
	TransitionRoot,
} from '@headlessui/vue'
import { XIcon, DotsVerticalIcon } from '@heroicons/vue/outline'
import Task from '@/Components/Panels/Components/Task.vue'
import Notification from '@/Components/Panels/Components/Notifications/Notification.vue'
import UserUpdated from '@/Components/Panels/Components/Notifications/UserUpdated.vue'
import NotificationsMenu from '@/Components/Panels/Components/NotificationsMenu.vue'
import { Inertia } from '@inertiajs/inertia'

export default {
	components: {
		Dialog,
		DialogOverlay,
		DialogTitle,
		TransitionChild,
		TransitionRoot,
		XIcon,
		DotsVerticalIcon,
		Task,
		Notification,
		UserUpdated,
		NotificationsMenu,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
	},
	emits: ['hasNotifications'],
	setup(props, ctx) {
		const tasks = reactive([])
		const notifications = reactive([])

		const addUserUpdatedNotification = (data) => {
			let notification = {
				id: data.id,
				component: 'UserUpdated',
				name: 'User Updated',
				description: data.name + ' updated their settings',
				type: 'success',
			}

			notifications.push(notification)
			ctx.emit('hasNotifications')
		}

		const createOrUpdateTaskStatus = (data) => {
			let index = tasks.findIndex((x) => x.batch_id === data.batch_id)
			console.log(tasks, data.batch_id, index)

			if (index > -1) {
				tasks[index] = data
			} else {
				tasks.push(data)
			}

			if (data.progress === 100 && index > -1) {
				setTimeout(() => {
					tasks.splice(index, 1)
				}, 5000)
			}

			ctx.emit('hasNotifications')
		}

		onMounted(() => {
			window.Echo.private(`App.Models.User.${Inertia.page.props.auth.user.id}`).notification(
				(notification) => {
					if (notification.type === 'App\\Notifications\\User\\UserUpdated')
						addUserUpdatedNotification(notification)
					if (notification.type === 'App\\Notifications\\Library\\ImportBooksBatch')
						createOrUpdateTaskStatus(notification)

					console.log(notification)
				}
			)
		})

		onUnmounted(() => {
			window.Echo.leaveChannel('App.Notifications')
			// window.Echo.leaveChannel('App.Tasks')
		})

		const closeNotification = (id) => {
			let index = notifications.findIndex((x) => x.id === id)
			console.log(notifications, id, index)

			if (index > -1) notifications.splice(index, 1)

			console.log(notifications)
		}

		const removeAllNotifications = () => {
			notifications.length = 0
		}

		return {
			tasks,
			notifications,
			closeNotification,
			removeAllNotifications,
		}
	},
}
</script>
