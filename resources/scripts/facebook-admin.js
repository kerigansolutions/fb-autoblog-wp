import { createApp } from "vue";
import FacebookAuth from "./components/FacebookAuth.vue";
import SyncTool from "./components/SyncTool.vue";

const app = createApp({

  components: {
    FacebookAuth: FacebookAuth,
    SyncTool: SyncTool,
  },

  data: () => ({
    mounted: false,
  }),

  mounted () {
    this.mounted = true;
  },

})

app.mount("#kma-facebook-settings")
