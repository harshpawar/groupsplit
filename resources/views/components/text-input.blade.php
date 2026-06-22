@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-2 border-gray-400 bg-white text-gray-900 px-4 py-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-300 shadow-sm transition']) }}
