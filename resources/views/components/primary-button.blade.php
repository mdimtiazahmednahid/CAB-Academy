<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full inline-flex justify-center items-center px-4 py-3.5 bg-green-700 border border-transparent rounded-xl font-semibold text-base text-white hover:bg-green-800 focus:bg-green-800 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all ease-in-out duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
