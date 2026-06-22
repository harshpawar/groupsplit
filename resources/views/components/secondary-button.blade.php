<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-200 to-gray-300 border-2 border-gray-400 rounded-lg font-bold text-base text-gray-800 uppercase tracking-wide shadow-md hover:from-gray-300 hover:to-gray-400 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 transform active:scale-95']) }}>
    {{ $slot }}
</button>
