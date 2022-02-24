<template>
	<div
		class="relative group py-6 px-5 flex flex-row items-center justify-between shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-opacity-20"
	>
		<!-- <div class="-m-1 basis-5/6 block p-1"> -->
		<div class="-m-1 flex-1 block p-1">
			<div
				class="absolute inset-0 group-hover:bg-gray-50 dark:group-hover:bg-gray-800"
				aria-hidden="true"
			/>

			<h2 class="relative text-gray-800 dark:text-gray-100">{{ task.name }}</h2>
			<em class="relative text-xs text-gray-600 dark:text-gray-300">{{ task.status }}</em>
			<div class="flex-1 flex items-center min-w-0 relative">
				<div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
					<div
						class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"
						:style="'width: ' + task.progress + '%'"
					>
						{{ task.progress }}%
					</div>
				</div>
			</div>
		</div>
		<!-- <Popover class="relative basis-1/6" v-slot="{ open }">
			<PopoverButton
				:class="[
					open ? 'text-gray-900' : 'text-gray-500',
					'group rounded-md inline-flex items-center text-base font-medium hover:text-gray-900 focus:outline-none hover:cursor-pointer float-right',
				]"
				@click="open === true ? (open = false) : (open = true)"
			>
				<span class="sr-only">Open options menu</span>
				<span class="flex items-center justify-center h-full w-full rounded-full">
					<DotsVerticalIcon
						:class="[
							open ? 'text-gray-500' : 'text-gray-300',
							'w-5 h-5 group-hover:text-gray-500 ',
						]"
						aria-hidden="true"
					/>
				</span>
			</PopoverButton>

			<transition
				v-show="open"
				enter-active-class="transition ease-out duration-200"
				enter-from-class="opacity-0 translate-y-1"
				enter-to-class="opacity-100 translate-y-0"
				leave-active-class="transition ease-in duration-150"
				leave-from-class="opacity-100 translate-y-0"
				leave-to-class="opacity-0 translate-y-1"
			>
				<PopoverPanel
					class="absolute right-1 top-3 mt-3 px-2 w-screen max-w-xs sm:px-0 z-20"
				>
					<div
						class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden"
					>
						<div
							class="relative grid gap-6 bg-white dark:bg-stone-800 px-5 py-6 sm:gap-8 sm:p-5"
						>
							<a
								v-for="action in actions"
								:key="action.name"
								:href="action.href"
								class="-m-3 p-3 block rounded-md hover:bg-gray-50 dark:hover:bg-stone-700 transition ease-in-out duration-150"
							>
								<p
									class="flex text-base font-medium text-gray-900 dark:text-gray-200 items-center"
								>
									<XCircleIcon
										class="w-5 h-5 mr-2 text-red-500"
										aria-hidden="true"
									/>
									{{ action.name }}
								</p>
								<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
									{{ action.description }}
								</p>
							</a>
						</div>
					</div>
				</PopoverPanel>
			</transition>
		</Popover> -->
	</div>
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
