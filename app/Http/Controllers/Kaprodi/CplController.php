<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Cpl;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CplController extends Controller
{
    /**
     * Menampilkan daftar CPL.
     */
    public function index(): View
    {
        $cpls = Cpl::orderBy('kode_cpl')
            ->paginate(10);

        return view('kaprodi.cpls.index', compact('cpls'));
    }

    /**
     * Menampilkan halaman tambah CPL konvensional.
     * Dapat dipertahankan jika route create masih digunakan.
     */
    public function create(): View
    {
        return view('kaprodi.cpls.create');
    }

    /**
     * Menyimpan data CPL baru.
     * Mendukung form biasa maupun AJAX/JSON
     * dari slide-over.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'kode_cpl' => [
                'required',
                'string',
                'max:20',
                'unique:cpl,kode_cpl',
            ],
            'deskripsi_cpl' => [
                'required',
                'string',
            ],
            'domain_kompetensi' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $cpl = Cpl::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data CPL berhasil ditambahkan.',
                'cpl' => [
                    'id' => $cpl->id,
                    'kode_cpl' => $cpl->kode_cpl,
                    'deskripsi_cpl' => $cpl->deskripsi_cpl,
                    'domain_kompetensi' => $cpl->domain_kompetensi,
                ],
            ], 201);
        }

        return redirect()
            ->route('kaprodi.cpls.index')
            ->with('success', 'Data CPL berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit CPL konvensional.
     * Dapat dipertahankan jika route edit masih digunakan.
     */
    public function edit(Cpl $cpl): View
    {
        return view('kaprodi.cpls.edit', compact('cpl'));
    }

    /**
     * Memperbarui data CPL.
     * Mendukung form biasa maupun AJAX/JSON
     * dari slide-over.
     */
    public function update(
        Request $request,
        Cpl $cpl
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            'kode_cpl' => [
                'required',
                'string',
                'max:20',
                Rule::unique('cpl', 'kode_cpl')
                    ->ignore($cpl->kode_cpl, 'kode_cpl'),
            ],
            'deskripsi_cpl' => [
                'required',
                'string',
            ],
            'domain_kompetensi' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $cpl->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data CPL berhasil diperbarui.',
                'cpl' => [
                    'id' => $cpl->id,
                    'kode_cpl' => $cpl->kode_cpl,
                    'deskripsi_cpl' => $cpl->deskripsi_cpl,
                    'domain_kompetensi' => $cpl->domain_kompetensi,
                ],
            ]);
        }

        return redirect()
            ->route('kaprodi.cpls.index')
            ->with('success', 'Data CPL berhasil diperbarui.');
    }

    /**
     * Menghapus data CPL.
     * CPL tidak boleh dihapus jika masih digunakan
     * oleh satu atau lebih periode PKL.
     */

/**
 * Menghapus data CPL.
 * CPL tidak boleh dihapus jika masih digunakan
 * oleh satu atau lebih periode PKL.
 */
    public function destroy(
        Request $request,
        Cpl $cpl
    ): RedirectResponse|JsonResponse {
        if ($cpl->pklPeriods()->exists()) {
            $message = 'CPL tidak dapat dihapus karena masih digunakan oleh periode PKL.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('kaprodi.cpls.index')
                ->with('error', $message);
        }

        $cpl->delete();

        $message = 'Data CPL berhasil dihapus.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('kaprodi.cpls.index')
            ->with('success', $message);
    }

}
