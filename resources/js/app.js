/* eslint-disable no-undef */
import '../css/app.css'
import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import moment from 'moment'
import { modal } from 'momentum-modal'
import { Icon } from '@iconify/vue'

function resolvePageComponent(name, pages) {
  for (const path in pages) {
    if (path.endsWith(`${name.replace('.', '/')}.vue`)) {
      return typeof pages[path] === 'function' ? pages[path]() : pages[path]
    }
  }

  throw new Error(`Page not found: ${name}`)
}

createInertiaApp({
  title: title => title ? `${title} | Rocket` : 'Rocket',
  progress: { color: '#e94d97', includeCSS: true },
  resolve: (name) => resolvePageComponent(name, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const Vue = createApp({ render: () => h(App, props) })
      .use(modal, {resolve: (name) => resolvePageComponent(name, import.meta.glob('./Pages/**/*.vue'))})
      .use(plugin)
      .use(ZiggyVue, Ziggy)

    const components = {
      ...import.meta.globEager('./Components/**/*.vue'),
      ...import.meta.globEager('./Shared/**/*.vue'),
    }

    Object.entries(components).forEach(([path, definition]) => {
      const componentName = path
        .split('/')
        .pop()
        .replace(/\.\w+$/, '')

      Vue.component(componentName, definition.default)
    })

    Vue.component('Link', Link)
    Vue.component('Head', Head)
    Vue.component('Icon', Icon)

    Vue.config.globalProperties.moment = moment

    return Vue.mount(el)
  },
})
