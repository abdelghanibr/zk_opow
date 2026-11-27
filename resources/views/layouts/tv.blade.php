<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>TV – Last Punch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        body {
            background: #000;
            color: #fff;
        }
    </style>
</head>
<body>

    @yield('content')

</body>
<style>
/* ===== GLOBAL ===== */
body {
    margin: 0;
    background: radial-gradient(circle at top, #0f172a, #020617);
    font-family: 'Cairo', 'Segoe UI', sans-serif;
    color: #fff;
}

.fullscreen {
    width: 100vw;
    height: 100vh;
}

.d-none {
    display: none !important;
}

.tv-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===== WELCOME ===== */
.tv-welcome {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.welcome-center h1 {
    font-size: 90px;
    margin-bottom: 20px;
}

.welcome-sub {
    font-size: 36px;
    opacity: .85;
}

.welcome-info {
    margin-top: 40px;
    font-size: 28px;
    line-height: 1.8;
}

/* ===== CARD ===== */
.tv-card {
    padding: 60px;
    box-sizing: border-box;
}

.tv-card.authorized {
    border-left: 16px solid #22c55e;
}

.tv-card.denied {
    border-left: 16px solid #ef4444;
}

.card-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 60px;
    height: 100%;
    align-items: center;
}

/* LEFT */
.card-left {
    text-align: center;
}

.tv-photo {
    width: 320px;
    height: 320px;
    object-fit: cover;
    border-radius: 50%;
    border: 8px solid #fff;
}

.tv-status {
    margin-top: 25px;
    font-size: 40px;
    font-weight: 900;
}

.tv-status.ok { color: #22c55e; }
.tv-status.no { color: #ef4444; }

/* RIGHT */
.card-right {
    font-size: 30px;
}

.tv-name {
    font-size: 60px;
    font-weight: 900;
    margin-bottom: 20px;
}

.tv-phone,
.tv-punch,
.tv-terminal,
.tv-dates {
    margin-bottom: 15px;
    opacity: .9;
}

.tv-slots h3 {
    margin-top: 25px;
    margin-bottom: 10px;
}

.tv-slots ul {
    padding-right: 20px;
}

.tv-slots li {
    margin-bottom: 6px;
}

.muted {
    opacity: .6;
}

</style>
<script>
function goFullscreen() {
    const el = document.documentElement;

    if (el.requestFullscreen) {
        el.requestFullscreen();
    } else if (el.webkitRequestFullscreen) { // Safari
        el.webkitRequestFullscreen();
    } else if (el.msRequestFullscreen) { // IE/Edge
        el.msRequestFullscreen();
    }
}

// دخول Fullscreen عند أول تفاعل
document.addEventListener('click', goFullscreen, { once: true });
document.addEventListener('keydown', goFullscreen, { once: true });

// منع scroll
document.body.style.overflow = 'hidden';
</script>

</html>
