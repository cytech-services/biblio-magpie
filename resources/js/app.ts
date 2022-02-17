import '../css/app.css'
import './bootstrap.ts'

import { App, plugin as inertiaPlugin } from '@inertiajs/inertia-vue3'
// import Toast, { POSITION } from 'vue-toastification'
import { createApp, h } from 'vue'

import { InertiaProgress } from '@inertiajs/progress'

const el = document.getElementById('app')!

let asyncViews = () => {
    return import.meta.glob('./Pages/**/*.vue')
}

createApp({
    render: () =>
        h(App, {
            initialPage: JSON.parse(el.dataset.page!),
            resolveComponent: async (name: string) => {
                if (import.meta.env.DEV) {
                    // console.log(import.meta.env)
                    return (await import(`./Pages/${name}.vue`)).default
                } else {
                    let pages = asyncViews()
                    const importPage = pages[`./Pages/${name}.vue`]
                    return importPage().then((module) => module.default)
                }
            },
        }),
})
    .use(inertiaPlugin)
    // .use(Toast, {
    //     position: POSITION.TOP_CENTER,
    // })
    .mount(el)

InertiaProgress.init({ color: '#4B5563' })
