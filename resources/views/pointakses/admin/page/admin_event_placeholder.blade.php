@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <h1>Halaman Placeholder Event Admin</h1>
    @include('pointakses.admin.include.sidebar_admin')

    <div id="pdfContainer" style="position: relative; display: inline-block; border: 1px solid black;">
        <canvas id="pdfCanvas"></canvas>
    </div>

    <!-- Perbaiki: tambahkan type="button" agar tidak auto-submit -->
    <button id="addPlaceholder" type="button">Tambah Placeholder</button>
    <button id="savePlaceholder" type="button">Simpan Posisi</button>

    <!-- Form POST ke route pdf.save -->
    <form id="saveForm" method="POST" action="{{ route('pdf.save', $event->id) }}">
        @csrf
        <input type="hidden" name="placeholders" id="placeholdersInput">
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script>
        let placeholders = @json($event->placeholders ? json_decode($event->placeholders, true) : []);
        let pdfCanvas = document.getElementById('pdfCanvas');
        let pdfContainer = document.getElementById('pdfContainer');
        let pdfUrl = "{{ asset('storage/' . $event->template_pdf) }}";

        // Muat PDF
        pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
            return pdf.getPage(1);
        }).then(page => {
            let viewport = page.getViewport({ scale: 1.0 });

            pdfCanvas.width = viewport.width;
            pdfCanvas.height = viewport.height;

            let context = pdfCanvas.getContext('2d');
            return page.render({ canvasContext: context, viewport: viewport }).promise;
        }).then(() => {
            pdfContainer.style.width = pdfCanvas.width + "px";
            pdfContainer.style.height = pdfCanvas.height + "px";

            placeholders.forEach(p => createPlaceholder(p.x, pdfCanvas.height - p.y));
        }).catch(err => {
            console.error("Error loading PDF: ", err);
        });

        function createPlaceholder(x, y) {
            let div = document.createElement('div');
            div.className = 'placeholder';
            div.style.position = 'absolute';
            div.style.left = x + 'px';
            div.style.top = y + 'px';
            div.style.background = '#f2f2f2';
            div.style.border = '1px dashed #333';
            div.style.padding = '4px';
            div.textContent = 'Nama';
            pdfContainer.appendChild(div);

            div.onmousedown = function (event) {
                event.preventDefault();
                let shiftX = event.clientX - div.getBoundingClientRect().left;
                let shiftY = event.clientY - div.getBoundingClientRect().top;

                function moveAt(pageX, pageY) {
                    let newX = pageX - pdfContainer.offsetLeft - shiftX;
                    let newY = pageY - pdfContainer.offsetTop - shiftY;

                    newX = Math.max(0, Math.min(newX, pdfCanvas.width - div.offsetWidth));
                    newY = Math.max(0, Math.min(newY, pdfCanvas.height - div.offsetHeight));

                    div.style.left = newX + 'px';
                    div.style.top = newY + 'px';
                }

                function onMouseMove(event) {
                    moveAt(event.pageX, event.pageY);
                }

                document.addEventListener('mousemove', onMouseMove);
                div.onmouseup = function () {
                    document.removeEventListener('mousemove', onMouseMove);
                    div.onmouseup = null;
                };
            };

            div.ondragstart = () => false;
        }

        // Tambah placeholder
        document.getElementById('addPlaceholder').addEventListener('click', () => {
            createPlaceholder(50, 50);
        });

        // Simpan posisi
        document.getElementById('savePlaceholder').addEventListener('click', function () {
            let placeholderData = [];
            document.querySelectorAll('.placeholder').forEach(div => {
                let x = parseFloat(div.style.left);
                let y = pdfCanvas.height - parseFloat(div.style.top);
                placeholderData.push({ x: x, y: y });
            });

            console.log("Saving placeholder:", placeholderData);

            document.getElementById('placeholdersInput').value = JSON.stringify(placeholderData);
            document.getElementById('saveForm').submit(); // PASTIKAN ini jalan
        });
    </script>
</div>
@endsection
