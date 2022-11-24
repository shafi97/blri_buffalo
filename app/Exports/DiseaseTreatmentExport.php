<?php

namespace App\Exports;

use App\Models\DiseaseTreatment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DiseaseTreatmentExport implements FromView
{
    public function view(): View
    {
        if (auth()->user()->permission == 1) {
            return view('admin.disease_treatment.excel', [
                'diseaseTreatments' => DiseaseTreatment::whereNotIn('animal_info_id',isCulling())->get()
            ]);
        } else {
            return view('admin.disease_treatment.excel', [
                'diseaseTreatments' => DiseaseTreatment::where('user_id', auth()->user()->id)->whereNotIn('animal_info_id',isCullingUser())->get()
            ]);
        }
    }
}