<div active form="about"
    class="flex flex-col visible w-full @if (!$has_user) d-none @endif h-auto min-w-0 break-words bg-white border-0 opacity-100 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">

    <div>
        <div class="flex flex-wrap">
            <div class="w-full max-w-full px-3 mt-6 text-left flex-0">

                <div class="flex">
                    <div class="flex-auto px-1">
                        <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            for="First Name">First
                            Name</label>
                        <input name="first_name" type="text" name="First Name" placeholder="Eg. John"
                            class="mb-4 focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />
                    </div>
                    <div class="flex-auto px-1">
                        <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            for="Last Name">Last
                            Name</label>
                        <input name="last_name" type="text" name="Last Name" placeholder="Eg. Doe"
                            class="mb-4 focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />
                    </div>
                </div>

                <div class="flex">
                    <div class="flex-auto px-1">
                        <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            for="Username">Username</label>
                        <input name="username" type="text" name="Username" placeholder="Eg. johndoe"
                            class="mb-4 focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />
                    </div>
                    <div class="flex-auto px-1">
                        <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            for="Password">Password</label>
                        <input name="password" type="text" name="Password" placeholder="xxxxxxxxx"
                            class="mb-4 focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />
                    </div>
                </div>

                <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80" for="Email Address">Email
                    Address</label>
                <input name="email" type="email" name="Email Address" placeholder="Eg. soft@dashboard.com"
                    class="mb-4 focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />

                <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                    for="Phone">Phone</label>
                <input name="phone" type="text" name="Phone" placeholder="Eg. 07xxxxxxxxx"
                    class="mb-4 focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />

            </div>
        </div>
        <div class="mt-2 text-center">
            <button type="button" aria-controls="account" next-form-btn href="javascript:;"  onclick="getFormValues()"
                class="inline-block px-6 py-3 mb-0 ml-auto font-bold text-right text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 bg-gradient-to-tl from-gray-900 to-slate-800 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">Next</button>
        </div>
    </div>
</div>
