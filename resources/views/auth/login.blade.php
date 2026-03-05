@extends('layouts.app')
@section('content')
    <style>
        :root {
            --navy: #0a1628;
            --deep: #0d1f3c;
            --accent: #c9a84c;
            --accent-light: #e8c97a;
            --surface: rgba(255,255,255,0.04);
            --border: rgba(201,168,76,0.2);
            --text: #e8e4da;
            --muted: rgba(232,228,218,0.5);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: PSU-Stidti-Regular;
            background: var(--navy);
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ─── Left Panel ─── */
        .left-panel {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('image/login.jpg') center/cover;
            filter: brightness(0.3) saturate(0.6);
            transform: scale(1.05);
            animation: slowZoom 20s ease-in-out infinite alternate;
        }

        @keyframes slowZoom {
            from { transform: scale(1.05); }
            to { transform: scale(1.12); }
        }

        .left-overlay {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 60px;
        }

        .left-overlay::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(10,22,40,0.6) 0%, transparent 60%, rgba(10,22,40,0.8) 100%);
        }

        .left-content { position: relative; z-index: 1; }

        .university-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(201,168,76,0.15);
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 8px 20px 8px 8px;
            margin-bottom: 32px;
            backdrop-filter: blur(8px);
        }

        .badge-dot {
            width: 32px;
            height: 32px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .badge-text {
            font-size: 13px;
            color: var(--accent-light);
            letter-spacing: 0.05em;
            font-weight: 500;
        }

        .left-title {
            font-family: PSU-Stidti-Regular;
            font-size: clamp(32px, 4vw, 52px);
            color: var(--text);
            line-height: 1.25;
            font-weight: 300;
            margin-bottom: 16px;
        }

        .left-title span {
            color: var(--accent);
            font-weight: 600;
        }

        .left-subtitle {
            font-size: 16px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 420px;
            margin-bottom: 48px;
        }

        .stats-row {
            display: flex;
            gap: 40px;
        }

        .stat {
            border-left: 2px solid var(--accent);
            padding-left: 16px;
        }

        .stat-num {
            font-size: 28px;
            color: var(--accent-light);
            font-weight: 600;
            line-height: 1;
            font-family: PSU-Stidti-Regular;
        }

        .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
            letter-spacing: 0.05em;
        }

        /* ─── Right Panel ─── */
        .right-panel {
            width: 420px;
            min-width: 420px;
            background: var(--deep);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: -200px;
            right: -200px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(201,168,76,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .right-panel::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(201,168,76,0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Gold top stripe */
        .top-stripe {
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
        }

        .right-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 48px 44px;
            position: relative;
            z-index: 1;
        }

        .logo-area {
            margin-bottom: 48px;
            animation: fadeDown 0.7s ease both;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(201,168,76,0.3);
        }

        .logo-title {
            font-family: PSU-Stidti-Regular;
            font-size: 22px;
            color: var(--text);
            font-weight: 600;
            margin-bottom: 6px;
        }

        .logo-sub {
            font-size: 13px;
            color: var(--muted);
            letter-spacing: 0.05em;
        }

        /* ─── Login Card ─── */
        .login-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeUp 0.8s ease 0.1s both;
        }

        .greeting {
            font-size: 13px;
            color: var(--accent);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .heading {
            font-family: PSU-Stidti-Regular;
            font-size: 28px;
            color: var(--text);
            font-weight: 400;
            margin-bottom: 8px;
        }

        .sub-heading {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 40px;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider-text {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 0.08em;
        }

        /* SSO Button */
        .btn-sso {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--accent) 0%, #b8933e 100%);
            border: none;
            border-radius: 12px;
            color: var(--navy);
            font-family: PSU-Stidti-Regular;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(201,168,76,0.35);
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .btn-sso::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
        }

        .btn-sso:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(201,168,76,0.5);
            color: var(--navy);
        }

        .btn-sso:active { transform: translateY(0); }

        .btn-icon {
            width: 28px;
            height: 28px;
            background: rgba(10,22,40,0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            position: relative;
            z-index: 1;
        }

        .btn-sso-text { position: relative; z-index: 1; }

        .btn-note {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 40px;
        }

        .btn-note span {
            color: var(--accent);
        }

        /* ─── Contact Footer ─── */
        .contact-section {
            border-top: 1px solid var(--border);
            padding-top: 28px;
            animation: fadeUp 0.8s ease 0.2s both;
        }

        .contact-label {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .contact-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: var(--muted);
            transition: color 0.2s;
        }

        .contact-item:hover { color: var(--accent-light); }

        .contact-item-icon {
            width: 28px;
            height: 28px;
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: var(--accent);
            flex-shrink: 0;
        }

        /* ─── Animations ─── */
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ─── Floating Particles ─── */
        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--accent);
            border-radius: 50%;
            opacity: 0;
            animation: float linear infinite;
        }

        @keyframes float {
            0% { opacity: 0; transform: translateY(100vh) scale(0); }
            10% { opacity: 0.6; }
            90% { opacity: 0.3; }
            100% { opacity: 0; transform: translateY(-20px) scale(1.5); }
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; min-width: unset; }
        }
    </style>


    <!-- Left decorative panel -->
    <div class="left-panel">
        <div class="left-overlay">
            <div class="left-content">
                <div class="university-badge">
                    <div class="badge-dot">🎓</div>
                    <span class="badge-text">Prince of Songkla University</span>
                </div>
                <h1 class="left-title">
                    ระบบขอใช้บริการ<br>
                    {{-- การจัดสอบ <span>PMC</span> --}}
                </h1>
                <p class="left-subtitle">
                    แพลตฟอร์มจัดการการบริการ<br>
                    คณะวิทยาศาสตร์ มหาวิทยาลัยสงขลานครินทร์
                </p>
                <div class="stats-row">
                    <div class="stat">
                        <div class="stat-num">CS</div>
                        <div class="stat-label">SERVICE SYSTEM</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">2025</div>
                        <div class="stat-label">ACADEMIC YEAR</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">PSU</div>
                        <div class="stat-label">PASSPORT SSO</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right login panel -->
    <div class="right-panel">
        <div class="top-stripe"></div>

        <!-- Floating particles -->
        <div class="particles" id="particles"></div>

        <div class="right-inner">

            <!-- Logo -->
            <div class="logo-area">
                <div class="logo-icon">🏛️</div>
                <div class="logo-title">ระบบขอใช้บริการ</div>
                <div class="logo-sub">Faculty of Science · PSU</div>
            </div>

            <!-- Login form -->
            <div class="login-card">
                <div class="greeting">Welcome Back</div>
                <h2 class="heading">เข้าสู่ระบบ</h2>
                <p class="sub-heading">กรุณายืนยันตัวตนด้วย PSU Passport</p>

                <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">SSO</span>
                    <div class="divider-line"></div>
                </div>

                <a href="{{ url('/auth/redirect') }}" class="btn-sso">
                    <div class="btn-icon">🔑</div>
                    <span class="btn-sso-text">เข้าสู่ระบบด้วย PSU Passport</span>
                </a>

                <p class="btn-note">
                    ใช้บัญชี <span>@psu.ac.th</span> ของคุณเพื่อเข้าใช้งาน
                </p>
            </div>

            <!-- Contact footer -->
            <div class="contact-section">
                <div class="contact-label">ติดต่อสอบถาม</div>
                <div class="contact-items">
                    <div class="contact-item">
                        <div class="contact-item-icon">📞</div>
                        <span>0-7428-8620</span>
                    </div>
                    <div class="contact-item">
                        <div class="contact-item-icon">✉️</div>
                        <span>wittaya.kh@psu.ac.th</span>
                    </div>
                    <div class="contact-item">
                        <div class="contact-item-icon">🏢</div>
                        <span>คณะวิทยาศาสตร์ มหาวิทยาลัยสงขลานครินทร์</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Floating particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 18; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + '%';
            p.style.width = p.style.height = (Math.random() * 3 + 1) + 'px';
            p.style.animationDuration = (Math.random() * 15 + 10) + 's';
            p.style.animationDelay = (Math.random() * 10) + 's';
            container.appendChild(p);
        }
    </script>
@endsection