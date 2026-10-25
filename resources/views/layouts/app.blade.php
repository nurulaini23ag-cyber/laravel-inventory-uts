
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name', 'Inventory & Sales System') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
      max-width: 1250px;
      width: 100%;
      margin: 0 auto;
      padding: 2rem 1.5rem;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      font-weight: 500;
      border-radius: 6px;
      border: 1px solid transparent;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-success { background: #16a34a; color: #fff; }
    .btn-success:hover { background: #15803d; }
    .btn-danger { background: #dc2626; color: #fff; }
    .btn-danger:hover { background: #b91c1c; }
    .btn-secondary { background: #e2e8f0; color: #334155; border-color: #cbd5e1; }
    .btn-secondary:hover { background: #cbd5e1; }
    .btn-warning { background: #f59e0b; color: #fff; }
    .btn-warning:hover { background: #d97706; }
    .card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
      border: 1px solid #e2e8f0;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .table-responsive { width: 100%; overflow-x: auto; }
    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.875rem;
    }
    th, td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #e2e8f0;
    }
    th {
      background: #f1f5f9;
      font-weight: 600;
      color: #475569;
    }
    tr:hover td { background: #f8fafc; }
    .badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .badge-success { background: #dcfce7; color: #166534; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #991b1b; }
    .badge-info { background: #e0f2fe; color: #075985; }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 0.35rem; color: #334155; }
    .form-control, .form-select {
      width: 100%;
      padding: 0.5rem 0.75rem;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 0.875rem;
      color: #1e293b;
      background: #fff;
    }
    .form-control:focus, .form-select:focus {
      border-color: #3b82f6;
      outline: 2px solid #93c5fd;
    }
    .is-invalid { border-color: #ef4444; }
    .invalid-feedback { color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block; }
    .pagination { display: flex; gap: 0.25rem; list-style: none; padding: 0; margin: 1.5rem 0 0; justify-content: center; }
    .pagination li a, .pagination li span {
      padding: 0.5rem 0.75rem;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      color: #334155;
      text-decoration: none;
      font-size: 0.875rem;
    }
    .pagination li.active span { background: #2563eb; color: #fff; border-color: #2563eb; }
    footer {
      background: #ffffff;
      border-top: 1px solid #e2e8f0;
      padding: 1rem 1.5rem;
      text-align: center;
      font-size: 0.8125rem;
      color: #64748b;
    }
  </style>
</head>
<body>
  @include('layouts.navigation')

  <main>
    {{-- Flash Message Alert --}}
    @if(session('success'))
      <x-alert type="success">
        {{ session('success') }}
      </x-alert>
    @endif

    @if(session('error'))
      <x-alert type="error">
        {{ session('error') }}
      </x-alert>
    @endif

    @if($errors->any())
      <x-alert type="error">
        <ul style="margin: 0; padding-left: 1.25rem;">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </x-alert>
    @endif

    @yield('content')
  </main>

  <footer>
    <div style="max-width: 1250px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
      <div>
        <strong>Inventory &amp; Sales Management System</strong> &mdash; Enterprise POS &amp; Stock Control
      </div>
      <div>
        <span>&copy; {{ date('Y') }} All Rights Reserved</span>
      </div>
    </div>
  </footer>
</body>
</html>
