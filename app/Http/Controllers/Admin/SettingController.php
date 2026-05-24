<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function attendanceTime(): View
    {
        return view('admin.settings.attendance-time');
    }

    public function whatsapp(): View
    {
        return view('admin.settings.whatsapp');
    }
}
