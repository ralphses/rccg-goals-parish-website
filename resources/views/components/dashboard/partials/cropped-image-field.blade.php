@props([
    'label',
    'sourceName',
    'croppedName',
    'sourceId' => null,
    'currentUrl' => null,
    'currentLabel' => 'Current image',
    'helper' => 'Upload an image, drag it to choose the exact section to keep, then confirm the 4:3 crop.',
    'emptyState' => 'Select an image to begin cropping.',
    'resultLabel' => 'Final uploaded image',
    'aspectLabel' => '4:3',
    'targetLabel' => '1600x1200',
    'previewRounded' => false,
])

@php
    $cropId = $sourceId ?: str_replace(['[', ']', '.'], '-', $sourceName);
    $oldCroppedValue = old($croppedName);
    $previewClass = $previewRounded ? 'rounded-circle' : 'rounded';
@endphp

<div class="dashboard-crop-field" data-shared-image-cropper data-aspect-width="4" data-aspect-height="3" data-target-width="1600" data-target-height="1200">
    @if ($currentUrl)
        <div class="dashboard-form-preview-panel mb-3">
            <p class="mb-2 fw-semibold">{{ $currentLabel }}</p>
            <img src="{{ $currentUrl }}" alt="{{ $label }}" class="img-fluid border {{ $previewClass }}" style="max-height: 220px;">
        </div>
    @endif

    <div class="mb-3">
        <label class="form-label" for="{{ $cropId }}">{{ $label }}</label>
        <input
            type="file"
            name="{{ $sourceName }}"
            id="{{ $cropId }}"
            class="form-control"
            accept="image/jpeg,image/png,image/jpg,image/webp"
            data-crop-source
        >
        <input type="hidden" name="{{ $croppedName }}" value="{{ $oldCroppedValue }}" data-crop-output>
        <small class="dashboard-form-helper d-block mt-2">{{ $helper }} The final uploaded image is standardized to {{ $aspectLabel }} at {{ $targetLabel }}.</small>
    </div>

    <div class="dashboard-crop-card">
        <div class="dashboard-crop-status text-muted" data-crop-status>{{ $oldCroppedValue ? 'Saved crop ready. Replace the image to crop again.' : $emptyState }}</div>
        <div class="dashboard-crop-frame-wrap mt-3">
            <div class="dashboard-crop-frame" data-crop-frame>
                <img alt="{{ $label }} cropper" data-crop-image>
            </div>
        </div>
        <div class="dashboard-crop-controls mt-3 d-none" data-crop-controls>
            <label class="form-label mb-1" for="{{ $cropId }}-zoom">Zoom and position</label>
            <input type="range" id="{{ $cropId }}-zoom" min="1" max="3" step="0.01" value="1" class="form-range" data-crop-zoom>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <button type="button" class="btn btn-outline-primary btn-sm" data-crop-confirm>Use This Crop</button>
                <span class="dashboard-form-helper">Drag the image inside the frame to choose what stays.</span>
            </div>
        </div>
    </div>

    <div class="dashboard-form-preview-panel mt-3 {{ $oldCroppedValue ? '' : 'd-none' }}" data-crop-result-wrap>
        <p class="mb-2 fw-semibold">{{ $resultLabel }}</p>
        <img src="{{ $oldCroppedValue ?: '' }}" alt="{{ $resultLabel }}" class="img-fluid border {{ $previewClass }}" data-crop-result>
    </div>
</div>

@once
    @push('scripts')
        <style>
            .dashboard-crop-card {
                padding: 18px;
                border-radius: 20px;
                border: 1px solid #e2e8f0;
                background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
            }

            .dashboard-crop-frame-wrap {
                display: flex;
                justify-content: center;
            }

            .dashboard-crop-frame {
                position: relative;
                width: min(100%, 480px);
                aspect-ratio: 4 / 3;
                overflow: hidden;
                border-radius: 22px;
                border: 1px solid rgba(15, 23, 42, 0.08);
                background:
                    linear-gradient(135deg, rgba(15, 118, 110, 0.08), rgba(14, 165, 233, 0.08)),
                    repeating-linear-gradient(
                        45deg,
                        rgba(255, 255, 255, 0.35) 0,
                        rgba(255, 255, 255, 0.35) 10px,
                        rgba(255, 255, 255, 0.15) 10px,
                        rgba(255, 255, 255, 0.15) 20px
                    );
                cursor: grab;
                touch-action: none;
            }

            .dashboard-crop-frame.is-dragging {
                cursor: grabbing;
            }

            .dashboard-crop-frame::after {
                content: '';
                position: absolute;
                inset: 0;
                border: 1px dashed rgba(15, 23, 42, 0.18);
                border-radius: 22px;
                pointer-events: none;
            }

            .dashboard-crop-frame img {
                position: absolute;
                top: 0;
                left: 0;
                max-width: none;
                user-select: none;
                -webkit-user-drag: none;
            }

            .dashboard-crop-controls {
                max-width: 480px;
                margin-inline: auto;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                class SharedFixedRatioCropper {
                    constructor(root) {
                        this.root = root;
                        this.fileInput = root.querySelector('[data-crop-source]');
                        this.hiddenInput = root.querySelector('[data-crop-output]');
                        this.frame = root.querySelector('[data-crop-frame]');
                        this.image = root.querySelector('[data-crop-image]');
                        this.resultWrap = root.querySelector('[data-crop-result-wrap]');
                        this.result = root.querySelector('[data-crop-result]');
                        this.status = root.querySelector('[data-crop-status]');
                        this.controls = root.querySelector('[data-crop-controls]');
                        this.zoomInput = root.querySelector('[data-crop-zoom]');
                        this.confirmButton = root.querySelector('[data-crop-confirm]');
                        this.form = root.closest('form');
                        this.aspectWidth = Number(root.dataset.aspectWidth || 4);
                        this.aspectHeight = Number(root.dataset.aspectHeight || 3);
                        this.targetWidth = Number(root.dataset.targetWidth || 1600);
                        this.targetHeight = Number(root.dataset.targetHeight || 1200);
                        this.sourceUrl = null;
                        this.naturalWidth = 0;
                        this.naturalHeight = 0;
                        this.baseWidth = 0;
                        this.baseHeight = 0;
                        this.displayWidth = 0;
                        this.displayHeight = 0;
                        this.offsetX = 0;
                        this.offsetY = 0;
                        this.startX = 0;
                        this.startY = 0;
                        this.initialOffsetX = 0;
                        this.initialOffsetY = 0;
                        this.isDragging = false;
                        this.bind();
                        this.updateSubmitState();
                    }

                    bind() {
                        this.fileInput?.addEventListener('change', (event) => this.loadFile(event));
                        this.zoomInput?.addEventListener('input', () => this.applyZoom());
                        this.confirmButton?.addEventListener('click', () => this.exportCrop());
                        this.frame?.addEventListener('pointerdown', (event) => this.startDrag(event));
                        window.addEventListener('pointermove', (event) => this.onDrag(event));
                        window.addEventListener('pointerup', () => this.stopDrag());
                        this.form?.addEventListener('submit', () => this.updateSubmitState());
                    }

                    loadFile(event) {
                        const file = event.target.files?.[0];
                        if (!file) {
                            this.updateSubmitState();
                            return;
                        }

                        this.hiddenInput.value = '';
                        this.resultWrap.classList.add('d-none');
                        this.status.textContent = 'Loading image. Drag the photo to frame the best section.';
                        this.controls.classList.add('d-none');

                        if (this.sourceUrl) {
                            URL.revokeObjectURL(this.sourceUrl);
                        }

                        this.sourceUrl = URL.createObjectURL(file);
                        this.image.onload = () => this.prepareImage();
                        this.image.src = this.sourceUrl;
                    }

                    prepareImage() {
                        this.naturalWidth = this.image.naturalWidth;
                        this.naturalHeight = this.image.naturalHeight;

                        const frameWidth = this.frame.clientWidth;
                        const frameHeight = this.frame.clientHeight;
                        const scale = Math.max(frameWidth / this.naturalWidth, frameHeight / this.naturalHeight);
                        this.baseWidth = this.naturalWidth * scale;
                        this.baseHeight = this.naturalHeight * scale;
                        this.displayWidth = this.baseWidth;
                        this.displayHeight = this.baseHeight;
                        this.offsetX = (frameWidth - this.displayWidth) / 2;
                        this.offsetY = (frameHeight - this.displayHeight) / 2;
                        this.zoomInput.value = '1';
                        this.controls.classList.remove('d-none');
                        this.status.textContent = 'Drag the image to choose the focus area, then confirm the crop.';
                        this.render();
                        this.updateSubmitState();
                    }

                    startDrag(event) {
                        if (!this.sourceUrl) {
                            return;
                        }

                        this.isDragging = true;
                        this.frame.classList.add('is-dragging');
                        this.startX = event.clientX;
                        this.startY = event.clientY;
                        this.initialOffsetX = this.offsetX;
                        this.initialOffsetY = this.offsetY;
                        this.frame.setPointerCapture(event.pointerId);
                    }

                    onDrag(event) {
                        if (!this.isDragging) {
                            return;
                        }

                        this.offsetX = this.initialOffsetX + (event.clientX - this.startX);
                        this.offsetY = this.initialOffsetY + (event.clientY - this.startY);
                        this.clampOffsets();
                        this.render();
                    }

                    stopDrag() {
                        if (!this.isDragging) {
                            return;
                        }

                        this.isDragging = false;
                        this.frame.classList.remove('is-dragging');
                    }

                    applyZoom() {
                        if (!this.sourceUrl) {
                            return;
                        }

                        const zoomValue = Number(this.zoomInput.value || 1);
                        const frameWidth = this.frame.clientWidth;
                        const frameHeight = this.frame.clientHeight;
                        const centerRatioX = (frameWidth / 2 - this.offsetX) / this.displayWidth;
                        const centerRatioY = (frameHeight / 2 - this.offsetY) / this.displayHeight;
                        this.displayWidth = this.baseWidth * zoomValue;
                        this.displayHeight = this.baseHeight * zoomValue;
                        this.offsetX = frameWidth / 2 - centerRatioX * this.displayWidth;
                        this.offsetY = frameHeight / 2 - centerRatioY * this.displayHeight;
                        this.clampOffsets();
                        this.render();
                    }

                    clampOffsets() {
                        const frameWidth = this.frame.clientWidth;
                        const frameHeight = this.frame.clientHeight;
                        const minX = frameWidth - this.displayWidth;
                        const minY = frameHeight - this.displayHeight;
                        this.offsetX = Math.min(0, Math.max(minX, this.offsetX));
                        this.offsetY = Math.min(0, Math.max(minY, this.offsetY));
                    }

                    render() {
                        this.image.style.width = `${this.displayWidth}px`;
                        this.image.style.height = `${this.displayHeight}px`;
                        this.image.style.left = `${this.offsetX}px`;
                        this.image.style.top = `${this.offsetY}px`;
                    }

                    exportCrop() {
                        if (!this.sourceUrl) {
                            return;
                        }

                        const frameWidth = this.frame.clientWidth;
                        const frameHeight = this.frame.clientHeight;
                        const sourceX = (-this.offsetX / this.displayWidth) * this.naturalWidth;
                        const sourceY = (-this.offsetY / this.displayHeight) * this.naturalHeight;
                        const sourceWidth = (frameWidth / this.displayWidth) * this.naturalWidth;
                        const sourceHeight = (frameHeight / this.displayHeight) * this.naturalHeight;
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.width = this.targetWidth;
                        canvas.height = this.targetHeight;
                        context.drawImage(this.image, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, this.targetWidth, this.targetHeight);
                        const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                        this.hiddenInput.value = dataUrl;
                        this.result.src = dataUrl;
                        this.resultWrap.classList.remove('d-none');
                        this.status.textContent = 'Crop confirmed. This result will be uploaded.';
                        this.updateSubmitState();
                    }

                    updateSubmitState() {
                        const requiresCrop = this.fileInput?.files?.length > 0;
                        const valid = !requiresCrop || Boolean(this.hiddenInput?.value);
                        this.form?.querySelectorAll('button[type="submit"]').forEach((button) => {
                            if (requiresCrop && !valid) {
                                button.disabled = true;
                            } else if (button.hasAttribute('disabled') && button.disabled) {
                                button.disabled = false;
                            }
                        });
                    }
                }

                document.querySelectorAll('[data-shared-image-cropper]').forEach((root) => {
                    if (!root.dataset.cropperInitialized) {
                        root.dataset.cropperInitialized = 'true';
                        new SharedFixedRatioCropper(root);
                    }
                });
            });
        </script>
    @endpush
@endonce
