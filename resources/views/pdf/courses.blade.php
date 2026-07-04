<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: DejaVu Sans, sans-serif; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; font-size: 12px; }
    th { background: #f5f5f5; }
  </style>
</head>
<body>

<h3>Saturday AlgoSpace Cyber Training Courses</h3>

<table>
  <thead>
    <tr>
      <th>Course</th>
      <th>Tier</th>
      <th>Schedule</th>
      <th>Duration</th>
      <th>Hours</th>
      <th>Price (KES)</th>
    </tr>
  </thead>
  <tbody>
    @foreach($courses as $c)
      <tr>
        <td>{{ $c->name }}</td>
        <td>{{ ucfirst($c->tier) }}</td>
        <td>{{ ucfirst($c->schedule_type) }}</td>
        <td>{{ $c->duration_units }} Saturdays</td>
        <td>{{ $c->session_hours }}</td>
        <td>{{ number_format($c->price) }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

</body>
</html>