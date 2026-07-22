<h2 style="text-align:center">
{{ $course->name }}
</h2>


<h4>Course Overview</h4>

<p>
{{ $course->outline->overview }}
</p>


<h4>Certificate Information</h4>

<p>
{{ $course->outline->certificate_information }}
</p>


<h4>Course Outline</h4>


@foreach($course->outline->items as $item)

<h5>
{{ ucfirst($item->section) }}:
{{ $item->title }}
</h5>

<p>
{{ $item->description }}
</p>

@endforeach



<h4>Course Sessions</h4>


@foreach($course->sessions as $session)

<h5>
Session {{ $session->session_number }}:
{{ $session->title }}
</h5>


<ul>

@foreach($session->topics as $topic)

<li>
<strong>{{ $topic->title }}</strong>
<br>
{{ $topic->description }}
</li>

@endforeach

</ul>


@endforeach