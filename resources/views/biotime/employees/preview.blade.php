@extends('layouts.app')

@section('content')
<div class="container" style="direction: rtl">

    <h4 class="mb-3">👀 معاينة الرياضيين المسجلين في الموقع</h4>
{{-- ===============================
     Messages d'erreurs / succès
   =============================== --}}

{{-- Message succès --}}
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Messages d'erreur --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>⚠️ حدثت الأخطاء التالية:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <p class="text-muted">
        عدد الأشخاص الرياضيين من الموقع البعيد:
        <strong>{{ count($employees) }}</strong>
    </p>


    <form id="importForm" method="POST"
          action="{{ route('biotime.import.execute', $complexId) }}">
        @csrf

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <button type="submit" class="btn btn-success">
                ✅ تأكيد الاستيراد (Importer)
            </button>

            <span id="exportBox"></span>

            <a href="{{ route('biotime.employees.index') }}"
               class="btn btn-secondary">
                ⬅ رجوع
            </a>

            <span class="badge bg-primary fs-6 ms-auto">
                Sélectionnés : <span id="selCount">0</span> / {{ count($employees) }}
            </span>
        </div>
        <table id="empTable" class="table table-bordered table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th class="text-center">
                        <input type="checkbox" id="checkAll" title="Tout sélectionner" checked>
                    </th>
                    <th>#</th>
                     <th>الصورة</th> {{-- ✅ جديد --}}
                    <th>الاسم</th>
                    <th>اللقب</th>
                    <th>الهاتف</th>
                    <th>البريد الإلكتروني</th>
                    <th>الفترة</th>
                    <th>الحصص الأسبوعية</th>
                    <th>الحالة</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $i => $emp)

                    @php
                        $slots = !empty($emp['time_slots'])
                            ? json_decode($emp['time_slots'], true)
                            : [];

                        $isAuthorized = ($emp['access_status'] ?? 'non_autorisé') === 'autorisé';
                    @endphp

                    <tr class="{{ $isAuthorized ? 'table-success' : 'table-danger' }}">
                        <td class="text-center">
                            <input type="checkbox"
                                   class="emp-check"
                                   value="{{ json_encode($emp, JSON_UNESCAPED_UNICODE) }}"
                                   checked>
                        </td>
                        <td>{{ $emp['id'] }}</td>

                       

  
  <td class="text-center">
    @if(!empty($emp['person_photo']))

        <div class="d-flex flex-column align-items-center gap-1">

            {{-- عرض الصورة --}}
            <img
                src="{{ $emp['person_photo'] }}"
                alt="photo"
                class="rounded-circle border"
                width="45"
                height="45"
                style="object-fit: cover;"
            >

            {{-- زر التحميل --}}
            <a href="{{ route('employee.photo.download', [
                    'id'    => $emp['id'],
                    'photo' => $emp['person_photo']
                ]) }}"
               class="btn btn-outline-primary btn-xs">
                ⬇ تحميل
            </a>

        </div>

    @else
        {{-- صورة افتراضية + بدون زر تحميل --}}
        <img
            src="{{ asset('images/avatar.png') }}"
            alt="no photo"
            class="rounded-circle border"
            width="45"
            height="45"
            style="object-fit: cover;"
        >
    @endif




</td>



                        <td>{{ $emp['firstname'] ?? '-' }}</td>
                        <td>{{ $emp['lastname'] ?? '-' }}</td>
                        <td>{{ $emp['person_phone'] ?? '-' }}</td>
                        <td>{{ $emp['email'] ?? '-' }}</td>

                        {{-- 📅 start_date → end_date --}}
                        <td>
                            @if(!empty($emp['start_date']) && !empty($emp['end_date']))
                                <span class="badge bg-info">
                                    {{ $emp['start_date'] }}
                                </span>
                                →
                                <span class="badge bg-info">
                                    {{ $emp['end_date'] }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- 🕒 Time Slots --}}
                        <td>
                            @if(count($slots))
                                <ul class="mb-0 ps-3">
                                    @foreach($slots as $slot)
                                        <li>
                                            <strong>
                                                {{ dayName($slot['day_number'] ?? -1) }}
                                            </strong>
                                            :
                                            {{ $slot['start'] ?? '?' }}
                                            →
                                            {{ $slot['end'] ?? '?' }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- 🔐 Access Status --}}
                        <td class="text-center">
                            @if($isAuthorized)
                                <span class="badge bg-success">
                                    مفعل
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    معطل
                                </span>
                            @endif
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>

    </form>

</div>

<div class="modal fade" id="progressModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pgTitle">Import en cours…</h5>
      </div>
      <div class="modal-body">
        <div class="progress mb-2" style="height:24px">
          <div id="pgBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
        </div>
        <div class="d-flex gap-3 mb-3 small">
          <span>Traités : <strong id="pgDone">0</strong> / <strong id="pgTotal">0</strong></span>
          <span class="text-success">Créés : <strong id="pgNew">0</strong></span>
          <span class="text-primary">Mis à jour : <strong id="pgUpd">0</strong></span>
          <span class="text-danger">Erreurs : <strong id="pgErr">0</strong></span>
        </div>
        <ul id="pgLog" class="list-group" style="max-height:340px;overflow:auto"></ul>
      </div>
      <div class="modal-footer">
        <button type="button" id="pgStop" class="btn btn-outline-danger">⏹ Arrêter</button>
        <a id="pgClose" href="{{ route('biotime.employees.index') }}" class="btn btn-success d-none">Voir les employés</a>
        <button type="button" id="pgBack" class="btn btn-secondary d-none" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
$(function () {
    const table = $('#empTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 50, 100], [10, 50, 100]],
        order: [[1, 'desc']],
        columnDefs: [{ orderable: false, targets: [0, 2] }],
        language: {
            search: 'Rechercher :',
            lengthMenu: 'Afficher _MENU_ par page',
            info: '_START_–_END_ sur _TOTAL_',
            infoEmpty: 'Aucun employé',
            infoFiltered: '(filtré sur _MAX_)',
            zeroRecords: 'Aucun résultat',
            emptyTable: 'Aucune donnée',
            paginate: { previous: '‹', next: '›' }
        }
    });

    // Export Excel (toutes les lignes filtrées, toutes pages)
    new $.fn.dataTable.Buttons(table, {
        buttons: [{
            extend: 'excelHtml5',
            text: '📊 Exporter Excel',
            className: 'btn btn-outline-success',
            title: null,
            filename: 'employes_complexe_{{ $complexId }}',
            exportOptions: {
                columns: [1, 3, 4, 5, 6, 7, 8, 9],
                modifier: { search: 'applied', order: 'applied' },
                format: { body: (data) => $('<div>').html(data).text().replace(/\s+/g, ' ').trim() }
            }
        }]
    });
    table.buttons().container().appendTo('#exportBox');

    const $all = $('#checkAll');

    // Les lignes hors page sont retirées du DOM : on passe par l'API DataTables.
    const checked = () => table.$('.emp-check:checked');

    function refresh() {
        const total = table.$('.emp-check').length;
        const sel   = checked().length;
        $('#selCount').text(sel);
        $all.prop('checked', total > 0 && sel === total)
            .prop('indeterminate', sel > 0 && sel < total);
    }

    // Tout cocher / décocher : uniquement les lignes filtrées par la recherche
    $all.on('change', function () {
        const state = this.checked;
        $(table.rows({ search: 'applied' }).nodes())
            .find('.emp-check').prop('checked', state);
        refresh();
    });

    $('#empTable').on('change', '.emp-check', refresh);

    const importUrl = "{{ route('biotime.import.one', $complexId) }}";
    const csrf      = "{{ csrf_token() }}";
    let stop = false;

    function esc(s) { return $('<div>').text(s ?? '').html(); }

    async function runImport(items) {
        stop = false;
        let done = 0, nNew = 0, nUpd = 0, nErr = 0;
        const total = items.length;

        $('#pgTotal').text(total);
        $('#pgLog').empty();
        $('#pgTitle').text('Import en cours…');
        $('#pgBar').addClass('progress-bar-animated bg-success').css('width', '0%').text('0%');
        $('#pgStop').removeClass('d-none').prop('disabled', false);
        $('#pgClose, #pgBack').addClass('d-none');
        bootstrap.Modal.getOrCreateInstance('#progressModal').show();

        for (const raw of items) {
            if (stop) break;
            let li;
            try {
                const res = await fetch(importUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ employee: raw })
                });
                const d = await res.json();

                if (d.ok) {
                    d.kind === 'inserted' ? nNew++ : nUpd++;
                    li = `<li class="list-group-item list-group-item-${d.kind === 'inserted' ? 'success' : 'primary'} py-1">
                            ${d.kind === 'inserted' ? '✅ Créé' : '🔄 Mis à jour'} — #${esc(d.id)} ${esc(d.name)}</li>`;
                } else {
                    nErr++;
                    li = `<li class="list-group-item list-group-item-danger py-1">
                            ❌ #${esc(d.id)} ${esc(d.name)} — ${esc(d.error || 'Erreur')}</li>`;
                }
            } catch (err) {
                nErr++;
                li = `<li class="list-group-item list-group-item-danger py-1">❌ ${esc(err.message)}</li>`;
            }

            done++;
            const pct = Math.round(done / total * 100);
            $('#pgBar').css('width', pct + '%').text(pct + '%');
            $('#pgDone').text(done); $('#pgNew').text(nNew); $('#pgUpd').text(nUpd); $('#pgErr').text(nErr);
            const $log = $('#pgLog').append(li);
            $log.scrollTop($log[0].scrollHeight);
        }

        $('#pgBar').removeClass('progress-bar-animated');
        if (nErr) $('#pgBar').removeClass('bg-success').addClass('bg-warning');
        $('#pgTitle').text(stop ? '⏹ Import arrêté' : (nErr ? '⚠️ Import terminé avec erreurs' : '✅ Import terminé'));
        $('#pgStop').addClass('d-none');
        $('#pgClose, #pgBack').removeClass('d-none');
    }

    $('#pgStop').on('click', function () { stop = true; $(this).prop('disabled', true); });

    $('#importForm').on('submit', function (e) {
        e.preventDefault();
        const sel = checked();

        if (sel.length === 0) {
            alert('Sélectionnez au moins un employé à importer.');
            return;
        }
        if (!confirm('Importer ' + sel.length + ' employé(s) dans ZKBio ?')) return;

        runImport(sel.map(function () { return this.value; }).get());
    });
    refresh();
});
</script>
@endpush
@endsection
