<x-app-layout>
    <x-slot name="title">{{ $portfolio->client->full_name }} — Edit Portfolio</x-slot>

    <livewire:portfolio-manager :portfolio="$portfolio" />
</x-app-layout>