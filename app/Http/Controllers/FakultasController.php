<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'fakultas')->get();
        $eventCount = Event::where('user_id', Auth::id())->count();
        $upcomingEvents = Event::where('user_id', Auth::id())
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->get(['title', 'date', 'type_event']);
        return view('pointakses.fakultas.index', compact('users', 'eventCount', 'upcomingEvents'));
    }
}
