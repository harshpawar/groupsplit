@props(['status'])

@php
    $enum = $status instanceof \App\Enums\SplitStatus ? $status : \App\Enums\SplitStatus::from($status);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$enum->color()]) }}>
    {{ $enum->label() }}
</span>
