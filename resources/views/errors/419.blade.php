@extends('errors.layout')

@section('code', '419')
@section('object', 'notebook')
@section('title', 'Halaman kedaluwarsa')
@section('message', 'Halaman ini terlalu lama terbuka, jadi isiannya belum terkirim. Buka lagi halamannya, lalu kirim ulang.')

@section('actions')
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="btn btn-primary">Buka lagi halamannya <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-clockwise', 'class' => 'size-3.5'])</span></a>
@endsection
