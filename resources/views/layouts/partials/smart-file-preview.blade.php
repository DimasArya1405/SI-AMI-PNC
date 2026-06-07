<div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
    <div class="relative h-[90%] w-[90%] overflow-hidden rounded-lg bg-white">
        <div class="flex items-center justify-between border-b px-4 py-3">
            <h3 id="previewTitle" class="truncate text-sm font-semibold text-gray-700">Preview File</h3>
            <button type="button" onclick="closeSmartPreview()"
                class="rounded bg-red-500 px-3 py-1 text-sm font-medium text-white hover:bg-red-700">
                Tutup
            </button>
        </div>

        <div id="previewLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
            <p class="mt-3 text-sm text-gray-600">Memuat preview...</p>
        </div>

        <div id="previewError" class="hidden h-[calc(100%-52px)] flex-col items-center justify-center px-4 text-center">
            <p class="mb-2 font-semibold text-red-500">Preview tidak tersedia</p>
            <p id="previewErrorText" class="text-sm text-gray-500">File ini tidak bisa ditampilkan langsung.</p>
        </div>

        <iframe id="previewFrame" class="hidden h-[calc(100%-52px)] w-full"></iframe>

        <div id="previewImageWrapper" class="hidden h-[calc(100%-52px)] w-full items-center justify-center overflow-auto bg-gray-100">
            <img id="previewImage" src="" class="max-h-full max-w-full object-contain" alt="Preview bukti">
        </div>
    </div>
</div>

<script>
    window.previewTimeout = window.previewTimeout || null;

    window.openSmartPreview = function(previewUrl, extension, fileName) {
        const modal = document.getElementById('previewModal');
        const title = document.getElementById('previewTitle');
        const loading = document.getElementById('previewLoading');
        const error = document.getElementById('previewError');
        const errorText = document.getElementById('previewErrorText');
        const frame = document.getElementById('previewFrame');
        const imageWrapper = document.getElementById('previewImageWrapper');
        const image = document.getElementById('previewImage');

        clearTimeout(window.previewTimeout);

        frame.onload = null;
        image.onload = null;
        image.onerror = null;

        title.textContent = fileName;
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        loading.classList.remove('hidden');
        error.classList.add('hidden');
        frame.classList.add('hidden');
        imageWrapper.classList.add('hidden');
        imageWrapper.classList.remove('flex');
        errorText.textContent = 'File ini tidak bisa ditampilkan langsung.';

        frame.src = 'about:blank';
        image.src = '';

        const pdfFiles = ['pdf'];
        const imageFiles = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        const textFiles = ['txt', 'csv'];
        const officeFiles = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        if (pdfFiles.includes(extension) || textFiles.includes(extension)) {
            frame.src = previewUrl;
            frame.onload = function() {
                clearTimeout(window.previewTimeout);
                loading.classList.add('hidden');
                frame.classList.remove('hidden');
            };
            return;
        }

        if (imageFiles.includes(extension)) {
            image.src = previewUrl;
            image.onload = function() {
                clearTimeout(window.previewTimeout);
                loading.classList.add('hidden');
                imageWrapper.classList.remove('hidden');
                imageWrapper.classList.add('flex');
            };
            image.onerror = function() {
                clearTimeout(window.previewTimeout);
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                error.classList.add('flex');
                errorText.textContent = 'Gambar gagal ditampilkan.';
            };
            return;
        }

        loading.classList.add('hidden');
        error.classList.remove('hidden');
        error.classList.add('flex');
        errorText.textContent = officeFiles.includes(extension)
            ? 'File Word, Excel, atau PowerPoint tidak bisa dipreview langsung di browser.'
            : 'Format file ini tidak mendukung preview langsung.';
    };

    window.closeSmartPreview = function() {
        const modal = document.getElementById('previewModal');
        const frame = document.getElementById('previewFrame');
        const image = document.getElementById('previewImage');
        const loading = document.getElementById('previewLoading');
        const error = document.getElementById('previewError');
        const imageWrapper = document.getElementById('previewImageWrapper');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        clearTimeout(window.previewTimeout);

        frame.onload = null;
        image.onload = null;
        image.onerror = null;
        frame.src = 'about:blank';
        image.src = '';

        loading.classList.add('hidden');
        error.classList.add('hidden');
        error.classList.remove('flex');
        imageWrapper.classList.add('hidden');
        imageWrapper.classList.remove('flex');
    };

    document.addEventListener('click', function(event) {
        const button = event.target.closest('[data-preview-url]');

        if (!button) {
            return;
        }

        window.openSmartPreview(
            button.dataset.previewUrl,
            button.dataset.extension || '',
            button.dataset.fileName || 'Preview File'
        );
    });

    document.addEventListener('submit', function(event) {
        const target = event.target.dataset.scrollTarget;

        if (target) {
            sessionStorage.setItem('tk-scroll-target', target);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const target = sessionStorage.getItem('tk-scroll-target') || window.location.hash.replace('#', '');

        if (!target) {
            return;
        }

        const element = document.getElementById(target);

        if (element) {
            setTimeout(function() {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                element.classList.add('ring-2', 'ring-blue-300');

                setTimeout(function() {
                    element.classList.remove('ring-2', 'ring-blue-300');
                }, 1600);
            }, 150);
        }

        sessionStorage.removeItem('tk-scroll-target');
    });
</script>
