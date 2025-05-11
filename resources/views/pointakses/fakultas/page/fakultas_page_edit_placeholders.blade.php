<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Placeholder - {{ $event->title }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <style>
        #pdfContainer {
            position: relative;
            display: inline-block;
            border: 1px solid black;
        }
        .placeholder {
            position: absolute;
            background: rgba(255, 0, 0, 0.7);
            padding: 5px;
            cursor: move;
            border-radius: 3px;
            user-select: none;
            font-size: 12px;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Edit Placeholder untuk {{ $event->title }}</h2>
    <div id="pdfContainer">
        <canvas id="pdfCanvas"></canvas>
    </div>

    <button id="addPlaceholder">Tambah Placeholder</button>
    <button id="savePlaceholder">Simpan Posisi</button>

    <form id="saveForm" method="POST" action="{{ route('fakultas.save.placeholder', ['event_id' => $event->id]) }}">
        @csrf
        <input type="hidden" name="placeholders" id="placeholdersInput">
    </form>

    <script>
        let placeholders = @json($event->placeholders ? json_decode($event->placeholders, true) : []);
        let pdfCanvas = document.getElementById('pdfCanvas');
        let pdfContainer = document.getElementById('pdfContainer');

        let pdfUrl = "{{ Storage::url($event->template_pdf) }}";

        // Ukuran PDF dalam pt (A4 Landscape)
        let pdfWidthPt = 841.92;
        let pdfHeightPt = 595.5;

        pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
            return pdf.getPage(1);
        }).then(page => {
            let viewport = page.getViewport({ scale: 1.0 });

            pdfCanvas.width = pdfWidthPt;
            pdfCanvas.height = pdfHeightPt;

            let context = pdfCanvas.getContext('2d');
            return page.render({ canvasContext: context, viewport: viewport }).promise;
        }).then(() => {
            pdfContainer.style.width = pdfWidthPt + "px";
            pdfContainer.style.height = pdfHeightPt + "px";

            placeholders.forEach(p => createPlaceholder(p.x, pdfHeightPt - p.y));
        });

        function createPlaceholder(x, y) {
            let div = document.createElement('div');
            div.className = 'placeholder';
            div.style.left = x + 'px';
            div.style.top = y + 'px';
            div.textContent = 'Nama';
            pdfContainer.appendChild(div);

            div.onmousedown = function (event) {
                event.preventDefault();
                let shiftX = event.clientX - div.getBoundingClientRect().left;
                let shiftY = event.clientY - div.getBoundingClientRect().top;

                function moveAt(pageX, pageY) {
                    let newX = pageX - pdfContainer.offsetLeft - shiftX;
                    let newY = pageY - pdfContainer.offsetTop - shiftY;

                    // Batasi agar placeholder tidak keluar dari batas PDF
                    newX = Math.max(0, Math.min(newX, pdfWidthPt - div.offsetWidth));
                    newY = Math.max(0, Math.min(newY, pdfHeightPt - div.offsetHeight));

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

            div.ondragstart = function () {
                return false;
            };
        }

        document.getElementById('addPlaceholder').addEventListener('click', function () {
            createPlaceholder(50, 50);
        });

        document.getElementById('savePlaceholder').addEventListener('click', function () {
            let placeholderData = [];
            document.querySelectorAll('.placeholder').forEach(div => {
                let x = parseFloat(div.style.left);
                let y = pdfHeightPt - parseFloat(div.style.top);
                placeholderData.push({ x: x, y: y });
            });

            document.getElementById('placeholdersInput').value = JSON.stringify(placeholderData);
            document.getElementById('saveForm').submit();
        });
    </script>
</body>
</html>
