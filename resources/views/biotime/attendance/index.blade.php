@extends('layouts.app')

@section('title','سجلات الحضور')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">📋 سجلات الحضور</h4>
</div>

{{-- Filtres --}}
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text"
               name="emp_code"
               value="{{ request('emp_code') }}"
               class="form-control"
               placeholder="رمز الموظف">
    </div>

    <div class="col-md-3">
        <input type="date"
               name="date"
               value="{{ request('date') }}"
               class="form-control">
    </div>

    <div class="col-md-3">
        <button class="btn btn-primary">
            🔍 بحث
        </button>
    </div>
</form>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>الموظف</th>
                    <th>التاريخ / الوقت</th>
                    <th>النوع</th>
                    <th>الجهاز</th>
                    <th>المنطقة</th>
                    <th>الحرارة</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $i => $log)
                <tr>
                    <td>{{ $logs->firstItem() + $i }}</td>

                    @php $person = $persons->get((int) $log->emp_code); @endphp
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if(!empty($person['person_photo']))
                                <img src="{{ $person['person_photo'] }}" alt=""
                                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                            @endif
                            <div>
                                {{ $log->emp_code }}
                                <div class="small text-muted">
                                    @if($person)
                                        {{ $person['firstname'] ?? '' }} {{ $person['lastname'] ?? '' }}
                                    @else
                                        {{ $log->employee->first_name ?? '' }}
                                        {{ $log->employee->last_name ?? '' }}
                                    @endif
                                </div>
                                @if($person)
                                    <span class="badge {{ ($person['access_status'] ?? '') === 'autorisé' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ($person['access_status'] ?? '') === 'autorisé' ? 'مصرح' : 'غير مصرح' }}
                                    </span>
                                    <span class="small text-muted">
                                        {{ $person['start_date'] ?? '—' }} → {{ $person['end_date'] ?? '—' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($log->punch_time)->format('d/m/Y H:i') }}
                    </td>

                    <td>
                        <span class="badge {{ $log->punch_state == 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $log->punch_type_label }}
                        </span>
                    </td>

                    <td>
                        {{ $log->terminal->alias ?? $log->terminal_alias }}
                    </td>

                    <td>{{ $log->area_alias ?? '-' }}</td>

                    <td>
                        {{ $log->temperature ? $log->temperature.' °C' : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        لا توجد سجلات
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $logs->links() }}
</div>

@endsection
