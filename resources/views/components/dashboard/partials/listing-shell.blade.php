@once
    @push('scripts')
        <style>
            .listing-shell {
                --listing-primary: #0f766e;
                --listing-primary-soft: #ecfeff;
                --listing-ink: #0f172a;
                --listing-muted: #64748b;
                --listing-line: #e2e8f0;
                --listing-surface: #ffffff;
                --listing-surface-soft: #f8fafc;
            }

            .listing-hero {
                overflow: hidden;
                border-radius: 28px;
                background:
                    radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 30%),
                    radial-gradient(circle at left bottom, rgba(16, 185, 129, 0.12), transparent 28%),
                    linear-gradient(135deg, #ffffff 0%, #f6fbff 52%, #f8fffd 100%);
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
                border: 0;
            }

            .listing-eyebrow {
                display: inline-block;
                margin-bottom: 12px;
                color: var(--listing-primary);
                font-size: 0.78rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 800;
            }

            .listing-title {
                color: var(--listing-ink);
                font-size: clamp(1.8rem, 2.6vw, 2.6rem);
                line-height: 1.15;
                margin-bottom: 8px;
            }

            .listing-subtitle {
                max-width: 760px;
                color: var(--listing-muted);
                font-size: 1rem;
                margin-bottom: 0;
            }

            .listing-hero-actions {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }

            .listing-primary-btn {
                border-radius: 16px;
                padding-inline: 24px;
                box-shadow: 0 16px 30px rgba(15, 118, 110, 0.2);
            }

            .listing-hero-note {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                color: var(--listing-muted);
                font-size: 0.92rem;
            }

            .listing-hero-note .dot {
                width: 10px;
                height: 10px;
                border-radius: 999px;
                background: #10b981;
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
            }

            .listing-stat-card {
                height: 100%;
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 18px;
                border-radius: 20px;
                border: 1px solid var(--listing-line);
                background: var(--listing-surface);
                box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
            }

            .listing-stat-icon {
                width: 52px;
                height: 52px;
                border-radius: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
            }

            .listing-stat-value {
                font-size: 1.55rem;
                font-weight: 800;
                color: var(--listing-ink);
                line-height: 1;
            }

            .listing-stat-label {
                margin-top: 4px;
                color: var(--listing-muted);
                font-size: 0.88rem;
            }

            .listing-library-card {
                border-radius: 26px;
                box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
                border: 0;
            }

            .listing-toolbar {
                display: flex;
                justify-content: space-between;
                gap: 16px;
                align-items: flex-start;
            }

            .listing-toolbar-title {
                color: var(--listing-ink);
                font-weight: 800;
                margin-bottom: 4px;
            }

            .listing-toolbar-subtitle,
            .listing-pagination-summary {
                color: var(--listing-muted);
            }

            .listing-toolbar-badge {
                white-space: nowrap;
                padding: 10px 14px;
                border-radius: 999px;
                background: var(--listing-primary-soft);
                color: var(--listing-primary);
                font-weight: 700;
                font-size: 0.9rem;
            }

            .listing-filter-form {
                padding: 20px;
                border-radius: 22px;
                background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
                border: 1px solid var(--listing-line);
            }

            .listing-filter-label {
                font-size: 0.84rem;
                font-weight: 700;
                letter-spacing: 0.03em;
                text-transform: uppercase;
                color: var(--listing-muted);
            }

            .listing-search-wrap {
                position: relative;
            }

            .listing-search-wrap i {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
            }

            .listing-search-wrap .form-control {
                padding-left: 40px;
            }

            .listing-filter-form .form-control,
            .listing-filter-form .form-select {
                min-height: 48px;
                border-radius: 14px;
                border-color: var(--listing-line);
                box-shadow: none;
            }

            .listing-filter-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .listing-bulk-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 16px 18px;
                border-radius: 20px;
                border: 1px solid var(--listing-line);
                background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
                margin-bottom: 18px;
            }

            .listing-bulk-summary {
                color: var(--listing-muted);
                font-size: 0.92rem;
                font-weight: 600;
            }

            .listing-bulk-summary strong {
                color: var(--listing-ink);
            }

            .listing-bulk-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .listing-check-cell {
                width: 52px;
                text-align: center;
            }

            .listing-check-input {
                width: 18px;
                height: 18px;
                cursor: pointer;
            }

            .listing-row.is-selected {
                background: rgba(15, 118, 110, 0.06);
            }

            .listing-table-wrap {
                border: 1px solid var(--listing-line);
                border-radius: 22px;
                overflow: hidden;
            }

            .listing-table {
                --bs-table-bg: transparent;
                --bs-table-hover-bg: rgba(15, 118, 110, 0.04);
                margin: 0;
            }

            .listing-table thead th {
                background: var(--listing-surface-soft);
                color: var(--listing-muted);
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-weight: 800;
                border-bottom: 1px solid var(--listing-line);
                padding: 16px 18px;
            }

            .listing-table tbody td {
                padding: 18px;
                border-color: #edf2f7;
                vertical-align: middle;
            }

            .listing-row {
                cursor: pointer;
            }

            .listing-main-cell {
                display: flex;
                align-items: center;
                gap: 14px;
                min-width: 240px;
            }

            .listing-thumb,
            .listing-thumb-icon {
                width: 104px;
                height: 78px;
                border-radius: 16px;
                border: 1px solid var(--listing-line);
                flex-shrink: 0;
            }

            .listing-thumb {
                object-fit: cover;
                display: block;
                background: var(--listing-surface-soft);
            }

            .listing-thumb-avatar {
                width: 54px;
                height: 54px;
                border-radius: 50%;
                object-fit: cover;
                border: 1px solid var(--listing-line);
                flex-shrink: 0;
            }

            .listing-thumb-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #f8fafc, #eef2ff);
                color: #475569;
                font-size: 1.2rem;
            }

            .listing-main-title {
                color: var(--listing-ink);
                font-weight: 800;
                line-height: 1.25;
                margin-bottom: 6px;
            }

            .listing-main-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 8px 12px;
                color: var(--listing-muted);
                font-size: 0.86rem;
            }

            .listing-pill {
                border-radius: 999px;
                padding: 8px 12px;
                font-weight: 700;
                font-size: 0.78rem;
            }

            .listing-pill.neutral {
                background: #f1f5f9;
                color: #334155;
            }

            .listing-pill.info {
                background: #eef6ff;
                color: #1d4ed8;
            }

            .listing-pill.success {
                background: #dcfce7;
                color: #15803d;
            }

            .listing-pill.warning {
                background: #fef3c7;
                color: #b45309;
            }

            .listing-pill.danger {
                background: #fee2e2;
                color: #dc2626;
            }

            .listing-pill.dark {
                background: #0f172a;
                color: #fff;
            }

            .listing-status-note,
            .listing-date-sub {
                color: var(--listing-muted);
                font-size: 0.84rem;
            }

            .listing-date {
                font-weight: 700;
                color: var(--listing-ink);
            }

            .listing-inline-link {
                color: #dc2626;
                font-size: 0.84rem;
                font-weight: 700;
                text-decoration: none;
            }

            .listing-inline-link:hover {
                text-decoration: underline;
            }

            .listing-action-btn {
                width: 38px;
                height: 38px;
                border-radius: 12px;
                border-color: var(--listing-line);
                background: #fff;
            }

            .listing-empty-state {
                text-align: center;
                padding: 56px 20px;
                border: 1px dashed #cbd5e1;
                border-radius: 24px;
                background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
            }

            .listing-empty-icon {
                width: 72px;
                height: 72px;
                margin: 0 auto 18px;
                border-radius: 22px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: var(--listing-primary-soft);
                color: var(--listing-primary);
                font-size: 1.5rem;
            }

            @media (max-width: 991.98px) {
                .listing-toolbar {
                    flex-direction: column;
                }

                .listing-toolbar-badge {
                    align-self: flex-start;
                }

                .listing-bulk-bar {
                    flex-direction: column;
                    align-items: stretch;
                }
            }

            @media (max-width: 767.98px) {
                .listing-hero,
                .listing-library-card {
                    border-radius: 22px;
                }

                .listing-stat-card,
                .listing-filter-form {
                    border-radius: 18px;
                }

                .listing-main-cell {
                    min-width: 220px;
                }

                .listing-thumb,
                .listing-thumb-icon {
                    width: 88px;
                    height: 66px;
                }
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-listing-row]').forEach(row => {
                    row.addEventListener('click', function(event) {
                        if (event.target.closest('.dropdown, .btn, a, button, form, input, label')) {
                            return;
                        }

                        const href = this.dataset.href;

                        if (href) {
                            window.location.href = href;
                        }
                    });
                });

                document.querySelectorAll('[data-bulk-form]').forEach(form => {
                    const selectAll = form.querySelector('[data-select-all]');
                    const checkboxes = Array.from(form.querySelectorAll('[data-select-item]'));
                    const countTarget = form.querySelector('[data-selected-count]');
                    const submitButton = form.querySelector('[data-bulk-submit]');

                    const updateState = () => {
                        const checked = checkboxes.filter(checkbox => checkbox.checked);

                        if (countTarget) {
                            countTarget.textContent = String(checked.length);
                        }

                        if (submitButton) {
                            submitButton.disabled = checked.length === 0;
                        }

                        if (selectAll) {
                            selectAll.checked = checked.length > 0 && checked.length === checkboxes.length;
                            selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
                        }

                        checkboxes.forEach(checkbox => {
                            const row = checkbox.closest('[data-listing-row]');
                            if (row) {
                                row.classList.toggle('is-selected', checkbox.checked);
                            }
                        });
                    };

                    if (selectAll) {
                        selectAll.addEventListener('click', event => event.stopPropagation());
                        selectAll.addEventListener('change', function() {
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = selectAll.checked;
                            });
                            updateState();
                        });
                    }

                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('click', event => event.stopPropagation());
                        checkbox.addEventListener('change', updateState);
                    });

                    form.addEventListener('submit', function(event) {
                        const checked = checkboxes.filter(checkbox => checkbox.checked);
                        if (!checked.length) {
                            event.preventDefault();
                            return;
                        }

                        if (!window.confirm('Delete the selected items? This action cannot be undone.')) {
                            event.preventDefault();
                        }
                    });

                    updateState();
                });
            });
        </script>
    @endpush
@endonce
