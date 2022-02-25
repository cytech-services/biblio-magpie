<template>
	<div v-if="Object.keys(selectedBook).length" class="book mt-5">
		<div class="image my-10 xl:mb-3 xl:mt-0">
			<img class="max-h-60 mx-auto shadow-lg" :src="selectedBook.small_image.url" />
		</div>

		<div class="bg-white dark:bg-gray-700 shadow overflow-hidden sm:rounded-lg">
			<div class="border-t border-gray-200 dark:border-gray-800">
				<dl>
					<div
						v-for="(metadata, index) in [
							'title',
							'sub_title',
							'authors',
							'identifications',
							'categories',
							'media',
						]"
						:key="metadata.id"
						:class="[
							index % 2 ? 'bg-white dark:bg-gray-600' : 'bg-gray-50 dark:bg-gray-700',
							'px-2 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-3',
						]"
					>
						<dt class="text-sm font-bold text-gray-500 dark:text-gray-200">
							{{ columnToDisplay(metadata) }}
						</dt>

						<dd
							v-if="metadata === 'identifications' && selectedBook[metadata]"
							class="mt-1 text-sm text-gray-900 dark:text-gray-300 sm:mt-0 sm:col-span-2"
						>
							<ul class="flex flex-col gap-1">
								<li
									v-for="identification in selectedBook[metadata]"
									:key="identification.identification_type"
								>
									{{ identification.identification_type.name }} -
									{{ identification.value }}
								</li>
							</ul>
						</dd>

						<dd
							v-else-if="metadata === 'authors' && selectedBook[metadata]"
							class="mt-1 text-sm text-gray-900 dark:text-gray-300 sm:mt-0 sm:col-span-2"
						>
							<ul class="flex flex-col gap-1">
								<li v-for="author in selectedBook[metadata]" :key="author">
									<a
										href="#"
										class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-300 dark:hover:text-indigo-400"
									>
										{{ author.name }}
									</a>
								</li>
							</ul>
						</dd>

						<dd
							v-else-if="metadata === 'media' && selectedBook[metadata]"
							class="mt-1 text-sm text-gray-900 dark:text-gray-300 sm:mt-0 sm:col-span-2"
						>
							<ul class="flex flex-col gap-1">
								<li v-for="media in selectedBook[metadata]" :key="media.id">
									<a
										href="#"
										class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-300 dark:hover:text-indigo-400"
									>
										{{ media.file_format.name }}
									</a>
								</li>
							</ul>
						</dd>

						<dd
							v-else-if="metadata === 'categories' && selectedBook[metadata]"
							class="mt-1 text-sm text-gray-900 dark:text-gray-300 sm:mt-0 sm:col-span-2"
						>
							<ul class="flex flex-col gap-1">
								<li v-for="category in selectedBook[metadata]" :key="category">
									{{ category.name }}
								</li>
							</ul>
						</dd>

						<dd
							v-else-if="selectedBook[metadata]"
							class="mt-1 text-sm text-gray-900 dark:text-gray-300 sm:mt-0 sm:col-span-2"
						>
							{{ selectedBook[metadata] }}
						</dd>
					</div>
				</dl>
			</div>
		</div>

		<p class="mt-8 dark:text-gray-400">{{ selectedBook.description }}</p>
	</div>
</template>

<script>
import { watch } from '@vue/runtime-core'
export default {
	props: {
		selectedBook: {
			type: Object,
			default() {
				return {}
			},
		},
	},
	setup(props) {
		const columnToDisplay = (column) => {
			return column.replace('_', ' ').replace(/\w\S*/g, function (txt) {
				return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
			})
		}

		return {
			columnToDisplay,
		}
	},
}
</script>
