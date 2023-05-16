<?php

namespace App\Http\Controllers\Admin;

use App\Models\AnimalInfo;
use App\Models\VisitorInfo;
use App\Models\MilkProduction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->permission == 1) {
            $milkU = MilkProduction::distinct('animal_info_id')->get('animal_info_id')->pluck('animal_info_id');
            $animalInfos = AnimalInfo::with(['getFarmInfo','getCommunityInfo', 'getCommunityInfo.district', 'milkYields' => function ($q) {
                    $q->where('milk_production', '>=', 4);
                }])->whereIs_culling(0)->get();
            $milkProduction = MilkProduction::select(DB::raw('count(animal_info_id) as num'))
                ->whereNotIn('animal_info_id', isCulling())
                ->where('milk_production', '>=', 4)
                ->groupBy('animal_info_id')
                ->get()
                ->count();
        } else {
            $animalInfos = AnimalInfo::with(['getFarmInfo', 'getCommunityInfo', 'getCommunityInfo.district', 'milkYields' => function ($q) {
                    $q->where('milk_production', '>=', 4);
                }])->whereIs_culling(0)->whereUser_id(Auth::user()->id)->get();
            $milkProduction = MilkProduction::whereUser_id(auth::user()->id)
                ->whereNotIn('animal_info_id', isCullingUser())
                ->where('milk_production', '>=', 4)
                ->groupBy('animal_info_id')
                ->count();
        }
        return view('admin.dashboard', compact('animalInfos', 'milkProduction'));
    }

    public function VisitorInfo()
    {
        $visitors = VisitorInfo::all();
        return view('admin.visitor_info.index', compact('visitors'));
    }
}
