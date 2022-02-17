<template>
    <app-layout>
        <Head title="Dashboard" />
        <h1>Welcome</h1>
        <p>Hello, welcome to your first Inertia app!</p>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/Default.vue'
import { Head } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Head,
        AppLayout,
    },
    props: {
        // user: Object,
    },
}
</script>
