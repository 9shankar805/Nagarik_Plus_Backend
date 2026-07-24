@extends('layouts.user')

@section('title', 'Digital Locker')
@section('subtitle', 'Store and manage your important documents securely')

@section('content')
    @livewire('user.document-table')
@endsection

@section('scripts')
    <!-- Bootstrap JS for modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
