<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Data Akun Auditee") }}
                </div>
            </div>
            <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
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
</x-app-layout>
