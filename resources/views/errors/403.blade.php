@extends('errors.layout')

@section('code', '403')
@section('object', 'basket')
@section('title', 'Kamu tidak punya akses')
@section('message', 'Halaman ini hanya untuk Admin koperasi. Kalau kamu memang perlu membukanya, minta Admin memberi akses.')

@section('actions')
    <button type="button" class="btn btn-ghost" onclick="history.back()">@include('partials.icon', ['name' => 'arrow-left', 'class' => 'size-4']) Kembali</button>
    <a href="{{ url('/') }}" class="btn btn-primary">Ke halaman utama <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-up-right', 'class' => 'size-3.5'])</span></a>
@endsection
