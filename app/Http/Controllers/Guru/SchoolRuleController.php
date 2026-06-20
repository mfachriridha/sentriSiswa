<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreSchoolRuleRequest;
use App\Models\SchoolRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolRuleController extends Controller
{
    public function index(): View
    {
        $schoolRules = SchoolRule::with('uploadedBy')
            ->latest()
            ->paginate(15);

        return view('kesiswaan.tata-tertib.index', compact('schoolRules'));
    }

    public function store(StoreSchoolRuleRequest $request): RedirectResponse
    {
        if ($request->boolean('is_published')) {
            SchoolRule::query()->update(['is_published' => false]);
        }

        SchoolRule::create([
            'title' => (string) $request->string('title'),
            'file_path' => $request->file('rule_pdf')->store('school-rules', 'public'),
            'is_published' => $request->boolean('is_published'),
            'uploaded_by_user_id' => Auth::id(),
        ]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil diunggah.');
    }

    public function publish(SchoolRule $schoolRule): RedirectResponse
    {
        SchoolRule::query()->where('id', '!=', $schoolRule->id)->update(['is_published' => false]);
        $schoolRule->update(['is_published' => true]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dipublikasikan.');
    }

    public function unpublish(SchoolRule $schoolRule): RedirectResponse
    {
        $schoolRule->update(['is_published' => false]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dinonaktifkan.');
    }

    public function destroy(SchoolRule $schoolRule): RedirectResponse
    {
        Storage::disk('public')->delete($schoolRule->file_path);
        $schoolRule->delete();

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dihapus.');
    }
}
