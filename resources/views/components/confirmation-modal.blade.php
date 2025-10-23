@props(['id' => 'confirm', 'maxWidth' => 'lg'])

<x-modal :name="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <x-slot name="title">
        {{ $title ?? '' }}
    </x-slot>

    <x-slot name="content">
        {{ $content ?? '' }}
    </x-slot>

    <x-slot name="footer">
        {{ $footer ?? '' }}
    </x-slot>
</x-modal>

