@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card card-custom shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <span class="display-6 text-white">
                                <i class="fas fa-calendar-edit"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-0 fw-bold">Edit Informasi Acara</h2>
                            <p class="text-white-75 mb-0">Perbarui detail acara Anda dengan lengkap</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('fakultas.event.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="form-elegant">
                        @csrf
                        @method('PUT')
                        
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           placeholder="Nama Event" 
                                           value="{{ old('title', $event->title) }}" 
                                           required>
                                    <label for="title" class="text-muted">
                                        <i class="fas fa-tag me-2"></i>Nama Event
                                    </label>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="date" 
                                           name="date" 
                                           id="date" 
                                           class="form-control @error('date') is-invalid @enderror" 
                                           value="{{ old('date', $event->date) }}" 
                                           required>
                                    <label for="date" class="text-muted">
                                        <i class="fas fa-calendar me-2"></i>Tanggal Acara
                                    </label>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" 
                                           name="type_event" 
                                           id="type_event" 
                                           class="form-control @error('type_event') is-invalid @enderror" 
                                           placeholder="Tipe Acara" 
                                           value="{{ old('type_event', $event->type_event) }}" 
                                           required>
                                    <label for="type_event" class="text-muted">
                                        <i class="fas fa-list-alt me-2"></i>Tipe Acara
                                    </label>
                                    @error('type_event')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                        <option value="">Pilih Kategori Acara</option>
                                        <option value="akademik" {{ old('category', $event->category) == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                        <option value="non-akademik" {{ old('category', $event->category) == 'non-akademik' ? 'selected' : '' }}>Non-Akademik</option>
                                        <option value="kemahasiswaan" {{ old('category', $event->category) == 'kemahasiswaan' ? 'selected' : '' }}>Kemahasiswaan</option>
                                    </select>
                                    <label for="category" class="text-muted">
                                        <i class="fas fa-stream me-2"></i>Kategori Acara
                                    </label>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea 
                                        name="description" 
                                        id="description" 
                                        class="form-control @error('description') is-invalid @enderror" 
                                        placeholder="Deskripsi Acara" 
                                        style="height: 150px">{{ old('description', $event->description) }}</textarea>
                                    <label for="description" class="text-muted">
                                        <i class="fas fa-paragraph me-2"></i>Deskripsi Acara
                                    </label>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-dashed mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-3">
                                            <i class="fas fa-image me-2"></i>Logo Acara
                                        </h5>
                                        <div class="mb-3">
                                            <input type="file" 
                                                   name="logo" 
                                                   id="logo" 
                                                   class="form-control @error('logo') is-invalid @enderror" 
                                                   accept="image/*">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if($event->logo)
                                            <div class="image-preview mt-3">
                                                <img src="{{ asset('storage/' . $event->logo) }}" 
                                                     alt="Logo Acara" 
                                                     class="img-fluid rounded shadow-sm" 
                                                     style="max-height: 200px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-dashed mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-3">
                                            <i class="fas fa-signature me-2"></i>Signature
                                        </h5>
                                        <div class="mb-3">
                                            <input type="file" 
                                                   name="signature" 
                                                   id="signature" 
                                                   class="form-control @error('signature') is-invalid @enderror" 
                                                   accept="image/*">
                                            @error('signature')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if($event->signature)
                                            <div class="image-preview mt-3">
                                                <img src="{{ asset('storage/' . $event->signature) }}" 
                                                     alt="Signature" 
                                                     class="img-fluid rounded shadow-sm" 
                                                     style="max-height: 200px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow-sm">
                                <i class="fas fa-save me-2"></i>Perbarui Acara
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-custom {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(to right, #4e73df 0%, #224abe 100%);
    }
    
    .form-floating > label {
        opacity: 0.7;
    }
    
    .border-dashed {
        border: 2px dashed #e0e0e0;
        border-radius: 10px;
    }
    
    .form-elegant .form-control, 
    .form-elegant .form-select {
        border-radius: 10px;
        border-color: #e0e0e0;
        transition: all 0.3s ease;
    }
    
    .form-elegant .form-control:focus, 
    .form-elegant .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        border-color: #4e73df;
    }
</style>
@endpush

@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection