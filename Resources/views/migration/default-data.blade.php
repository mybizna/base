<div form="address"
    class="absolute top-0 left-0 flex flex-col invisible w-full h-0 min-w-0 p-4 break-words bg-white border-0 opacity-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">

    <div class="flex flex-wrap -mx-3 text-center">
        <div class="w-10/12 max-w-full px-3 mx-auto flex-0">
            <h5 class="font-normal dark:text-white">Are you living in a nice area?</h5>
            <p>One thing I love about the later sunsets is the chance to go for a walk through
                the neighborhood woods before dinner</p>
        </div>
    </div>

    <div>
        <div class="flex flex-wrap -mx-3 text-left">
            <div class="w-full max-w-full px-3 mt-4 ml-auto flex-0 md:w-8/12">
                <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80" for="Street Name">Street
                    Name</label>
                <input type="text" name="Street Name" placeholder="Eg. Soft"
                    class="focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />
            </div>
            <div class="w-full max-w-full px-3 mt-4 ml-auto flex-0 md:w-4/12">
                <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80" for="Street No">Street
                    No</label>
                <input type="number" name="Street No" min="01" placeholder="Eg 221"
                    class="focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />
            </div>
            <div class="w-full max-w-full px-3 mt-4 ml-auto flex-0 md:w-7/12">
                <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80" for="City">City</label>
                <input type="text" name="City" placeholder="Eg Tokyo"
                    class="focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none" />
            </div>
            <div class="w-full max-w-full px-3 mt-4 ml-auto flex-0 md:w-5/12">
                <label class="mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                    for="Country">Country</label>
                <select choice
                    class="focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                    name="choices-country" id="choices-country">
                    <option value="Argentina">Argentina</option>
                    <option value="Albania">Albania</option>
                    <option value="Algeria">Algeria</option>
                    <option value="Andorra">Andorra</option>
                    <option value="Angola">Angola</option>
                    <option value="Brasil">Brasil</option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap -mx-3">
            <div class="flex w-full max-w-full px-3 mt-6 flex-0">
                <button type="button" aria-controls="account" prev-form-btn href="javascript:;"
                    class="inline-block px-6 py-3 mb-0 font-bold text-right uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-gray-400 to-gray-100 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 text-slate-800">Prev</button>
                <button type="button" send-form-btn href="javascript:;"
                    class="inline-block px-6 py-3 mb-0 ml-auto font-bold text-right text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 bg-gradient-to-tl from-gray-900 to-slate-800 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">Send</button>
            </div>
        </div>
    </div>
</div>
