<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Cpl;
use App\Models\PklPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PklPeriodController extends Controller
{
    public function index(): View
    {
        $periods = PklPeriod::with(['batches', 'cpls'])
            ->latest()
            ->paginate(10);

        $cpls = Cpl::orderBy('kode_cpl')->get();
        $defaults = $this->recommendedAcademicPeriod();

        return view(
            'kaprodi.pkl-periods.index',
            compact('periods', 'cpls', 'defaults')
        );
    }

    /**
     * Menampilkan form tambah periode PKL.
     * Semester dan tahun akademik hanya berupa rekomendasi awal.
     */
    public function create(): View
    {
        $cpls = Cpl::orderBy('kode_cpl')->get();

        $defaults = $this->recommendedAcademicPeriod();

        return view(
            'kaprodi.pkl-periods.create',
            compact('cpls', 'defaults')
        );
    }

    /**
     * Menyimpan periode PKL baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'semester' => [
                'required',
                Rule::in(['Ganjil', 'Genap']),
                Rule::unique('pkl_periods', 'semester')
                    ->where(fn ($query) => $query->where(
                        'tahun_akademik',
                        $request->input('tahun_akademik')
                    )),
            ],

            'tahun_akademik' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
            ],

            'cpls' => [
                'required',
                'array',
                'min:1',
            ],

            'cpls.*' => [
                'required',
                'string',
                'distinct',
                'exists:cpl,kode_cpl',
            ],
        ], [
            'semester.required' =>
                'Silakan pilih semester.',

            'semester.in' =>
                'Semester harus berupa Ganjil atau Genap.',

            'semester.unique' =>
                'Periode PKL untuk semester dan tahun akademik tersebut sudah terdaftar. Silakan pilih kombinasi lain.',

            'tahun_akademik.required' =>
                'Tahun akademik wajib diisi.',

            'tahun_akademik.regex' =>
                'Format tahun akademik harus YYYY/YYYY, misalnya 2026/2027.',

            'cpls.required' =>
                'Pilih minimal satu CPL.',

            'cpls.array' =>
                'Format data CPL tidak valid.',

            'cpls.min' =>
                'Pilih minimal satu CPL.',

            'cpls.*.required' =>
                'Kode CPL tidak boleh kosong.',

            'cpls.*.distinct' =>
                'CPL yang sama tidak boleh dipilih lebih dari satu kali.',

            'cpls.*.exists' =>
                'CPL yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($validated) {
            $cplCodes = $validated['cpls'];

            unset($validated['cpls']);

            // Periode baru selalu berstatus Draft.
            $validated['status'] = 'draft';

            $period = PklPeriod::create($validated);

            // CPL dipilih secara manual oleh Kaprodi.
            $period->cpls()->sync($cplCodes);
        });

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with(
                'success',
                'Periode PKL berhasil ditambahkan dengan status Draft.'
            );
    }

    /**
     * Menampilkan form edit periode PKL.
     */
    public function edit(PklPeriod $pklPeriod): View
    {
        $cpls = Cpl::orderBy('kode_cpl')->get();

        $pklPeriod->load('cpls');

        return view(
            'kaprodi.pkl-periods.edit',
            compact('pklPeriod', 'cpls')
        );
    }

    /**
     * Memperbarui data periode PKL.
     * Status tidak diubah melalui halaman Edit.
     */
    public function update(
        Request $request,
        PklPeriod $pklPeriod
    ): RedirectResponse {
        $validated = $request->validate([
            'semester' => [
                'required',
                Rule::in(['Ganjil', 'Genap']),
                Rule::unique('pkl_periods', 'semester')
                    ->where(fn ($query) => $query->where(
                        'tahun_akademik',
                        $request->input('tahun_akademik')
                    ))
                    ->ignore(
                        $pklPeriod->getKey(),
                        $pklPeriod->getKeyName()
                    ),
            ],

            'tahun_akademik' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
            ],

            'cpls' => [
                'required',
                'array',
                'min:1',
            ],

            'cpls.*' => [
                'required',
                'string',
                'distinct',
                'exists:cpl,kode_cpl',
            ],
        ], [
            'semester.required' =>
                'Silakan pilih semester.',

            'semester.in' =>
                'Semester harus berupa Ganjil atau Genap.',

            'semester.unique' =>
                'Periode PKL untuk semester dan tahun akademik tersebut sudah terdaftar. Silakan pilih kombinasi lain.',

            'tahun_akademik.required' =>
                'Tahun akademik wajib diisi.',

            'tahun_akademik.regex' =>
                'Format tahun akademik harus YYYY/YYYY, misalnya 2026/2027.',

            'cpls.required' =>
                'Pilih minimal satu CPL.',

            'cpls.array' =>
                'Format data CPL tidak valid.',

            'cpls.min' =>
                'Pilih minimal satu CPL.',

            'cpls.*.required' =>
                'Kode CPL tidak boleh kosong.',

            'cpls.*.distinct' =>
                'CPL yang sama tidak boleh dipilih lebih dari satu kali.',

            'cpls.*.exists' =>
                'CPL yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($validated, $pklPeriod) {
            $cplCodes = $validated['cpls'];

            unset($validated['cpls']);

            // Hanya semester dan tahun akademik yang dikirim
            // sebagai data periode; status tetap tidak berubah.
            $pklPeriod->update($validated);

            $pklPeriod->cpls()->sync($cplCodes);
        });

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with(
                'success',
                'Data periode PKL berhasil diperbarui.'
            );
    }

    /**
     * Mengaktifkan periode Draft.
     * Periode Aktif sebelumnya akan diakhiri.
     */
    public function activate(PklPeriod $pklPeriod): RedirectResponse
    {
        DB::transaction(function () use ($pklPeriod) {
            $keyName = $pklPeriod->getKeyName();

            $periods = PklPeriod::query()
                ->orderBy($keyName)
                ->lockForUpdate()
                ->get();

            $period = $periods->first(
                fn (PklPeriod $item) =>
                    (string) $item->getKey()
                    === (string) $pklPeriod->getKey()
            );

            if (! $period) {
                abort(404);
            }

            if ($period->status !== 'draft') {
                throw ValidationException::withMessages([
                    'status' =>
                        'Hanya periode Draft yang dapat diaktifkan.',
                ]);
            }

            // Akhiri periode aktif sebelumnya, jika ada.
            foreach ($periods->where('status', 'aktif') as $activePeriod) {
                $activePeriod->update([
                    'status' => 'selesai',
                ]);
            }

            // Aktifkan periode yang dipilih.
            $period->update([
                'status' => 'aktif',
            ]);
        });

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with(
                'success',
                'Periode berhasil diaktifkan. Periode aktif sebelumnya, jika ada, telah diakhiri.'
            );
    }

    /**
     * Mengakhiri periode yang sedang aktif.
     */
    public function finish(PklPeriod $pklPeriod): RedirectResponse
    {
        DB::transaction(function () use ($pklPeriod) {
            $period = PklPeriod::query()
                ->whereKey($pklPeriod->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($period->status !== 'aktif') {
                throw ValidationException::withMessages([
                    'status' =>
                        'Hanya periode Aktif yang dapat diakhiri.',
                ]);
            }

            $period->update([
                'status' => 'selesai',
            ]);
        });

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with(
                'success',
                'Periode PKL berhasil diakhiri.'
            );
    }

    public function destroy(PklPeriod $pklPeriod): RedirectResponse
    {
        // Periode aktif tidak boleh dihapus.
        if ($pklPeriod->status === 'aktif') {
            return redirect()
                ->route('kaprodi.pkl-periods.index')
                ->with(
                    'error',
                    'Periode yang sedang aktif tidak dapat dihapus. Akhiri periode terlebih dahulu.'
                );
        }

        DB::transaction(function () use ($pklPeriod) {
            // Hapus relasi many-to-many dengan CPL.
            $pklPeriod->cpls()->detach();

            // Hapus periode. Batch terkait akan mengikuti
            // aturan cascadeOnDelete pada database.
            $pklPeriod->delete();
        });

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with(
                'success',
                'Periode PKL beserta batch terkait berhasil dihapus.'
            );
    }

    private function recommendedAcademicPeriod(): array
    {
        $now = now();

        $year = $now->year;
        $month = $now->month;

        if ($month >= 7) {
            $semester = 'Ganjil';
            $startYear = $year;
        } else {
            $semester = 'Genap';
            $startYear = $year - 1;
        }

        return [
            'semester' => $semester,
            'tahun_akademik' => $startYear . '/' . ($startYear + 1),
        ];
    }

    public function acc(PklPeriod $pklPeriod)
    {
        $pklPeriod->load([
            'batches' => function ($query) {
                $query->orderBy('tanggal_mulai');
            },
        ]);

        return view('kaprodi.pkl-periods.acc', compact(
            'pklPeriod'
        ));
    }
}