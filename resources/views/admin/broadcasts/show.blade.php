<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Broadcast Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="font-bold text-lg mb-2">{{ $broadcast->title }}</h3>
                    <p class="text-gray-700 mb-4">{{ $broadcast->content }}</p>
                    <p class="text-sm text-gray-500">
                        {{ __('Created At:') }} {{ $broadcast->created_at }}
                    </p>

                    <div class="flex items-center justify-start mt-4">
                        <a href="{{ route('admin.broadcasts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                        <a href="{{ route('admin.broadcasts.edit', $broadcast) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                           {{ __('Edit Broadcast') }}
                        </a>
                        <form method="POST" action="{{ route('admin.broadcasts.destroy', $broadcast) }}" class="ml-4" x-data="{ confirmDelete() { if (window.confirm('{{ __('Are you sure you want to delete this broadcast?') }}')) { $el.closest('form').submit(); } } }">
                            @csrf
                            @method('DELETE')
                            <button type="button" x-on:click="confirmDelete()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Delete Broadcast') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6 p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="font-bold text-lg mb-4">{{ __('User Interactions') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('User Name') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Received Date') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Read Date') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Deleted Date') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($states as $state)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $state->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $state->created_at }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $state->read_at ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $state->deleted_at ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" colspan="4">{{ __('No user interactions found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $states->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>