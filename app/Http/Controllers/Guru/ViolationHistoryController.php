<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\StudentViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ViolationHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            return view('guru.pelanggaran.empty');
        }

        $violations = StudentViolation::with(['studentProfile.user', 'violationType', 'recordedBy'])
            ->whereHas('studentProfile', fn ($q) => $q->where('class_id', $class->id))
            ->when($request->student_id, fn ($q, $id) => $q->where('student_profile_id', $id))
            ->when($request->category, fn ($q, $cat) => $q->where('violation_category', $cat))
            ->when($request->date_from, fn ($q, $date) => $q->where('violation_date', '>=', $date))
            ->when($request->date_to, fn ($q, $date) => $q->where('violation_date', '<=', $date))
            ->latest('violation_date')
            ->paginate(25)
            ->withQueryString();

        $students = $class->students()
            ->join('users', 'student_profiles.user_id', '=', 'users.id')
            ->with('user')
            ->orderBy('users.name')
            ->select('student_profiles.*')
            ->get();

        return view('guru.pelanggaran.index', compact('class', 'violations', 'students'));
    }
}
