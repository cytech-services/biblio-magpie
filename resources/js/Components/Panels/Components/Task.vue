<template>
	<transition
		enter-active-class="transform ease-out duration-300 transition"
		enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
		enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
		leave-active-class="transition ease-in duration-100"
		leave-from-class="opacity-100"
		leave-to-class="opacity-0"
	>
		<div
			class="relative group py-3 px-5 flex flex-row items-center justify-between shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-opacity-20"
		>
			<!-- <div class="-m-1 basis-5/6 block p-1"> -->
			<div class="-m-1 flex-1 block p-1">
				<div
					class="absolute inset-0 group-hover:bg-gray-50 dark:group-hover:bg-gray-800"
					aria-hidden="true"
				/>

				<div class="h-28">
					<h2 class="block relative text-xl text-gray-800 dark:text-gray-100">
						{{ task.name }}
					</h2>
					<em class="block relative text-lg text-gray-600 dark:text-gray-300">
						{{ task.status }}
					</em>
					<em
						v-show="task.sub_status"
						class="block relative text-sm text-gray-400 dark:text-gray-300"
					>
						{{ task.sub_status }}
					</em>
				</div>
				<div class="flex-1 flex items-center min-w-0 relative">
					<div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
						<div
							class="bg-blue-600 text-lg font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"
							:style="'width: ' + task.progress + '%'"
						>
							{{ task.progress }}%
						</div>
					</div>
				</div>
			</div>
		</div>
	</transition>
</template>

<script>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { DotsVerticalIcon, XCircleIcon } from '@heroicons/vue/solid'

const actions = [
	{
		name: 'Cancel Task',
		description: 'Cancel the current tasks. This does not revert changes.',
		href: '#',
	},
]

export default {
	props: {
		task: {
			type: Object,
		},
	},
	components: { Popover, PopoverButton, PopoverPanel, DotsVerticalIcon, XCircleIcon },
	setup(props) {
		// const open = ref(false)

		return {
			// open,
			actions,
		}
	},
}
</script>
