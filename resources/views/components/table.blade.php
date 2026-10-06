@props(['headers' => []])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-neutral-border shadow-xs overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-[#F8F8F7] border-b border-neutral-border">
                <tr>
                    @foreach($headers as $header)
                        <th class="px-5 py-3 text-[11px] font-bold text-[#666666] uppercase tracking-wider whitespace-nowrap {{ $loop->last ? 'text-right' : 'text-left' }}">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-border text-neutral-dark text-sm">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
