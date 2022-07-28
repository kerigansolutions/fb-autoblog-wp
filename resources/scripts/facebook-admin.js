import { createApp, defineAsyncComponent } from "vue";

const app = createApp({

  components: {
    FacebookAuth: defineAsyncComponent(() => import('./components/FacebookAuth.vue')),
    SyncTool: defineAsyncComponent(() => import('./components/SyncTool.vue')),
  },

  data: () => ({
    mounted: false,
  }),

  mounted () {
    this.mounted = true;
  },

})

app.mount("#kma-facebook-settings")
