import "./bootstrap"
import Vue from "vue"
import moment from 'moment';
import { InertiaApp } from "@inertiajs/inertia-vue"
import { InertiaProgress } from '@inertiajs/progress'

InertiaProgress.init({
  delay: 0,
  color: 'rgb(236, 72, 153)',
  includeCSS: true,
  showSpinner: false,
})

Vue.use(InertiaApp)

Vue.prototype.$route = route
Vue.prototype.$moment = moment
Vue.prototype._ = window._;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

const app = document.getElementById('app')

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => require(`./pages/${name}`).default,
        },
    }),
}).$mount(app)
