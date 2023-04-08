<template>
    <div class="p-2 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-18 sm:h-32 gap-2 overflow-hidden">
        <div v-for="(item, index) in $store.state.system.menu" :key="index" class="sm:h-28">
            <image-link :title="item.title" :url="'#' + item.path" :icon="item.icon" :class_str="item.class_str"
                @click="loadModule(item.path, index)">
            </image-link>
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
  