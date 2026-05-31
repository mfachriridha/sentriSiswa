<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClassRosterController extends Controller
{
    public function index(Request $request): View
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            return view('guru.kelas-saya.empty');
        }

        $students = $class->students()
            ->join('users', 'student_profiles.user_id', '=', 'users.id')
            ->with(['user', 'biodata'])
            ->withSum('studentViolations', 'point_deduction')
            ->when($request->search, function ($q, $search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('student_profiles.nisn', 'like', "%{$search}%")
                    ->orWhere('student_profiles.nis', 'like', "%{$search}%");
            })
            ->orderBy('users.name')
            ->select('student_profiles.*')
            ->paginate(25)
            ->withQueryString();

        return view('guru.kelas-saya.index', compact('class', 'students'));
    }
}
