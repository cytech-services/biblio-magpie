import '../css/app.css'
import './bootstrap.js'
import 'vue-toastification/dist/index.css'

import { plugin as FormkitPlugin, defaultConfig } from '@formkit/vue'
import { createApp, h } from 'vue'

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
            .use(FormkitPlugin, defaultConfig({
                config: {
                    rootClasses(sectionKey, node) {
                        // this is an incomplete theme for demonstration purposes
                        const type = node.props.type
                        // create a classConfig object that contains either strings or functions
                        // that return strings. We'll loop over the output later to create our
                        // class objects of keys with boolean values.
                        const classConfig = {
                            outer: 'mb-5', // string values apply to everything
                            legend: 'block mb-1 font-bold',
                            label() { // functions that return strings allow you to diff on types
                                if (type === 'text') { return 'block mb-1 font-bold' }
                                if (type === 'radio') { return 'text-sm text-gray-600 mt-0.5' }
                            },
                            options() {
                                if (type === 'radio') { return 'flex flex-col flex-grow mt-2' }
                            },
                            input() {
                                if (type === 'text') { return 'w-full h-10 px-3 mb-2 text-base text-gray-700 placeholder-gray-400 border rounded-lg focus:shadow-outline' }
                                if (type === 'radio') { return 'mr-2' }
                            },
                            wrapper() {
                                if (type === 'radio') { return 'flex flex-row flex-grow' }
                            },
                            help: 'text-xs text-gray-500'
                        }

                        // helper function to created class object from strings
                        function createClassObject(classesArray) {
                            const classList = {}
                            if (typeof classesArray !== 'string') return classList
                            classesArray.split(' ').forEach(className => {
                                classList[className] = true
                            })
                            return classList
                        }

                        const target = classConfig[sectionKey]
                        // return a class objects from strings and return them for each
                        // section key defined by inputs in FormKit
                        if (typeof target === 'string') {
                            return createClassObject(target)
                        } else if (typeof target === 'function') {
                            return createClassObject(target())
                        }

                        return {} // if no matches return an empty object
                    }
                }
            }))
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
