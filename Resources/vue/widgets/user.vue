<template>
    <div class="bg-white relative shadow rounded-lg ">

        <div class="flex justify-center">
            <img src="https://avatars0.githubusercontent.com/u/35900628?v=4" alt=""
                class="rounded-full mx-auto absolute -top-6 w-12 h-12 shadow-md border-4 border-white transition duration-200 transform hover:scale-110">
        </div>

        <div class="mt-6">
            <h1 class="font-bold text-center text-3xl text-gray-900">{{ $store.state.auth.user.name }}</h1>
            <p class="text-center text-sm text-gray-400 font-medium">{{ $store.state.auth.user.email }}</p>
            <div class="my-5 px-6">
                <a href="#"
                    class="text-gray-200 block rounded-lg text-center font-medium leading-6 px-6 py-3 bg-gray-900 hover:bg-black hover:text-white">
                    <span class="font-bold">{{ $store.state.auth.user.username }}</span>
                </a>
            </div>
        </div>
    </div>
</template>
  
<style >
.greeting-card-trophy-wrapper {
    background-image: url('images/misc/triangle-light.png');
    background-repeat: no-repeat;
    background-position: right;
    background-size: cover;
}

.greeting-card {
    position: relative;
}

.greeting-card .greeting-card-bg {
    position: absolute;
    bottom: 0;
    right: 0;
}

.greeting-card.greeting-card-trophy {
    position: absolute;
    bottom: 10%;
    right: 8%;
}

.v-application .v-application--is-rtl .greeting-card-bg {
    right: initial;
    left: 0;
    transform: rotateY(180deg);
}

.v-application .v-application--is-rtl .greeting-card-trophy {
    left: 8%;
    right: initial;
}
</style>
  