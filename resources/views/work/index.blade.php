@extends('layouts.branch')

@section('page-title', 'Our Work')

@section('content')
<section>
    <div class="container">
        <div class="row g-4">
            <!-- service item begin -->
            @foreach($projects as $project)

            <div class="col-lg-4 col-sm-6">
                <div class="hover rounded-1 overflow-hidden relative text-light text-center wow fadeInRight" data-wow-delay=".0s">
                    <img src="{{ asset('storage/' . $project->cover_image ?? 'templates/marketing_site/images/projects/1.webp') }}" class="hover-scale-1-1 w-100" alt="">
                    <div class="abs w-100 px-4 hover-op-1 z-4 hover-mt-40 abs-centered">
                        <div class="mb-3">{{$project->description}}</div>
                        <a class="btn-line" href="{{ route('work.show', $project->id) }}">
                            View Details
                        </a>
                    </div>
                    <div class="abs bg-color z-2 top-0 w-100 h-100 hover-op-1"></div>
                    <div class="abs z-2 bottom-0 mb-3 w-100 text-center hover-op-0">
                        <h4 class="fs-20 mb-3">{{$project->title}}</h4>
                    </div>
                    <div class="gradient-edge-bottom abs w-100 h-60 bottom-0"></div>
                </div>
            </div>
            <!-- service item end -->
            @endforeach


        </div>
    </div>
</section>
@endsection