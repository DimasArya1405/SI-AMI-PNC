<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('Data Akun Auditee') }}
                </div>
            </div>
            <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                <div class="flex justify-between items-center p-4">
                    <button data-modal-target="modal-tambah" data-modal-toggle="modal-tambah"
                        class="flex items-center gap-2 bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="button">
                        <i class="bi bi-plus text-xl"></i> Tambah Data
                    </button>
                </div>
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox" class="sr-only">Table checkbox</label>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 font-semibold">
                                Product name
                            </th>
                            <th scope="col" class="px-6 py-3 font-semibold">
                                Color
                            </th>
                            <th scope="col" class="px-6 py-3 font-semibold">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 font-semibold">
                                Accessories
                            </th>
                            <th scope="col" class="px-6 py-3 font-semibold">
                                Available
                            </th>
                            <th scope="col" class="px-6 py-3 font-semibold">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 font-semibold">
                                Weight
                            </th>
                            <th scope="col" class="px-6 py-3  font-semibold">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-2" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-2" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Apple MacBook Pro 17"
                            </th>
                            <td class="px-6 py-4">
                                Silver
                            </td>
                            <td class="px-6 py-4">
                                Laptop
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                $2999
                            </td>
                            <td class="px-6 py-4">
                                3.0 lb.
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-3" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-3" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Microsoft Surface Pro
                            </th>
                            <td class="px-6 py-4">
                                White
                            </td>
                            <td class="px-6 py-4">
                                Laptop PC
                            </td>
                            <td class="px-6 py-4">
                                No
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                $1999
                            </td>
                            <td class="px-6 py-4">
                                1.0 lb.
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-4" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-4" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Magic Mouse 2
                            </th>
                            <td class="px-6 py-4">
                                Black
                            </td>
                            <td class="px-6 py-4">
                                Accessories
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                No
                            </td>
                            <td class="px-6 py-4">
                                $99
                            </td>
                            <td class="px-6 py-4">
                                0.2 lb.
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-5" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-5" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Apple Watch
                            </th>
                            <td class="px-6 py-4">
                                Black
                            </td>
                            <td class="px-6 py-4">
                                Watches
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                No
                            </td>
                            <td class="px-6 py-4">
                                $199
                            </td>
                            <td class="px-6 py-4">
                                0.12 lb.
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-6" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-6" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Apple iMac
                            </th>
                            <td class="px-6 py-4">
                                Silver
                            </td>
                            <td class="px-6 py-4">
                                PC
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                $2999
                            </td>
                            <td class="px-6 py-4">
                                7.0 lb.
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-7" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-7" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Apple AirPods
                            </th>
                            <td class="px-6 py-4">
                                White
                            </td>
                            <td class="px-6 py-4">
                                Accessories
                            </td>
                            <td class="px-6 py-4">
                                No
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                $399
                            </td>
                            <td class="px-6 py-4">
                                38 g
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-8" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-8" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                iPad Pro
                            </th>
                            <td class="px-6 py-4">
                                Gold
                            </td>
                            <td class="px-6 py-4">
                                Tablet
                            </td>
                            <td class="px-6 py-4">
                                No
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                $699
                            </td>
                            <td class="px-6 py-4">
                                1.3 lb.
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-9" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-9" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Magic Keyboard
                            </th>
                            <td class="px-6 py-4">
                                Black
                            </td>
                            <td class="px-6 py-4">
                                Accessories
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                $99
                            </td>
                            <td class="px-6 py-4">
                                453 g
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-10" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-10" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                Apple TV 4K
                            </th>
                            <td class="px-6 py-4">
                                Black
                            </td>
                            <td class="px-6 py-4">
                                TV
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                No
                            </td>
                            <td class="px-6 py-4">
                                $179
                            </td>
                            <td class="px-6 py-4">
                                1.78 lb.
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                        <tr class="bg-neutral-primary-soft hover:bg-neutral-secondary-medium">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="table-checkbox-11" type="checkbox" value=""
                                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                                    <label for="table-checkbox-11" class="sr-only">Table checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                AirTag
                            </th>
                            <td class="px-6 py-4">
                                Silver
                            </td>
                            <td class="px-6 py-4">
                                Accessories
                            </td>
                            <td class="px-6 py-4">
                                Yes
                            </td>
                            <td class="px-6 py-4">
                                No
                            </td>
                            <td class="px-6 py-4">
                                $29
                            </td>
                            <td class="px-6 py-4">
                                53 g
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                                <a href="#" class="font-medium text-danger hover:underline ms-3">Remove</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

{{-- MODAL --}}
<div id="modal-tambah" tabindex="-1" aria-hidden="true" class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                <h3 class="text-lg font-medium text-heading">
                    Create new product
                </h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-tambah">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form action="#">
                <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                    <div class="col-span-2">
                        <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Name</label>
                        <input type="text" name="name" id="name" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="Type product name" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="price" class="block mb-2.5 text-sm font-medium text-heading">Price</label>
                        <input type="number" name="price" id="price" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="$2999" required="">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="category" class="block mb-2.5 text-sm font-medium text-heading">Category</label>
                        <select id="category" class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body">
                            <option selected="">Select category</option>
                            <option value="TV">TV/Monitors</option>
                            <option value="PC">PC</option>
                            <option value="GA">Gaming/Console</option>
                            <option value="PH">Phones</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="description" class="block mb-2.5 text-sm font-medium text-heading">Product Description</label>
                        <textarea id="description" rows="4" class="block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body" placeholder="Write product description here"></textarea>                    
                    </div>
                </div>
                <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                    <button type="submit" class="inline-flex items-center  text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                        <svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                        Add new product
                    </button>
                    <button data-modal-hide="crud-modal" type="button" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div> 
</x-app-layout>
