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
        $user = Auth::user();
        $broadcasts = Broadcast::withTrashed()
            ->orderBy('created_at', 'desc')
            ->get()
            ->loadExists([
                'userStates as has_state' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->withTrashed();
                },
                'userStates as is_deleted' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->onlyTrashed();
                }
            ]);

        // Debug log before mapping
        Log::info('Broadcasts before mapping:', $broadcasts->toArray());

        $broadcasts = $broadcasts->map(function ($broadcast) {
            $state = [
                'id' => $broadcast->id,
                'title' => $broadcast->title,
                'content' => $broadcast->content,
                'created_at' => $broadcast->created_at,
                'user_state_class' => $broadcast->user_state_class,
                'is_trashed' => $broadcast->trashed()
            ];
            
            Log::info("Broadcast {$broadcast->id} state:", $state);
            
            return $state;
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
}