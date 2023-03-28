import { defineConfig } from 'vitepress'
import Unocss from 'unocss/vite'

export default defineConfig({
    title: 'Rocket',
    description: 'A free, open-source, and self-hosted alternative to services like Envoyer.',

    themeConfig: {
        nav: [
            { text: 'Guide', link: '/guide/', activeMatch: '/guide/' },
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