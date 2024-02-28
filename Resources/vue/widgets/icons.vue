<template>
    <h4 class="font-semibold text-sm">Regular APPs</h4>
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-18 gap-2 overflow-hidden">
        <div v-for="(item, index) in $store.state.system.backendmenu" :key="index">
            
            <a :href="'#' + item.path" :title="title" class="text-center text-decoration-none"
                @click="loadModule(item.path, index)">
                <h2 :class="'border rounded-circle m-2 mb-0 h-10 w-10 text-2xl p-1 ' + item.class_str">
                    <i :class="item.icon"></i>
                </h2>

                <p class="text-black  text-xs">{{ item.title }}</p>
            </a>

        </div>
    </div>
</template>
  
<script>
export default {
    methods: {
        loadModule(path, app) {
            this.$store.commit("system/active_menu", app);
            this.$router.push(path);
        },
    },

};
</script>
  