@extends('layouts.branch')

@section('page-title', $book->title)

@section('content')
<section>
    <div class="container">

        <div class="row gx-5">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-8">

                <div class="row">

                    <div class="col-md-4">

                        <img
                            src="{{ $book->cover_url ?: asset('templates/marketing_site/images/misc/book-placeholder.webp') }}"
                            class="img-fluid rounded shadow-sm"
                            alt="{{ $book->title }}"
                        >

                    </div>

                    <div class="col-md-8">

                        <div class="mb-2">

                            <span class="badge bg-warning text-dark">
                                {{ $book->genre ?? 'General' }}
                            </span>

                            <span class="badge bg-info">
                                {{ strtoupper($book->book_type) }}
                            </span>

                            @if($book->status == 'available')
                                <span class="badge bg-success">
                                    Available
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    Borrowed
                                </span>
                            @endif

                        </div>

                        <h2 class="mb-1">
                            {{ $book->title }}
                        </h2>

                        <h5 class="text-muted mb-4">
                            {{ $book->author ?: 'Unknown Author' }}
                        </h5>

                        <div class="mb-4">

                            @if($book->book_type == 'ebook')

                                <a
                                    href="{{ route('ebooks.download',$book) }}"
                                    class="btn-main fx-slide btn-line me-2">

                                    <span>Download E-Book</span>

                                </a>

                                <a
                                    href="{{ route('ebooks.read',$book) }}"
                                    class="btn-main fx-slide btn-line">

                                    <span>Read Online</span>

                                </a>

                            @elseif($book->status=='available')

                                <a
                                    href="{{ route('submit.job') }}"
                                    class="btn-main fx-slide btn-line">

                                    <span>Borrow this Book</span>

                                </a>

                            @else

                                <button class="btn btn-secondary" disabled>
                                    Currently Borrowed
                                </button>

                            @endif

                        </div>

                    </div>

                </div>

                <div class="spacer-single"></div>

                <h3>About this Book</h3>

                <p>
                    {{ $book->description ?: 'No description has been provided for this book.' }}
                </p>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">

                <div class="widget p-4 bg-light rounded-1">

                    <h4 class="mb-3">
                        Book Information
                    </h4>

                    <table class="table">

                        <tr>
                            <th>Author</th>
                            <td>{{ $book->author ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Genre</th>
                            <td>{{ $book->genre ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Type</th>
                            <td>{{ ucfirst($book->book_type) }}</td>
                        </tr>

                        <tr>
                            <th>Pages</th>
                            <td>{{ $book->pages ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Language</th>
                            <td>{{ $book->language }}</td>
                        </tr>

                        <tr>
                            <th>Condition</th>
                            <td>{{ ucfirst($book->condition ?: '-') }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>{{ ucfirst($book->status) }}</td>
                        </tr>

                        <tr>
                            <th>ISBN</th>
                            <td>{{ $book->barcode ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Shelf</th>
                            <td>{{ $book->shelf_location ?: '-' }}</td>
                        </tr>

                        <tr>
                            <th>Copies</th>
                            <td>{{ $book->copies }}</td>
                        </tr>

                        <tr>
                            <th>Available</th>
                            <td>{{ $book->available_copies }}</td>
                        </tr>

                        <tr>
                            <th>Owner</th>
                            <td>{{ optional($book->partner)->name ?: 'AlgoSpace Library' }}</td>
                        </tr>

                        <tr>
                            <th>Added By</th>
                            <td>{{ optional($book->addedBy)->name ?: '-' }}</td>
                        </tr>

                        @if($book->book_type=='ebook')

                        <tr>
                            <th>Downloads</th>
                            <td>{{ number_format($book->download_count) }}</td>
                        </tr>

                        <tr>
                            <th>File Size</th>
                            <td>{{ $book->file_size_human ?: '-' }}</td>
                        </tr>

                        @endif

                        <tr>
                            <th>Added</th>
                            <td>{{ $book->created_at->format('d M Y') }}</td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>
</section>
@endsection