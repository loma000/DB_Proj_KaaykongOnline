@props([
    'sidebar' => false,
])
@if($sidebar)
    <flux:sidebar.brand name="KaayKongKruaOnline" {{ $attributes }}>
        <x-slot name="logo" class="flex size-7 items-center justify-center rounded-lg bg-[#cf1e1e]">
            <span class="block h-[9px] w-[14px] translate-y-[3px] rounded-b-[6px] bg-white"></span>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="KaayKongKruaOnline" {{ $attributes }}>
        <x-slot name="logo" class="flex size-7 items-center justify-center rounded-lg bg-[#cf1e1e]">
            <span class="block h-[9px] w-[14px] translate-y-[3px] rounded-b-[6px] bg-white"></span>
        </x-slot>
    </flux:brand>
@endif