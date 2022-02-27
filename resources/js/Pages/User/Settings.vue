<template>
	<DefaultLayout :title="$options.name">
		<PageHeader :title="$options.name" />

		<div class="max-w-7xl mx-auto my-5 p-5">
			<div>
				<div class="md:grid md:grid-cols-3 md:gap-6">
					<div class="md:col-span-1">
						<div class="px-4 sm:px-0">
							<h3 class="text-lg font-medium leading-6 text-gray-900">
								Personal Information
							</h3>
							<p class="mt-1 text-sm text-gray-600">
								Use a permanent address where you can receive mail.
							</p>
						</div>
					</div>
					<div class="mt-5 md:mt-0 md:col-span-2">
						<FormKit
							type="form"
							:value="user"
							actions-class="hidden"
							submit-label="Save"
							@submit="save"
						>
							<div class="shadow sm:rounded-md sm:overflow-hidden">
								<div class="px-4 py-5 bg-white space-y-6 sm:p-6">
									<div class="col-span-6 sm:col-span-3">
										<FormKit
											type="text"
											name="name"
											label="Full Name"
											autocomplete="full-name"
											validation="required"
										/>
									</div>

									<div class="col-span-6 sm:col-span-4">
										<FormKit
											type="email"
											name="email"
											label="Email address"
											autocomplete="email"
											validation="required"
										/>
									</div>

									<div class="col-span-6 sm:col-span-4">
										<FormKit
											type="password"
											name="password"
											label="New Password"
											autocomplete="new_password"
											validation=""
											validation-visibility="live"
										/>
									</div>

									<div class="col-span-6 sm:col-span-4">
										<FormKit
											type="password"
											name="password_confirm"
											label="Confirm password"
											autocomplete="new_password"
											validation="confirm"
											validation-visibility="live"
											validation-label="Password confirmation"
										/>
									</div>

									<!-- <div>
										<label class="block text-sm font-medium text-gray-700">
											Photo
										</label>
										<div class="mt-1 flex gap-x-5 items-center">
											<span
												class="inline-block h-24 w-24 rounded-full overflow-hidden bg-gray-100"
											>
												<svg
													class="h-full w-full text-gray-300"
													fill="currentColor"
													viewBox="0 0 24 24"
												>
													<path
														d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"
													/>
												</svg>
											</span>
											<FormKit
												type="file"
												inner-class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-0"
											/>
										</div>
									</div> -->
								</div>
								<div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
									<BreezeButton
										class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hover:cursor-pointer"
										:class="{ 'opacity-25': formProcessing }"
										:disabled="formProcessing"
										:loading="formProcessing"
									>
										Submit
									</BreezeButton>
								</div>
							</div>
						</FormKit>
					</div>
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<script>
import DefaultLayout from '@/Layouts/Default.vue'
import PageHeader from '@/Components/Header.vue'
import { onMounted, reactive, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import BreezeButton from '@/Components/Button.vue'
import { useToast } from 'vue-toastification'

Inertia.on('exception', (event) => {
	event.preventDefault()
	console.log(`An unexpected error occurred during an Inertia visit.`)
	console.log(event.detail.error)
})

export default {
	name: 'User Settings',
	components: { DefaultLayout, PageHeader, BreezeButton },
	setup() {
		const toast = useToast()
		const user = reactive(Inertia.page.props.auth.user)
		const formProcessing = ref(false)

		const save = (data, node) => {
			// Fix to not submit password if it was deleted
			if (typeof data.password !== 'undefined' && data.password.trim() === '') {
				data.password = undefined
				data.password_confirmation = undefined
			} else {
				data.password_confirmation = data.password_confirm
			}

			Inertia.put(route('user.settings', { id: user.id }), data, {
				replace: true,
				preserveState: true,
				onStart: (visit) => {
					// console.log('start')
					formProcessing.value = true
				},
				onSuccess: (page) => {
					toast.success('Successfully updated your settings')
					// console.log('page', page)
				},
				onError: (errors) => {
					node.setErrors([], errors)

					if ('auth' in errors) {
						toast.error(errors.auth)
					}
				},
				onFinish: (visit) => {
					// console.log('finish', visit)
					formProcessing.value = false
				},
			})
		}

		return {
			user,
			save,
			formProcessing,
		}
	},
}
</script>
