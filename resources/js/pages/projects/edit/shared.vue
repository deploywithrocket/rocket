<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.edit', project)" class="text-gray-500 hover:underline">Settings</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Shared
        </nav>

        <div class="flex flex-col md:flex-row">
            <project-edit-nav :project="project" />

            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <h2>Shared</h2>
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <div class="w-full px-5 py-4">
                        <div class="mb-8">
                            <div class="mb-4">
                                <p class="mb-2">On this page, you can specify files and directories that Rocket will symlink into your release on each deployment.</p>

                                <p class="mb-2">The process is split into the following steps:</p>
                                <ul class="mb-2">
                                    <li class="ml-2">- Copy dir from <span class="font-mono">release_path</span> to <span class="font-mono">shared</span> if doesn’t exists,</li>
                                    <li class="ml-2">- Delete dir from <span class="font-mono">release_path</span>,</li>
                                    <li class="ml-2">- Symlink dir from <span class="font-mono">shared</span> to release_path.</li>
                                </ul>

                                <p class="mb-2">The same steps are followed for shared files.</p>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h2 class="mb-4 text-xl font-bold">Linked directories</h2>

                            <div class="mb-4">
                                <div class="flex flex-wrap items-start justify-start -mx-2">
                                    <div
                                        class="p-4 mx-2 mb-4 font-mono border rounded "
                                        v-for="(dir) in form.linked_dirs"
                                        v-bind:key="dir"
                                    >
                                        {{ dir }}<button @click.prevent="rmDir(dir)" class="px-4 py-2 ml-4 text-sm font-semibold text-red-500 bg-gray-100 rounded focus:outline-none hover:bg-gray-200"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>

                                <form @submit.prevent="addDir">
                                    <div class="flex items-end justify-start">
                                        <form-input label="Add the following directory" v-model="add_dir" class="flex-grow" />
                                        <button type="submit" class="px-4 py-2 ml-4 text-sm font-semibold bg-gray-100 rounded focus:outline-none hover:bg-gray-200"><i class="fa fa-plus"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h2 class="mb-4 text-xl font-bold">Linked files</h2>

                            <div class="mb-4">
                                <div class="flex flex-wrap items-start justify-start -mx-2">
                                    <div
                                        class="p-4 mx-4 mb-4 font-mono border rounded "
                                        v-for="(file) in form.linked_files"
                                        v-bind:key="file"
                                    >
                                        {{ file }}<button @click.prevent="rmFile(file)" class="px-4 py-2 ml-4 text-sm font-semibold text-red-500 bg-gray-100 rounded focus:outline-none hover:bg-gray-200"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>

                                <form @submit.prevent="addFile">
                                    <div class="flex items-end justify-start">
                                        <form-input label="Add the following file" v-model="add_file" class="flex-grow" />
                                        <button type="submit" class="px-4 py-2 ml-4 text-sm font-semibold bg-gray-100 rounded focus:outline-none hover:bg-gray-200"><i class="fa fa-plus"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="fas fa-check"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../../layouts/app').default,

        props: {
            project: Object,
        },

        data() {
            return {
                add_dir: '',
                add_file: '',
                form: {
                    linked_dirs: [],
                    linked_files: [],
                }
            }
        },

        mounted() {
            this.form.linked_dirs = this.project.linked_dirs || []
            this.form.linked_files = this.project.linked_files || []
        },

        methods: {
            submit() {
                this.$page.props.errors = {}

                this.$inertia.put(
                    this.$route('projects.update.shared', this.project), { ...this.form }
                )
            },
            addDir(){
                if (!_.find(this.form.linked_dirs, (elem) => elem == this.add_dir))
                    this.form.linked_dirs.push(this.add_dir)
                this.add_dir = ''
            },
            rmDir(dir){
                this.form.linked_dirs = _.reject(this.form.linked_dirs, (elem) => elem == dir)
            },
            addFile(){
                if (!_.find(this.form.linked_files, (elem) => elem == this.add_file))
                    this.form.linked_files.push(this.add_file)
                this.add_file = ''
            },
            rmFile(file){
                this.form.linked_files = _.reject(this.form.linked_files, (elem) => elem == file)
            }
        }
    }
</script>
