@extends('layouts.user')

@section('title', 'Profile & Settings')
@section('subtitle', 'Manage your account and preferences')

@section('content')
    @livewire('user.profile-form')
@endsection
