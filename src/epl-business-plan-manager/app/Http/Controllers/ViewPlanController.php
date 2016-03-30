<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Goat;
use App\Department;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ViewPlanController extends Controller
{
    public function index() {
        // TODO: CACHE THIS!!
        $bp = Goat::all();

        $sorted = collect();
        foreach ($bp as $goat) {
            if ($goat->type === 'G') {
                $sorted->push($goat);
                continue;
            }

            for ($i = 0, $len = $sorted->count(); $i < $len; $i++) {
                if ($sorted[$i]->id == $goat->parent_id) {
                    // Hacky fix since when you splice $goat into $sorted, 
                    // it converts the $goat into an array instead of 
                    // keeping it as a Model object...
                    $sorted->splice($i + 1, 0, "temp");
                    $sorted->put($i + 1, $goat);
                    break;
                }
            }
        }

        $leadOf = array();
        foreach (Auth::user()->leadOf as $dept) {
            array_push($leadOf, $dept->id);
        }
        
        return view('view_plan')->with(['bp' => $sorted, 'users' => User::all(),
            'depts' => Department::all(), 'leadOf' => $leadOf]);
    }

    public function showChanges($id) {
        return view('goat_history', ['changes' => Goat::findOrFail($id)->changes()->orderBy('created_at', 'desc')->get()]);
    }
}
