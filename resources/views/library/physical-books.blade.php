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
                    <div class="bg-dark-2 text-light rounded-20 overflow-hidden h-100">

                        {{-- Book Cover --}}
                        <div class="relative">
                            <img
                                src="{{ $book->cover_image ?? asset('templates/marketing_site/images/misc/book-placeholder.webp') }}"
                                class="w-100"
                                alt="{{ $book->title }}"
                                style="height: 320px; object-fit: cover;"
                            >
                        </div>

                        {{-- Book Details --}}
                        <div class="p-30">
                            <div class="bg-color rounded-1 p-0 px-2 d-inline-block mb-2">
                                {{ ucfirst($book->genre ?? 'General') }}
                            </div>

                            <h4 class="mb-1">{{ $book->title }}</h4>
                            <div class="fs-14 mb-2">✍️ {{ $book->author }}</div>

                            <div class="mt-3">
                                <span class="badge bg-success">
                                    {{ ucfirst($book->status) }}
                                </span>
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