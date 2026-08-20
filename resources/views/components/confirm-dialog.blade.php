@props([
    'name',
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone.',
    'confirmText' => 'Delete',
    'cancelText' => 'Cancel',
    'buttonText' => 'Delete',
    'buttonClass' => 'btn-danger',
    'action' => null,
    'wireClick' => null,
])

<div>
    <button type="button" {{ $attributes->merge(['class' => $buttonClass]) }}
            @click="$dispatch('open-modal', '{{ $name }}')">
        {{ $buttonText }}
    </button>

    <x-modal :name="$name" :maxWidth="'sm'">
        <div class="p-6">
            <div class="text-center text-lg font-medium text-slate-900 dark:text-white" style="text-wrap: auto">{{ $title }}</div>
            <div class="mt-2 text-center text-sm text-slate-500 dark:text-slate-400" style="text-wrap: auto">{{ $message }}</div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" class="btn-secondary"
                        @click="$dispatch('close-modal', '{{ $name }}')">{{ $cancelText }}</button>

                @if ($action)
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">{{ $confirmText }}</button>
                    </form>
                @elseif ($wireClick)
                    <button type="button" class="btn-danger" wire:click="{{ $wireClick }}"
                            @click="$dispatch('close-modal', '{{ $name }}')">{{ $confirmText }}</button>
                @endif
            </div>
        </div>
    </x-modal>
</div>
