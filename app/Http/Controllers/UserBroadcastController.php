<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Broadcast;
use App\Models\BroadcastUserState;
use Illuminate\Support\Facades\Auth;

class UserBroadcastController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $broadcasts = Broadcast::all();
    
        foreach ($broadcasts as $broadcast) {
            $state = BroadcastUserState::where('user_id', $user->id)
                                       ->where('broadcast_id', $broadcast->id)
                                       ->withTrashed()
                                       ->first();
    
             if (!$state) {
            $broadcast->state = 'font-bold';
            } elseif ($state->deleted_at) {
                $broadcast->state = 'text-red-500';
            } else {
                $broadcast->state = 'font-normal ';
            }
            
        }
    
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
}