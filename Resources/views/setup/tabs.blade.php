<ol class="items-center col-span-full space-y-4 sm:flex sm:space-x-8 sm:space-y-0">
    
    @if (!($has_user || $is_wordpress))
        <li class="flex items-center text-blue-600 dark:text-blue-500 space-x-2.5">
            <span
                class="flex items-center justify-center w-8 h-8 border border-blue-600 rounded-full shrink-0 dark:border-blue-500">
                1
            </span>
            <div class="inline-block text-lg font-medium leading-5">
                Admin Account
            </div>
        </li>
    @endif

    <li class="flex items-center text-gray-500 dark:text-gray-400 space-x-2.5">
        <span
            class="flex items-center justify-center w-8 h-8 border border-gray-500 rounded-full shrink-0 dark:border-gray-400">
            2
        </span>
        <div class="inline-block text-lg font-medium leading-5">
            Migrate DB & Data
        </div>
    </li>
   
</ol>
