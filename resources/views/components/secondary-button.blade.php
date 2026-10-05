<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-white border border-primary rounded-md font-semibold text-xs text-primary uppercase tracking-widest shadow-xs hover:bg-primary-light hover:border-primary-dark hover:text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
