<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\SchoolRule;
use Illuminate\View\View;

class SchoolRuleController extends Controller
{
    public function index(): View
    {
        $schoolRule = SchoolRule::query()
            ->where('is_published', true)
            ->latest()
            ->first();

        return view('siswa.tata-tertib.index', compact('schoolRule'));
    }
}
