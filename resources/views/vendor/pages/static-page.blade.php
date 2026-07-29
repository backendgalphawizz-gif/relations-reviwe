@extends('vendor.app')

@push('css_or_js')
    <title>{{ $pageTitle ?? 'Page' }}</title>
    <style>
        /* Hide scrollbar on privacy/terms pages (keep scrolling) */
        .content {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .content::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            display: none !important;
        }
        .side-nav {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .side-nav::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            display: none !important;
        }

        .static-page-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08), 0 8px 24px rgba(15, 23, 42, 0.04);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .static-page-card__header {
            background: linear-gradient(135deg, #426f7f 0%, #2f5563 100%);
            color: #fff;
            padding: 1.25rem 1.5rem;
        }
        .static-page-card__header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .static-page-content {
            padding: 1.5rem 1.75rem 2rem;
            color: #334155;
            font-size: 0.975rem;
            line-height: 1.75;
        }
        .static-page-content h1,
        .static-page-content h2,
        .static-page-content h3,
        .static-page-content h4 {
            color: #1e293b;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .static-page-content h1:first-child,
        .static-page-content h2:first-child,
        .static-page-content h3:first-child {
            margin-top: 0;
        }
        .static-page-content p {
            margin-bottom: 0.9rem;
        }
        .static-page-content ul,
        .static-page-content ol {
            margin: 0.75rem 0 1rem 1.25rem;
            padding-left: 0.5rem;
        }
        .static-page-content li {
            margin-bottom: 0.4rem;
        }
        .static-page-content a {
            color: #426f7f;
            font-weight: 500;
            text-decoration: underline;
        }
        .static-page-content a:hover {
            color: #2f5563;
        }
        .static-page-content strong {
            color: #1e293b;
        }
    </style>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12">
            <div class="static-page-card intro-y">
                <div class="static-page-card__header">
                    <h2>{{ $pageTitle ?? 'Page' }}</h2>
                </div>
                <div class="static-page-content">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
    </script>
@endpush
