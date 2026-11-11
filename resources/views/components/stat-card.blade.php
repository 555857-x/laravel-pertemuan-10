@props(['label', 'value', 'color' => 'text-blue-600'])

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="text-sm text-gray-500 uppercase tracking-wider">{{ $label }}</div>
    <div class="text-3xl font-bold {{ $color }} mt-2">{{ $value }}</div>
</div>
