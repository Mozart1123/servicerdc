<!-- Cropper.js Modal (Point 8) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" integrity="sha512-hvNR0F/e2J7zPPfLC9auFe3/SE0yG4aJknTDq1G1XRzDeTR56dACTahuTx9EZBgG5IqxDceXYqKq1lmVDPeApw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" integrity="sha512-9KkIqdfN7bVQSLiIOomA3WasITVQCZ82EewMQ4Yq4K8nkv32Kvo9zFuF209UOnWDvVpeaF55SX8sKNGUfiRR7w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div id="cropper-modal" class="fixed inset-0 z-[999] bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full overflow-hidden shadow-2xl flex flex-col max-h-[90vh] animate-scale-in">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-crop text-rdc-blue"></i>
                <h3 class="font-bold text-slate-900 text-base" id="cropper-modal-title">Recadrer l image</h3>
            </div>
            <button type="button" onclick="cancelCrop()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center text-sm transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Modal Body -->
        <div class="p-4 bg-slate-900 flex-1 overflow-hidden flex items-center justify-center min-h-[300px] max-h-[55vh]">
            <div class="max-w-full max-h-full">
                <img id="cropper-target-image" src="" alt="A recadrer" class="max-w-full block" style="max-height: 50vh;">
            </div>
        </div>
        <!-- Toolbar -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="cropperZoom(0.1)" title="Zoomer" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors shadow-sm"><i class="fas fa-search-plus"></i></button>
                <button type="button" onclick="cropperZoom(-0.1)" title="Dezoomer" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors shadow-sm"><i class="fas fa-search-minus"></i></button>
                <button type="button" onclick="cropperRotate(-90)" title="Pivoter gauche" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors shadow-sm"><i class="fas fa-rotate-left"></i></button>
                <button type="button" onclick="cropperRotate(90)" title="Pivoter droite" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors shadow-sm"><i class="fas fa-rotate-right"></i></button>
                <button type="button" onclick="cropperReset()" title="Reinitialiser" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors shadow-sm"><i class="fas fa-arrows-rotate"></i></button>
            </div>
            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">Glissez et zoomez pour ajuster</span>
        </div>
        <!-- Footer -->
        <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-end gap-3">
            <button type="button" onclick="cancelCrop()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition-all">Annuler</button>
            <button type="button" onclick="confirmCrop()" class="px-6 py-2.5 rounded-xl bg-rdc-blue hover:bg-rdc-blue-dark text-white text-xs font-bold transition-all shadow-md flex items-center gap-2">
                <i class="fas fa-check"></i><span>Valider le recadrage</span>
            </button>
        </div>
    </div>
</div>
<script>
let currentCropper = null;
let currentFileInput = null;
let currentPreviewImg = null;
let currentCallback = null;
let currentOriginalFile = null;

function openImageCropper(options) {
    const modal = document.getElementById('cropper-modal');
    const imageEl = document.getElementById('cropper-target-image');
    const titleEl = document.getElementById('cropper-modal-title');
    currentFileInput = options.input || null;
    currentPreviewImg = options.preview || null;
    currentCallback = options.callback || null;
    currentOriginalFile = options.file || (options.input && options.input.files ? options.input.files[0] : null);
    if (!currentOriginalFile) return;
    if (titleEl && options.title) titleEl.textContent = options.title;
    const reader = new FileReader();
    reader.onload = function (e) {
        imageEl.src = e.target.result;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (currentCropper) currentCropper.destroy();
        currentCropper = new Cropper(imageEl, {
            aspectRatio: options.aspectRatio !== undefined ? options.aspectRatio : 1,
            viewMode: 2, dragMode: 'move', autoCropArea: 0.95,
            restore: false, guides: true, center: true, highlight: false,
            cropBoxMovable: true, cropBoxResizable: true, toggleDragModeOnDblclick: false,
        });
    };
    reader.readAsDataURL(currentOriginalFile);
}

function cropperZoom(delta) { if (currentCropper) currentCropper.zoom(delta); }
function cropperRotate(deg) { if (currentCropper) currentCropper.rotate(deg); }
function cropperReset() { if (currentCropper) currentCropper.reset(); }

function cancelCrop() {
    const modal = document.getElementById('cropper-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
    if (currentCropper) { currentCropper.destroy(); currentCropper = null; }
}

function confirmCrop() {
    if (!currentCropper) return;
    const canvas = currentCropper.getCroppedCanvas({
        maxWidth: 1920, maxHeight: 1920, fillColor: '#fff',
        imageSmoothingEnabled: true, imageSmoothingQuality: 'high',
    });
    canvas.toBlob(function (blob) {
        if (!blob) { cancelCrop(); return; }
        const fileName = currentOriginalFile
            ? (currentOriginalFile.name.replace(/\.[^/.]+$/, '') + '-cropped.jpg') : 'cropped.jpg';
        const croppedFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });

        // Strategy 1: DataTransfer (desktop & recent Android Chrome)
        let dataTransferSuccess = false;
        if (currentFileInput && window.DataTransfer) {
            try {
                const dt = new DataTransfer();
                dt.items.add(croppedFile);
                currentFileInput.files = dt.files;
                dataTransferSuccess = (currentFileInput.files.length > 0 && currentFileInput.files[0].size === croppedFile.size);
            } catch (err) {
                dataTransferSuccess = false;
                console.warn('[Cropper] DataTransfer failed, using FormData fallback', err);
            }
        }

        // Strategy 2: Blob registry + form submit interceptor (old Android WebView)
        if (!dataTransferSuccess && currentFileInput) {
            window.__cropperBlobRegistry = window.__cropperBlobRegistry || {};
            const inputName = currentFileInput.getAttribute('name');
            if (inputName) {
                window.__cropperBlobRegistry[inputName] = croppedFile;
                currentFileInput.setAttribute('data-cropper-blob', inputName);
                console.info('[Cropper] Blob stored for field:', inputName);
            }
        }

        // Update preview
        if (currentPreviewImg) {
            const dataUrl = canvas.toDataURL('image/jpeg');
            if (typeof currentPreviewImg === 'string') {
                const el = document.querySelector(currentPreviewImg);
                if (el) el.src = dataUrl;
            } else if (currentPreviewImg instanceof HTMLElement) {
                currentPreviewImg.src = dataUrl;
            }
        }

        // Callback
        if (typeof currentCallback === 'function') {
            currentCallback({ blob: blob, file: croppedFile, dataUrl: canvas.toDataURL('image/jpeg') });
        }
        cancelCrop();
    }, 'image/jpeg', 0.92);
}

// FormData/fetch submit interceptor — fires only when DataTransfer failed
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function (e) {
        var registry = window.__cropperBlobRegistry;
        if (!registry || Object.keys(registry).length === 0) return;
        var form = e.target;
        var affected = form.querySelectorAll('[data-cropper-blob]');
        if (affected.length === 0) return;
        e.preventDefault();
        var formData = new FormData(form);
        affected.forEach(function (input) {
            var name = input.getAttribute('data-cropper-blob');
            if (registry[name]) {
                formData.delete(name);
                formData.append(name, registry[name], registry[name].name);
                delete registry[name];
                input.removeAttribute('data-cropper-blob');
            }
        });
        fetch(form.action, {
            method: form.method || 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            redirect: 'follow',
        }).then(function (r) {
            if (r.redirected) { window.location.href = r.url; }
            else if (r.ok) { window.location.reload(); }
            else { form.submit(); }
        }).catch(function () { form.submit(); });
    }, true);
});
</script>
