@extends('layouts.app')

@section('content')
<div class="container" style="direction: rtl">

    <h4 class="mb-4">➕ إضافة موظف (ZKBio – Direct DB)</h4>

    {{-- ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('biotime.employees.store') }}">
        @csrf

        <div class="card shadow-sm">
            <div class="card-body row g-3">

                {{-- EMP CODE --}}
                <div class="col-md-4">
                    <label class="form-label">رمز الموظف (EMP CODE)</label>
                    <input type="text"
                           name="emp_code"
                           class="form-control"
                           value="{{ old('emp_code') }}"
                           required>
                </div>

                {{-- FIRST NAME --}}
                <div class="col-md-4">
                    <label class="form-label">الاسم</label>
                    <input type="text"
                           name="first_name"
                           class="form-control"
                           value="{{ old('first_name') }}">
                </div>

                {{-- LAST NAME --}}
                <div class="col-md-4">
                    <label class="form-label">اللقب</label>
                    <input type="text"
                           name="last_name"
                           class="form-control"
                           value="{{ old('last_name') }}">
                </div>

                {{-- DEPARTMENT --}}
                <div class="col-md-4">
                    <label class="form-label">القسم</label>
                    <select name="department" class="form-select" required>
                        <option value="">-- اختر القسم --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->dept_name ?? $dept->alias }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- AREAS --}}
                <div class="col-md-4">
                    <label class="form-label">المناطق (Area)</label>
                    <select name="area[]" class="form-select" multiple required>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}"
                                {{ collect(old('area'))->contains($area->id) ? 'selected' : '' }}>
                                {{ $area->area_name ?? $area->alias }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">يمكن اختيار أكثر من منطقة</small>
                </div>

           {{-- STATUS (BOOLEAN) --}}
<div class="col-md-4">
    <label class="form-label">الحالة</label>
    <select name="is_active" class="form-select" required>
        <option value="1"
            {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
            مفعل
        </option>

        <option value="0"
            {{ old('is_active') == '0' ? 'selected' : '' }}>
            معطل
        </option>
    </select>
</div>


                {{-- COMPANY (hidden or select if needed) --}}
                <input type="hidden" name="company" value="1">

            </div>

            <div class="card-footer text-end">
                <button class="btn btn-success">
                    💾 حفظ
                </button>
                <a href="{{ route('biotime.employees.index') }}" class="btn btn-secondary">
                    ↩️ رجوع
                </a>
            </div>
        </div>

    </form>
</div>
@endsection
