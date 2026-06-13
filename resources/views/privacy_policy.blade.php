<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <title>Privacy Policy — {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light dark; }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.65;
            color: #1f2933;
            background: #f7f8fa;
            margin: 0;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        h1 { font-size: 1.9rem; margin: 0 0 .25rem; }
        h2 { font-size: 1.25rem; margin-top: 2rem; color: #111827; }
        p, li { color: #374151; }
        a { color: #2563eb; }
        .meta { color: #6b7280; font-size: .9rem; margin-bottom: 2rem; }
        ul { padding-left: 1.25rem; }
        code { background: #f3f4f6; padding: .1rem .35rem; border-radius: 4px; }
        footer { margin-top: 2.5rem; font-size: .85rem; color: #6b7280; }
        @media (prefers-color-scheme: dark) {
            body { background: #0f1115; color: #e5e7eb; }
            .container { background: #1a1d23; box-shadow: none; }
            h1, h2 { color: #f3f4f6; }
            p, li { color: #cbd5e1; }
            code { background: #2a2e35; }
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>Privacy Policy</h1>
        <p class="meta">{{ config('app.name') }} &middot; Last updated: {{ $lastUpdated }}</p>

        <p>
            This Privacy Policy explains how {{ config('app.name') }} ("we", "us", or "the app")
            collects, uses, and protects information when you use our mobile application and related
            services. By using the app, you agree to the practices described below.
        </p>

        <h2>1. Information We Collect</h2>
        <p>We collect only the information needed to operate the app:</p>
        <ul>
            <li><strong>Account information</strong> — your name, email address, and password when you register or sign in. If you use Google Sign-In, we receive basic profile information (name and email) from your Google account.</li>
            <li><strong>Profile information</strong> — optional details you provide, such as your date of birth and preferred language.</li>
            <li><strong>Location data (while in use)</strong> — with your permission, the app accesses your device's precise location <strong>only while you are actively using the app</strong> to power the map, detect when you enter game areas, and run location-based challenges and scoring. We do <strong>not</strong> collect your location in the background or when the app is closed.</li>
            <li><strong>Gameplay data</strong> — challenges completed, scores, rankings, and prizes redeemed.</li>
            <li><strong>Diagnostics &amp; crash data</strong> — to keep the app stable, we collect crash reports, error logs, device information (such as device model and operating system), and your IP address through our error-monitoring provider. This also includes limited recordings of in-app screen interactions (session replay) used solely to diagnose problems.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>To create and manage your account and authenticate you.</li>
            <li>To provide core features: maps, location-based challenges, scoring, rankings, and prize redemption.</li>
            <li>To diagnose crashes and errors and improve the stability and performance of the app.</li>
            <li>To maintain the security and integrity of the service.</li>
            <li>To communicate with you about your account when necessary (for example, password reset emails).</li>
        </ul>

        <h2>3. Google Sign-In</h2>
        <p>
            If you choose to sign in with Google, we receive basic profile information (such as your
            name and email address) from your Google account to create and identify your account.
            We do not access your Google data beyond what is required for authentication.
        </p>

        <h2>4. Third-Party Services We Use</h2>
        <p>
            We rely on a small number of trusted third-party providers to operate the app. These
            providers process data only on our behalf and for the purposes described here:
        </p>
        <ul>
            <li><strong>Sentry</strong> — crash reporting, error monitoring, and session replay. Receives diagnostics data, device information, IP address, and limited screen-interaction recordings. This data may be processed on servers located in the European Union. See <a href="https://sentry.io/privacy/" target="_blank" rel="noopener">Sentry's Privacy Policy</a>.</li>
            <li><strong>Google</strong> — Google Maps (to display the map) and Google Sign-In (optional authentication). See <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google's Privacy Policy</a>.</li>
            <li><strong>Expo (EAS)</strong> — delivers over-the-air app updates. See <a href="https://expo.dev/privacy" target="_blank" rel="noopener">Expo's Privacy Policy</a>.</li>
        </ul>

        <h2>5. Sharing of Information</h2>
        <p>
            We do <strong>not</strong> sell your personal information and we do <strong>not</strong>
            share it with third parties for advertising. We share data only with the service providers
            listed above as needed to operate the app, or where required by law. Because some of these
            providers operate internationally, your information may be transferred to and processed in
            countries outside your own, including the European Union.
        </p>

        <h2>6. Data Retention</h2>
        <p>
            We retain your account information for as long as your account is active or as needed to
            provide the service. Diagnostics and crash data are retained for a limited period for
            troubleshooting. You may request deletion of your account and associated personal data at
            any time by contacting us at the address below.
        </p>

        <h2>7. Children's Privacy</h2>
        <p>
            We are committed to protecting the privacy of children. We do not knowingly collect more
            personal information from children than is necessary to use the app, and we do not use it
            for advertising. If you believe a child has provided us with personal information without
            appropriate consent, please contact us and we will take steps to remove that information.
        </p>

        <h2>8. Security</h2>
        <p>
            We use reasonable technical and organizational measures to protect your information against
            unauthorized access, alteration, or disclosure, and data is transmitted over encrypted
            (HTTPS) connections. No method of transmission or storage is 100% secure, but we strive to
            protect your data.
        </p>

        <h2>9. Your Rights</h2>
        <p>
            You may access, update, or request deletion of your personal information by contacting us.
            Depending on your location, you may have additional rights under applicable data
            protection laws.
        </p>

        <h2>10. Changes to This Policy</h2>
        <p>
            We may update this Privacy Policy from time to time. Changes will be posted on this page
            with an updated "Last updated" date.
        </p>

        <h2>11. Contact Us</h2>
        <p>
            If you have any questions about this Privacy Policy or your data, contact us at:
            <br>
            <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
        </p>

        <footer>
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </footer>
    </main>
</body>
</html>
