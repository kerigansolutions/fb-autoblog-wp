<template>
  <div>
    <p v-if="businesses.length > 0" class="text-gray-400 text uppercase font-bold mb-2">Select a Facebook Business Page To Sync:</p>
    <div v-else-if="accessToken && !loading" class="p-4 mb-4 border-error border-2" >
      <p class="text-gray-800 text-lg lg:text-2xl text font-bold mb-2">No Facebook business pages found.</p>
      <p class="text-gray-800 text mb-2">Did you log into the correct account? You must be an admin on the Business page you'd like to sync to your website.</p>
    </div>
    <div
      v-for="(business, index) in businesses"
      :key="business.index"
      class="px-4 py-2 bg-gray-200 rounded flex items-center gap-4 mb-1 border border-gray-300 group group-hover:bg-accent cursor-pointer"
      @click.prevent="selectBusiness(business)"
    > <strong class="inline-block h-6 w-6 rounded-full bg-gray-200 text-gray-700 flex justify-center items-center">{{ index+1 }}</strong>
      <strong class="text-primary">{{ business.name }}</strong>
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
      businesses: [],
      loading: true,
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
          // console.log(res)
          this.longLivedToken = res.access_token;
          document.getElementById('facebooktoken').value = res.access_token;
        })

      this.businesses = []
      this.loading = true
    },

    authorize () {
      let auth = this

      FB.login(function (response) {
        auth.accessToken = response.authResponse.accessToken
        auth.data_access_expiration_time = response.authResponse.data_access_expiration_time
        auth.expiresIn = response.authResponse.expiresIn
        auth.graphDomain = response.authResponse.graphDomain
        auth.signedRequest = response.authResponse.signedRequest
        auth.userID = response.authResponse.userID,
        auth.status = response.status

        FB.api('/' + auth.userID + '/accounts', function (response) {
          // console.log(response)
          auth.businesses = response.data

          if(response.data && response.paging && response.paging.next) {

            fetch(response.paging.next, {
              method: 'GET',
              mode: 'cors',
              cache: 'no-cache',
              headers: {
                'Content-Type': 'application/json',
              },
            }).then(r => r.json()).then(res => {
              // console.log(res)

              if(res.data) {
                auth.businesses = auth.businesses.concat(res.data)

                if(res.data && res.paging && res.paging.next) {

                  fetch(res.paging.next, {
                    method: 'GET',
                    mode: 'cors',
                    cache: 'no-cache',
                    headers: {
                      'Content-Type': 'application/json',
                    },
                  }).then(r => r.json()).then(res2 => {

                    if(res2.data) {
                      auth.businesses = auth.businesses.concat(res2.data)
                    }

                    auth.loading = false
                    
                  })
                }
              }

              auth.loading = false
              
            })
          } else {
            auth.loading = false
          }

        })

      }, {
        scope: 'pages_show_list, pages_read_engagement, pages_read_user_content, public_profile, business_management'
      });
    }
  }
}
</script>
