<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('pages.laporan.index');
    }

    public function generate(Request $request)
    {
        // Validate the request data
        $request->validate([
            'jenis_laporan' => 'required|string',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        // Process the report generation logic here
        // This could involve querying the database, generating a PDF, etc.

        // For demonstration, we'll just return a success message
        return response()->json([
            'message' => 'Laporan berhasil dibuat.',
            'data' => [
                'jenis_laporan' => $request->jenis_laporan,
                'tanggal_awal' => $request->tanggal_awal,
                'tanggal_akhir' => $request->tanggal_akhir,
            ],
        ]);
    }
}
