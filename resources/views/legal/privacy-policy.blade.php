@extends('layouts.public')

@section('title', 'Privacy Policy')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-slate-900 mb-4">Privacy Policy</h1>
            <p class="text-lg text-slate-600">Effective Date: August 2, 2026</p>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 prose prose-slate max-w-none">
            
            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">1. Introduction</h2>
                <p class="text-slate-700 leading-relaxed">
                    Welcome to <strong>Nagarik Plus</strong> ("we," "our," or "us"). We are committed to protecting your personal information and your right to privacy. This Privacy Policy explains how we collect, use, store, and protect your data when you use the Nagarik Plus mobile application (the "App"), which serves as a digital wallet and e-governance portal for your essential documents, vital event certificates, and civil services.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">2. Information We Collect</h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    To provide you with secure digital locker services and government integrations, we may collect the following types of information:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                    <li><strong>Personal Identification Information:</strong> Name, date of birth, citizenship number, national ID, passport details, and vital event data (birth, marriage, death, and migration certificates).</li>
                    <li><strong>Uploaded Documents:</strong> Any images, PDFs, or scanned documents you upload or scan using the App's camera scanner.</li>
                    <li><strong>Authentication Data:</strong> Phone numbers, email addresses, PINs, and on-device biometric data (Fingerprint/Face ID). <em>Note: Biometric data is stored securely on your device's hardware enclave and is never transmitted to our servers.</em></li>
                    <li><strong>Device Information:</strong> Device model, operating system, unique device identifiers, and mobile network information used for security and fraud prevention.</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">3. How We Use Your Information</h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    We use your data exclusively to provide and improve the services within Nagarik Plus:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                    <li><strong>Digital Wallet:</strong> To securely store, categorize, and display your vital event certificates, identity documents, and financial records.</li>
                    <li><strong>Cloud Synchronization:</strong> To back up your documents securely to our servers (when Cloud Sync is enabled) so you can retrieve them on other devices.</li>
                    <li><strong>Authentication:</strong> To verify your identity and ensure that only you can access your sensitive documents.</li>
                    <li><strong>Service Delivery:</strong> To facilitate fast access to government services, office locators, and civic news.</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">4. How We Protect Your Data</h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    Security is our highest priority. We implement state-of-the-art security measures to protect your data:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                    <li><strong>End-to-End Encryption:</strong> All documents and personal data transmitted between your device and our servers are encrypted using TLS/SSL protocols.</li>
                    <li><strong>Local Secure Storage:</strong> Data stored locally on your device is encrypted and protected behind your device's biometric authentication (App Lock).</li>
                    <li><strong>Zero-Knowledge Architecture (Where Applicable):</strong> Certain sensitive documents are encrypted in a way that prevents unauthorized access, even by our system administrators.</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">5. Sharing Your Information</h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    We <strong>do not</strong> sell, rent, or trade your personal information to third parties. We only share your data under the following circumstances:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                    <li><strong>With Government Agencies:</strong> When you explicitly request a service or verification that requires securely transmitting your data to official government endpoints.</li>
                    <li><strong>Legal Compliance:</strong> If required by law, court order, or to protect against legal liability.</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">6. User Rights and Controls</h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    You have full control over your data within Nagarik Plus:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-700">
                    <li><strong>Access and Edit:</strong> You can view, edit, or delete any document stored in your digital locker at any time.</li>
                    <li><strong>Revoke Sync:</strong> You can disable Cloud Sync, which will keep your documents strictly on your local device.</li>
                    <li><strong>Account Deletion:</strong> You can request the permanent deletion of your account and all associated cloud-synced documents by contacting our support team or using the <a href="{{ route('delete-account') }}" class="text-indigo-600 hover:text-indigo-800 underline">in-app deletion tool</a>.</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">7. Changes to This Privacy Policy</h2>
                <p class="text-slate-700 leading-relaxed">
                    We may update this Privacy Policy from time to time to reflect changes in legal requirements or our app's features. We will notify you of any significant changes via in-app alerts.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">8. Contact Us</h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    If you have any questions, concerns, or requests regarding this Privacy Policy or your data, please contact our Data Protection Officer at:
                </p>
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <p class="text-slate-700"><strong>Email:</strong> <a href="mailto:privacy@nagarikplus.gov.np" class="text-indigo-600 hover:text-indigo-800">privacy@nagarikplus.gov.np</a></p>
                    <p class="text-slate-700"><strong>Address:</strong> Singha Durbar, Kathmandu, Nepal</p>
                </div>
            </section>

        </div>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
