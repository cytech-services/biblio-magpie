<template>
	<default-layout :title="$options.name">
		<page-header :title="$options.name" />

		<main class="min-h-full">
			<div class="w-full h-full mx-auto py-6 sm:px-6 lg:px-8">
				<div class="px-4 py-4 sm:px-0">
					<ag-grid-vue
						class="w-full h-[70vh] ag-theme-alpine"
						rowModelType="serverSide"
						:pagination="true"
						paginationPageSize="15"
						:columnDefs="columnDefs"
						@grid-ready="onGridReady"
						:defaultColDef="defaultColDef"
						:sideBar="sideBar"
						:rowData="rowData"
						rowSelection="multiple"
					></ag-grid-vue>
				</div>

				<button @click="getSelectedRows()">Get Selected Rows</button>
			</div>
		</main>
	</default-layout>
</template>

<script>
import { reactive, onMounted, ref } from 'vue'
import DefaultLayout from '@/Layouts/Default.vue'
import PageHeader from '@/Components/Header.vue'
import 'ag-grid-enterprise'
import { AgGridVue } from 'ag-grid-vue3'

export default {
	name: 'Library',
	components: {
		DefaultLayout,
		PageHeader,
		AgGridVue,
	},
	props: {},
	setup() {
		let rowData = reactive([])
		let gridApi = ref(null)
		let gridColumnApi = ref(null)
		let columnApi = ref(null)
		let sideBar = ref(null)

		onMounted(() => {
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

		const getSelectedRows = () => {
			const selectedNodes = gridApi.getSelectedNodes()
			const selectedData = selectedNodes.map((node) => node.data)
			const selectedDataStringPresentation = selectedData
				.map((node) => `${node.make} ${node.model}`)
				.join(', ')
			console.log(`Selected nodes: ${selectedDataStringPresentation}`)
		}

		return {
			columnDefs: [
				{
					field: 'library',
					minWidth: 200,
					filter: 'agTextColumnFilter',
					checkboxSelection: true,
				},
				{ field: 'title' },
				{ field: 'sub_title', minWidth: 200 },
				{ field: 'authors', minWidth: 200 },
				{ field: 'size_on_disk', maxWidth: 130 },
				{ field: 'rating', maxWidth: 130, filter: false },
				{ field: 'categories', filter: false },
				{ field: 'series', filter: false },
				{ field: 'publisher', filter: false },
				{ field: 'publish_date', maxWidth: 130 },
				{ field: 'language', maxWidth: 120 },
				{ field: 'formats' },
			],
			defaultColDef: {
				flex: 1,
				minWidth: 100,
				filter: true,
				resizable: true,
			},
			sideBar,
			rowData,
			onGridReady,
			getSelectedRows,
		}
	},
}
</script>
