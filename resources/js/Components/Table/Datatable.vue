<template>
	<div class="flex flex-col">
		<div class="flex gap-x-5 justify-end mb-2">
			<!-- CLEAR FILTERS -->
			<button
				type="button"
				class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none"
				@click="clearFilters"
			>
				<XCircleIcon class="-ml-0.5 mr-2 h-4 w-4" aria-hidden="true" />
				Clear Filters
			</button>

			<!-- PAGINATION LIMIT -->
			<div class="relative">
				<Menu as="div" class="relative inline-block text-left">
					<div>
						<MenuButton
							class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-0"
							@click="toggleColumnOptions"
						>
							Options
							<ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
						</MenuButton>
					</div>
				</Menu>

				<transition
					v-show="columnSettingsOpen"
					enter-active-class="transition ease-out duration-100"
					enter-from-class="transform opacity-0 scale-95"
					enter-to-class="transform opacity-100 scale-100"
					leave-active-class="transition ease-in duration-75"
					leave-from-class="transform opacity-100 scale-100"
					leave-to-class="transform opacity-0 scale-95"
				>
					<ul
						id="columnOptions"
						role="list"
						class="absolute right-0 top-10 w-60 divide-y divide-gray-200 z-10 bg-white rounded-md shadow-xl"
					>
						<li v-for="columnDef in columns" :key="columnDef.field" class="flex">
							<div class="flex-1 flex px-5 justify-between">
								<label
									:for="columnDef.field"
									class="flex-1 py-4 text-sm font-medium text-gray-900 uppercase hover:cursor-pointer"
								>
									{{ getColumnHeader(columnDef) }}
								</label>
								<input
									:name="columnDef.field"
									class="h-4 w-4 my-4 rounded hover:cursor-pointer"
									type="checkbox"
									:id="columnDef.field"
									v-model="columnDef.visible"
								/>
							</div>
						</li>
					</ul>
				</transition>
			</div>
		</div>

		<div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
			<div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
				<div
					class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-900 sm:rounded-lg"
				>
					<table :class="[name ? name : '', 'min-w-full divide-y divide-gray-200']">
						<!-- HEADER -->
						<thead class="bg-gray-300 dark:bg-gray-800">
							<tr
								class="bg-gray-200 dark:bg-gray-700 border-b border-gray-400 dark:border-gray-900"
							>
								<td
									:colspan="columns.length"
									class="px-4 py-2 text-sm text-gray-500 dark:text-gray-300"
								>
									<div class="flex gap-x-5 items-center">
										<!-- PAGINATION LIMIT -->
										<select
											v-model="params.limit"
											class="flex-shrink block pl-3 pr-10 py-2 text-base dark:text-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-900 focus:outline-none sm:text-sm rounded-md"
											@change="updatePaginationLimit"
										>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="15">15</option>
											<option value="30">30</option>
											<option value="50">50</option>
											<option value="100">100</option>
										</select>

										<!-- VIEWING -->
										<div class="flex-1">
											Viewing records
											<span class="font-bold">{{ rowData.from }}</span>
											to
											<span class="font-bold">{{ rowData.to }}</span>
											of
											<span class="font-bold">{{ rowData.total }}</span>
											records
										</div>

										<!-- PAGINATION -->
										<div class="flex-1 flex gap-x-1 justify-end">
											<div v-for="link in rowData.links" :key="link.label">
												<Link
													v-if="link.url"
													:href="link.url"
													replace
													:class="[
														link.active
															? 'bg-gray-400 dark:bg-gray-600 text-gray-50 dark:text-gray-300'
															: '',
														'text-lg rounded-md py-1 px-2 hover:bg-gray-300 dark:hover:bg-gray-500',
													]"
												>
													<span v-html="link.label" />
												</Link>
												<span
													v-else
													class="text-gray-300 dark:text-gray-500 text-lg rounded-md py-1 px-2"
												>
													<span v-html="link.label" />
												</span>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<th
									v-show="columnDef.visible"
									v-for="(columnDef, index) in columns"
									:key="columnDef.field"
									:id="index"
									scope="col"
									:class="[
										'maxWidth' in columnDef ? columnDef.maxWidth : '',
										'pl-3 pr-3 pt-4 pb-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider align-bottom whitespace-nowrap',
									]"
								>
									{{ getColumnHeader(columnDef) }}
								</th>
							</tr>
							<tr>
								<th
									v-show="columnDef.visible"
									v-for="columnDef in columns"
									:key="columnDef.field"
									scope="col"
									:class="[
										'maxWidth' in columnDef ? columnDef.maxWidth : '',
										'pl-3 pr-10 pb-4 text-left text-xs font-bold text-gray-500',
									]"
								>
									<input
										v-if="columnDef.filter"
										type="text"
										:name="columnDef.filter"
										v-model="params.filter[columnDef.filter]"
										class="dark:bg-zinc-700 dark:text-zinc-300 shadow-sm focus:outline-none focus:ring-0 block w-full sm:text-sm border-gray-300 dark:border-zinc-900 rounded-md"
									/>
								</th>
							</tr>
						</thead>

						<!-- BODY -->
						<tbody
							class="bg-white dark:bg-gray-600 align-top divide-y divide-gray-200 dark:divide-gray-400"
						>
							<tr
								v-for="(row, index) in rowData.data"
								:key="row.id"
								:id="name + '_' + computeIndex(index)"
								@click="selectRow(row, computeIndex(index))"
							>
								<td
									v-show="columnDef.visible"
									v-for="columnDef in columns"
									:key="columnDef.field"
									:class="[
										'maxWidth' in columnDef ? columnDef.maxWidth : '',
										'px-3 py-2 text-sm text-gray-500 dark:text-gray-300',
									]"
								>
									<!-- IMAGE -->
									<span
										v-if="
											'display' in columnDef &&
											columnDef.display.type === 'image'
										"
									>
										<img
											:src="getColumnValue(row, columnDef)"
											:class="[
												'height' in columnDef.display
													? columnDef.display.height
													: '',
												'width' in columnDef.display
													? columnDef.display.width
													: '',
											]"
										/>
									</span>

									<!-- LIST -->
									<span
										v-if="
											'display' in columnDef &&
											columnDef.display.type === 'list'
										"
									>
										<ul>
											<li
												v-for="item in getColumnValue(row, columnDef)"
												:key="item.id"
											>
												{{ getColumnValue(item, columnDef.display) }}
											</li>
										</ul>
									</span>

									<!-- RATING -->
									<span
										v-if="
											'display' in columnDef &&
											columnDef.display.type === 'rating'
										"
									>
										<ul class="flex">
											<li
												v-for="(rate, index) in [1, 2, 3, 4, 5]"
												:key="index"
											>
												<StarSolidIcon
													v-if="rate <= getColumnValue(row, columnDef)"
													class="h-5 w-5 text-amber-500"
													aria-hidden="true"
												/>
												<StarOutlineIcon
													v-else
													class="h-5 w-5 opacity-25"
													aria-hidden="true"
												/>
											</li>
										</ul>
									</span>

									<!-- DEFAULT -->
									<span v-else-if="!('display' in columnDef)">
										{{ getColumnValue(row, columnDef) }}
									</span>
								</td>
							</tr>
						</tbody>

						<!-- FOOTER -->
						<tfoot>
							<tr
								class="bg-gray-200 dark:bg-gray-700 border-t border-gray-400 dark:border-gray-900"
							>
								<td
									:colspan="columns.length"
									class="px-4 py-2 text-sm text-gray-500 dark:text-gray-300"
								>
									<div class="flex gap-x-5 items-center">
										<!-- PAGINATION LIMIT -->
										<select
											v-model="params.limit"
											class="flex-shrink block pl-3 pr-10 py-2 text-base dark:text-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-900 focus:outline-none sm:text-sm rounded-md"
											@change="updatePaginationLimit"
										>
											<option value="5">5</option>
											<option value="10">10</option>
											<option value="15">15</option>
											<option value="30">30</option>
											<option value="50">50</option>
											<option value="100">100</option>
										</select>

										<!-- VIEWING -->
										<div class="flex-1">
											Viewing records
											<span class="font-bold">{{ rowData.from }}</span>
											to
											<span class="font-bold">{{ rowData.to }}</span>
											of
											<span class="font-bold">{{ rowData.total }}</span>
											records
										</div>

										<!-- PAGINATION -->
										<div class="flex-1 flex gap-x-1 justify-end">
											<div v-for="link in rowData.links" :key="link.label">
												<Link
													v-if="link.url"
													:href="link.url"
													replace
													:class="[
														link.active
															? 'bg-gray-400 dark:bg-gray-600 text-gray-50 dark:text-gray-300'
															: '',
														'text-lg rounded-md py-1 px-2 hover:bg-gray-300 dark:hover:bg-gray-500',
													]"
												>
													<span v-html="link.label" />
												</Link>
												<span
													v-else
													class="text-gray-300 dark:text-gray-500 text-lg rounded-md py-1 px-2"
												>
													<span v-html="link.label" />
												</span>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import { Menu, MenuButton } from '@headlessui/vue'
import { reactive, ref } from '@vue/reactivity'
import { onMounted, onUnmounted, watch } from '@vue/runtime-core'
import { Inertia } from '@inertiajs/inertia'
import { StarIcon as StarOutlineIcon } from '@heroicons/vue/outline'
import { StarIcon as StarSolidIcon, XCircleIcon, ChevronDownIcon } from '@heroicons/vue/solid'

export default {
	props: {
		name: {
			type: String,
		},
		rowData: {
			type: Object,
		},
		columnDefs: {
			type: Object,
		},
	},
	components: {
		Link,
		Menu,
		MenuButton,
		StarOutlineIcon,
		StarSolidIcon,
		XCircleIcon,
		ChevronDownIcon,
	},
	emits: ['rowSelected'],
	setup(props, { emit }) {
		console.log(props.rowData)
		// console.log(props.columnDefs)

		let columnSettingsOpen = ref(false)
		let keyShift = ref(false)
		let keyControl = ref(false)
		let classesToToggle = ['selected', 'bg-gray-100', 'dark:bg-neutral-800']

		// Load the selected rows from persistence or default to empty array
		let selectedRows = reactive(
			props.name + '_selectedRows' in localStorage
				? JSON.parse(localStorage[props.name + '_selectedRows'])
				: []
		)

		// Load the table params from persistence or default
		let params = reactive(
			props.name + '_tableState' in localStorage
				? JSON.parse(localStorage[props.name + '_tableState'])
				: {
						filter: {},
						sort: {},
						limit: 5,
				  }
		)

		// Load the table columns from persistence or default to prop
		let columns = reactive(
			props.name + '_tableColumns' in localStorage
				? JSON.parse(localStorage[props.name + '_tableColumns'])
				: props.columnDefs
		)

		onMounted(() => {
			// console.log('columns', columns)
			// console.log('selectedRows', selectedRows)

			columns.forEach((column) => {
				if (!('visible' in column)) column.visible = true
			})

			selectedRows.forEach((selectedRow) => {
				let row = document.getElementById(selectedRow.row_id)
				if (row) row.classList.add(...classesToToggle)
			})

			window.addEventListener('keydown', (event) => {
				switch (event.key) {
					case 'Shift':
						keyShift.value = true
						break
					case 'Control':
						keyControl.value = true
						break
				}
			})
			window.addEventListener('keyup', (event) => {
				switch (event.key) {
					case 'Shift':
						keyShift.value = false
						break
					case 'Control':
						keyControl.value = false
						break
				}
			})

			const target = document.querySelector('#columnOptions')
			document.addEventListener('click', (event) => {
				const withinBoundaries = event.composedPath().includes(target)
				if (!withinBoundaries && isVisible(target)) columnSettingsOpen.value = false
			})

			// setTimeout(() => {
			// 	let columnHeaders = document.getElementsByClassName('columnHeader')
			// 	for (const key in columnHeaders) {
			// 		if (Object.hasOwnProperty.call(columnHeaders, key)) {
			// 			let el = columnHeaders[key]
			// 			el.addEventListener(
			// 				'contextmenu',
			// 				(event, key) => {
			// 					event.preventDefault()

			// 					console.log(el.id)

			// 					columns[el.id].visible = false
			// 					console.log(columns)
			// 				},
			// 				false
			// 			)
			// 		}
			// 	}
			// }, 200)
		})

		const getColumnHeader = (columnDef) => {
			if ('headerName' in columnDef) return columnDef.headerName

			return columnDef.field
		}

		const index = (obj, i) => {
			if (obj === null || !(i in obj)) return null

			return obj[i]
		}

		const computeIndex = (index) => {
			return (
				props.rowData.current_page * props.rowData.per_page +
				(index + 1) -
				props.rowData.per_page
			)
		}

		const getColumnValue = (row, columnDef) => {
			const getValue = (source, path) => {
				return path.split('.').reduce(index, source)
			}

			if (columnDef.field.includes('.')) {
				// console.log(columnDef.field, getValue(row, columnDef.field))
				return getValue(row, columnDef.field)
			}

			return row[columnDef.field]
		}

		// Save column state changes to the local storage for persistance
		const updateTableState = () => {
			localStorage[props.name + '_tableState'] = JSON.stringify(params)
			Inertia.get(buildUrl(), {}, { replace: true, preserveState: true })
		}

		const clearFilters = () => {
			params = {
				filter: {},
				sort: {},
				limit: 5,
			}
			Inertia.get(buildUrl(), {}, { replace: true, preserveState: true })
		}

		const updatePaginationLimit = () => {
			updateTableState()
		}

		const buildUrl = () => {
			let url = route('library.index', params)

			return url
		}

		const updateSelectedRowsPersistence = () => {
			localStorage[props.name + '_selectedRows'] = JSON.stringify(selectedRows)
		}

		const updateColumnsPersistence = () => {
			localStorage[props.name + '_tableColumns'] = JSON.stringify(columns)
		}

		const selectRow = (row, index) => {
			let row_id = props.name + '_' + index

			// Restrict to adding a row once
			if (
				selectedRows.filter((obj) => {
					return obj.row_id === row_id
				}).length
			) {
				// Remove the row if the user Control clicked it
				if (keyControl.value || (!keyShift.value && !keyControl.value)) {
					let selectedIndex = selectedRows.findIndex((x) => x.row_id === row_id)
					if (selectedIndex > -1) {
						selectedRows.splice(selectedIndex, 1)
					}

					document.getElementById(row_id).classList.remove(...classesToToggle)
					updateSelectedRowsPersistence()
				}
				return false
			}

			if (!keyShift.value && !keyControl.value) {
				selectedRows = [
					{
						row_id: row_id,
						id: row.id,
					},
				]

				let selected = document.querySelector('table.' + props.name + ' tbody tr.selected')
				if (selected) selected.classList.remove(...classesToToggle)

				document.getElementById(row_id).classList.add(...classesToToggle)

				emit('rowSelected', row)
			} else if (keyShift.value) {
				let ids = selectedRows
					.map(function (selectedRow) {
						return selectedRow.id
					})
					.sort()

				let result = selectedRows.filter((obj) => {
					return obj.id === ids[0]
				})

				console.log(ids, result)

				// selectedRows = {}
			} else if (keyControl.value) {
				selectedRows.push({
					row_id: row_id,
					id: row.id,
				})

				document.getElementById(row_id).classList.add(...classesToToggle)
			}

			console.log(selectedRows)

			updateSelectedRowsPersistence()
		}

		const toggleColumnOptions = () => {
			columnSettingsOpen.value = !columnSettingsOpen.value
		}

		const isVisible = (elem) =>
			!!elem && !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length)

		watch(params, (newValue, oldValue) => {
			updateTableState()
		})

		watch(columns, (newValue, oldValue) => {
			updateColumnsPersistence()
		})

		return {
			computeIndex,
			getColumnHeader,
			getColumnValue,
			updatePaginationLimit,
			clearFilters,
			selectRow,
			toggleColumnOptions,
			params,
			columns,
			columnSettingsOpen,
		}
	},
}
</script>
