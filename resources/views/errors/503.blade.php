@extends('errors.layout')

@section('code', '503')
@section('object', 'coins')
@section('title', 'SIKOP sedang dirawat')
@section('message', 'Sistem sedang diperbarui sebentar. Coba buka lagi beberapa menit lagi.')

@section('actions')
    <button type="button" class="btn btn-primary" onclick="location.reload()">Coba lagi <span class="btn-icon">@include('partials.icon', ['name' => 'arrow-clockwise', 'class' => 'size-3.5'])</span></button>
@endsection
