<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function store(Request $request) {
        $user = Auth::user();
        
        $item = Item::create([
            'content' => $request['link'],
            'user_id' => $user->id
        ]);
    }

    public function getItems() {
        $user = Auth::user();

        return response()->json(['items' => Item::where('user_id', $user->id)->get()]);
    }
}
