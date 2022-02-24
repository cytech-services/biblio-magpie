<template>
	<Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
		<div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex items-center justify-between h-14">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<img
							class="h-8 w-8"
							src="https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg"
							alt="Workflow"
						/>
					</div>
					<div class="hidden md:block">
						<div class="ml-10 flex items-baseline space-x-4">
							<a
								v-for="item in navigation"
								:key="item.name"
								:href="item.href"
								:class="[
									item.current
										? 'bg-gray-900 text-white'
										: 'text-gray-300 hover:bg-gray-700 hover:text-white',
									'px-3 py-2 rounded-md text-sm font-medium',
								]"
								:aria-current="item.current ? 'page' : undefined"
							>
								{{ item.name }}
							</a>
						</div>
					</div>
				</div>
				<div class="hidden md:block">
					<div class="ml-4 flex items-center md:ml-6">
						<button
							type="button"
							class="bg-gray-800 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none relative"
							@click="showNotofications = true"
						>
							<span class="sr-only">View notifications</span>
							<BellIcon class="h-6 w-6" aria-hidden="true" />

							<span class="flex h-3 w-3 absolute bottom-0 right-0">
								<span
									class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
								></span>
								<span
									class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"
								></span>
							</span>
						</button>

						<!-- Profile dropdown -->
						<Menu as="div" class="ml-3 relative">
							<div>
								<MenuButton
									class="max-w-xs bg-gray-800 rounded-full flex items-center text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
								>
									<span class="sr-only">Open user menu</span>
									<img class="h-8 w-8 rounded-full" :src="user.imageUrl" alt="" />
								</MenuButton>
							</div>
							<transition
								enter-active-class="transition ease-out duration-100"
								enter-from-class="transform opacity-0 scale-95"
								enter-to-class="transform opacity-100 scale-100"
								leave-active-class="transition ease-in duration-75"
								leave-from-class="transform opacity-100 scale-100"
								leave-to-class="transform opacity-0 scale-95"
							>
								<MenuItems
									class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-slate-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
								>
									<SwitchGroup as="div" class="flex items-center px-4 py-2">
										<SwitchLabel as="span" class="mr-3">
											<span
												class="text-sm font-medium text-gray-900 dark:text-white"
											>
												Dark Mode
											</span>
										</SwitchLabel>
										<Switch
											v-model="darkMode"
											:class="[
												darkMode ? 'bg-green-700' : 'bg-gray-200',
												'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none',
											]"
										>
											<span
												aria-hidden="true"
												:class="[
													darkMode ? 'translate-x-5' : 'translate-x-0',
													'pointer-events-none inline-block h-5 w-5 rounded-full bg-white dark:bg-sc-dark-alt shadow transform ring-0 transition ease-in-out duration-200',
												]"
											/>
										</Switch>
									</SwitchGroup>

									<MenuItem
										v-for="item in userNavigation"
										:key="item.name"
										v-slot="{ active }"
									>
										<a
											:href="item.href"
											:class="[
												active ? 'bg-gray-100' : '',
												'block px-4 py-2 text-sm text-gray-700 dark:text-white dark:hover:bg-slate-600',
											]"
										>
											{{ item.name }}
										</a>
									</MenuItem>
								</MenuItems>
							</transition>
						</Menu>
					</div>
				</div>
				<div class="-mr-2 flex md:hidden">
					<!-- Mobile menu button -->
					<DisclosureButton
						class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
					>
						<span class="sr-only">Open main menu</span>
						<MenuIcon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
						<XIcon v-else class="block h-6 w-6" aria-hidden="true" />
					</DisclosureButton>
				</div>
			</div>
		</div>

		<DisclosurePanel class="md:hidden">
			<div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
				<DisclosureButton
					v-for="item in navigation"
					:key="item.name"
					as="a"
					:href="item.href"
					:class="[
						item.current
							? 'bg-gray-900 text-white'
							: 'text-gray-300 hover:bg-gray-700 hover:text-white',
						'block px-3 py-2 rounded-md text-base font-medium',
					]"
					:aria-current="item.current ? 'page' : undefined"
				>
					{{ item.name }}
				</DisclosureButton>
			</div>
			<div class="pt-4 pb-3 border-t border-gray-700">
				<div class="flex items-center px-5">
					<div class="flex-shrink-0">
						<img class="h-10 w-10 rounded-full" :src="user.imageUrl" alt="" />
					</div>
					<div class="ml-3">
						<div class="text-base font-medium text-white">{{ user.name }}</div>
						<div class="text-sm font-medium text-gray-400">{{ user.email }}</div>
					</div>
					<button
						type="button"
						class="ml-auto bg-gray-800 flex-shrink-0 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
						@click="showNotofications = true"
					>
						<span class="sr-only">View notifications</span>
						<BellIcon class="h-6 w-6" aria-hidden="true" />
					</button>
				</div>
				<div class="mt-3 px-2 space-y-1">
					<DisclosureButton
						v-for="item in userNavigation"
						:key="item.name"
						as="a"
						:href="item.href"
						class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700"
					>
						{{ item.name }}
					</DisclosureButton>
				</div>
			</div>
		</DisclosurePanel>
	</Disclosure>

	<notifications :open="showNotofications" @close-notifications="closeNotifications" />
</template>

<script>
import { onMounted, ref, watch } from 'vue'
import {
	Disclosure,
	DisclosureButton,
	DisclosurePanel,
	Menu,
	MenuButton,
	MenuItem,
	MenuItems,
	Switch,
	SwitchGroup,
	SwitchLabel,
} from '@headlessui/vue'
import { BellIcon, MenuIcon, XIcon } from '@heroicons/vue/outline'
import Notifications from '@/Components/Panels/Notifications.vue'

const navigation = [
	{ name: 'Library', href: '#', current: true },
	{ name: 'Authors', href: '#', current: false },
	{ name: 'Series', href: '#', current: false },
	{ name: 'Settings', href: '#', current: false },
	{ name: 'Share', href: '#', current: false },
]
const userNavigation = [
	{ name: 'Your Profile', href: '#' },
	{ name: 'Settings', href: '#' },
	{ name: 'Sign out', href: '#' },
]

export default {
	components: {
		Disclosure,
		DisclosureButton,
		DisclosurePanel,
		Menu,
		MenuButton,
		MenuItem,
		MenuItems,
		BellIcon,
		MenuIcon,
		XIcon,
		Notifications,
		Switch,
		SwitchGroup,
		SwitchLabel,
	},
	props: {
		title: {
			type: String,
			default: 'Test',
		},
		user: Object,
	},
	setup() {
		var darkMode = ref(false)
		const showNotofications = ref(false)

		onMounted(() => {
			if (
				localStorage.theme === 'dark' ||
				(!('theme' in localStorage) &&
					window.matchMedia('(prefers-color-scheme: dark)').matches)
			) {
				darkMode.value = true
			}
		})

		watch(darkMode, (newValue, oldValue) => {
			if (newValue) {
				document.documentElement.classList.add('dark')
				localStorage.theme = 'dark'
			} else {
				document.documentElement.classList.remove('dark')
				localStorage.theme = 'light'
			}

			window.dispatchEvent(
				new CustomEvent('theme-changed', {
					detail: {
						theme: localStorage.theme,
					},
				})
			)
		})

		const closeNotifications = () => {
			showNotofications.value = false
		}

		return {
			darkMode,
			navigation,
			userNavigation,
			showNotofications,
			closeNotifications,
		}
	},
}
</script>
