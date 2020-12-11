<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Shared
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
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
                                    class="p-4 mx-2 mb-4 font-mono border rounded"
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
                                    class="p-4 mx-4 mb-4 font-mono border rounded"
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

                    <div class="flex justify-end mt-8">
                        <button type="submit" class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-pink-500 rounded-lg hover:bg-pink-600 focus:outline-none">Save</button>
                    </div>
                </div>
            </div>
        </form>
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
                this.$page.errors = {}

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
