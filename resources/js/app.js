import '../css/app.css'
import './bootstrap.js'
import 'vue-toastification/dist/index.css'

import { createApp, h } from 'vue'

import Echo from 'laravel-echo';
import { InertiaProgress } from '@inertiajs/progress'
import Toast from 'vue-toastification'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { useToast } from 'vue-toastification'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel'
const toast = useToast()

let asyncViews = () => {
    return import.meta.glob('./Pages/**/*.vue')
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        if (import.meta.env.DEV) {
            // console.log(import.meta.env)
            return (await import(`./Pages/${name}.vue`)).default
        } else {
            let pages = asyncViews()
            const importPage = pages[`./Pages/${name}.vue`]
            return importPage().then((module) => module.default)
        }
    },
    setup({ el, app, props, plugin }) {
        const myApp = createApp({ render: () => h(app, props) })
            .use(Toast)
            .use(plugin)
            .mixin({ methods: { route } })

        // config global property after createApp and before mount
        myApp.config.globalProperties.$route = route
        myApp.config.globalProperties.$toast = toast

        myApp.mount(el)
        return myApp
    },
})

InertiaProgress.init({ color: '#4B5563' })

// import { App, plugin as inertiaPlugin } from '@inertiajs/inertia-vue3'
// import Toast, { POSITION } from 'vue-toastification'
// import { createApp, h } from 'vue'

// import { InertiaProgress } from '@inertiajs/progress'

// const el = document.getElementById('app')!

// let asyncViews = () => {
// 	return import.meta.glob('./Pages/**/*.vue')
// }

// createApp({
// 	render: () =>
// 		h(App, {
// 			initialPage: JSON.parse(el.dataset.page!),
// 			resolveComponent: async (name: string) => {
// 				if (import.meta.env.DEV) {
// 					// console.log(import.meta.env)
// 					return (await import(`./Pages/${name}.vue`)).default
// 				} else {
// 					let pages = asyncViews()
// 					const importPage = pages[`./Pages/${name}.vue`]
// 					return importPage().then((module) => module.default)
// 				}
// 			},
// 		}),
// })
// 	.use(inertiaPlugin)
// 	.use(Toast, {
// 		position: POSITION.TOP_CENTER,
// 	})
// 	.use(route)
// 	.mount(el)

// InertiaProgress.init({ color: '#4B5563' })
