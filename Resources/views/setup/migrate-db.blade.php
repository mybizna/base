<div form="account" id="migrate-db"
    class=" @if (!($has_user || $is_wordpress)) d-none @endif w-full min-w-0 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">

    <div class="p-2 grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-18  gap-2 overflow-hidden">
        <div class=" my-1">
            <div class="card card-coin p-1">
                <div class="card-body text-center image-link p-2"><a href="#/account/admin/invoice" title="Account"
                        class="text-center text-decoration-none">
                        <h2 class="border rounded-circle m-2 mt-0 text-primary border-primary">
                            <i class="fas fa-funnel-dollar"></i>
                        </h2>
                        <p class="text-black m-0">Account</p>
                    </a></div>
            </div>
        </div>
        <div class=" my-1">
            <div class="card card-coin p-1">
                <div class="card-body text-center image-link p-2"><a href="#/partner/admin/partner" title="Partner"
                        class="text-center text-decoration-none">
                        <h2 class="border rounded-circle m-2 mt-0 text-info border-info"><i class="fas fa-store"></i>
                        </h2>
                        <p class="text-black m-0">Partner</p>
                    </a></div>
            </div>
        </div>
        <div class=" my-1">
            <div class="card card-coin p-1">
                <div class="card-body text-center image-link p-2"><a href="#/mpesa/admin/payment" title="Mpesa"
                        class="text-center text-decoration-none">
                        <h2 class="border rounded-circle m-2 mt-0 text-green border-green"><i
                                class="fas fa-sack-dollar"></i></h2>
                        <p class="text-black m-0">Mpesa</p>
                    </a></div>
            </div>
        </div>
        <div class=" my-1">
            <div class="card card-coin p-1">
                <div class="card-body text-center image-link p-2"><a href="#/core/admin/country" title="Core"
                        class="text-center text-decoration-none">
                        <h2 class="border rounded-circle m-2 mt-0 text-warning border-warning"><i
                                class="fas fa-receipt"></i></h2>
                        <p class="text-black m-0">Core</p>
                    </a></div>
            </div>
        </div>
        <div class=" my-1">
            <div class="card card-coin p-1">
                <div class="card-body text-center image-link p-2"><a href="#/isp/admin/subscriber" title="Isp"
                        class="text-center text-decoration-none">
                        <h2 class="border rounded-circle m-2 mt-0 text-secondary border-secondary"><i
                                class="fas fa-network-wired"></i></h2>
                        <p class="text-black m-0">Isp</p>
                    </a></div>
            </div>
        </div>

    </div>

    <div class="text-right text-sm"> (22) More Modules</div>

    <div class="text-left p-1 d-none">
        <button type="button"
            class="inline-flex items-center px-4 py-2 m-1 text-sm font-medium text-center border border-blue-700 rounded-lg ">
            SMS
        </button>

        <button type="button"
            class="inline-flex items-center px-4 py-2 m-1 text-sm font-medium text-center border border-blue-700 rounded-lg ">
            SMS Mass
        </button>

        <button type="button"
            class="inline-flex items-center px-4 py-2 m-1 text-sm font-medium text-center border border-blue-700 rounded-lg ">
            SMS Reader
        </button>
    </div>


    <div class="terminal shadow">
        <div class="top">
            <div class="btns">
                <span class="circle red"></span>
                <span class="circle yellow"></span>
                <span class="circle green"></span>
            </div>
            <div class="title">Migrate DB & Data</div>
        </div>

        <pre id="terminal_logger" class="body  text-left m-0 text-xs overflow-x-hidden overflow-y-scroll h-36">
DB Migration >$ Starting....
        </pre>
    </div>


</div>
