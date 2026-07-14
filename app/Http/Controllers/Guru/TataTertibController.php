<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreSchoolRuleRequest;
use App\Models\TataTertib;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TataTertibController extends Controller
{
    public function index(): View
    {
        $tataTertibs = TataTertib::with('diunggahOleh')
            ->latest()
            ->paginate(15);

        return view('kesiswaan.tata-tertib.index', compact('tataTertibs'));
    }

    public function store(StoreSchoolRuleRequest $request): RedirectResponse
    {
        if ($request->boolean('dipublikasikan')) {
            TataTertib::query()->update(['dipublikasikan' => false]);
        }

        $judul = (string) $request->string('judul');

        TataTertib::create([
            'judul' => $judul,
            'path_file' => $request->file('file_pdf')->storeAs(
                'school-rules',
                $this->namaBerkas($judul),
                'public',
            ),
            'dipublikasikan' => $request->boolean('dipublikasikan'),
            'diunggah_oleh_id' => Auth::id(),
        ]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil diunggah.');
    }

    /**
     * Nama berkas diturunkan dari judulnya, bukan diacak. Siswa membukanya lewat
     * peramban ponsel, dan yang tertulis di sana adalah nama berkasnya - kalau ia
     * berupa deretan huruf acak, tidak ada petunjuk sama sekali bahwa itu berkas
     * yang benar.
     *
     * Waktu unggahnya ditempelkan supaya dua tata tertib berjudul sama tidak saling
     * menimpa.
     */
    private function namaBerkas(string $judul): string
    {
        return Str::slug($judul).'-'.now()->format('YmdHis').'.pdf';
    }

    public function publish(TataTertib $tataTertib): RedirectResponse
    {
        TataTertib::query()->where('id', '!=', $tataTertib->id)->update(['dipublikasikan' => false]);
        $tataTertib->update(['dipublikasikan' => true]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dipublikasikan.');
    }

    public function unpublish(TataTertib $tataTertib): RedirectResponse
    {
        $tataTertib->update(['dipublikasikan' => false]);

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dinonaktifkan.');
    }

    public function destroy(TataTertib $tataTertib): RedirectResponse
    {
        Storage::disk('public')->delete($tataTertib->path_file);
        $tataTertib->delete();

        return redirect()->route('kesiswaan.tata-tertib.index')->with('success', 'Tata tertib berhasil dihapus.');
    }
}
