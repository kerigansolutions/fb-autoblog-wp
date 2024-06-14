<template>
  <div>
    <p v-if="businesses.length > 0" class="text-gray-400 text uppercase font-bold mb-2">Select a Facebook Business Page To Sync:</p>
    <div
      v-for="business in businesses"
      :key="business.index"
      class="p-2 bg-gray-200 rounded flex items-center mb-1 border border-gray-300 group group-hover:bg-accent cursor-pointer"
      @click.prevent="selectBusiness(business)"
    >
      <strong class="text-primary px-4">{{ business.name }}</strong>
      <span class="text-sm text-gray-800" > {{ business.id }}</span>
      <button class="px-3 py-1 ml-auto leading-none h-8 border-2 uppercase rounded text-primary border-primary group-hover:bg-primary group-hover:text-white group-hover:border-transparent" >select</button>
    </div>
    <div class="flex" >
      <button
        @click.prevent="authorize"
        class="form-button bg-accent hover:bg-white border-2 border-transparent hover:border-accent text-primary rounded"
      >
        <slot />
      </button>
    </div>
  </div>
</template>
<script>
export default {
  name: "FacebookAuth",

  data() {
    return {
      accessToken: undefined,
      data_access_expiration_time: undefined,
      expiresIn: undefined,
      graphDomain: undefined,
      signedRequest: undefined,
      userID: undefined,
      status: undefined,
      longLivedToken: undefined,
      businesses: []
    }
  },

  methods: {
    selectBusiness (business) {
      document.getElementById('fbcompanyid').value = business.id;

      fetch("/wp-json/kerigansolutions/v1/autoblogtoken?token=" + business.access_token, {
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
          this.longLivedToken = res.access_token;
          document.getElementById('facebooktoken').value = res.access_token;
        })

      this.businesses = []
    },

    authorize () {
      let auth = this

      let companies = []

      FB.login(function (response) {
        auth.accessToken = response.authResponse.accessToken
        auth.data_access_expiration_time = response.authResponse.data_access_expiration_time
        auth.expiresIn = response.authResponse.expiresIn
        auth.graphDomain = response.authResponse.graphDomain
        auth.signedRequest = response.authResponse.signedRequest
        auth.userID = response.authResponse.userID,
        auth.status = response.status

        FB.api('/' + auth.userID + '/accounts', function (response) {
          console.log(response)
          companies = response.data

          if(response.paging.next) {
            fetch(response.paging.next, {
              method: 'GET',
              mode: 'cors',
              cache: 'no-cache',
              headers: {
                'Content-Type': 'application/json',
              },
            }).then(r => r.json()).then(res => {
              console.log(res)

              if(res.data) {
                auth.businesses = companies.concat(res.data)
              }
            })
          }

        })

      }, {
        scope: 'pages_show_list, pages_read_engagement, pages_read_user_content, public_profile, business_management'
      });
    }
  }
}
</script>
