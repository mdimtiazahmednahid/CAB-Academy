@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'px-4 py-3.5 border-gray-200 focus:border-green-600 focus:ring-green-600 rounded-xl shadow-sm text-base w-full transition-shadow']) }}>
