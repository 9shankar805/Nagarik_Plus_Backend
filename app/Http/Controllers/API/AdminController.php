<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\News;
use App\Models\QuizQuestion;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    // -------------------------------------------------------------------------
    // Stats
    // -------------------------------------------------------------------------

    /**
     * Return aggregate platform statistics, cached for 5 minutes.
     */
    public function stats(): JsonResponse
    {
        $data = Cache::remember('admin_stats', 300, function () {
            return [
                'total_users'          => User::count(),
                'active_users'         => User::whereNotNull('last_active_at')
                                              ->where('last_active_at', '>=', now()->subDays(30))
                                              ->count(),
                'total_documents'      => Document::count(),
                'total_reminders'      => Reminder::count(),
                'total_news'           => News::where('is_published', true)->count(),
                'total_quiz_questions' => QuizQuestion::count(),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // -------------------------------------------------------------------------
    // User management
    // -------------------------------------------------------------------------

    /**
     * Paginated list of users with optional search and status filter.
     * Includes per-user document_count and reminder_count.
     */
    public function listUsers(Request $request): JsonResponse
    {
        $query = User::withCount(['documents', 'reminders']);

        // Search by name, email, or phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter: active | banned
        if ($status = $request->input('status')) {
            if ($status === 'banned') {
                $query->whereNotNull('banned_at');
            } elseif ($status === 'active') {
                $query->whereNull('banned_at');
            }
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->through(fn ($u) => [
            'id'             => $u->id,
            'name'           => $u->name,
            'email'          => $u->email,
            'phone'          => $u->phone,
            'role'           => $u->role,
            'is_active'      => $u->is_active,
            'banned_at'      => $u->banned_at?->toDateTimeString(),
            'last_active_at' => $u->last_active_at?->toDateTimeString(),
            'created_at'     => $u->created_at->toDateTimeString(),
            'document_count' => $u->documents_count,
            'reminder_count' => $u->reminders_count,
        ]);

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Show detailed information for a single user.
     * Includes documents, reminders, and active Sanctum tokens.
     */
    public function showUser(int $id): JsonResponse
    {
        $user = User::with(['documents', 'reminders', 'tokens'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone,
                'role'           => $user->role,
                'avatar'         => $user->avatar,
                'is_active'      => $user->is_active,
                'banned_at'      => $user->banned_at?->toDateTimeString(),
                'last_active_at' => $user->last_active_at?->toDateTimeString(),
                'created_at'     => $user->created_at->toDateTimeString(),
                'documents'      => $user->documents->map(fn ($d) => [
                    'id'          => $d->id,
                    'title'       => $d->title,
                    'type'        => $d->type,
                    'status'      => $d->status,
                    'expiry_date' => $d->expiry_date?->toDateString(),
                ]),
                'reminders'      => $user->reminders->map(fn ($r) => [
                    'id'         => $r->id,
                    'title'      => $r->title,
                    'due_date'   => $r->due_date?->toDateString(),
                    'is_enabled' => $r->is_enabled,
                ]),
                'tokens'         => $user->tokens->map(fn ($t) => [
                    'id'         => $t->id,
                    'name'       => $t->name,
                    'last_used'  => $t->last_used_at?->toDateTimeString(),
                    'created_at' => $t->created_at->toDateTimeString(),
                ]),
            ],
        ]);
    }

    /**
     * Ban a user: set banned_at = now(), revoke all tokens, log the action.
     */
    public function banUser(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user->update(['banned_at' => now()]);
        $user->tokens()->delete();

        ActivityLog::record(
            'user_banned',
            $request->user(),
            "User #{$id} ({$user->name}) was banned. Reason: " . ($request->input('reason') ?? 'No reason given.'),
            ['subject_type' => User::class, 'subject_id' => $user->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'User banned and all tokens revoked.',
        ]);
    }

    /**
     * Soft-delete a user and log the action.
     */
    public function deleteUser(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        ActivityLog::record(
            'user_deleted',
            request()->user(),
            "User #{$id} ({$user->name}) was soft-deleted.",
            ['subject_type' => User::class, 'subject_id' => $user->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}
