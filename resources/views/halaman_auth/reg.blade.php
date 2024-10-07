
<!DOCTYPE html>
<html lang="en">
<!-- Menentukan tipe dokumen dan bahasa yang digunakan -->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Menetapkan pengkodean karakter untuk dokumen -->

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <!-- Memastikan halaman bersifat responsif dan menyesuaikan tata letak untuk berbagai ukuran layar -->

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Menginstruksikan Internet Explorer untuk menggunakan mesin rendering terbaru -->

    <meta name="msapplication-tap-highlight" content="no">
    <!-- Menonaktifkan efek highlight pada Windows Phone ketika pengguna mengetuk link atau tombol -->

    <title>Registrasi</title>
    <!-- Menetapkan judul dokumen -->

    <!-- Favicons-->
    <link rel="icon" href="{{ asset('frontend/images/favicon.png') }}" sizes="32x32">
    <!-- Menentukan favicon untuk situs web -->

    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed" href="{{ asset('account/images/favicon/apple-touch-icon-152x152.png') }}">
    <!-- Menentukan ikon sentuh Apple untuk situs web -->

    <meta name="msapplication-TileColor" content="#00bcd4">
    <!-- Menetapkan warna tile untuk Windows tiles -->

    <meta name="msapplication-TileImage" content="{{ asset('account/images/favicon/mstile-144x144.png') }}">
    <!-- Menentukan gambar yang digunakan untuk Windows tiles -->

    <!-- CSS INTI-->
    <link href="{{ asset('account/css/materialize.min.css') }}" type="text/css" rel="stylesheet" media="screen,projection">
    <!-- Menautkan ke kerangka kerja CSS Materialize -->

    <link href="{{ asset('account/css/style.min.css') }}" type="text/css" rel="stylesheet" media="screen,projection">
    <!-- Menautkan ke stylesheet utama untuk halaman -->

    <!-- CSS Kustom-->
    <link href="{{ asset('account/css/custom/custom.min.css') }}" type="text/css" rel="stylesheet" media="screen,projection">
    <!-- Menautkan ke stylesheet kustom -->

    <link href="{{ asset('account/css/layouts/page-center.css') }}" type="text/css" rel="stylesheet" media="screen,projection">
    <!-- Menautkan ke stylesheet tata letak halaman tengah -->

    <!-- CSS PLUGIN YANG DISERTAKAN PADA HALAMAN INI -->
    <link href="{{ asset('account/js/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" type="text/css" rel="stylesheet" media="screen,projection">
    <!-- Menautkan ke stylesheet plugin scrollbar sempurna -->

    <!-- Gaya khusus untuk tombol loading -->
    <style>
        .loading-btn {
            cursor: not-allowed;
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
    <!-- Menambahkan gaya khusus untuk tombol loading -->

</head>

<body class="cyan">
    <!-- Mulai Loading Halaman -->
    <div id="loader-wrapper">
        <div id="loader"></div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>
    </div>
    <!-- Selesai Loading Halaman -->

    <div id="login-page" class="row">
        <div class="col s12 z-depth-4 card-panel">
            <!-- Membuat panel kartu dengan depth z-index 4 -->
            <form method="POST" action="{{ route('registrasi') }}" class="login-form" id="form">
                <!-- Formulir untuk mengirim data registrasi -->
                @csrf
                <!-- Token CSRF untuk keamanan -->

                <div class="row">
                    <div class="input-field col s12 center">
                        <p class="center login-form-text">Login untuk Sistem Pemesanan Makanan</p>
                        <!-- Teks instruksi untuk pengguna -->
                        @if (session('error_mail'))
                            <div class="text-danger">
                                {{ session('error_mail') }}
                                <!-- Menampilkan pesan kesalahan jika ada -->
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    <!-- Menampilkan pesan kesalahan jika ada -->
                                    @foreach ($errors->all() as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (Session::get('success'))
                            <div class="alert alert-success alert-dismissable fade show">
                                <ul>
                                    <!-- Menampilkan pesan sukses jika ada -->
                                    <li>{{ Session::get('success') }}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-av-recent-actors prefix"></i>
                        <!-- Ikon input -->
                        <input name="nama_lengkap" id="username" type="text">
                        <!-- Input untuk nama lengkap -->
                        <label for="nama_lengkap" class="center-align">Nama Lengkap</label>
                        <!-- Label untuk input nama lengkap -->
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-social-person-outline prefix"></i>
                        <!-- Ikon input -->
                        <input name="email" id="username" type="text">
                        <!-- Input untuk alamat email -->
                        <label for="email" class="center-align">Email</label>
                        <!-- Label untuk input email -->
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-action-lock-outline prefix"></i>
                        <!-- Ikon input -->
                        <input name="password" id="password" type="password">
                        <!-- Input untuk password -->
                        <label for="password">Password</label>
                        <!-- Label untuk input password -->
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-action-perm-contact-cal prefix"></i>
                        <!-- Ikon input -->
                        <input name="no_tlp" id="username" type="text">
                        <!-- Input untuk nomor telepon -->
                        <label for="no_tlp" class="center-align">No. Telepon</label>
                        <!-- Label untuk input nomor telepon -->
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-action-work prefix"></i>
                        <!-- Ikon input -->
                        <input name="unit_kerja" id="username" type="text">
                        <!-- Input untuk unit kerja -->
                        <label for="unit_kerja" class="center-align">Unit Kerja</label>
                        <!-- Label untuk input unit kerja -->
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-action-work prefix"></i>
                        <!-- Ikon input -->
                        <input name="alamat" id="username" type="text">
                        <!-- Input untuk alamat -->
                        <label for="alamat" class="center-align">Alamat</label>
                        <!-- Label untuk input alamat -->
                    </div>
                </div>

                <div class="input-field col s6 m6 l6">
                    <button type="submit" class="btn" style="background-color: #499848;" id="regis-btn">Regis</button>
                    <!-- Tombol submit untuk registrasi -->
                </div>

                <div class="row">
                    <div class="input-field col s6 m6 l6">
                        <p class="margin medium-small">
                            <a href="{{ route('login') }}" class="text-primary">Login</a>
                            <!-- Link untuk halaman login -->
                        </p>
                        <p class="margin medium-small">
                            <a href="/" style="float: left;">Kembali</a>
                            <!-- Link untuk kembali ke halaman utama -->
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ================================================
    Skrip
    ================================================ -->

    <!-- Pustaka jQuery -->
    <script type="text/javascript" src="{{ asset('account/js/plugins/jquery-1.11.2.min.js') }}"></script>
    <!-- Materialize js -->
    <script type="text/javascript" src="{{ asset('account/js/materialize.min.js') }}"></script>
    <!-- Scrollbar -->
    <script type="text/javascript" src="{{ asset('account/js/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

    <!-- plugins.js - Beberapa Kode JS Spesifik untuk Pengaturan Plugin -->
    <script type="text/javascript" src="{{ asset('account/js/plugins.min.js') }}"></script>
    <!-- custom-script.js - Tambahkan JS Kustom Tema Anda -->
    <script type="text/javascript" src="{{ asset('account/js/custom-script.js') }}"></script>

    <!-- Skrip JavaScript khusus untuk menangani pengiriman form -->
    <script type="text/javascript">
        document.getElementById('form').addEventListener('submit', function() {
            var regisBtn = document.getElementById('regis-btn');
            regisBtn.innerHTML = 'Loading...';
            regis

Btn.classList.add('loading-btn');
            regisBtn.disabled = true;
        });
    </script>
    <!-- Menambahkan skrip untuk menangani efek loading pada tombol submit saat formulir dikirim -->

</body>

</html>