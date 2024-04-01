<template>
  <div :class="$is_backend ? 'h-screen h-100' : ''" class="mb-2 row justify-content-center align-items-center">
    <div class="col-md-6">
      <div class="authincation-content border rounded shadow bg-white">
        <div class="m-3">
          <div class="auth-form">
            <div class="text-center my-4">
              <img :src="$assets_url + 'images/logos/logo.png'" alt="" style="margin: 0 auto; max-width:160px;" />
            </div>

            <h4 class="font-semibold text-lg text-center my-6">Login to your account</h4>

            <Vueform :model-value="model" :sync="true" @submit="login()">

              <div class="py-1">
                <TextElement name="username" label="Username" id="username"
                  info="Your username should be smallcase alphabet and unique." :debounce="500"
                  rules="required"
                  :messages="{ regex: 'Your username should be smallcase alphabet' }"
                  
                  :add-class="{  input: 'h-12 bg-blue-50'}"/>

              </div>

              <div class="py-1">
                <TextElement name="password" label="Password" id="password" 
                  info="Enter Password with; <br>Character: a-z <br> One Character: A-Z <br>Numbers: 0-9 and <br> Special Character: #$%&-_@"
                  input-type="password" :debounce="500" rules="required|regex:/^[a-zA-Z0-9#$%&\-_@]+$/" :add-class="{  input: 'h-12  bg-blue-50'}"/>
              </div>


              <div class="flex mt-4" style="margin: 0 auto;">

                <div class="flex-auto">
                  <CheckboxElement name="remember" id="remember">
                    Remember Me
                  </CheckboxElement>
                </div>
                <div class="flex-auto text-right">
                  <ButtonElement name="login" :button-class="['font-semibold', 'btn', 'bg-green-400', 'px-8']"
                    @click="login" :loading="loading">
                    LOGIN
                  </ButtonElement>

                </div>
              </div>

              <div class="margin-top: -10px;">
                <router-link to="/forgotpassword">Forgot Password?</router-link>
              </div>

              <div class="new-account mt-3">
                <router-link to="/register" class="btn bg-orange-400 text-white w-full">CREATE ACCOUNT</router-link>
              </div>

            </Vueform>


          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  watch: {
    "$store.state.auth.token": {
      immediate: true,
      handler() {
        // update locally relevant data
        if (this.$store.getters["auth/loggedIn"]) {
          this.$store.dispatch("auth/getUser", { that: this });

          if (window.is_frontend) {
            this.$router.push("/dashboard");
          } else {
            this.$router.push("/manage/dashboard");
          }
        }
      },
    },
  },
  created() {
    if (window.autologin) {
      this.$store.dispatch("auth/autologin", { that: this });
    }
  },
  data: () => ({
    loading: false,
    has_register: false,
    model: {
      username: "",
      password: "",
      remember: false,
    },
  }),

  methods: {
    login() {
      let data = {
        username: this.model.username,
        password: this.model.password,
        that: this,
      };

      this.$store.dispatch("auth/authenticate", data);
    },
  },
};
</script>

<style>
.vf-errors{
  display:none;
}
.vf-element-error,
.vf-errors div {
  color: red !important;
  font-size: 12px !important;
}
</style>
