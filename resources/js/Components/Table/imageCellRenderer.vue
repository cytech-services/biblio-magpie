<template>
	<img :src="thumbnail" style="height: 100%; width: 100%" />
</template>

<script>
export default {
	setup(props) {
		const thumbnail = props.params.value

		return {
			thumbnail,
		}
	},
}
</script>
