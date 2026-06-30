<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreSchoolRuleRequest;
use App\Models\TataTertib;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolRuleController extends Controller
{
    public function index(): View
    {
        $schoolRules = TataTertib::with('diunggahOleh')
            ->latest()
            ->paginate(15);

        return view('kesiswaan.tata-tertib.index', compact('schoolRules'));
    }

    public function store(StoreSchoolRuleRequest $request): RedirectResponse
    {
        if ($request->boolean('is_published')) {
            TataTertib::query()->update(['dipublikasikan' => false]);
        }

        TataTertib::create([
            'judul' => (string) $request->string('title'),
            'path_file' => $request->file('rule_pdf')->store('school-rules', 'public'),
            'dipublikasikan' => $request->boolean('is_published'),
            'diunggah_oleh_id' => Auth::id(),
        ]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil diunggah.');
    }

    public function publish(TataTertib $schoolRule): RedirectResponse
    {
        TataTertib::query()->where('id', '!=', $schoolRule->id)->update(['dipublikasikan' => false]);
        $schoolRule->update(['dipublikasikan' => true]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dipublikasikan.');
    }

    public function unpublish(TataTertib $schoolRule): RedirectResponse
    {
        $schoolRule->update(['dipublikasikan' => false]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dinonaktifkan.');
    }

    public function destroy(TataTertib $schoolRule): RedirectResponse
    {
        Storage::disk('public')->delete($schoolRule->path_file);
        $schoolRule->delete();

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dihapus.');
    }
}
