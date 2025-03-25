<!-- resources/views/scan.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scanner QR ChaCha20-Poly1305</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        #result-container {
            margin-top: 20px;
            display: none;
        }
        .spinner-border {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Scanner QR ChaCha20-Poly1305</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <button id="start-scanner" class="btn btn-primary">Mulai Scanner</button>
                            <button id="stop-scanner" class="btn btn-danger d-none">Hentikan Scanner</button>
                        </div>
                        
                        <div id="qr-reader"></div>
                        
                        <div class="mt-3">
                            <div class="form-group">
                                <label for="ad-input">Additional Data (AD) - Opsional:</label>
                                <input type="text" id="ad-input" class="form-control" value="skripsiku" placeholder="Masukkan additional data">
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <div id="result-container" class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Hasil Dekripsi</h5>
                                </div>
                                <div class="card-body">
                                    <div id="decryption-result"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrCode = new Html5Qrcode("qr-reader");
            const startButton = document.getElementById('start-scanner');
            const stopButton = document.getElementById('stop-scanner');
            const resultContainer = document.getElementById('result-container');
            const decryptionResult = document.getElementById('decryption-result');
            const adInput = document.getElementById('ad-input');
            const spinner = document.querySelector('.spinner-border');
            let scanning = false;

            function onScanSuccess(decodedText) {
                if (scanning) {
                    stopScanner();
                    spinner.style.display = 'inline-block';
                    
                    // Send the encrypted data to the server for decryption
                    const ad = adInput.value || 'skripsiku';
                    
                    fetch('/decrypt-qr', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            encrypted_data: decodedText,
                            ad: ad
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        spinner.style.display = 'none';
                        resultContainer.style.display = 'block';
                        
                        if (data.success) {
                            // Format the result based on type
                            if (typeof data.data === 'object') {
                                let htmlResult = '<div class="table-responsive"><table class="table table-bordered">';
                                htmlResult += '<thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>';
                                
                                for (const [key, value] of Object.entries(data.data)) {
                                    htmlResult += `<tr><td>${key}</td><td>${value}</td></tr>`;
                                }
                                
                                htmlResult += '</tbody></table></div>';
                                decryptionResult.innerHTML = htmlResult;
                            } else {
                                decryptionResult.innerHTML = `<div class="alert alert-success">${data.data}</div>`;
                            }
                        } else {
                            decryptionResult.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        spinner.style.display = 'none';
                        resultContainer.style.display = 'block';
                        decryptionResult.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
                    });
                }
            }

            function onScanFailure(error) {
                // Silent handle of scan failures
                console.warn(`QR scanning failed: ${error}`);
            }

            function startScanner() {
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    onScanFailure
                ).then(() => {
                    scanning = true;
                    startButton.classList.add('d-none');
                    stopButton.classList.remove('d-none');
                    resultContainer.style.display = 'none';
                }).catch(err => {
                    alert(`Tidak dapat memulai scanner: ${err}`);
                });
            }

            function stopScanner() {
                if (html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        scanning = false;
                        startButton.classList.remove('d-none');
                        stopButton.classList.add('d-none');
                    }).catch(err => {
                        console.error(`Gagal menghentikan scanner: ${err}`);
                    });
                }
            }

            startButton.addEventListener('click', startScanner);
            stopButton.addEventListener('click', stopScanner);
        });
    </script>
</body>
</html>