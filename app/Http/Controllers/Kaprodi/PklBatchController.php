<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\PklBatch;
use App\Models\PklPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PklBatchController extends Controller
{
    /**
     * Tidak memerlukan halaman daftar batch terpisah.
     * Pengelolaan batch dilakukan melalui halaman periode PKL.
     */
    public function index()
    {
        return redirect()->route('kaprodi.pkl-periods.index');
    }

    /**
     * Membuat batch baru dengan nama otomatis.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pkl_period_id' => [
                'required',
                'integer',
                'exists:pkl_periods,id',
            ],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $period = PklPeriod::whereKey(
                $validated['pkl_period_id']
            )->lockForUpdate()->firstOrFail();

            if ($period->status !== 'aktif') {
                throw ValidationException::withMessages([
                    'pkl_period_id' =>
                        'Batch hanya dapat dibuat pada periode PKL yang aktif.',
                ]);
            }

            $nomorTerakhir = $period->nomor_batch_terakhir ?? 0;
            $nomorBaru = $nomorTerakhir + 1;

            PklBatch::create([
                'pkl_period_id' => $period->id,
                'nama_batch' => 'Batch ' . $nomorBaru,
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'status' => 'draft',
            ]);

            $period->nomor_batch_terakhir = $nomorBaru;
            $period->save();
        });

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with('success', 'Batch PKL berhasil dibuat.');
    }

    /**
     * Memperbarui periode dan jadwal batch.
     */

    public function update(Request $request, PklBatch $pklBatch)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        $pklBatch->update([
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
        ]);

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with('success', 'Jadwal batch berhasil diperbarui.');
    }


    /**
     * Menghapus batch.
     */
    public function destroy(PklBatch $pklBatch)
    {
        $pklBatch->delete();

        return redirect()
            ->route('kaprodi.pkl-periods.index')
            ->with('success', 'Batch PKL berhasil dihapus.');
    }
}
