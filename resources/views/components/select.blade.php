@props(['disabled' => false, 'options' => [], 'placeholder' => null])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'input']) !!}>
    @if ($placeholder)
        <option value="" disabled selected>{{ $placeholder }}</option>
    @endif
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected($attributes->get('value') == $value && $attributes->has('value'))>{{ $label }}</option>
    @endforeach
    {{ $slot }}
</select>