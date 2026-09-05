<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'payable']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('payment_method', $request->gateway);
        }
        if ($request->filled('transaction_code')) {
            $query->where('transaction_code', 'like', "%{$request->transaction_code}%");
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();
        
        // Calculate basic stats for top cards
        $stats = [
            'total_volume' => Transaction::where('status', 'completed')->sum('amount'),
            'today_volume' => Transaction::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
            'pending_count' => Transaction::where('status', 'pending')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'payable']);
        return view('admin.transactions.show', compact('transaction'));
    }
}
