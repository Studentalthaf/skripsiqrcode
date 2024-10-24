<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    
    <!-- Memuat pustaka jsQR -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
        }

        h1 {
            font-family: Arial, sans-serif;
            margin-bottom: 20px;
        }

        video {
            width: 300px; /* Ukuran video */
            border: 2px solid #000; /* Border untuk video */
        }
    </style>
</head>
<body>

    <h1>Scan QR Code</h1>
    <video id="camera" autoplay playsinline></video>

    <script>
        const video = document.getElementById('camera');
        const canvasElement = document.createElement('canvas');
        const canvasContext = canvasElement.getContext('2d');

        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;

                video.setAttribute('playsinline', true); // Untuk iOS
                video.play();

                requestAnimationFrame(scanQRCode);
            } catch (error) {
                console.error('Gagal mengakses kamera:', error);
                alert('Gagal mengakses kamera. Periksa izin kamera di browser.');
            }
        }

        async function scanQRCode() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvasElement.height = video.videoHeight;
                canvasElement.width = video.videoWidth;
                canvasContext.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                const imageData = canvasContext.getImageData(0, 0, canvasElement.width, canvasElement.height);
                const code = jsQR(imageData.data, canvasElement.width, canvasElement.height);

                if (code) {
                    console.log(`QR Code Detected: ${code.data}`);
                    alert(`QR Code Detected: ${code.data}`);
                    // Redirect ke URL yang di-scan
                    window.location.href = code.data;
                } else {
                    console.log('QR Code tidak terdeteksi. Silakan coba lagi.');
                }
            }
            requestAnimationFrame(scanQRCode);
        }

        // Memulai kamera saat halaman dimuat
        window.onload = startCamera;
    </script>

</body>
</html>
