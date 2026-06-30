<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\TataTertib;
use Illuminate\View\View;

class SchoolRuleController extends Controller
{
    public function index(): View
    {
        $schoolRule = TataTertib::query()
            ->where('dipublikasikan', true)
            ->latest()
            ->first();

        return view('siswa.tata-tertib.index', compact('schoolRule'));
    }
}
