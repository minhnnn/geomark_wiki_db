import Vue from 'vue'
import {Link} from '@inertiajs/inertia-vue'
import {createInertiaApp} from '@inertiajs/inertia-vue'

Vue.prototype.$backendRoute = route;

createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        Vue.use(plugin)
        Vue.component('Link', Link)
        new Vue({
            render: h => h(App, props),
        }).$mount(el)
    },
})
