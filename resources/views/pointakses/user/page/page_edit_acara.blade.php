@extends('pointakses.user.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Edit Acara</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('user.acara.update', $event->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="title">Nama Event:</label>
                        <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                    </div>

                    <div class="form-group">
                        <label for="date">Tanggal:</label>
                        <input type="date" name="date" class="form-control" value="{{ $event->date }}" required>
                    </div>

                    <div class="form-group">
                        <label for="type_event">Tipe Acara:</label>
                        <input type="text" name="type_event" class="form-control" value="{{ $event->type_event }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi:</label>
                        <textarea name="description" class="form-control">{{ $event->description }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Acara</button>
                </form>
            </div>
        </div>
    </div>
</div>
@include('pointakses.user.include.sidebar_user')
@endsection
