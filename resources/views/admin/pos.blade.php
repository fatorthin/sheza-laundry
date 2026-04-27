@extends('layouts.admin')
@section('title', 'Kasir (POS)')
@section('content')
    <div class="min-h-[calc(100vh-8rem)]">
        @livewire('pos')
    </div>
@endsection
