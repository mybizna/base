<template>
    <div class="bg-white relative shadow rounded-lg ">


        <div class="bg-white relative shadow rounded-lg ">
        <div class="">
            <div class="">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <span class="block text-[0.9375rem] font-semibold">My Profile</span>
                        <span class="block text-[0.75rem] text-[#8c9097] dark:text-white/50">
                            67% Completed -
                            <a href="javascript:void(0);" class="text-center text-primary">Click Here
                                <i class="bi bi-box-arrow-up-right text-[0.625rem] ms-2 align-middle"></i>
                            </a>
                        </span>
                    </div>
                    <div>
                        <div class="hs-dropdown ti-dropdown">
                            <a aria-label="anchor" href="javascript:void(0);"
                                class="flex items-center justify-center w-[1.75rem] h-[1.75rem] !text-defaulttextcolor !text-[0.8rem] !py-1 !px-2 rounded-sm bg-light border-light shadow-none !font-medium"
                                aria-expanded="false">
                                <i class="fe fe-more-vertical text-[0.8rem]"></i>
                            </a>
                            <ul class="hs-dropdown-menu ti-dropdown-menu hidden">
                                <li><a class="ti-dropdown-item !py-2 !px-[0.9375rem] !text-[0.8125rem] !font-medium block"
                                        href="javascript:void(0);">Week</a></li>
                                <li><a class="ti-dropdown-item !py-2 !px-[0.9375rem] !text-[0.8125rem] !font-medium block"
                                        href="javascript:void(0);">Month</a></li>
                                <li><a class="ti-dropdown-item !py-2 !px-[0.9375rem] !text-[0.8125rem] !font-medium block"
                                        href="javascript:void(0);">Year</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl avatar-rounded circle-progress p-1">
                            <img src="../assets/images/faces/9.jpg" alt="">
                        </span>
                    </div>
                    <div>
                        <h5 class="font-semibold !mb-0 text-[1.25rem]">Json Taylor</h5>
                        <p class="text-[.8125rem] text-[#8c9097] dark:text-white/50">jsontaylor345@gmail.com</p>
                    </div>
                </div>
                <div class=" text-center">
                    <a href="profile.html"
                        class="ti-btn ti-btn-primary !me-[0.375rem] !font-medium !py-1 !px-2 !text-[0.75rem]"> Edit Profile
                    </a>
                    <a href="profile.html" class="ti-btn bg-primary text-white !py-1 !px-2 !text-[0.75rem]"> View
                        Profile </a>
                </div>
            </div>
        </div>
    </div>






        <div class="flex justify-center">
            <img :src="$assets_url + 'images/avatars/1.png'" alt=""
                class="rounded-full mx-auto absolute -top-6 w-12 h-12 shadow-md border-4 border-white transition duration-200 transform hover:scale-110">
        </div>

        <div class="mt-6 pb-2">
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
  