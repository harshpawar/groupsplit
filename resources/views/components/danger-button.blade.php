<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 border-2 border-red-800 rounded-lg font-bold text-base text-white uppercase tracking-wide hover:from-red-700 hover:to-red-800 hover:shadow-lg active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 transform active:scale-95']) }}
    {{ $slot }}
</button>
