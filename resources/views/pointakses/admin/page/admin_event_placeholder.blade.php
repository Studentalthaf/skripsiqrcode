@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <h1>Halaman Placeholder Event Admin</h1>

    @include('pointakses.admin.include.sidebar_admin')

    <div id="pdfContainer" style="position: relative; display: inline-block; border: 1px solid #ccc; background: #f8f8f8;">
        <canvas id="pdfCanvas" style="display: block;"></canvas>
        <div id="gridOverlay"></div>
    </div>

    <div class="mt-3">
        <button id="addPlaceholder" type="button" class="btn btn-primary">Tambah Placeholder</button>
        <button id="savePlaceholder" type="button" class="btn btn-success">Simpan Posisi</button>
    </div>

    <form id="saveForm" method="POST" action="{{ route('pdf.save', $event->id) }}">
        @csrf
        <input type="hidden" name="placeholders" id="placeholdersInput">
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pdfCanvas = document.getElementById('pdfCanvas');
            const pdfContainer = document.getElementById('pdfContainer');
            const gridOverlay = document.getElementById('gridOverlay');
            const placeholdersInput = document.getElementById('placeholdersInput');
            const pdfUrl = "{{ asset('storage/' . $event->template_pdf) }}";
            const pdfScale = 1.5;

            const placeholders = @json(($event->name_x !== null && $event->name_y !== null) ? [
                ['x' => $event->name_x, 'y' => $event->name_y]
            ] : []);

            // Load PDF document and render on canvas
            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => pdf.getPage(1)).then(page => {
                const viewport = page.getViewport({ scale: pdfScale });
                pdfCanvas.width = viewport.width;
                pdfCanvas.height = viewport.height;

                const context = pdfCanvas.getContext('2d');
                return page.render({ canvasContext: context, viewport: viewport }).promise;
            }).then(() => {
                pdfContainer.style.width = pdfCanvas.width + 'px';
                pdfContainer.style.height = pdfCanvas.height + 'px';

                gridOverlay.style.width = pdfCanvas.width + 'px';
                gridOverlay.style.height = pdfCanvas.height + 'px';

                // Add initial placeholders if any
                placeholders.forEach(p => createPlaceholder(p.x, p.y));
            }).catch(err => {
                console.error("Error loading PDF: ", err);
                alert('Gagal memuat file PDF.');
            });

            // Function to create draggable placeholders
            function createPlaceholder(x, y) {
                const div = document.createElement('div');
                div.className = 'placeholder';
                div.style.left = (x * pdfScale) - 50 + 'px';
                div.style.top = (y * pdfScale) - 10 + 'px';
                div.textContent = 'Nama Peserta';
                pdfContainer.appendChild(div);
                makeDraggable(div);
            }

            // Function to make placeholders draggable
            function makeDraggable(element) {
                let offsetX = 0, offsetY = 0, startX = 0, startY = 0;

                element.onmousedown = function(e) {
                    e.preventDefault();
                    startX = e.clientX;
                    startY = e.clientY;
                    document.onmouseup = stopDragging;
                    document.onmousemove = dragging;
                };

                function dragging(e) {
                    offsetX = startX - e.clientX;
                    offsetY = startY - e.clientY;
                    startX = e.clientX;
                    startY = e.clientY;

                    let newLeft = element.offsetLeft - offsetX;
                    let newTop = element.offsetTop - offsetY;

                    // Batasi agar tidak keluar dari kanvas
                    newLeft = Math.max(0, Math.min(newLeft, pdfCanvas.width - element.offsetWidth));
                    newTop = Math.max(0, Math.min(newTop, pdfCanvas.height - element.offsetHeight));

                    element.style.left = newLeft + "px";
                    element.style.top = newTop + "px";
                }

                function stopDragging() {
                    document.onmouseup = null;
                    document.onmousemove = null;
                }
            }

            // Add a new placeholder when button is clicked
            document.getElementById('addPlaceholder').addEventListener('click', () => {
                createPlaceholder(150 / pdfScale, 150 / pdfScale);
            });

            // Save the positions of all placeholders
            document.getElementById('savePlaceholder').addEventListener('click', () => {
                const placeholders = [];
                document.querySelectorAll('.placeholder').forEach(p => {
                    const x = (parseFloat(p.style.left) + (p.offsetWidth / 2)) / pdfScale;
                    const y = (parseFloat(p.style.top) + (p.offsetHeight / 2)) / pdfScale;
                    placeholders.push({ x, y });
                });

                if (placeholders.length === 0) {
                    alert('Silakan tambahkan placeholder terlebih dahulu.');
                    return;
                }

                placeholdersInput.value = JSON.stringify(placeholders);
                document.getElementById('saveForm').submit();
            });
        });
    </script>

    <style>
        #pdfContainer {
            position: relative;
        }
        .placeholder {
            position: absolute;
            width: 100px;
            height: 20px;
            background: rgba(242, 242, 242, 0.7);
            border: 1px dashed #333;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
            cursor: move;
        }
        #gridOverlay {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
            background-size: 20px 20px;
            z-index: 1;
        }
    </style>
</div>
@endsection
