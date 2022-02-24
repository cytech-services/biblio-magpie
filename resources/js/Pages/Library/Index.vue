<template>
	<default-layout :title="$options.name">
		<page-header :title="$options.name" />

		<button @click="getSelectedRows()">Get Selected Rows</button>
		<main class="min-h-full flex flex-col xl:flex-row">
			<div class="flex-1 xl:basis-auto w-full h-full">
				<div class="library">
					<ag-grid-vue
						:class="[
							isDarkMode ? 'ag-theme-alpine-dark' : 'ag-theme-alpine',
							'w-full library-table xl:library-table-full',
						]"
						rowModelType="serverSide"
						rowHeight="120"
						:pagination="true"
						paginationPageSize="15"
						:columnDefs="columnDefs"
						:defaultColDef="defaultColDef"
						:rowData="rowData"
						rowSelection="multiple"
						@grid-ready="onGridReady"
						@row-selected="onRowSelected"
						@sort-changed="onSortChanged"
						@column-resized="saveTableState"
						@column-visible="saveTableState"
						@column-pivot-changed="saveTableState"
						@column-row-group-changed="saveTableState"
						@column-value-changed="saveTableState"
						@column-moved="saveTableState"
						@column-pinned="saveTableState"
					></ag-grid-vue>
				</div>
			</div>

			<div
				v-if="selectedBook.value"
				class="flex-1 xl:basis-[30rem] px-5 pb-5 library-table xl:library-table-full overflow-y-auto"
			>
				<BookPreview :selected-book="selectedBook.value" />
			</div>
		</main>
	</default-layout>
</template>

<script>
import { reactive, onMounted, ref, watch, computed } from 'vue'
import DefaultLayout from '@/Layouts/Default.vue'
import PageHeader from '@/Components/Header.vue'
import 'ag-grid-enterprise'
import { AgGridVue } from 'ag-grid-vue3'
import imageCellRenderer from '@/Components/Table/imageCellRenderer.vue'
import BookPreview from '@/Components/Library/BookPreview.vue'

export default {
	name: 'Library',
	components: {
		DefaultLayout,
		PageHeader,
		AgGridVue,
		imageCellRenderer,
		BookPreview,
	},
	props: {},
	setup() {
		var darkMode = ref(false)

		let rowData = reactive([])
		let gridApi = ref(null)
		let gridColumnApi = ref(null)
		let columnApi = ref(null)
		let sideBar = ref(null)

		let selectedBook = reactive({})

		const isDarkMode = computed(() => {
			return darkMode.value === 'dark'
		})

		onMounted(() => {
			window.addEventListener('theme-changed', (event) => {
				darkMode.value = event.detail.theme
			})

			sideBar.value = {
				toolPanels: [
					{
						id: 'columns',
						labelDefault: 'Columns',
						labelKey: 'columns',
						iconKey: 'columns',
						toolPanel: 'agColumnsToolPanel',
						toolPanelParams: {
							suppressSyncLayoutWithGrid: true,
							suppressPivots: true,
							suppressPivotMode: true,
							suppressColumnMove: true,
							suppressRowGroups: true,
							suppressValues: true,
						},
					},
					{
						id: 'filters',
						labelDefault: 'Filters',
						labelKey: 'filters',
						iconKey: 'filter',
						toolPanel: 'agFiltersToolPanel',
						toolPanelParams: {
							suppressExpandAll: false,
							suppressFilterSearch: false,
							suppressSyncLayoutWithGrid: true,
						},
					},
				],
				defaultToolPanel: 'columns',
			}
		})

		const onGridReady = (params) => {
			gridApi = params.api
			columnApi = params.columnApi
			gridColumnApi = params.columnApi

			// If there is a saved state then apply it
			if (localStorage.libraryTableState) {
				gridColumnApi.applyColumnState({
					state: JSON.parse(localStorage.libraryTableState),
				})
			}

			const updateData = (data) => {
				// setup the fake server with entire dataset
				var fakeServer = createFakeServer(data)
				// create datasource with a reference to the fake server
				var datasource = createServerSideDatasource(fakeServer)
				// register the datasource with the grid
				params.api.setServerSideDatasource(datasource)
			}

			fetch('/api/library/fetch_books')
				.then((resp) => resp.json())
				.then((data) => updateData(data))
		}

		// Save column state changes to the local storage for persistance
		const saveTableState = () => {
			let state = gridColumnApi.getColumnState()
			localStorage.libraryTableState = JSON.stringify(state)
		}

		const getSelectedRows = () => {
			const selectedNodes = gridApi.getSelectedNodes()
			const selectedData = selectedNodes.map((node) => node.data)
			const selectedDataStringPresentation = selectedData
				.map((node) => `${node.make} ${node.model}`)
				.join(', ')
			console.log(`Selected nodes: ${selectedDataStringPresentation}`)
		}

		const onRowSelected = (event) => {
			if (event.node.isSelected()) {
				selectedBook.value = event.node.data
			}
		}

		return {
			darkMode,
			isDarkMode,
			saveTableState,
			selectedBook,
			columnDefs: [
				{
					field: 'library',
					maxWidth: 130,
				},
				{
					field: 'thumbnail',
					headerName: 'Image',
					maxWidth: 120,
					resizable: false,
					filter: false,
					cellRenderer: 'imageCellRenderer',
				},
				{
					field: 'title',
					minWidth: 200,
					wrapText: true,
				},
				{
					field: 'sub_title',
					headerName: 'Subtitle',
					minWidth: 200,
					wrapText: true,
				},
				{
					field: 'authors',
					minWidth: 200,
					wrapText: true,
				},
				{
					field: 'size_on_disk',
					headerName: 'Size On Disk',
					maxWidth: 130,
				},
				{
					field: 'rating',
					maxWidth: 130,
				},
				{
					field: 'categories',
					wrapText: true,
				},
				{ field: 'series', floatingFilter: true },
				{ field: 'publisher', floatingFilter: true },
				{
					field: 'publish_date',
					headerName: 'Publish Date',
					maxWidth: 130,
				},
				{ field: 'language', maxWidth: 120, floatingFilter: true },
				{ field: 'formats', floatingFilter: true },
			],
			defaultColDef: {
				flex: 1,
				minWidth: 100,
				filter: true,
				resizable: true,
				filter: 'agTextColumnFilter',
				floatingFilter: true,
			},
			sideBar,
			rowData,
			onGridReady,
			getSelectedRows,
			onRowSelected,
		}
	},
}
</script>
