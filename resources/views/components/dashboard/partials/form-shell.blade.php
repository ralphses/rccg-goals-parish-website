@once
    @push('scripts')
        <style>
            .dashboard-form-shell {
                --form-primary: #0f766e;
                --form-primary-soft: #ecfeff;
                --form-ink: #0f172a;
                --form-muted: #64748b;
                --form-line: #e2e8f0;
            }

            .dashboard-form-hero {
                overflow: hidden;
                border-radius: 28px;
                background:
                    radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 30%),
                    radial-gradient(circle at left bottom, rgba(16, 185, 129, 0.12), transparent 28%),
                    linear-gradient(135deg, #ffffff 0%, #f6fbff 52%, #f8fffd 100%);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
                border: 0;
            }

            .dashboard-form-eyebrow {
                display: inline-block;
                margin-bottom: 12px;
                color: var(--form-primary);
                font-size: 0.78rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 800;
            }

            .dashboard-form-title {
                color: var(--form-ink);
                font-size: clamp(1.8rem, 2.6vw, 2.6rem);
                line-height: 1.15;
                margin-bottom: 8px;
            }

            .dashboard-form-subtitle {
                max-width: 760px;
                color: var(--form-muted);
                font-size: 1rem;
                margin-bottom: 0;
            }

            .dashboard-form-hero-actions {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }

            .dashboard-form-secondary-btn,
            .dashboard-form-primary-btn {
                border-radius: 16px;
                padding-inline: 22px;
            }

            .dashboard-form-primary-btn {
                box-shadow: 0 16px 30px rgba(15, 118, 110, 0.2);
            }

            .dashboard-form-note {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                color: var(--form-muted);
                font-size: 0.92rem;
            }

            .dashboard-form-note .dot {
                width: 10px;
                height: 10px;
                border-radius: 999px;
                background: #10b981;
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
            }

            .dashboard-form-card {
                border-radius: 26px;
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
                border: 0;
                overflow: hidden;
            }

            .dashboard-form-card .card-header {
                padding: 24px 24px 16px;
                background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
                border-bottom: 1px solid var(--form-line);
            }

            .dashboard-form-card .card-title,
            .dashboard-form-card .card-header h4 {
                color: var(--form-ink);
                font-weight: 800;
                margin-bottom: 0;
            }

            .dashboard-form-card .card-body {
                padding: 24px;
            }

            .dashboard-form-shell .alert {
                border-radius: 16px;
            }

            .dashboard-form-shell .form-group,
            .dashboard-form-shell .mb-3 {
                margin-bottom: 1rem;
            }

            .dashboard-form-shell .form-label,
            .dashboard-form-shell label {
                font-weight: 700;
                color: var(--form-ink);
                margin-bottom: 0.45rem;
            }

            .dashboard-form-shell .form-control,
            .dashboard-form-shell .form-select,
            .dashboard-form-shell .custom-file-label,
            .dashboard-form-shell select.form-control {
                min-height: 48px;
                border-radius: 14px;
                border-color: var(--form-line);
                box-shadow: none;
            }

            .dashboard-form-shell textarea.form-control {
                min-height: 120px;
            }

            .dashboard-form-shell .form-check {
                padding: 14px 16px 14px 40px;
                border-radius: 16px;
                background: #f8fafc;
                border: 1px solid var(--form-line);
            }

            .dashboard-form-shell .form-check-input {
                margin-top: 0.2rem;
            }

            .dashboard-form-shell .card-action,
            .dashboard-form-shell .dashboard-form-actions {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 24px;
            }

            .dashboard-form-shell .img-thumbnail,
            .dashboard-form-shell .dashboard-form-preview-image,
            .dashboard-form-shell video.img-thumbnail {
                border-radius: 16px;
                border-color: var(--form-line);
            }

            .dashboard-form-shell .dashboard-form-preview-panel {
                padding: 18px;
                border-radius: 20px;
                border: 1px solid var(--form-line);
                background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
            }

            .dashboard-form-shell .dashboard-form-helper {
                color: var(--form-muted);
                font-size: 0.88rem;
            }

            .dashboard-form-shell .list-group-item {
                border-color: var(--form-line);
            }

            @media (max-width: 767.98px) {
                .dashboard-form-hero,
                .dashboard-form-card {
                    border-radius: 22px;
                }

                .dashboard-form-card .card-header,
                .dashboard-form-card .card-body {
                    padding: 18px;
                }

                .dashboard-form-shell .card-action,
                .dashboard-form-shell .dashboard-form-actions {
                    justify-content: stretch;
                }

                .dashboard-form-shell .card-action .btn,
                .dashboard-form-shell .dashboard-form-actions .btn {
                    width: 100%;
                }
            }
        </style>
    @endpush
@endonce
