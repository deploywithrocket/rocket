import { resolve } from 'node:path'
import { readFileSync } from 'node:fs'
import { defineConfig } from 'vitepress'
import Unocss from 'unocss/vite'

const title = 'Rocket'
const description = 'A free, open-source, and self-hosted alternative to services like Envoyer.'
const url = 'https://deploywithrocket.dev'
const image = '/og.jpg'
const twitter = 'mgkprod'
const github = 'https://github.com/deploywithrocket/core'

const version = readFileSync(resolve('config/.version'), { encoding: 'utf-8' })

export default defineConfig({
    title,
    description,

    themeConfig: {
        logo: '/logo.svg',

        nav: [
            { text: 'Guide', link: '/guide/', activeMatch: '/guide/' },
            { text: `v${version}`, link: `${github}/releases/tag/${version}` },
        ],

        socialLinks: [
            { icon: 'twitter', link: `https://twitter.com/${twitter}` },
            { icon: 'github', link: `${github}` },
        ],

        sidebar: {
            '/guide/': [
                {
                    text: 'Getting started',
                    items: [
                        {
                            text: 'What is Rocket?',
                            link: '/guide/',
                        },
                        {
                            text: 'Questions and issues',
                            link: '/guide/questions-and-issues',
                        },
                    ],
                },
            ],
        },
    },

    vite: {
        plugins: [
            Unocss(),
        ],
    },
})