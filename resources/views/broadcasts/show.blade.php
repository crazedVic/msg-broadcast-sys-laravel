<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Broadcast Message') }}
        </h2>
    </x-slot>
    <p><strong>Title:</strong> {{ $broadcast->title }}</p>
    <p><strong>State:</strong> {{ $broadcast->state ? $broadcast->state : 'Normal' }}</p>
    <p><strong>Content:</strong> {{ $broadcast->content }}</p>

</x-app-layout>
