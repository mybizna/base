<template>
    <div class="bg-white relative shadow rounded-lg ">

        <div class="flex justify-center">
            <img src="https://avatars0.githubusercontent.com/u/35900628?v=4" alt=""
                class="rounded-full mx-auto absolute -top-6 w-12 h-12 shadow-md border-4 border-white transition duration-200 transform hover:scale-110">
        </div>

        <div class="mt-6">
            <h1 class="font-bold text-center text-3xl text-gray-900">Pantazi Software</h1>
            <p class="text-center text-sm text-gray-400 font-medium">UI Components Factory</p>
            <p>
                <span>

                </span>
            </p>
            <div class="my-5 px-6">
                <a href="#"
                    class="text-gray-200 block rounded-lg text-center font-medium leading-6 px-6 py-3 bg-gray-900 hover:bg-black hover:text-white">Connect
                    with <span class="font-bold">@pantazisoft</span></a>
            </div>
            <div class="flex justify-between items-center my-5 px-6">
                <a href=""
                    class="text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded transition duration-150 ease-in font-medium text-sm text-center w-full py-3">Facebook</a>
                <a href=""
                    class="text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded transition duration-150 ease-in font-medium text-sm text-center w-full py-3">Twitter</a>
                <a href=""
                    class="text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded transition duration-150 ease-in font-medium text-sm text-center w-full py-3">Instagram</a>
                <a href=""
                    class="text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded transition duration-150 ease-in font-medium text-sm text-center w-full py-3">Email</a>
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
  