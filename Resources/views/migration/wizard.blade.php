<div>

    <div class="w-full max-w-full px-3 text-center flex-0">
        <h3 class="mt-12">Migration Wizard.</h3>
        <p class="font-normal dark:text-white text-slate-400">
            System not set yet. Please follow the steps below to set up the system.
        </p>
        <div multisteps-form class="mb-12">

            <div class="w-full max-w-full px-3 m-auto flex-0 lg:w-8/12">
                @include('base::migration.tabs')
            </div>

            <div class="flex flex-wrap">
                <div class="w-full max-w-full px-3 m-auto flex-0 lg:w-8/12">
                    <form class="relative mb-32">

                        @include('base::migration.admin-account')
                        @include('base::migration.migrate-db')
                        @include('base::migration.default-data')

                    </form>
                </div>
            </div>
        </div>
    </div>


</div>
