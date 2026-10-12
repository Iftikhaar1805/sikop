@extends('errors.layout')

@section('code', '404')
@section('object', 'box')
@section('title', 'Halaman tidak ditemukan')
@section('message', 'Alamat yang kamu buka tidak ada di SIKOP. Mungkin salah ketik, atau halamannya sudah dipindah.')

@section('actions')
    <button type="button" class="btn btn-ghost" onclick="history.back()">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'size-4']) Kembali</button>
    <a href="{{ url('/') }}" class="btn btn-primary">Ke halaman utama <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></a>
@endsection
