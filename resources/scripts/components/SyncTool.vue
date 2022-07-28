<template>
  <div>
    <div class="mb-6 bg-gray-300 rounded px-4 py-3" >
      <div><strong>Number in DB:</strong> {{ objects.length }}
        <a
          :href="'https://mexicobeach.test/wordpress/wp-admin/edit.php?post_type=' + endpoint"
          class="text-primary underline ml-4"
          target="_blank"
        >manage</a></div>
    </div>
    <div class="flex space-x-6" >
      <button
        @click="sync(4)"
        class="form-button bg-primary hover:bg-white border-2 border-transparent hover:border-primary text-white hover:text-primary rounded"
      >Sync</button>
      <button
        @click="sync(99)"
        class="form-button bg-accent hover:bg-white border-2 border-transparent hover:border-accent text-primary rounded"
      >Build</button>
      <div class="flex items-center">{{ status }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SyncTool",

  props: {
    endpoint: {
      type: String,
      default: ""
    }
  },

  data () {
    return {
      objects: [],
      status: ''
    }
  },

  mounted () {
    this.get()
  },

  methods: {
    get () {
      fetch("/wp-json/kerigansolutions/v1/get-" + this.endpoint, {
        method: 'GET',
        mode: 'cors',
        cache: 'no-cache',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then(r => r.json())
        .then((res) => {
          this.objects = res
        })
    },

    sync ( num = 30 ) {
      this.status = 'getting data...'

      fetch("/wp-json/kerigansolutions/v1/sync-" + this.endpoint + '?num=' + num, {
        method: 'GET',
        mode: 'cors',
        cache: 'no-cache',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then(r => r.json())
        .then((res) => {
          console.log(res)
          this.get()
          this.status = 'Success!'
        })
    },
  }
}
</script>
