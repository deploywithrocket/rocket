<template>
  <Head title="Settings" />

  <div class="flex flex-row items-center mb-8">
    <div>
      <h1 class="text-base font-bold">Settings</h1>
      <p class="text-sm text-gray-500">Manage your account settings.</p>
    </div>
  </div>

  <div>
    <div v-if="hasGithubAccount">
      <!-- <div class="flex items-center space-x-2">
        <h1 class="text-base font-bold">Great!</h1>
      </div>
      <p class="mt-4">Next, let's connect to a source control and server provider so Rocket can deploy your code.</p> -->

      <div class="flex flex-col gap-2 card">
        <div class="card-body">
          <h5 class="font-bold">Source Control</h5>
          <p>Rocket needs to know where to find your application's code. Connect with one or more of your preferred source control providers below to get started. You can always connect to additional source control providers at a later time within your account settings.</p>
          <div class="grid w-1/2 grid-cols-2 gap-4 mt-4">
            <a :href="route('auth.github')" class="h-14 btn btn-secondary"><Icon icon="fa:github-alt" /> Connect to GitHub</a>
            <button disabled="disabled" href="#" class="h-14 btn btn-secondary"><Icon icon="fa:gitlab" /> Connect to GitLab</button>
            <button disabled="disabled" href="#" class="h-14 btn btn-secondary"><Icon icon="fa:bitbucket" /> Connect to Bitbucket</button>
            <button disabled="disabled" href="#" class="h-14 btn btn-secondary"><Icon icon="fa:git" /> Use a custom VCS</button>
          </div>
        </div>
      </div>
    <!-- We are now ready for take off! -->
    </div>
  </div>
</template>

<script>
import App from '@/Layouts/App.vue'

export default {
  layout: App,

  props: {
    hasGithubAccount: Boolean,
  },
}
</script>