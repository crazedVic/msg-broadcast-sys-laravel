<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Broadcast;
use App\Models\BroadcastUserState;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserBroadcastController extends Controller
{
    public function index()
    {
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = Auth::user();
        //Log::info('User:', $user->attributesToArray());

        $broadcasts = Broadcast::withTrashed()
            ->with([
                'userState' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->withTrashed(); // Include soft-deleted user states
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($broadcast) use ($user) {
                $userState = $broadcast->userState; // Access the hasOne relationship

                return [
                    'id' => $broadcast->id,
                    'title' => $broadcast->title,
                    'content' => $broadcast->content,
                    'created_at' => $broadcast->created_at,
                    'user_state_class' => $broadcast->userStateClass,
                    'is_trashed' => $broadcast->trashed(),
                ];
            });
        
        return view('broadcasts.inbox', compact('broadcasts'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $broadcast = Broadcast::findOrFail($id);
        $state = BroadcastUserState::where('user_id', $user->id)
                                   ->where('broadcast_id', $broadcast->id)
                                   ->first();

        if (!$state) {
            $broadcast->state = 'font-bold';
        } elseif ($state->deleted_at) {
            $broadcast->state = 'text-red-500';
        } else {
            $broadcast->state = 'font-normal';
        }

        return view('broadcasts.show', compact('broadcast'));
    }

    public function softDelete($id)
    {
        $user = Auth::user();
        $broadcast = Broadcast::findOrFail($id);
        $state = BroadcastUserState::where('user_id', $user->id)
                                   ->where('broadcast_id', $broadcast->id)
                                   ->first();

        if ($state) {
            $state->delete(); // Soft delete the state
        }

        return redirect()->back()->with('success', 'Broadcast soft deleted successfully.');
    }

    public function apiIndex(Request $request)
    {
        $user = $request->user();
        
        $broadcasts = Broadcast::where('broadcasts.deleted_at', null)
            ->where(function($query) {
                // First query - no state exists
                $query->whereNotExists(function($subquery) {
                    $subquery->select('*')
                        ->from('broadcast_user_states')
                        ->whereColumn('broadcast_user_states.broadcast_id', 'broadcasts.id')
                        ->where('broadcast_user_states.user_id', 2);
                })
                // OR second query - unread state exists
                ->orWhereExists(function($subquery) {
                    $subquery->select('*')
                        ->from('broadcast_user_states')
                        ->whereColumn('broadcast_user_states.broadcast_id', 'broadcasts.id')
                        ->where('broadcast_user_states.user_id', 2)
                        ->whereNull('broadcast_user_states.deleted_at')
                        ->whereNull('broadcast_user_states.read_at');
                });
            })
            ->orderBy('broadcasts.id')
            ->get()
        ->map(function ($broadcast) {
            return [
                'id' => $broadcast->id,
                'title' => $broadcast->title,
                'content' => $broadcast->content,
                'created_at' => $broadcast->created_at,
                'is_read' => false,
                'is_deleted' => false
            ];
        });

        return response()->json($broadcasts)
            ->header('recordCount', $broadcasts->count());
    }

    public function apiUpdateState(Request $request, Broadcast $broadcast)
    {
        $user = $request->user();
        $action = $request->input('action'); // 'read' or 'delete'

        $state = BroadcastUserState::firstOrNew([
            'user_id' => $user->id,
            'broadcast_id' => $broadcast->id
        ]);

        if ($action === 'read') {
            if ($state->exists) {
                return response()->json(['message' => 'Broadcast already marked as read']);
            }
            $state->save();
            return response()->json(['message' => 'Broadcast marked as read']);
        }

        if ($action === 'delete') {
            if ($state->exists) {
                $state->delete();
                return response()->json(['message' => 'Broadcast deleted']);
            }
            return response()->json(['message' => 'Broadcast already deleted']);
        }

        return response()->json(['error' => 'Invalid action'], 400);
    }
}