<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border-2 border-blue-800 rounded-lg font-bold text-base text-white uppercase tracking-wide hover:from-blue-700 hover:to-blue-800 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 transform active:scale-95']) }}>
    {{ $slot }}
</button>
