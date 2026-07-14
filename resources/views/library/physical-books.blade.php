@extends('layouts.branch')

@section('page-title', 'Physical Books')

@section('content')
<section>
    <div class="container">
        {{-- Borrowing Notice --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-dark-2 text-light rounded-20 p-20 border border-warning">
                    <h5 class="mb-2">📌 Borrowing Information</h5>
                    <p class="mb-1">
                        Physical books can only be borrowed by <strong>registered members</strong>.
                    </p>
                    <ul class="mb-0">
                        <li>One-time registration fee: <strong>KES 50</strong></li>
                        <li>Borrowing fee: <strong>KES 20 per book</strong></li>
                    </ul>
                </div>
            </div>
        </div>        
        <div class="row g-4">

            @forelse ($books as $book)
                <div class="col-lg-4">
                    <div class="bg-dark-2 text-light rounded-20 overflow-hidden h-100 shadow-sm">

                        {{-- Cover --}}
                        <div class="position-relative">

                            <img
                                src="{{ $book->cover_url ?: asset('templates/marketing_site/images/misc/book-placeholder.webp') }}"
                                alt="{{ $book->title }}"
                                class="w-100"
                                style="height:340px;object-fit:cover;"
                            >

                            <span class="badge bg-success position-absolute top-0 end-0 m-3">
                                {{ ucfirst($book->status) }}
                            </span>

                        </div>

                        <div class="p-4">

                            <span class="badge bg-warning text-dark mb-2">
                                {{ $book->genre ?? 'General' }}
                            </span>

                            <h4 class="mb-1">
                                {{ $book->title }}
                            </h4>

                            <p class="mb-3 text-light-50">
                                <i class="fa fa-user me-1"></i>
                                {{ $book->author ?? 'Unknown Author' }}
                            </p>

                            <table class="table table-borderless table-sm text-light mb-3">

                                <tr>
                                    <td width="45%">
                                        <strong>Pages</strong>
                                    </td>

                                    <td>
                                        {{ $book->pages ?: '-' }}
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
                                        <strong>Condition</strong>
                                    </td>

                                    <td>
                                        {{ ucfirst($book->condition ?? '-') }}
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

                            @if(!empty($book->description))
                                <p class="small text-light-50 mb-3">
                                    {{ \Illuminate\Support\Str::limit($book->description, 120) }}
                                </p>
                            @endif

                            <div class="d-grid">

                                @if($book->status == 'available')

                                    <button class="btn btn-success rounded-pill">
                                        Available for Borrowing
                                    </button>

                                @else

                                    <button class="btn btn-secondary rounded-pill" disabled>
                                        Currently Borrowed
                                    </button>

                                @endif

                            </div>

                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p>No physical books available at the moment.</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if ($books->hasPages())
                <div class="col-lg-12 pt-4 text-center">
                    {{ $books->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>
</section>
@endsection