<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inbox') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-end mb-4">
    <table class="table w-full">
        <thead>
            <tr>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($broadcasts as $broadcast)
            <tr >
                <td class="mb-8"><div class="text-lg {{$broadcast->state }}">
                    {{ $broadcast->created_at }} {{$broadcast->title }}<div>
                    <div class="font-extralight text-sm ml-3"> {{$broadcast->content}} </div>    


                    </td>
                          </tr>
            @endforeach
        </tbody>
    </table>
                    </div></div></div></div></div>  
</x-app-layout>