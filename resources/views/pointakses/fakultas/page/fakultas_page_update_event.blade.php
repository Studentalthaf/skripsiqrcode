@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Edit Acara</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('fakultas.event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                
                    <div class="form-group">
                        <label for="title">Nama Event:</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
                        @error('title')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group">
                        <label for="date">Tanggal:</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', $event->date) }}" required>
                        @error('date')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group">
                        <label for="type_event">Tipe Acara:</label>
                        <input type="text" name="type_event" class="form-control" value="{{ old('type_event', $event->type_event) }}" required>
                        @error('type_event')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group">
                        <label for="description">Deskripsi:</label>
                        <textarea name="description" class="form-control">{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group">
                        <label for="logo">Logo Acara:</label>
                        <input type="file" name="logo" class="form-control">
                        @if($event->logo)
                            <img src="{{ asset('storage/' . $event->logo) }}" alt="Logo" width="100" class="mt-2">
                        @endif
                        @error('logo')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group">
                        <label for="signature">Signature:</label>
                        <input type="file" name="signature" class="form-control">
                        @if($event->signature)
                            <img src="{{ asset('storage/' . $event->signature) }}" alt="Signature" width="100" class="mt-2">
                        @endif
                        @error('signature')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <button type="submit" class="btn btn-primary">Update Acara</button>
                </form>
                
            </div>
        </div>
    </div>
</div>
@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection
