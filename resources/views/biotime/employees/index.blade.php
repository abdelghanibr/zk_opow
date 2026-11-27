@extends('layouts.app')

@section('title','Liste des employés')

@section('content')
<div class="container" style="direction: rtl">

    <h4 class="mb-3">👥 قائمة الرياضيين (ZKBio – Direct DB)</h4>

    {{-- رسالة خطأ API --}}
    @if ($errors->has('api'))
        <div class="alert alert-danger">
            {{ $errors->first('api') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">📋 قائمة الرياضيين</h5>

                <div class="d-flex gap-2">

                    <a href="{{ route('biotime.employees.create') }}"
                       class="btn btn-success btn-sm">
                        ➕ رياضي جديد
                    </a>

                    <a href="{{ route('biotime.employees.preview', config('biotime.complex_id')) }}"
                       class="btn btn-secondary btn-sm">
                        👀 معاينة الرياضيين من الموقع
                    </a>

                </div>
            </div>

            <table id="employeesTable"
                   class="table table-bordered table-hover align-middle text-center w-100">

                <thead class="table-light">
                    <tr>
                        <th>EMP CODE</th>
                        <th>الاسم</th>
                        <th>اللقب</th>
                        <th>القسم</th>
                        <th>الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>الحالة</th>
                        <th style="width:140px">الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($employees as $emp)
                    <tr>
                        <td>{{ $emp->emp_code }}</td>
                        <td>{{ $emp->first_name ?? '-' }}</td>
                        <td>{{ $emp->last_name ?? '-' }}</td>
                        <td>{{ $emp->department_name ?? '-' }}</td>
                        <td>{{ $emp->mobile ?? '-' }}</td>
                        <td>{{ $emp->email ?? '-' }}</td>

                        <td>
                            @if($emp->status === 0)
                                <span class="badge bg-success">مفعل</span>
                            @else
                                <span class="badge bg-secondary">معطل</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1">

                                <a href="{{ route('biotime.employees.edit', $emp->id) }}"
                                   class="btn btn-warning btn-sm"
                                   title="تعديل">
                                    ✏️
                                </a>

                              {{-- 🔒 Désactiver --}}
<form method="POST"
      action="{{ route('biotime.employees.disable', $emp->emp_code) }}"
      onsubmit="return confirm('⚠️ هل أنت متأكد من تعطيل هذا الموظف؟')"
      class="d-inline">
    @csrf
    @method('PUT')

    <button class="btn btn-warning btn-sm" title="تعطيل">
        ⛔
    </button>
</form>

{{-- 🗑️ Supprimer définitivement --}}
<form method="POST"
      action="{{ route('biotime.employees.destroy', $emp->emp_code) }}"
      onsubmit="return confirm('❌ هذا الحذف نهائي. هل أنت متأكد؟')"
      class="d-inline">
    @csrf
    @method('DELETE')

    <button class="btn btn-danger btn-sm" title="حذف نهائي">
        🗑️
    </button>
</form>

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
    </div>
{{-- ❌ Messages d'erreur --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>⚠️ {{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
{{-- ✅ Message de succès --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

    {{-- =========================
         📊 Rapport d'importation
       ========================= --}}
    @isset($report)
        <div class="card mt-4">
            <div class="card-body">

                <h4 class="mb-3">📊 تقرير الاستيراد</h4>

                <div class="alert alert-success">
                    🟢 تمت الإضافة : {{ count($report['inserted']) }}
                </div>

                <div class="alert alert-info">
                    🔵 تم التحديث : {{ count($report['updated']) }}
                </div>

                @if(count($report['inserted']) > 0)
                    <h5>🟢 موظفون جدد</h5>
                    <ul>
                        @foreach($report['inserted'] as $emp)
                            <li>{{ $emp['firstname'] }} {{ $emp['lastname'] }}</li>
                        @endforeach
                    </ul>
                @endif

                @if(count($report['updated']) > 0)
                    <h5>🔵 موظفون تم تحديثهم</h5>
                    <ul>
                        @foreach($report['updated'] as $emp)
                            <li>{{ $emp['firstname'] }} {{ $emp['lastname'] }}</li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    @endisset

</div>
@endsection


@push('scripts')
<script>
$(document).ready(function () {

    $('#employeesTable').DataTable({

        responsive: true,
        autoWidth: false,
        processing: true,

        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
        },

        dom: 'Bfrtip',

        buttons: [
            {
                extend: 'excelHtml5',
                text: '📊 Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                text: '📄 CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '📕 PDF',
                orientation: 'landscape',
                pageSize: 'A4',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '🖨️ Print',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],

        order: [[0, 'desc']],

        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });

});
</script>
@endpush
