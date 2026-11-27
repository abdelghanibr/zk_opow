<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','ZK Attendance')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root{
            --zk-green:#16a34a;
            --zk-green-dark:#15803d;
            --bg:#f6f7fb;
            --text:#111827;
            --muted:#6b7280;
        }

        body{
            background:var(--bg);
            font-family:'Cairo',sans-serif;
            color:var(--text);
        }

        /* ===== Navbar ===== */
        .navbar{
            box-shadow:0 8px 24px rgba(0,0,0,.04);
        }

        .navbar-brand{
            font-weight:800;
            color:var(--zk-green) !important;
        }

        main.container{
            max-width:1200px;
        }

        /* ===== Cards ===== */
        .card{
            border:0;
            border-radius:16px;
            box-shadow:0 12px 30px rgba(0,0,0,.06);
        }

        /* ===== DataTables ===== */
        table.dataTable th,
        table.dataTable td{
            vertical-align:middle;
            text-align:center;
        }

        div.dataTables_wrapper div.dataTables_filter,
        div.dataTables_wrapper div.dataTables_paginate{
            text-align:left;
        }

        /* ===== Buttons (2026 Style) ===== */
        .btn-zk-primary{
            background:linear-gradient(135deg,var(--zk-green),var(--zk-green-dark));
            color:#fff;
            border:0;
            border-radius:14px;
            padding:6px 16px;
            font-weight:700;
            display:inline-flex;
            align-items:center;
            gap:6px;
            box-shadow:0 6px 16px rgba(22,163,74,.25);
            transition:.25s ease;
        }
        .btn-zk-primary:hover{
            transform:translateY(-1px);
            box-shadow:0 10px 22px rgba(22,163,74,.35);
            color:#fff;
        }

        .btn-zk-danger{
            background:#fff;
            color:#dc2626;
            border:1px solid #dc2626;
            border-radius:14px;
            padding:6px 14px;
            font-weight:700;
            display:inline-flex;
            align-items:center;
            gap:6px;
            transition:.25s ease;
        }
        .btn-zk-danger:hover{
            background:#dc2626;
            color:#fff;
        }

        .btn-zk-ghost{
            background:#f1f5f9;
            color:#334155;
            border-radius:14px;
            padding:6px 14px;
            font-weight:700;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .btn-zk-ghost:hover{
            background:#e2e8f0;
        }

        .zk-user{
            background:#f0fdf4;
            color:#15803d;
            border-radius:999px;
            padding:6px 14px;
            font-weight:700;
            font-size:13px;
            display:flex;
            align-items:center;
            gap:6px;
        }

        .footer{
            text-align:center;
            color:var(--muted);
            font-size:13px;
            margin:40px 0 10px;
        }
    </style>

    @stack('styles')
</head>

<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg bg-white mb-4">
    <div class="container">

        <a class="navbar-brand" href="{{ route('dashboard') }}">
            ⏱️ ZK Attendance
        </a>

        <div class="ms-auto d-flex align-items-center gap-2">

            @php($user = session('auth_user'))

            @if($user)

                <div class="zk-user d-none d-md-flex">
                    <i class="fa-solid fa-user"></i>
                    {{ $user['username'] }}
                </div>

                <a href="{{ route('dashboard') }}" class="btn btn-zk-ghost btn-sm">
                    <i class="fa-solid fa-gauge-high"></i>
                    لوحة التحكم
                </a>

                <a href="{{ route('biotime.attendance.index') }}" class="btn btn-zk-primary btn-sm">
                    <i class="fa-solid fa-fingerprint"></i>
                    الحضور
                </a>
                 <a href="{{ route('biotime.employees.index') }}" class="btn btn-zk-primary btn-sm">
                    <i class="fa-solid fa-fingerprint"></i>
                 فائمة الموظفين
                </a>
                 <a href="{{ route('tv.last.punch') }}" class="btn btn-zk-primary btn-sm">
                    <i class="fa-solid fa-fingerprint"></i>
                واجهة التلفاز
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-zk-danger btn-sm">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        خروج
                    </button>
                </form>

            @endif

        </div>
    </div>
</nav>

{{-- ===== MAIN ===== --}}
<main class="container">
    @yield('content')
</main>

<div class="footer">
    ZK Attendance © {{ date('Y') }} — لوحة تحكم حديثة
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

{{-- Buttons --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

{{-- Export --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

@stack('scripts')

</body>
</html>
