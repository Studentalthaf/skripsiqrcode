
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Scan QR Code</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --border-radius: 12px;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            margin: 0 auto;
        }
        
        header {
            text-align: center;
            padding: 20px 0;
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            margin-bottom: 30px;
            box-shadow: var(--box-shadow);
        }
        
        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .scanner-container {
            position: relative;
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        #reader {
            width: 100% !important;
            border-radius: 8px;
            overflow: hidden !important;
            border: none !important;
        }
        
        /* Override html5-qrcode styling */
        #reader video {
            border-radius: 8px !important;
        }
        
        #reader * {
            border: none !important;
        }
        
        #reader__dashboard_section {
            padding: 10px !important;
        }
        
        #reader__dashboard_section_csr button {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            border: none !important;
            cursor: pointer !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 0.9rem !important;
        }
        
        /* Hasil scanning */
        .result-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
        }
        
        .result-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ccc;
            margin-right: 8px;
        }
        
        .status-indicator.active {
            background-color: #4cc9f0;
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.3);
        }
        
        #hasil {
            padding: 15px;
            background-color: #f1f3f5;
            border-radius: 8px;
            min-height: 50px;
            word-break: break-all;
        }
        
        #hasil:empty::before {
            content: "Menunggu scan QR Code...";
            color: #6c757d;
            font-style: italic;
        }
        
        /* Styling untuk tabel hasil pemindaian */
        .scan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .scan-table th, .scan-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .scan-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.9rem;
        }
        
        .scan-table tr:last-child td {
            border-bottom: none;
        }
        
        .scanner-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            border: 2px solid var(--success-color);
            border-radius: 8px;
            z-index: 9;
            pointer-events: none;
            box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.3);
        }
        
        .scanner-frame::before,
        .scanner-frame::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: var(--success-color);
            border-style: solid;
            border-width: 0;
        }
        
        /* Sudut kiri atas */
        .scanner-frame::before {
            top: -2px;
            left: -2px;
            border-top-width: 4px;
            border-left-width: 4px;
        }
        
        /* Sudut kanan atas */
        .scanner-frame::after {
            top: -2px;
            right: -2px;
            border-top-width: 4px;
            border-right-width: 4px;
        }
        
        .scanner-frame-bottom::before,
        .scanner-frame-bottom::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: var(--success-color);
            border-style: solid;
            border-width: 0;
        }
        
        /* Sudut kiri bawah */
        .scanner-frame-bottom::before {
            bottom: -2px;
            left: -2px;
            border-bottom-width: 4px;
            border-left-width: 4px;
        }
        
        /* Sudut kanan bawah */
        .scanner-frame-bottom::after {
            bottom: -2px;
            right: -2px;
            border-bottom-width: 4px;
            border-right-width: 4px;
        }
        
        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, rgba(76, 201, 240, 0), rgba(76, 201, 240, 1), rgba(76, 201, 240, 0));
            top: 50%;
            animation: scan 2s linear infinite;
        }
        
        @keyframes scan {
            0% {
                transform: translateY(-125px);
            }
            50% {
                transform: translateY(125px);
            }
            100% {
                transform: translateY(-125px);
            }
        }
        
        .scanner-tip {
            position: absolute;
            bottom: -50px;
            left: 0;
            width: 100%;
            text-align: center;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 8px;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        
        /* Animasi loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(76, 201, 240, 0.3);
            border-radius: 50%;
            border-top-color: var(--success-color);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .copyright {
            margin-top: auto;
            padding: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .scanner-frame {
                width: 220px;
                height: 220px;
            }
            
            @keyframes scan {
                0% {
                    transform: translateY(-110px);
                }
                50% {
                    transform: translateY(110px);
                }
                100% {
                    transform: translateY(-110px);
                }
            }
            
            .scan-table th, .scan-table td {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Scan QR Code</h1>
        <div class="subtitle">Arahkan kamera ke QR code untuk memindai</div>
    </header>
    
    <div class="container">
        <div class="scanner-container">
            <div id="reader"></div>
            <div class="scanner-frame">
                <div class="scanner-frame-bottom"></div>
                <div class="scan-line"></div>
                <div class="scanner-tip">Posisikan QR code di dalam bingkai</div>
            </div>
        </div>
        
        <div class="result-container">
            <div class="result-label">
                <div>
                    <span class="status-indicator" id="status-light"></span>
                    Status Pemindaian
                </div>
                <div id="scanning-status">Siap memindai</div>
            </div>
            <div id="hasil"></div>
        </div>
    </div>
    
    <div class="copyright">
        &copy; 2025 QR Scanner App
    </div>

    <script>
        // Konfigurasi scanner dan UI
        const hasilElement = document.getElementById('hasil');
        const statusLight = document.getElementById('status-light');
        const scanningStatus = document.getElementById('scanning-status');
        
        // Fungsi untuk menangani hasil scan yang sukses
        function onScanSuccess(decodedText, decodedResult) {
            // Update UI
            statusLight.classList.add('active');
            scanningStatus.innerHTML = 'QR Code terdeteksi <span class="loading"></span>';
            
            // Kirim hasil ke server untuk dekripsi
            fetch("{{ route('qr.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ qr_data: decodedText })
            })
            .then(res => res.json())
            .then(data => {
                if (data.hasil) {
                    // Konversi hasil menjadi format tabel
                    try {
                        // Coba parse jika hasilnya adalah JSON
                        let hasilData;
                        try {
                            hasilData = JSON.parse(data.hasil);
                        } catch (e) {
                            // Jika bukan JSON, tampilkan sebagai string biasa dalam tabel sederhana
                            hasilData = { "Hasil Pemindaian": data.hasil };
                        }
                        
                        // Buat tabel HTML
                        let tableHTML = '<table class="scan-table">';
                        
                        // Tambahkan baris untuk setiap pasangan key-value
                        for (const [key, value] of Object.entries(hasilData)) {
                            tableHTML += `
                                <tr>
                                    <th>${key}</th>
                                    <td>${value}</td>
                                </tr>
                            `;
                        }
                        
                        tableHTML += '</table>';
                        hasilElement.innerHTML = tableHTML;
                        scanningStatus.textContent = 'Berhasil dipindai';
                        
                    } catch (err) {
                        // Fallback jika terjadi error saat membuat tabel
                        hasilElement.textContent = data.hasil;
                        scanningStatus.textContent = 'Berhasil dipindai';
                    }
                } else {
                    hasilElement.innerHTML = `
                        <table class="scan-table">
                            <tr>
                                <th>Status</th>
                                <td>Error</td>
                            </tr>
                            <tr>
                                <th>Pesan</th>
                                <td>${data.error || 'Terjadi kesalahan'}</td>
                            </tr>
                        </table>
                    `;
                    scanningStatus.textContent = 'Gagal memproses';
                }
            })
            .catch(error => {
                hasilElement.innerHTML = `
                    <table class="scan-table">
                        <tr>
                            <th>Status</th>
                            <td>Error</td>
                        </tr>
                        <tr>
                            <th>Pesan</th>
                            <td>Gagal menghubungi server</td>
                        </tr>
                    </table>
                `;
                scanningStatus.textContent = 'Gagal terhubung';
                console.error('Error:', error);
            });
        }

        // Fungsi untuk menangani error
        function onScanFailure(error) {
            // Bisa ditambahkan handling error jika diperlukan
            // console.warn(`Scan error: ${error}`);
        }

        // Konfigurasi QR scanner
        const html5QrCode = new Html5Qrcode("reader");
        const config = {
            fps: 10,              // Frame per second
            qrbox: 250,           // Ukuran area scan
            aspectRatio: 1.0,     // Rasio aspek area scan
            disableFlip: false,   // Jangan flip gambar
            formatsToSupport: [   // Format yang didukung
                Html5QrcodeSupportedFormats.QR_CODE
            ]
        };

        // Mulai scanner
        html5QrCode.start(
            { facingMode: "environment" }, // Gunakan kamera belakang
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error(`Unable to start scanning: ${err}`);
            scanningStatus.textContent = 'Gagal memulai kamera';
        });
    </script>
</body>
</html>
