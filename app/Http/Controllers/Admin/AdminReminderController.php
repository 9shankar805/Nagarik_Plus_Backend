<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class AdminReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::with('user')->latest()->paginate(15);
        return view('admin.reminders.index', compact('reminders'));
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();
        return redirect()->route('admin.reminders.index')->with('success', 'Reminder deleted successfully.');
    }
}
