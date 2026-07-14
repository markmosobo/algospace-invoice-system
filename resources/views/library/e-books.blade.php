@extends('layouts.branch')

@section('page-title', 'E-Books')

@section('content')
<section>
    <div class="container">

        {{-- Digital Library Notice --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-dark-2 text-light rounded-20 p-20 border border-success">
                    <h5 class="mb-2">📚 Digital Library</h5>

                    <p class="mb-1">
                        Browse and instantly download our growing collection of digital books.
                    </p>

                    <ul class="mb-0">
                        <li>Instant PDF & EPUB downloads.</li>
                        <li>Available 24/7 from anywhere.</li>
                        <li>Downloads help us understand which books are most popular.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row g-4">

            @forelse($books as $book)

            <div class="col-lg-4">

                <div class="bg-dark-2 text-light rounded-20 overflow-hidden h-100 shadow-sm">

                    {{-- Cover --}}
                    <div class="position-relative">

                        <img
                            src="{{ $book->cover_url ?: asset('templates/marketing_site/images/misc/book-placeholder.webp') }}"
                            class="w-100"
                            alt="{{ $book->title }}"
                            style="height:340px;object-fit:cover;"
                        >

                        {{-- E-book Badge --}}
                        <span class="badge bg-primary position-absolute top-0 start-0 m-3">
                            <i class="fa fa-tablet-alt me-1"></i>
                            E-BOOK
                        </span>

                        {{-- Genre --}}
                        <span class="badge bg-warning text-dark position-absolute bottom-0 end-0 m-3">
                            {{ $book->genre ?? 'General' }}
                        </span>

                    </div>

                    {{-- Details --}}
                    <div class="p-4">

                        <h4 class="mb-1">
                            {{ $book->title }}
                        </h4>

                        <p class="mb-3 text-light-50">
                            <i class="fa fa-user me-1"></i>
                            {{ $book->author ?? 'Unknown Author' }}
                        </p>

                        <table class="table table-borderless table-sm text-light mb-3">

                            <tr>
                                <td width="40%">
                                    <strong>Pages</strong>
                                </td>
                                <td>
                                    {{ $book->pages ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>Language</strong>
                                </td>
                                <td>
                                    {{ $book->language }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>Format</strong>
                                </td>
                                <td>
                                    {{ strtoupper(pathinfo($book->ebook_file ?? '', PATHINFO_EXTENSION)) ?: '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>File Size</strong>
                                </td>
                                <td>
                                    {{ $book->file_size_human ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>Downloads</strong>
                                </td>
                                <td>
                                    {{ number_format($book->download_count) }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>Owner</strong>
                                </td>
                                <td>
                                    {{ optional($book->partner)->name ?? 'AlgoSpace Library' }}
                                </td>
                            </tr>

                        </table>

                        {{-- Description --}}
                        @if(!empty($book->description))
                            <div class="mb-3">
                                <small class="text-light-50">
                                    {{ \Illuminate\Support\Str::limit($book->description,140) }}
                                </small>
                            </div>
                        @endif

                        {{-- Buttons --}}
                        <div class="row g-2 mt-2">

                            <div class="col-6">

                                <a
                                    href="{{ route('ebooks.download', $book) }}"
                                    class="btn-main fx-slide btn-line w-100 text-center">

                                    <span>
                                        <i class="fa fa-download me-2"></i>
                                        Download
                                    </span>

                                </a>

                            </div>

                            <div class="col-6">

                                <a
                                    href="{{ route('ebooks.read', $book) }}"
                                    class="btn-main fx-slide btn-line w-100 text-center">

                                    <span>
                                        <i class="fa fa-book-open me-2"></i>
                                        Read Online
                                    </span>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            @empty

            <div class="col-12">

                <div class="text-center py-5">

                    <i class="fa fa-book-open fa-4x text-muted mb-3"></i>

                    <h4>No E-Books Available</h4>

                    <p class="text-muted">
                        Our digital library is continually expanding.
                        Please check back soon.
                    </p>

                </div>

            </div>

            @endforelse

        </div>

        {{-- Pagination --}}
        @if($books->hasPages())

        <div class="row mt-5">

            <div class="col-12 text-center">

                {{ $books->links('pagination::bootstrap-5') }}

            </div>

        </div>

        @endif

    </div>
</section>
@endsection