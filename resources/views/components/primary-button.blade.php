<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-primary border border-primary rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover focus:bg-primary-hover active:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/40 focus:ring-offset-2 transition ease-in-out duration-150 shadow-xs cursor-pointer']) }}>
    {{ $slot }}
</button>

