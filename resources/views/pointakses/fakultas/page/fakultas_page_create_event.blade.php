@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="content-wrapper event-creation-page">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-4 align-items-center">
                <div class="col-sm-6">
                    <h1 class="page-title text-dark">
                        <i class="fas fa-calendar-plus mr-2 text-primary"></i>Tambah Acara Baru
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/fakultas') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('fakultas.event') }}">Acara</a></li>
                        <li class="breadcrumb-item active">Tambah Acara</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Form Section (Left) -->
                <div class="col-lg-8">
                    <div class="card card-outline card-primary event-form-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit mr-2"></i>Detail Acara
                            </h3>
                        </div>
                        <form action="{{ route('fakultas.event.store') }}" method="POST" enctype="multipart/form-data" id="eventCreateForm">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <!-- Judul Acara -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title">
                                                Judul Acara 
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                                </div>
                                                <input type="text" 
                                                       name="title" 
                                                       class="form-control @error('title') is-invalid @enderror" 
                                                       id="title" 
                                                       placeholder="Masukkan judul acara" 
                                                       value="{{ old('title') }}" 
                                                       required 
                                                       maxlength="100">
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Maksimal 100 karakter</small>
                                        </div>
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Deskripsi Acara</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                                </div>
                                                <textarea 
                                                    name="description" 
                                                    class="form-control @error('description') is-invalid @enderror" 
                                                    id="description" 
                                                    rows="4" 
                                                    placeholder="Deskripsi singkat acara (opsional)"
                                                    maxlength="500">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Maksimal 500 karakter</small>
                                        </div>
                                    </div>

                                    <!-- Tanggal dan Jenis Acara -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date">
                                                Tanggal Acara 
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date" 
                                                       name="date" 
                                                       class="form-control @error('date') is-invalid @enderror" 
                                                       id="date" 
                                                       value="{{ old('date') }}" 
                                                       required>
                                                @error('date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type_event">
                                                Jenis Acara 
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-list-alt"></i></span>
                                                </div>
                                                <select 
                                                    name="type_event" 
                                                    class="form-control custom-select @error('type_event') is-invalid @enderror" 
                                                    id="type_event" 
                                                    required>
                                                    <option value="">Pilih Jenis Acara</option>
                                                    <option value="Seminar" {{ old('type_event') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                                                    <option value="Workshop" {{ old('type_event') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                                    <option value="Konferensi" {{ old('type_event') == 'Konferensi' ? 'selected' : '' }}>Konferensi</option>
                                                    <option value="Pelatihan" {{ old('type_event') == 'Pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                                                </select>
                                                @error('type_event')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- File Uploads -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="logo">Logo Acara</label>
                                            <div class="custom-file">
                                                <input type="file" 
                                                       name="logo" 
                                                       class="custom-file-input @error('logo') is-invalid @enderror" 
                                                       id="logo" 
                                                       accept="image/*" 
                                                       data-preview="logoPreview">
                                                <label class="custom-file-label" for="logo">Pilih logo...</label>
                                                @error('logo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <img id="logoPreview" src="" class="img-fluid mt-2 d-none" alt="Logo Preview">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="signature">Tanda Tangan</label>
                                            <div class="custom-file">
                                                <input type="file" 
                                                       name="signature" 
                                                       class="custom-file-input @error('signature') is-invalid @enderror" 
                                                       id="signature" 
                                                       accept="image/*" 
                                                       data-preview="signaturePreview">
                                                <label class="custom-file-label" for="signature">Pilih tanda tangan...</label>
                                                @error('signature')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <img id="signaturePreview" src="" class="img-fluid mt-2 d-none" alt="Signature Preview">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="template_pdf">Template Sertifikat</label>
                                            <div class="custom-file">
                                                <input type="file" 
                                                       name="template_pdf" 
                                                       class="custom-file-input @error('template_pdf') is-invalid @enderror" 
                                                       id="template_pdf" 
                                                       accept="application/pdf">
                                                <label class="custom-file-label" for="template_pdf">Pilih PDF...</label>
                                                @error('template_pdf')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save mr-2"></i>Simpan Acara
                                        </button>
                                    </div>
                                    <div>
                                        <a href="{{ route('fakultas.event') }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="fas fa-times mr-2"></i>Batalkan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Guidance Section (Right) -->
                <div class="col-lg-4">
                    <div class="card card-outline card-info event-guidance-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Panduan Pembuatan Acara
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-light" role="alert">
                                <h5 class="alert-heading">
                                    <i class="fas fa-lightbulb text-warning mr-2"></i>Tips Pembuatan Acara
                                </h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Judul acara harus deskriptif dan jelas
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Pilih tanggal acara dengan tepat
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Unggah logo dengan resolusi yang baik
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Template sertifikat harus rapi
                                    </li>
                                </ul>
                            </div>
                            <div class="alert alert-info" role="alert">
                                <h5 class="alert-heading">
                                    <i class="fas fa-file-upload text-white mr-2"></i>Persyaratan File
                                </h5>
                                <p class="text-white-75">
                                    <small>
                                    - Logo: Maks 2MB, format JPG/PNG
                                    - Tanda Tangan: Maks 2MB, format JPG/PNG
                                    - Template PDF: Maks 5MB, format PDF
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection

@push('styles')
<style>
    .event-creation-page {
        background-color: #f4f6f9;
    }
    .page-title {
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    .event-form-card, .event-guidance-card {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .event-form-card:hover, .event-guidance-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        transform: translateY(-5px);
    }
    #logoPreview, #signaturePreview {
        max-height: 150px;
        object-fit: contain;
    }
    .input-group-prepend .input-group-text {
        background-color: #f8f9fa;
    }
    .custom-file-input:lang(en)::after {
        content: "Browse";
    }
    .custom-file-input:lang(en)::before {
        content: "Choose file";
    }
    @media (max-width: 768px) {
        .btn-lg {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File input label update
        document.querySelectorAll('.custom-file-input').forEach(input => {
            input.addEventListener('change', function () {
                const fileName = this.files[0]?.name || 'Pilih file...';
                const label = this.nextElementSibling;
                label.textContent = fileName;

                // Image preview
                const previewId = this.getAttribute('data-preview');
                if (previewId) {
                    const previewImg = document.getElementById(previewId);
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewImg.classList.remove('d-none');
                        }
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        previewImg.src = '';
                        previewImg.classList.add('d-none');
                    }
                }
            });
        });

        // Form validation
        const form = document.getElementById('eventCreateForm');
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Date validation
            const dateField = document.getElementById('date');
            const selectedDate = new Date(dateField.value);
            const today = new Date();
            
            if (selectedDate < today) {
                dateField.classList.add('is-invalid');
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal acara harus di masa depan.',
                    confirmButtonText: 'Mengerti'
                });
                e.preventDefault();
                return;
            }

            // Required file validation
            const requiredFileInputs = ['logo', 'signature', 'template_pdf'];
            requiredFileInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (!input.files || input.files.length === 0) {
                    input.classList.add('is-invalid');
                    isValid = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'File Wajib Diisi',
                        text: `Silakan unggah ${inputId === 'template_pdf' ? 'template PDF' : inputId}`,
                        confirmButtonText: 'Mengerti'
                    });
                } else {
                    const fileSize = input.files[0].size / 1024 / 1024; // in MB
                    let maxSize = 2; // default 2MB
                    
                    if (inputId === 'template_pdf') {
                        maxSize = 5; // 5MB for PDF
                    }

                    // File type validation
                    const allowedTypes = {
                        'logo': ['image/jpeg', 'image/png', 'image/gif'],
                        'signature': ['image/jpeg', 'image/png', 'image/gif'],
                        'template_pdf': ['application/pdf']
                    };

                    const fileType = input.files[0].type;
                    const allowedTypesList = allowedTypes[inputId];

                    if (fileSize > maxSize) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ukuran File Terlalu Besar',
                            text: `Ukuran file ${input.name} maksimal ${maxSize}MB`,
                            confirmButtonText: 'Mengerti'
                        });
                    }

                    if (!allowedTypesList.includes(fileType)) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Tipe File Tidak Sesuai',
                            text: `Silakan unggah file dengan tipe ${allowedTypesList.map(type => type.split('/')[1].toUpperCase()).join(' atau ')}`,
                            confirmButtonText: 'Mengerti'
                        });
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Character limit for title and description
        const titleInput = document.getElementById('title');
        const descriptionInput = document.getElementById('description');

        [titleInput, descriptionInput].forEach(input => {
            input.addEventListener('input', function() {
                const maxLength = parseInt(this.getAttribute('maxlength'));
                const currentLength = this.value.length;
                
                if (currentLength >= maxLength) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Batas Karakter',
                        text: `Anda telah mencapai batas maksimal ${maxLength} karakter.`,
                        confirmButtonText: 'Mengerti'
                    });
                }
            });
        });
    });
</script>
@endpush