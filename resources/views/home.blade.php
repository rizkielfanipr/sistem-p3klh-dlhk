@extends('layouts.app')

@section('content')
    <x-navbar />
    <x-hero />
    <x-menu />
    {{-- Passes the data to the x-news component --}}
    <x-news :pengumumanItems="$pengumumanItems" :informasiItems="$informasiItems" />
    <x-faq />
    <x-footer />
@endsection