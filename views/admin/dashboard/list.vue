<template>
    <div class="p-2 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-18  gap-1">
        <div v-for="(item, index) in $store.state.system.menu" :key="index" class="">
            <image-link :title="item.title" :url="'#' + item.path" :icon="item.icon" :class_str="item.class_str"
                @click="loadModule(item.path, index)">
            </image-link>
        </div>
    </div>

    <div class="row mt-1 mx-1">
        <div class="col-12 col-md-4 px-1 mb-1">
            <dashboard-congratulation-john></dashboard-congratulation-john>
        </div>
        <div class="col-12 col-md-8 px-1 mb-1">

            <div class="card">
                <div class="card-body p-1">
                    <div class="d-flex justify-content-between">
                        <h3 class="me-3 p-1">Events</h3>
                        <div class="text-xs text--disabled cursor-pointer p-1">
                            View All
                        </div>
                    </div>
                    <div class="py-1">
                        <calendar></calendar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

// demos

export default {
    components: {
        DashboardCongratulationJohn: window.$func.fetchComponent(
            "base/admin/dashboard/DashboardCongratulationJohn.vue"
        )
    },
    methods: {
        loadModule(path, app) {
            this.$store.commit("system/active_menu", app);
            this.$router.push(path);
        },
    },

};
</script>
