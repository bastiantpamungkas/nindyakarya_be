<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Progres;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function counts()
    {

        $user = User::count();
        $progres = Progres::count();
        $project = Project::count();
        
        return response()->json([
            'user_count' =>  $user ?? 0,
            'progres_count' =>  $progres ?? 0,
            'project_count' =>  $project ?? 0,
        ], 200);
    }
}
