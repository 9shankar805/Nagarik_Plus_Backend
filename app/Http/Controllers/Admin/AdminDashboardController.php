<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Document;
use App\Models\News;
use App\Models\Office;
use App\Models\Advisor;
use App\Models\Reminder;
use App\Models\Hospital;
use App\Models\DeviceToken;
use App\Models\VerificationAuditLog;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'              => User::count(),
            'total_documents'          => Document::count(),
            'published_news'           => News::where('is_published', true)->count(),
            'total_offices'            => Office::where('is_active', true)->count(),
            'total_advisors'           => Advisor::count(),
            'total_reminders'          => Reminder::count(),
            'total_hospitals'          => Hospital::count(),
            'total_tokens'             => DeviceToken::count(),
            'verification_today'       => VerificationAuditLog::whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();
        $recentNews  = News::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentNews'));
    }
}
