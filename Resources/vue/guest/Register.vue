<template>
  <div>
    <div class="offset-sm-2 offset-lg-3 col-sm-8 col-lg-6 h-screen">
      <div class="card mt-5">
        <div class="card-body">

          <div class="text-center  mb-3">
            <img class="m-2 inline-block" :src="$assets_url + 'images/logos/logo.png'" alt=""
              style="max-width:160px;" />
          </div>

          <Vueform :model-value="model" :sync="true" @submit="register()">

            <div class="alert alert-primary" role="alert">
              Account Registration
            </div>

            <TextElement name="first_name" label="First Name" id="first_name" :debounce="500" rules="required" />

            <TextElement name="last_name" label="Last Name" id="last_name" :debounce="500" rules="required" />

            <TextElement name="username" label="Username" id="username"
              info="Your username should be smallcase alphabet and unique." :debounce="500"
              rules="required|regex:/^[a-z]+$/|unique:checkuser"
              :messages="{ regex: 'Your username should be smallcase alphabet' }" />

            <TextElement name="password" label="Password" id="password"
              info="Enter Password with; <br>Character: a-z <br> One Character: A-Z <br>Numbers: 0-9 and <br> Special Character: #$%&-_@"
              input-type="password" :debounce="500" rules="required|regex:/^[a-zA-Z0-9#$%&\-_@]+$/" />

            <TextElement name="phone" label="Phone" id="phone" input-type="phone" :debounce="500"
              rules="required|regex:/^\(?\d{3}\)?[-.]?\s?\d{3}[-.]?\s?\d{4}$/" />

            <TextElement name="email" label="Email Address" id="email" info="Your Email address should Be unique."
              input-type="email" :debounce="500" rules="required|email|unique:checkuser" />


            <div class="text-center reg-step-button">
              <ButtonElement name="register" :button-class="['font-semibold', 'btn', 'btn-primary']" @click="register">
                Register
              </ButtonElement>

              <div class="text-xs">Or</div>
              <router-link class="btn btn-link btn-sm register-button mb-3" :to="'/login'">
                Login
              </router-link>
            </div>

          </Vueform>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  created() {
  },
  data: () => ({
    loading: false,
    reg_form: null,
    model: {
      id: "",
      first_name: "",
      last_name: "",
      username: "",
      password: "",
      email: "",
      phone: "",
    },
    user: {
      id: "",
      first_name: "",
      last_name: "",
      username: "",
      email: "",
      phone: "",
    },
  }),
  methods: {

    async register(form$) {

      const data = form$.data // form data including conditional data
      const requestData = form$.requestData // form data excluding conditional data

      await form$.validate();

      console.log(form$.hasErrors);


      if (!form$.hasErrors) {

        await this.$axios.post("/registeruser", requestData).then((res) => {
          if (res.data.has_error) {
            this.notification(res.data.error_message);
          } else {
            this.notification("User Registered Successfully", "success");
            this.$router.push("/login");
          }
        });

      }

    },
    checkuser(field) {
      var t = this;

      var tmp_query_str =
        "?username=" +
        this.model.username.toLowerCase();

      if (field == 'email') {
        tmp_query_str =
          "?email=" +
          this.model.email.toLowerCase();
      }

      window.axios.get("/user/checkuser/" + tmp_query_str).then((res) => {
        if (res.data.has_error) {
          t.error[field] = res.data.error_message;
          t.notification("Error:" + res.data.error_message);
        }
      });
    },
    notification(message, type = "error") {
      this.$notify({
        title: type.toUpperCase() + " MESSAGE",
        text: message,
        type: type,
      });
    },
  },
};
</script>
<style>
#main-wrapper {
  overflow: scroll;
  overflow-x: hidden;
}

.vf-element-error,
.vf-errors div {
  color: red !important;
  font-size: 12px !important;
}

</style>
