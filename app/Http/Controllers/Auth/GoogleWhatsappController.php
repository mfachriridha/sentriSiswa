<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GoogleWhatsappController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session('google_pending_whatsapp')) {
            return redirect()->route(Auth::user()->dashboardRouteName());
        }

        return view('auth.google-whatsapp', [
            'teacher' => Auth::user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{8,13}$/'],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xx, 628xx, atau +628xx.',
        ]);

        $teacher = Auth::user();
        $profile = $teacher->teacherProfile;

        if (! $profile) {
            $profile = $teacher->teacherProfile()->create([]);
        }

        $profile->update(['phone' => $request->phone]);

        session()->forget('google_pending_whatsapp');

        return redirect()->route($teacher->dashboardRouteName())
            ->with('success', 'Pendaftaran berhasil! Selamat datang di Sentri Siswa.');
    }
}
