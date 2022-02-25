<template>
	<default-layout :title="$options.name">
		<page-header :title="$options.name" />

		<!-- <button @click="getSelectedRows()">Get Selected Rows</button> -->
		<main class="min-h-full flex flex-col xl:flex-row">
			<div class="flex-1 xl:basis-auto w-full h-full">
				<div class="library m-5">
					<Datatable name="library" :row-data="books" :column-defs="columnDefs" />
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
import { Inertia } from '@inertiajs/inertia'
import DefaultLayout from '@/Layouts/Default.vue'
import PageHeader from '@/Components/Header.vue'
import imageCellRenderer from '@/Components/Table/imageCellRenderer.vue'
import BookPreview from '@/Components/Library/BookPreview.vue'

import Datatable from '@/Components/Table/Datatable.vue'

export default {
	name: 'Library',
	components: {
		DefaultLayout,
		PageHeader,
		imageCellRenderer,
		BookPreview,
		Datatable,
	},
	props: {
		books: {
			type: Object,
		},
	},
	setup(props) {
		var darkMode = ref(false)

		let gridColumnApi = ref(null)

		let selectedBook = reactive({})

		const isDarkMode = computed(() => {
			return darkMode.value === 'dark'
		})

		onMounted(() => {
			window.addEventListener('theme-changed', (event) => {
				darkMode.value = event.detail.theme
			})
		})

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
			selectedBook,

			columnDefs: [
				{
					field: 'library.name',
					filter: 'library.name',
					headerName: 'Library',
					maxWidth: 'max-w-[10rem]',
				},
				// {
				// 	field: 'thumbnail_image.url',
				// 	filter: false,
				// 	headerName: 'Image',
				// 	display: {
				// 		type: 'image',
				// 		height: 'h-28',
				// 	},
				// },
				{
					field: 'title',
					filter: 'title',
				},
				{
					field: 'sub_title',
					filter: 'sub_title',
					headerName: 'Subtitle',
				},
				{
					field: 'authors',
					filter: 'authors.name',
					display: {
						type: 'list',
						field: 'name',
					},
				},
				{
					field: 'rating',
					filter: false,
					display: {
						type: 'rating',
					},
				},
				{
					field: 'categories',
					filter: 'categories.name',
					display: {
						type: 'list',
						field: 'name',
					},
				},
				{
					field: 'series',
					filter: 'series.name',
					display: {
						type: 'list',
						field: 'name',
					},
				},
				{ field: 'publisher.name', filter: 'publisher.name', headerName: 'Publisher' },
				{
					field: 'publish_date',
					filter: false,
					headerName: 'Publish Date',
				},
				{ field: 'language', filter: 'language', maxWidth: 'max-w-[8rem]' },
				// {
				// 	field: 'media',
				// 	headerName: 'Formats',
				// 	display: {
				// 		type: 'list',
				// 		field: 'file_format.name',
				// 	},
				// 	floatingFilter: true,
				// },
			],
			getSelectedRows,
			onRowSelected,
		}
	},
}
</script>
