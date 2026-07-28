@once
    @push('scripts')
        <style>
            .show-shell {
                --show-primary: #0f766e;
                --show-primary-soft: #ecfeff;
                --show-ink: #0f172a;
                --show-muted: #64748b;
                --show-line: #e2e8f0;
                --show-surface: #ffffff;
                --show-surface-soft: #f8fafc;
            }

            .show-hero {
                overflow: hidden;
                border-radius: 28px;
                background:
                    radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 30%),
                    radial-gradient(circle at left bottom, rgba(16, 185, 129, 0.12), transparent 28%),
                    linear-gradient(135deg, #ffffff 0%, #f6fbff 52%, #f8fffd 100%);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
                border: 0;
            }

            .show-eyebrow {
                display: inline-block;
                margin-bottom: 12px;
                color: var(--show-primary);
                font-size: 0.78rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 800;
            }

            .show-title {
                color: var(--show-ink);
                font-size: clamp(1.8rem, 2.6vw, 2.6rem);
                line-height: 1.15;
                margin-bottom: 8px;
            }

            .show-subtitle {
                max-width: 760px;
                color: var(--show-muted);
                font-size: 1rem;
                margin-bottom: 0;
            }

            .show-hero-actions {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }

            .show-primary-btn {
                border-radius: 16px;
                padding-inline: 22px;
                box-shadow: 0 16px 30px rgba(15, 118, 110, 0.2);
            }

            .show-hero-note {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                color: var(--show-muted);
                font-size: 0.92rem;
            }

            .show-hero-note .dot {
                width: 10px;
                height: 10px;
                border-radius: 999px;
                background: #10b981;
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
            }

            .show-stat-card,
            .show-detail-card,
            .show-media-card,
            .show-side-card,
            .show-info-card {
                border-radius: 24px;
                border: 1px solid var(--show-line);
                background: var(--show-surface);
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            }

            .show-stat-card {
                height: 100%;
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 18px;
            }

            .show-stat-icon {
                width: 52px;
                height: 52px;
                border-radius: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
            }

            .show-stat-value {
                font-size: 1.45rem;
                font-weight: 800;
                color: var(--show-ink);
                line-height: 1;
            }

            .show-stat-label {
                margin-top: 4px;
                color: var(--show-muted);
                font-size: 0.88rem;
            }

            .show-detail-card,
            .show-side-card {
                overflow: hidden;
            }

            .show-card-header {
                padding: 24px 24px 16px;
                border-bottom: 1px solid var(--show-line);
                background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            }

            .show-card-title {
                color: var(--show-ink);
                font-weight: 800;
                margin-bottom: 4px;
            }

            .show-card-subtitle {
                color: var(--show-muted);
                margin-bottom: 0;
                font-size: 0.92rem;
            }

            .show-card-body {
                padding: 24px;
            }

            .show-media-frame {
                border-radius: 22px;
                border: 1px solid var(--show-line);
                overflow: hidden;
                background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            }

            .show-media-frame img,
            .show-media-frame video,
            .show-media-frame iframe {
                width: 100%;
                display: block;
                border: 0;
            }

            .show-media-frame img,
            .show-media-frame video {
                object-fit: cover;
            }

            .show-media-frame.visual img,
            .show-media-frame.visual video {
                aspect-ratio: 4 / 3;
            }

            .show-media-frame.video iframe {
                aspect-ratio: 16 / 9;
            }

            .show-pill {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: 8px 12px;
                font-size: 0.78rem;
                font-weight: 700;
            }

            .show-pill.neutral {
                background: #f1f5f9;
                color: #334155;
            }

            .show-pill.info {
                background: #eef6ff;
                color: #1d4ed8;
            }

            .show-pill.success {
                background: #dcfce7;
                color: #15803d;
            }

            .show-pill.warning {
                background: #fef3c7;
                color: #b45309;
            }

            .show-pill.danger {
                background: #fee2e2;
                color: #dc2626;
            }

            .show-pill.dark {
                background: #0f172a;
                color: #fff;
            }

            .show-meta-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
            }

            .show-meta-item {
                padding: 16px 18px;
                border-radius: 18px;
                background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
                border: 1px solid var(--show-line);
            }

            .show-meta-label {
                display: block;
                margin-bottom: 8px;
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: var(--show-muted);
                font-weight: 800;
            }

            .show-meta-value {
                color: var(--show-ink);
                font-weight: 700;
                line-height: 1.45;
            }

            .show-content-block {
                padding: 20px;
                border-radius: 20px;
                background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
                border: 1px solid var(--show-line);
                color: #334155;
                line-height: 1.7;
            }

            .show-content-block p:last-child {
                margin-bottom: 0;
            }

            .show-side-stack {
                display: grid;
                gap: 14px;
            }

            .show-side-item {
                display: flex;
                gap: 14px;
                padding: 16px;
                border-radius: 18px;
                border: 1px solid var(--show-line);
                background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            }

            .show-side-icon {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: #ecfeff;
                color: var(--show-primary);
            }

            .show-side-item h6 {
                margin-bottom: 4px;
                color: var(--show-ink);
                font-weight: 800;
            }

            .show-side-item p,
            .show-side-item small {
                margin-bottom: 0;
                color: var(--show-muted);
            }

            .show-action-row {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
            }

            .show-attachment-list {
                display: grid;
                gap: 12px;
            }

            .show-attachment-item {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: center;
                padding: 16px 18px;
                border-radius: 18px;
                border: 1px solid var(--show-line);
                background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            }

            .show-empty-state {
                text-align: center;
                padding: 36px 24px;
                color: var(--show-muted);
            }

            .show-empty-state i {
                font-size: 1.8rem;
                color: #94a3b8;
                margin-bottom: 12px;
            }

            .show-thumb-list {
                display: grid;
                gap: 14px;
            }

            .show-thumb-card {
                border: 1px solid var(--show-line);
                border-radius: 20px;
                overflow: hidden;
                background: #fff;
            }

            .show-thumb-card img,
            .show-thumb-card video {
                width: 100%;
                aspect-ratio: 4 / 3;
                object-fit: cover;
                display: block;
            }

            .show-thumb-card-body {
                padding: 14px 16px;
            }

            .show-thumb-card-title {
                color: var(--show-ink);
                font-weight: 800;
                margin-bottom: 6px;
            }

            .show-note-card {
                border-radius: 20px;
                padding: 16px 18px;
            }

            @media (max-width: 991.98px) {
                .show-meta-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 767.98px) {
                .show-hero,
                .show-detail-card,
                .show-side-card {
                    border-radius: 22px;
                }

                .show-card-header,
                .show-card-body {
                    padding: 18px;
                }
            }
        </style>
    @endpush
@endonce
