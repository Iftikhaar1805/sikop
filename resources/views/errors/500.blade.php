@extends('errors.layout')

@section('code', '500')
@section('object', 'coins')
@section('title', 'Ada gangguan di sistem')
@section('message', 'SIKOP gagal memproses permintaanmu. Pastikan MySQL di XAMPP sudah menyala, lalu coba lagi.')

@section('actions')
    <button type="button" class="btn btn-primary" onclick="location.reload()">Coba lagi <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-clockwise', 'class' => 'size-3.5'])</span></button>
    <a href="{{ url('/') }}" class="btn btn-primary">Ke halaman utama <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></a>
@endsection
