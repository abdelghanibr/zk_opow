@extends('layouts.tv')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@700;800;900&display=swap');

/* ===== RESET ===== */
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: radial-gradient(circle at top, #0b2a3c, #040b14);
    font-family: 'Cairo', sans-serif;
    color: #fff;
}

.hidden { display: none !important; }

/* ===== HEADER ===== */
.header {
    text-align: center;
    padding: 40px 20px 30px;
}





.header .title {
    font-family: 'Noto Kufi Arabic', sans-serif;
    font-size: 2.8rem;
    font-weight: 900;
    letter-spacing: 0;
}

.header .state,
.header .ministry {
    font-family: 'Noto Kufi Arabic', sans-serif;
    font-weight: 700;
}


.header .subtitle {
    font-size: 1.3rem;
    opacity: .85;
}

.header .complex {
    margin-top: 18px;
    font-size: 1.6rem;
    font-weight: 800;
    color: #facc15;
}

/* ===== CENTER ===== */
.center {
    height: calc(100vh - 260px);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===== WELCOME ===== */
.welcome {
    text-align: center;
}

.welcome h1 {
    font-size: 3.8rem;
    font-weight: 900;
}

.welcome p {
    margin-top: 20px;
    font-size: 2.4rem;
    font-weight: 700;
    opacity: .95;
}

/* ===== PERSON CARD ===== */
.person {
    text-align: center;
}

.photo {
    width: 280px;
    height: 280px;
    border-radius: 50%;
    border: 10px solid #22c55e;
    object-fit: cover;
    margin-bottom: 25px;
}

.person.denied .photo {
    border-color: #ef4444;
}

.name {
    font-size: 3rem;
    font-weight: 900;
}

.status {
    font-size: 2.4rem;
    margin: 10px 0;
}

.authorized { color: #22c55e; }
.denied { color: #ef4444; }

.info {
    font-size: 1.5rem;
    margin-top: 8px;
    opacity: .95;
}

/* ===== SLOTS ===== */
.slots {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,.15);
}

.slot {
    font-size: 1.4rem;
    margin-top: 6px;
}
</style>

{{-- ===== HEADER ===== --}}
<div class="header">
    <div class="state">الجمهورية الجزائرية الديمقراطية الشعبية</div>
    <div class="ministry">وزارة الرياضة</div>

    <div class="title">ديوان المركب المتعدد الرياضات لولاية ميلة</div>

    <div class="subtitle">
        منصة إلكترونية حديثة لتنظيم الأنشطة الرياضية، متابعة المنخرطين، وحجز المرافق عن بعد
    </div>

    <div class="complex">
        🏟 {{ $complexName ?? 'المركب الرياضي' }}
    </div>
</div>

{{-- ===== CENTER ===== --}}
<div class="center">

    {{-- WELCOME --}}
    <div id="welcome" class="welcome">
        <h1>👋 مرحبًا بكم</h1>
        <p>يرجى تمرير البطاقة أو التعرف على الوجه</p>
    </div>

    {{-- PERSON --}}
    <div id="person" class="person hidden">
        <img id="photo" class="photo">

        <div id="name" class="name"></div>
        <div id="status" class="status"></div>

        <div id="punchDate" class="info"></div>
        <div id="validity" class="info"></div>

        <div id="slots" class="slots"></div>
    </div>

</div>

<script>
const daysMap = {
    0:'الأحد',1:'الإثنين',2:'الثلاثاء',
    3:'الأربعاء',4:'الخميس',5:'الجمعة',6:'السبت'
};

const SHOW_DURATION = 10;
let lastShownAt = null;
let lastPunch = null;

async function loadTV() {
    const res = await fetch('/tv/last-punch/json', { cache: 'no-store' });
    const data = await res.json();
    const now = Date.now();

    if (data.mode === 'welcome') {
        showWelcome();
        return;
    }

    const punchTime = new Date(data.punch.time).getTime();

    if (!lastPunch || punchTime !== lastPunch) {
        lastPunch = punchTime;
        lastShownAt = now;
        showPerson(data);
        return;
    }

    if ((now - lastShownAt) / 1000 > SHOW_DURATION) {
        showWelcome();
    }
}

function showWelcome() {
    document.getElementById('welcome').classList.remove('hidden');
    document.getElementById('person').classList.add('hidden');
}

function showPerson(data) {
    document.getElementById('welcome').classList.add('hidden');
    document.getElementById('person').classList.remove('hidden');

    const ok = data.person.access === 'autorisé';
    const p = document.getElementById('person');
    p.className = 'person ' + (ok ? 'authorized' : 'denied');

    document.getElementById('photo').src = data.person.photo;
    document.getElementById('name').innerText =
        data.person.firstname + ' ' + data.person.lastname;

    document.getElementById('status').innerText =
        ok ? '✔ مصرح' : '✖ غير مصرح';

    const d = new Date(data.punch.time);
    document.getElementById('punchDate').innerText =
        '⏱ ' + d.toLocaleDateString('fr-FR') + ' ' + d.toLocaleTimeString('fr-FR');

    document.getElementById('validity').innerText =
        '📅 الصلاحية: ' +
        (data.person.start_date ?? '—') + ' → ' +
        (data.person.end_date ?? '—');

    let html = '';
    if (!data.person.time_slots || data.person.time_slots.length === 0) {
        html = '🕒 لا توجد حصص مبرمجة';
    } else {
        data.person.time_slots.forEach(s => {
            html += `<div class="slot">🕒 ${daysMap[s.day_number]} : ${s.start} → ${s.end}</div>`;
        });
    }
    document.getElementById('slots').innerHTML = html;
}

setInterval(loadTV, 1000);
loadTV();
</script>

@endsection
