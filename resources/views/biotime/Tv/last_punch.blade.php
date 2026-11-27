@extends('layouts.tv')

@section('content')

{{-- ===== FONTS ===== --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@500;700;900&family=Cairo:wght@400;600;700;900&display=swap');

/* ===== TOKENS ===== */
:root {
    --bg-0: #040b14;
    --bg-1: #0b2a3c;
    --glass: rgba(255,255,255,.06);
    --glass-border: rgba(255,255,255,.12);
    --text: #f1f5f9;
    --muted: rgba(241,245,249,.65);
    --ok: #22c55e;
    --ok-glow: rgba(34,197,94,.35);
    --ko: #ef4444;
    --ko-glow: rgba(239,68,68,.35);
    --accent: #4fd1c5;
    --gold: #facc15;
}

* { box-sizing: border-box; }

html, body { height: 100%; }

body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    color: var(--text);
    font-family: 'Cairo', 'Segoe UI', sans-serif;
    background:
        radial-gradient(ellipse 80% 60% at 50% -10%, rgba(79,209,197,.18), transparent 60%),
        radial-gradient(ellipse 60% 50% at 100% 100%, rgba(250,204,21,.07), transparent 60%),
        linear-gradient(160deg, var(--bg-1), var(--bg-0) 70%);
}

.hidden { display: none !important; }

/* ===== HEADER ===== */
.header {
    flex: 0 0 auto;
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 3fr) minmax(0, 1fr);
    align-items: center;
    gap: clamp(8px, 2vw, 32px);
    padding: clamp(10px, 1.6vh, 22px) clamp(16px, 4vw, 80px);
    background: linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,0));
    border-bottom: 1px solid var(--glass-border);
    backdrop-filter: blur(10px);
}

.header-content { text-align: center; }

.header .state,
.header .ministry {
    font-family: 'Noto Kufi Arabic', sans-serif;
    font-size: clamp(.8rem, 1.3vw, 1.25rem);
    font-weight: 700;
    letter-spacing: .02em;
}

.header .ministry { color: var(--accent); }

.header .title {
    font-family: 'Noto Kufi Arabic', sans-serif;
    font-size: clamp(1.2rem, 2.6vw, 2.6rem);
    font-weight: 900;
    line-height: 1.3;
    margin: .3em 0;
}

.header .complex {
    display: inline-block;
    margin-top: .2em;
    padding: .25em 1.1em;
    font-size: clamp(.95rem, 1.6vw, 1.5rem);
    font-weight: 800;
    color: var(--gold);
    background: rgba(250,204,21,.1);
    border: 1px solid rgba(250,204,21,.3);
    border-radius: 999px;
}

.logo { display: flex; align-items: center; }
.logo-left  { justify-content: flex-start; }
.logo-right { justify-content: flex-end; }

.header .logo img {
    width: 100%;
    max-width: clamp(70px, 9vw, 170px);
    max-height: clamp(60px, 12vh, 120px);
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 4px 14px rgba(0,0,0,.5));
}

/* ===== CENTER ===== */
.center {
    flex: 1 1 auto;
    min-height: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: clamp(12px, 2.5vh, 32px) clamp(16px, 4vw, 80px);
}

/* ===== WELCOME ===== */
.welcome { text-align: center; }

.welcome h1 {
    font-family: 'Noto Kufi Arabic', sans-serif;
    font-size: clamp(2.2rem, 5vw, 5rem);
    font-weight: 900;
    margin: 0;
    color: #fff;
    text-shadow: 0 4px 30px rgba(79,209,197,.35);
}

.welcome p {
    margin: .6em 0 0;
    font-size: clamp(1.1rem, 2.3vw, 2.2rem);
    font-weight: 600;
    color: var(--muted);
    animation: pulse 2.6s ease-in-out infinite;
}

@keyframes pulse { 50% { opacity: .45; } }

/* ===== PERSON CARD ===== */
.person {
    width: min(100%, 1400px);
    max-height: 100%;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    grid-template-rows: repeat(6, auto);
    column-gap: clamp(20px, 4vw, 72px);
    align-content: center;
    align-items: center;
    padding: clamp(18px, 3.5vh, 48px) clamp(20px, 4vw, 64px);
    text-align: start;
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 28px;
    backdrop-filter: blur(18px);
    box-shadow: 0 30px 80px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.1);
    animation: pop .45s cubic-bezier(.2,.9,.3,1.2);
}

@keyframes pop { from { opacity: 0; transform: scale(.96) translateY(10px); } }

.person.authorized { box-shadow: 0 30px 80px rgba(0,0,0,.45), 0 0 80px var(--ok-glow); border-color: rgba(34,197,94,.45); }
.person.denied     { box-shadow: 0 30px 80px rgba(0,0,0,.45), 0 0 80px var(--ko-glow); border-color: rgba(239,68,68,.5); }

.photo {
    grid-column: 1;
    grid-row: 1 / -1;
    width: clamp(160px, 24vw, 380px);
    height: clamp(160px, 24vw, 380px);
    max-height: 58vh;
    max-width: 58vh;
    border-radius: 50%;
    object-fit: cover;
    border: 8px solid var(--ok);
    box-shadow: 0 0 0 10px rgba(255,255,255,.05), 0 0 50px var(--ok-glow);
}

.person.denied .photo { border-color: var(--ko); box-shadow: 0 0 0 10px rgba(255,255,255,.05), 0 0 50px var(--ko-glow); }

.person > :not(.photo) { grid-column: 2; }

.name {
    color: var(--text);
    font-family: 'Noto Kufi Arabic', sans-serif;
    font-size: clamp(1.8rem, 3.6vw, 3.6rem);
    font-weight: 900;
    line-height: 1.25;
}

.status {
    justify-self: start;
    margin: .35em 0 .5em;
    padding: .1em .8em;
    font-size: clamp(1.3rem, 2.4vw, 2.4rem);
    font-weight: 800;
    border-radius: 999px;
}

.authorized .status { color: var(--ok); background: rgba(34,197,94,.14); border: 1px solid rgba(34,197,94,.4); }
.denied .status     { color: var(--ko); background: rgba(239,68,68,.14); border: 1px solid rgba(239,68,68,.4); }

.info {
    margin-top: .3em;
    font-size: clamp(1rem, 1.7vw, 1.7rem);
    color: var(--muted);
}

/* ===== SLOTS ===== */
.slots {
    margin-top: 1em;
    padding-top: .9em;
    border-top: 1px solid var(--glass-border);
    display: flex;
    flex-wrap: wrap;
    gap: .5em;
}

.slot {
    margin: 0;
    padding: .25em .9em;
    font-size: clamp(.9rem, 1.4vw, 1.4rem);
    color: var(--text);
    background: rgba(255,255,255,.07);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
}

/* ===== ALERT EXPIRATION ===== */
.alert-expire {
    margin-top: .6em;
    font-size: clamp(1rem, 1.6vw, 1.6rem);
    font-weight: 800;
}
.alert-expire:empty { display: none; }

.alert-5 { color: var(--gold); }
.alert-3 { color: #fb923c; }
.alert-1 { color: var(--ko); }

/* ===== PORTRAIT / SMALL SCREENS ===== */
@media (max-width: 900px) {
    body { overflow: auto; }
    .header { grid-template-columns: 1fr; text-align: center; }
    .logo { display: none; }
    .person { grid-template-columns: 1fr; text-align: center; }
    .photo { grid-row: auto; grid-column: 1; justify-self: center; margin-bottom: 1em; }
    .person > :not(.photo) { grid-column: 1; }
    .status { justify-self: center; }
    .slots { justify-content: center; }
}
</style>

{{-- ===== HEADER ===== --}}
<div class="header">

    <!-- logo gauche -->
    <div class="logo logo-left">
        <img src="{{ asset('images/logo_left.png') }}" alt="Logo gauche">
    </div>

    <!-- contenu centré -->
    <div class="header-content">
        <div class="state">الجمهورية الجزائرية الديمقراطية الشعبية</div>
        <div class="ministry">وزارة الرياضة</div>
        <div class="title">ديوان المركب المتعدد الرياضات لولاية ميلة</div>
        <div class="complex">
            🏟 {{ $complexName ?? 'المركب الرياضي' }}
        </div>
    </div>

    <!-- logo droite -->
    <div class="logo logo-right">
        <img src="{{ asset('images/logo_right.png') }}" alt="Logo droite">
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
        <div id="expireAlert" class="alert-expire"></div>
        <div id="slots" class="slots"></div>
    </div>

</div>

<script>
const daysMap = {
    0:'الأحد',1:'الإثنين',2:'الثلاثاء',
    3:'الأربعاء',4:'الخميس',5:'الجمعة',6:'السبت'
};

const SHOW_DURATION = 10; // secondes
let lastShownAt = null;
let lastPunchTime = null;

async function loadTV() {
    try {
        const res = await fetch('/tv/last-punch/json', { cache: 'no-store' });
        const data = await res.json();
        const now = Date.now();

        if (data.mode === 'welcome') {
            showWelcome();
            return;
        }

        const punchTime = new Date(data.punch.time).getTime();

        if (!lastPunchTime || punchTime !== lastPunchTime) {
            lastPunchTime = punchTime;
            lastShownAt = now;
            showPerson(data);
            return;
        }

        if (lastShownAt && (now - lastShownAt) / 1000 > SHOW_DURATION) {
            showWelcome();
        }

    } catch (e) {
        console.error('TV error', e);
    }
}

function showWelcome() {
    document.getElementById('welcome').classList.remove('hidden');
    document.getElementById('person').classList.add('hidden');


    // ⛔ إخفاء تنبيه انتهاء الاشتراك
    document.getElementById('expireAlert').innerHTML = '';
}
function normalizeDate(dateStr) {
    if (!dateStr || typeof dateStr !== 'string') return null;

    // نحاول استخراج YYYY-MM-DD من أي string
    const match = dateStr.match(/(\d{4})[-\/](\d{2})[-\/](\d{2})/);

    if (!match) return null;

    const year  = parseInt(match[1], 10);
    const month = parseInt(match[2], 10) - 1; // JS months 0-based
    const day   = parseInt(match[3], 10);

    return new Date(year, month, day);
}

function parseDateLocal(dateStr) {
    if (!dateStr) return null;
    const [y, m, d] = dateStr.split('-').map(Number);
    return new Date(y, m - 1, d); // ⬅️ local date
}

function showPerson(data) {
    document.getElementById('welcome').classList.add('hidden');
    document.getElementById('person').classList.remove('hidden');
       document.querySelector('.complex').innerText =
    '🏟 ' + (data.complex?.name ?? 'المركب الرياضي');
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

   
   // ===== ALERT EXPIRATION =====
// ===== ALERT EXPIRATION (FIX FINAL) =====
// ===== ALERT EXPIRATION (FINAL & ROBUST) =====
const alertBox = document.getElementById('expireAlert');
alertBox.innerHTML = '';

const endDate = normalizeDate(data.person.end_date);

if (endDate) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    endDate.setHours(0, 0, 0, 0);

    const diffDays = Math.round(
        (endDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24)
    );

    if (diffDays < 0) {
        alertBox.className = 'alert-expire alert-1';
        alertBox.innerText = '⛔ الاشتراك منتهي';
        return;
    }

    if (diffDays <= 5) {
        let cls = 'alert-5';
        let msg = `⚠️ الاشتراك ينتهي بعد ${diffDays} أيام`;

        if (diffDays <= 3) {
            cls = 'alert-3';
            msg = `⚠️ تنبيه: الاشتراك ينتهي بعد ${diffDays} أيام`;
        }

        if (diffDays === 1) {
            cls = 'alert-1';
            msg = '⛔ الاشتراك ينتهي غدًا';
        }

        if (diffDays === 0) {
            cls = 'alert-1';
            msg = '⛔ الاشتراك منتهي اليوم';
        }

        alertBox.className = `alert-expire ${cls}`;
        alertBox.innerText = msg;
    }
}


   
        // ===== TIME SLOTS (CORRECT HANDLING) =====
    let slots = data.person.time_slots;

    if (typeof slots === 'string') {
        try { slots = JSON.parse(slots); }
        catch { slots = []; }
    }
    if (!Array.isArray(slots)) slots = [];

    let html = '';
    if (slots.length === 0) {
        html = '<div class="slot">🕒 لا توجد حصص مبرمجة</div>';
    } else {
        slots.forEach(s => {
            html += `
                <div class="slot">
                    🕒 <strong>${daysMap[s.day_number] ?? '—'}</strong>
                    : ${s.start ?? '?'} → ${s.end ?? '?'}
                </div>`;
        });
    }
    document.getElementById('slots').innerHTML = html;
}

setInterval(loadTV, 1000);
loadTV();
</script>
<button id="fsBtn" style="
    position:fixed;
    bottom:20px;
    right:20px;
    z-index:9999;
    padding:10px 16px;
    font-size:14px;
    border-radius:8px;
    border:none;
    background:#22c55e;
    color:#000;
    cursor:pointer;
">
📺 وضع التلفاز
</button>

<script>
document.getElementById('fsBtn').onclick = () => {
    goFullscreen();
    document.getElementById('fsBtn').style.display = 'none';
};
</script>

@endsection
