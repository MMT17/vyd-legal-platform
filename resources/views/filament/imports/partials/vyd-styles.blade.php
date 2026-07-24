@once
    <style>
        .vyd-ui {
            --vyd-navy-950: #071525;
            --vyd-navy-900: #0f2744;
            --vyd-navy-800: #16365d;
            --vyd-gold-600: #b8862f;
            --vyd-gold-500: #c89b3c;
            --vyd-gold-400: #d7a947;
            --vyd-success: #16a34a;
            --vyd-warning: #d97706;
            --vyd-danger: #dc2626;
            --vyd-info: #2563eb;
            --vyd-bg: #f6f7f9;
            --vyd-surface: #ffffff;
            --vyd-surface-raised: #ffffff;
            --vyd-border: #dde2e8;
            --vyd-text: #18202a;
            --vyd-text-muted: #667085;
            --vyd-radius-sm: 8px;
            --vyd-radius-md: 12px;
            --vyd-radius-lg: 16px;
            --vyd-space-3: 0.75rem;
            --vyd-space-4: 1rem;
            --vyd-shadow-sm: 0 1px 2px rgb(7 21 37 / 0.06), 0 1px 1px rgb(7 21 37 / 0.04);
            --vyd-focus: 0 0 0 3px rgb(200 155 60 / 0.28);
            --vyd-transition: 180ms ease;
        }

        .dark .vyd-ui {
            --vyd-bg: #0b0d10;
            --vyd-surface: #14171c;
            --vyd-surface-raised: #1b1f26;
            --vyd-border: #303640;
            --vyd-text: #f5f7fa;
            --vyd-text-muted: #a7afbc;
        }

        .vyd-ui .vyd-panel,
        .vyd-ui.vyd-panel {
            border: 1px solid var(--vyd-border);
            border-radius: var(--vyd-radius-lg);
            background: var(--vyd-surface);
            color: var(--vyd-text);
            box-shadow: var(--vyd-shadow-sm);
        }

        .vyd-ui .vyd-card,
        .vyd-ui.vyd-card {
            border: 1px solid var(--vyd-border);
            border-radius: var(--vyd-radius-md);
            background: var(--vyd-surface-raised);
            color: var(--vyd-text);
            box-shadow: var(--vyd-shadow-sm);
            transition: border-color var(--vyd-transition), box-shadow var(--vyd-transition);
        }

        .vyd-ui .vyd-card:focus-within,
        .vyd-ui.vyd-card:focus-within,
        .vyd-ui .vyd-focusable:focus-visible,
        .vyd-ui.vyd-focusable:focus-visible {
            outline: none;
            box-shadow: var(--vyd-focus);
        }

        .vyd-ui .vyd-muted,
        .vyd-ui.vyd-muted {
            color: var(--vyd-text-muted);
        }

        .vyd-ui .vyd-section-title,
        .vyd-ui.vyd-section-title {
            color: var(--vyd-text);
            font-size: 1.125rem;
            font-weight: 650;
            line-height: 1.35;
        }

        .vyd-ui .vyd-table-scroll,
        .vyd-ui.vyd-table-scroll {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .vyd-ui .vyd-grid-metrics,
        .vyd-ui.vyd-grid-metrics {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: var(--vyd-space-3);
        }

        .vyd-ui .vyd-import-layout,
        .vyd-ui.vyd-import-layout {
            display: grid;
            gap: var(--vyd-space-4);
        }

        .vyd-ui .vyd-stepper {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin: 0;
            padding: 0.5rem;
            list-style: none;
            border: 1px solid var(--vyd-border);
            border-radius: var(--vyd-radius-lg);
            background: var(--vyd-surface);
            box-shadow: var(--vyd-shadow-sm);
        }

        .vyd-ui .vyd-stepper__item {
            position: relative;
            display: flex;
            min-width: 0;
            flex: 1 1 0;
            align-items: center;
            gap: 0.5rem;
            border-radius: var(--vyd-radius-sm);
            padding: 0.5rem 0.625rem;
            color: var(--vyd-text-muted);
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.25rem;
        }

        .vyd-ui .vyd-stepper__item--active {
            color: var(--vyd-text);
            background: color-mix(in srgb, var(--vyd-gold-500) 10%, transparent);
        }

        .vyd-ui .vyd-stepper__item--completed {
            color: #166534;
        }

        .dark .vyd-ui .vyd-stepper__item--completed {
            color: #bbf7d0;
        }

        .vyd-ui .vyd-stepper__marker {
            display: inline-flex;
            width: 1.75rem;
            height: 1.75rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--vyd-border);
            border-radius: 9999px;
            background: var(--vyd-surface-raised);
            color: var(--vyd-text-muted);
            font-size: 0.75rem;
            font-weight: 700;
        }

        .vyd-ui .vyd-stepper__marker svg {
            width: 1rem;
            height: 1rem;
        }

        .vyd-ui .vyd-stepper__item--active .vyd-stepper__marker {
            border-color: var(--vyd-gold-500);
            background: #fffbeb;
            color: #92400e;
            box-shadow: 0 0 0 3px rgb(200 155 60 / 0.22);
        }

        .dark .vyd-ui .vyd-stepper__item--active .vyd-stepper__marker {
            background: rgb(69 26 3 / 0.45);
            color: #fde68a;
        }

        .vyd-ui .vyd-stepper__item--completed .vyd-stepper__marker {
            border-color: #16a34a;
            background: #16a34a;
            color: #ffffff;
        }

        .vyd-ui .vyd-stepper__label {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .vyd-ui .vyd-stepper__connector {
            display: none;
            height: 1px;
            flex: 1 1 auto;
            background: var(--vyd-border);
        }

        .vyd-ui .vyd-upload {
            position: relative;
            display: flex;
            min-height: 16rem;
            cursor: pointer;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #d1d5db;
            border-radius: var(--vyd-radius-lg);
            background: var(--vyd-surface);
            padding: 2.5rem 1.5rem;
            text-align: center;
            transition: border-color var(--vyd-transition), background-color var(--vyd-transition), box-shadow var(--vyd-transition);
        }

        .vyd-ui .vyd-upload:hover,
        .vyd-ui .vyd-upload.is-dragging,
        .vyd-ui .vyd-upload:focus-within {
            border-color: var(--vyd-gold-500);
            background: color-mix(in srgb, var(--vyd-gold-500) 7%, var(--vyd-surface));
        }

        .vyd-ui .vyd-upload:focus-within {
            box-shadow: var(--vyd-focus);
        }

        .dark .vyd-ui .vyd-upload {
            border-color: #374151;
            background: rgb(3 7 18 / 0.4);
        }

        .dark .vyd-ui .vyd-upload:hover,
        .dark .vyd-ui .vyd-upload.is-dragging,
        .dark .vyd-ui .vyd-upload:focus-within {
            border-color: var(--vyd-gold-500);
            background: rgb(69 26 3 / 0.16);
        }

        .vyd-ui .vyd-upload__input {
            position: absolute;
            inset: 0;
            z-index: 10;
            width: 100%;
            height: 100%;
            cursor: pointer;
            opacity: 0;
        }

        .vyd-ui .vyd-native-file {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
            padding: 0;
        }

        .vyd-ui .vyd-upload__icon,
        .vyd-ui .vyd-upload__title,
        .vyd-ui .vyd-upload__separator,
        .vyd-ui .vyd-upload__button,
        .vyd-ui .vyd-upload__helper {
            pointer-events: none;
        }

        .vyd-ui .vyd-upload__icon {
            display: inline-flex;
            width: 3.5rem;
            height: 3.5rem;
            align-items: center;
            justify-content: center;
            border-radius: var(--vyd-radius-md);
            background: #fffbeb;
            color: #b45309;
            box-shadow: inset 0 0 0 1px #fde68a;
            transition: transform var(--vyd-transition);
        }

        .vyd-ui .vyd-upload__icon svg {
            width: 1.75rem;
            height: 1.75rem;
        }

        .vyd-ui .vyd-upload:hover .vyd-upload__icon,
        .vyd-ui .vyd-upload.is-dragging .vyd-upload__icon {
            transform: scale(1.04);
        }

        .dark .vyd-ui .vyd-upload__icon {
            background: rgb(69 26 3 / 0.32);
            color: #fcd34d;
            box-shadow: inset 0 0 0 1px rgb(120 53 15 / 0.7);
        }

        .vyd-ui .vyd-upload__title {
            margin-top: 1rem;
            color: var(--vyd-text);
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5rem;
        }

        .vyd-ui .vyd-upload__separator {
            margin-top: 0.25rem;
            color: var(--vyd-text-muted);
            font-size: 0.875rem;
        }

        .vyd-ui .vyd-upload__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 0.75rem;
            border-radius: var(--vyd-radius-sm);
            background: var(--vyd-navy-900);
            color: #ffffff;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 700;
            box-shadow: var(--vyd-shadow-sm);
        }

        .vyd-ui .vyd-upload:hover .vyd-upload__button,
        .vyd-ui .vyd-upload.is-dragging .vyd-upload__button {
            background: var(--vyd-navy-800);
        }

        .vyd-ui .vyd-upload__helper {
            margin-top: 0.75rem;
            color: var(--vyd-text-muted);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        @media (min-width: 640px) {
            .vyd-ui .vyd-stepper {
                flex-direction: row;
                align-items: center;
                gap: 0;
            }

            .vyd-ui .vyd-stepper__connector {
                display: block;
            }

            .vyd-ui .vyd-grid-metrics,
            .vyd-ui.vyd-grid-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .vyd-ui .vyd-grid-metrics,
            .vyd-ui.vyd-grid-metrics {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .vyd-ui .vyd-import-layout,
            .vyd-ui.vyd-import-layout {
                grid-template-columns: minmax(0, 1.2fr) minmax(18rem, 0.8fr);
            }
        }

        .vyd-ui .vyd-file-action {
            position: relative;
            display: inline-flex;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            border-radius: var(--vyd-radius-sm);
            background: var(--vyd-surface);
            color: var(--vyd-text);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 650;
            line-height: 1.25rem;
            box-shadow: var(--vyd-shadow-sm);
            transition: background-color var(--vyd-transition), border-color var(--vyd-transition), box-shadow var(--vyd-transition);
        }

        .vyd-ui .vyd-file-action:hover,
        .vyd-ui .vyd-file-action:focus-within {
            border-color: var(--vyd-gold-500);
            background: color-mix(in srgb, var(--vyd-gold-500) 7%, var(--vyd-surface));
        }

        .vyd-ui .vyd-file-action:focus-within {
            box-shadow: var(--vyd-focus);
        }

        .vyd-ui .vyd-metrics-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: var(--vyd-space-3);
        }

        .vyd-ui .vyd-summary-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: var(--vyd-space-3);
        }

        .vyd-ui .vyd-metric {
            min-height: 7rem;
            padding: 1.25rem;
        }

        .vyd-ui .vyd-metric__content {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 1rem;
        }

        .vyd-ui .vyd-metric__icon {
            display: inline-flex;
            width: 2.75rem;
            height: 2.75rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: var(--vyd-radius-sm);
            background: #f3f4f6;
            color: #374151;
        }

        .vyd-ui .vyd-metric__icon svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .vyd-ui .vyd-metric__icon--success {
            background: #f0fdf4;
            color: #15803d;
        }

        .vyd-ui .vyd-metric__icon--warning,
        .vyd-ui .vyd-metric__icon--gold {
            background: #fffbeb;
            color: #b45309;
        }

        .vyd-ui .vyd-metric__icon--danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .vyd-ui .vyd-metric__icon--info {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .dark .vyd-ui .vyd-metric__icon {
            background: #1f2937;
            color: #d1d5db;
        }

        .dark .vyd-ui .vyd-metric__icon--success {
            background: rgb(20 83 45 / 0.35);
            color: #86efac;
        }

        .dark .vyd-ui .vyd-metric__icon--warning,
        .dark .vyd-ui .vyd-metric__icon--gold {
            background: rgb(69 26 3 / 0.35);
            color: #fcd34d;
        }

        .dark .vyd-ui .vyd-metric__icon--danger {
            background: rgb(127 29 29 / 0.35);
            color: #fca5a5;
        }

        .dark .vyd-ui .vyd-metric__icon--info {
            background: rgb(30 58 138 / 0.35);
            color: #93c5fd;
        }

        .vyd-ui .vyd-metric__body {
            min-width: 0;
        }

        .vyd-ui .vyd-metric__value {
            margin: 0;
            color: var(--vyd-text);
            font-size: 1.875rem;
            font-weight: 700;
            letter-spacing: 0;
            line-height: 1;
        }

        .vyd-ui .vyd-metric__label {
            margin: 0.5rem 0 0;
            color: var(--vyd-text-muted);
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.25rem;
        }

        .vyd-ui .vyd-metric__description {
            margin: 0.75rem 0 0;
            color: var(--vyd-text-muted);
            font-size: 0.875rem;
        }

        .vyd-ui .vyd-info-pair {
            border: 1px solid var(--vyd-border);
            border-radius: var(--vyd-radius-sm);
            background: var(--vyd-surface-raised);
            padding: 0.875rem 1rem;
        }

        .vyd-ui .vyd-info-pair__label {
            margin: 0;
            color: var(--vyd-text-muted);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            line-height: 1rem;
            text-transform: uppercase;
        }

        .vyd-ui .vyd-info-pair__value {
            margin: 0.375rem 0 0;
            color: var(--vyd-text);
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.35rem;
            overflow-wrap: anywhere;
        }

        .vyd-ui .vyd-preview-table-wrap {
            max-width: 100%;
            overflow: auto;
            border: 1px solid var(--vyd-border);
            border-radius: var(--vyd-radius-md);
            background: var(--vyd-surface);
            box-shadow: var(--vyd-shadow-sm);
            -webkit-overflow-scrolling: touch;
        }

        .vyd-ui .vyd-preview-table {
            width: 100%;
            min-width: max-content;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--vyd-text);
            font-size: 0.875rem;
            line-height: 1.35rem;
            text-align: left;
        }

        .vyd-ui .vyd-preview-table__head {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f9fafb;
            color: #4b5563;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .dark .vyd-ui .vyd-preview-table__head {
            background: #111827;
            color: #d1d5db;
        }

        .vyd-ui .vyd-preview-table__row {
            background: var(--vyd-surface);
            transition: background-color var(--vyd-transition);
        }

        .vyd-ui .vyd-preview-table__row:hover {
            background: color-mix(in srgb, var(--vyd-text) 4%, var(--vyd-surface));
        }

        .vyd-ui .vyd-preview-table__cell {
            border-bottom: 1px solid var(--vyd-border);
            padding: 0.75rem 1rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        .vyd-ui .vyd-preview-table__cell--head {
            padding-top: 0.875rem;
            padding-bottom: 0.875rem;
        }

        .vyd-ui .vyd-preview-table__cell--row,
        .vyd-ui .vyd-preview-table__cell--caso,
        .vyd-ui .vyd-preview-table__cell--nis,
        .vyd-ui .vyd-preview-table__cell--ruc,
        .vyd-ui .vyd-preview-table__cell--rit,
        .vyd-ui .vyd-preview-table__cell--fecha-presentacion {
            min-width: 7rem;
        }

        .vyd-ui .vyd-preview-table__cell--status {
            min-width: 11rem;
        }

        .vyd-ui .vyd-preview-table__cell--nombre {
            min-width: 18rem;
            white-space: normal;
        }

        .vyd-ui .vyd-preview-table__cell--comuna {
            min-width: 11rem;
        }

        .vyd-ui .vyd-preview-table__cell--juzgado {
            min-width: 18rem;
            white-space: normal;
        }

        .vyd-ui .vyd-preview-table__cell--monto-total {
            min-width: 10rem;
            text-align: right;
        }

        .vyd-ui .vyd-preview-table__cell--caso .vyd-preview-table__value,
        .vyd-ui .vyd-preview-table__cell--nis .vyd-preview-table__value,
        .vyd-ui .vyd-preview-table__cell--ruc .vyd-preview-table__value,
        .vyd-ui .vyd-preview-table__cell--rit .vyd-preview-table__value,
        .vyd-ui .vyd-preview-table__cell--monto-total .vyd-preview-table__value {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-variant-numeric: tabular-nums;
            font-weight: 650;
        }

        .vyd-ui .vyd-preview-table__empty {
            padding: 2.5rem 1rem;
            text-align: center;
            color: var(--vyd-text-muted);
            font-size: 0.875rem;
        }

        @media (min-width: 768px) {
            .vyd-ui .vyd-metrics-grid--review,
            .vyd-ui .vyd-metrics-grid--result,
            .vyd-ui .vyd-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .vyd-ui .vyd-metrics-grid--review {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .vyd-ui .vyd-metrics-grid--result,
            .vyd-ui .vyd-summary-grid--compact {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .vyd-ui .vyd-metrics-grid--result {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .vyd-ui {
                --vyd-transition: 0ms linear;
            }
        }
    </style>
@endonce
