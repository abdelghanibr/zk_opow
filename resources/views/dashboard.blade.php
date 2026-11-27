@extends('layouts.app')

@section('content')

<style>
    body {
        background: #f6f7fb;
    }

    /* ====== Cards ====== */
    .card-modern {
        border: 0;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 12px 30px rgba(0,0,0,.06);
        transition: all .3s ease;
    }

    .card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(0,0,0,.08);
    }

    /* ====== KPI ====== */
    .kpi-value {
        font-size: 32px;
        font-weight: 800;
        color: #111827;
    }

    .kpi-label {
        color: #6b7280;
        font-size: 14px;
    }

    /* ====== Header ====== */
    .page-title {
        font-weight: 800;
        color: #111827;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 14px;
    }

    /* ====== Table ====== */
    .table thead th {
        background: #f9fafb;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
    }

    .table td {
        vertical-align: middle;
    }

    /* ====== Mobile ====== */
    @media (max-width: 768px) {
        .kpi-value {
            font-size: 26px;
        }
    }
</style>

{{-- ====== Header ====== --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

    <div>
        <h3 class="page-title mb-1">📊 لوحة التحكم</h3>
        <div class="page-subtitle">
            متابعة الأجهزة، الموظفين، وحركات الحضور والانصراف
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="#" class="btn btn-outline-primary btn-sm">
            🔄 مزامنة المستخدمين
        </a>
        <a href="#" class="btn btn-outline-success btn-sm">
            ⏱️ مزامنة الحضور
        </a>
    </div>

</div>

{{-- ====== KPIs ====== --}}
<div class="row g-3 mb-4">

    <div class="col-md-4 col-12">
        <div class="card-modern p-4">
            <div class="kpi-label">الأجهزة</div>
            <div class="kpi-value">{{ $devicesCount }}</div>
            <div class="kpi-label">أجهزة مسجلة</div>
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="card-modern p-4">
            <div class="kpi-label">الموظفون</div>
            <div class="kpi-value">{{ $usersCount }}</div>
            <div class="kpi-label">موظفون نشطون</div>
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="card-modern p-4">
            <div class="kpi-label">اليوم</div>
            <div class="kpi-value">{{ $logsToday }}</div>
            <div class="kpi-label">عدد عمليات الحضور</div>
        </div>
    </div>

</div>

{{-- ====== Latest Logs ====== --}}
<div class="card-modern p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">🕒 آخر عمليات الحضور</h5>
        <span class="text-muted small">آخر 10 عمليات</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">

            <thead>
                <tr>
                    <th>#</th>
                    <th>الموظف</th>
                    <th>التاريخ والوقت</th>
                    <th>الجهاز</th>
                </tr>
            </thead>

            <tbody>
            @forelse($latestLogs as $i => $log)
                <tr>
                    <td>{{ $i + 1 }}</td>

                    <td>
                        <strong>{{ $log->employee->emp_code ?? '-' }}</strong>
                        <div class="small text-muted">
                            {{ $log->employee->first_name ?? '' }}
                            {{ $log->employee->last_name ?? '' }}
                        </div>
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($log->punch_time)->format('d/m/Y H:i') }}
                    </td>

                    <td>
                        {{ $log->terminal->alias ?? 'جهاز #' . $log->terminal_id }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        لا توجد عمليات حضور حالياً
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

</div>

@endsection
