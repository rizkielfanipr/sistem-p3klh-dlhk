@extends('dashboard.layouts.adminlayout')

@section('title', 'Dashboard')

@section('content')
@section('breadcrumb', 'Dashboard')

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-5 gap-4">
    @php
        // Define an array of standard solid color styles for the cards
        $cardStyles = [
            ['bg_class' => 'bg-blue-500', 'icon_bg_class' => 'bg-blue-600'],
            ['bg_class' => 'bg-green-500', 'icon_bg_class' => 'bg-green-600'],
            ['bg_class' => 'bg-indigo-500', 'icon_bg_class' => 'bg-indigo-600'], // Using indigo instead of purple
            ['bg_class' => 'bg-red-500', 'icon_bg_class' => 'bg-red-600'],
            ['bg_class' => 'bg-yellow-500', 'icon_bg_class' => 'bg-yellow-600'],
        ];
    @endphp

    {{-- Data $cards comes from the controller --}}
    @foreach($cards as $index => $card)
        @php
            // Cycle through styles if there are more cards than defined styles
            $style = $cardStyles[$index % count($cardStyles)];
        @endphp
        <div class="{{ $style['bg_class'] }} text-white p-4 rounded-lg shadow-sm flex items-center justify-between space-x-4">
            <div>
                <i class="{{ $card['icon'] }} text-2xl"></i>
                <h3 class="text-base font-semibold mt-1">{{ $card['title'] }}</h3>
                <p class="text-xl font-bold">{{ $card['count'] }}</p>
            </div>
            <div class="{{ $style['icon_bg_class'] }} p-3 rounded-full">
                <i class="{{ $card['icon'] }} text-xl"></i>
            </div>
        </div>
    @endforeach
</div>
@endsection