<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Broadcast') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.broadcasts.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-label for="title" value="{{ __('Title') }}" />
                            <x-input id="title" class="block mt-1 w-full" type="text" name="title" required autofocus />
                            <x-input-error for="title" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-label for="content" value="{{ __('Content') }}" />
                            <textarea id="content" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="content" required rows="5"></textarea>
                            <x-input-error for="content" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.broadcasts.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </a>

                            <x-button class="ml-4">
                                {{ __('Create') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>