@extends('layouts.branch')

@section('page-title')
    {{ ucfirst($course->tier) }} Course — {{ $course->name }}
@endsection

@php
$tierContent = [
    'basic' => [
        'intro' => 'This course introduces learners to essential computer concepts needed for everyday digital tasks and office environments.',
        'points' => [
            ['title' => 'Computer Basics', 'desc' => 'Learn how computers work, including hardware, software, and operating systems.'],
            ['title' => 'Keyboard & Mouse Skills', 'desc' => 'Build speed and confidence using common input devices.'],
            ['title' => 'File Management', 'desc' => 'Understand folders, files, saving, and organizing documents.'],
            ['title' => 'Internet Basics', 'desc' => 'Learn safe browsing, email usage, and online research skills.'],
            ['title' => 'Introduction to Office Tools', 'desc' => 'Get familiar with Word, Excel, and basic document creation.'],
            ['title' => 'Digital Safety', 'desc' => 'Understand basic cybersecurity and safe computer practices.'],
            ['title' => 'Confidence Building', 'desc' => 'Gain confidence to work with computers independently.'],
        ],
        'tags' => ['Computer Basics', 'Digital Literacy', 'Beginners', 'Office Skills'],
    ],

    'practical' => [
        'intro' => 'This hands-on course focuses on real-world office and cyber operations required in workplaces and business centers.',
        'points' => [
            ['title' => 'Advanced Word Processing', 'desc' => 'Create professional documents, letters, and reports.'],
            ['title' => 'Excel for Daily Use', 'desc' => 'Perform calculations, tables, and basic data analysis.'],
            ['title' => 'Cyber Operations', 'desc' => 'Learn printing, scanning, KRA, NTSA, and online services.'],
            ['title' => 'Customer Handling', 'desc' => 'Understand client service and workflow efficiency.'],
            ['title' => 'Internet Applications', 'desc' => 'Use online platforms for work and business tasks.'],
            ['title' => 'File & Device Management', 'desc' => 'Handle flash disks, printers, and backups.'],
            ['title' => 'Workplace Readiness', 'desc' => 'Prepare for real office or cyber job roles.'],
        ],
        'tags' => ['Office Skills', 'Cyber Cafe', 'Practical Training', 'Work Skills'],
    ],

    'refresher' => [
        'intro' => 'Designed for individuals who already have computer knowledge but need an update on modern tools and workflows.',
        'points' => [
            ['title' => 'Updated Office Tools', 'desc' => 'Learn the latest versions of Word, Excel, and PowerPoint.'],
            ['title' => 'Speed & Accuracy', 'desc' => 'Improve productivity and efficiency.'],
            ['title' => 'Modern Internet Tools', 'desc' => 'Use cloud tools and collaboration platforms.'],
            ['title' => 'Cyber Safety Updates', 'desc' => 'Stay informed about new digital threats and protection.'],
            ['title' => 'Professional Standards', 'desc' => 'Adopt modern workplace best practices.'],
            ['title' => 'Task Automation', 'desc' => 'Learn shortcuts and workflow optimizations.'],
            ['title' => 'Confidence Refresh', 'desc' => 'Rebuild confidence with current technology.'],
        ],
        'tags' => ['Skills Update', 'Office Refresh', 'Productivity'],
    ],

    'coding' => [
        'intro' => 'This course introduces learners to programming concepts, logic building, and software development fundamentals.',
        'points' => [
            ['title' => 'Programming Basics', 'desc' => 'Understand how code works and how programs are structured.'],
            ['title' => 'Logic & Problem Solving', 'desc' => 'Develop analytical thinking skills.'],
            ['title' => 'Web Technologies', 'desc' => 'Learn HTML, CSS, and introductory JavaScript.'],
            ['title' => 'Backend Concepts', 'desc' => 'Understand how servers and databases work.'],
            ['title' => 'Version Control', 'desc' => 'Learn how developers manage code changes.'],
            ['title' => 'Project Building', 'desc' => 'Create small real-world applications.'],
            ['title' => 'Career Pathways', 'desc' => 'Understand opportunities in software development.'],
        ],
        'tags' => ['Programming', 'Coding', 'Web Development', 'Logic'],
    ],
];

$content = $tierContent[$course->tier] ?? null;
@endphp

@section('content')
<section>
    <div class="container">
        <div class="row gx-5">
            <div class="col-lg-8">
                <div class="blog-read">

                    <p>{{ $content['intro'] }}</p>

                    <ol class="ol-style-1">
                    @foreach ($content['points'] as $point)
                        <li>
                            <h4>{{ $point['title'] }}</h4>
                            <p>{{ $point['desc'] }}</p>
                        </li>
                    @endforeach
                    </ol>

                    <img
                        src="{{ asset('templates/marketing_site/images/' . $course->tier_image) }}"
                        class="w-100 rounded-1 mb-4"
                        alt="{{ $course->name }}"
                    >
                </div>

                <div class="spacer-single"></div>

                <!--Comments to go here in the future-->    
            </div>

            <div class="col-lg-4">
                <div class="widget mb-4 p-4 bg-light rounded-1 text-center">
                    <h4 class="mb-2">Enroll in this Course</h4>

                    <div class="fs-14 mb-2">
                        💰 <strong>KES {{ number_format($course->price) }}</strong><br>
                        @php
                            $units = $course->duration_units;

                            if ($units == 0.5) {
                                $durationLabel = '½ Saturday';
                            } elseif ($units == 1) {
                                $durationLabel = '1 Saturday';
                            } else {
                                $durationLabel = $units . ' Saturdays';
                            }
                        @endphp
                        📅 {{ $durationLabel ?? 'Saturdays' }}
                    </div>

                    <a
                        href="{{ route('training.enroll', $course->id) }}"
                        class="btn btn-primary w-100"
                    >
                        Enroll Now
                    </a>

                    <small class="d-block mt-2 text-muted">
                        Limited slots available
                    </small>
                </div>
                <div class="widget widget-post">
                <h4>Related Courses</h4>
                <ul class="de-bloglist-type-1">
                @foreach ($relatedCourses as $related)
                    <li>
                        <div class="d-image">
                        <img src="{{ asset('templates/marketing_site/images/' . $related->tier_image) }}">                        </div>
                        <div class="d-content">
                            <a href="{{ route('training-courses.show', $related->id) }}">
                                <h4>{{ $related->name }}</h4>
                            </a>
                            <div class="d-date">
                                {{ ucfirst($related->tier) }} Course
                            </div>
                        </div>
                    </li>
                @endforeach
                </ul>
                </div>

                <div class="widget widget_tags">
                <h4>Popular Tags</h4>
                <ul>
                @foreach ($content['tags'] as $tag)
                    <li><a href="#">{{ $tag }}</a></li>
                @endforeach
                </ul>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection