<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-danger border border-danger rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#801F21] active:bg-[#68191B] focus:outline-none focus:ring-2 focus:ring-danger/30 focus:ring-offset-2 transition ease-in-out duration-150 shadow-xs cursor-pointer']) }}>
    {{ $slot }}
</button>

