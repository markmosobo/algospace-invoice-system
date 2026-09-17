<!DOCTYPE html>
<html>
<head>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 20px;
      text-transform: uppercase;
    }

    h3 {
      font-size: 28px;
      font-weight: bold;
      text-align: center;
      margin: 0 0 20px 0;
      text-transform: uppercase;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #999;
      padding: 14px 10px;
      font-size: 20px;
      line-height: 1.4;
      text-transform: uppercase;
      vertical-align: middle;
    }

    /* TABLE HEADINGS */
    th {
      background: #f0f0f0;
      font-size: 21px;
      font-weight: bold;
      text-align: center;
    }

    /* TABLE DATA */
    td {
      font-weight: normal;
    }
  </style>
</head>

<body>

<h3>ALGOSPACE CYBER TRAINING COURSES</h3>

<table>
  <thead>
    <tr>
      <th>COURSE</th>
      <th>TIER</th>
      <th>SCHEDULE</th>
      <th>DURATION</th>
      <th>PRICE (KES)</th>
    </tr>
  </thead>

  <tbody>
    @foreach($courses as $c)
      <tr>
        <td>{{ strtoupper($c->name) }}</td>
        <td>{{ strtoupper($c->tier) }}</td>
        <td>{{ strtoupper($c->schedule_type) }}</td>
        <td>{{ $c->duration_units }} SESSIONS<br>({{ $c->session_hours }} HRS each)</td>
        <td>{{ number_format($c->price) }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

</body>
</html>