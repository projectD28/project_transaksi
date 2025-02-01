<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
use App\Models\Pembayaran;
use App\Models\PembayaranRekap;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function KomisiPenjualan()
    {
        $sales = Penjualan::with("marketing")->get()->groupBy([
            function ($d) {
                return Carbon::parse($d->date)->translatedFormat('F');
            },
            'marketing.name',
        ]);

        $Komisi = [];
        foreach ($sales as $mon =>  $sale) {

            foreach ($sale as $key => $value) {
                $totalbalance = $value->sum("total_balance");

                if ($totalbalance <= 100000000) {
                    $jmlkomisi = 0;
                    $komisinominal = 0;
                } else if ($totalbalance >= 100000000  && $totalbalance <= 200000000) {
                    $jmlkomisi = 2.5;
                    $komisinominal = $totalbalance * $jmlkomisi / 100;
                } else if ($totalbalance >= 200000000 && $totalbalance <= 500000000) {
                    $jmlkomisi = 5;
                    $komisinominal = $totalbalance * $jmlkomisi / 100;
                } else if ($totalbalance >= 500000000) {
                    $jmlkomisi = 10;
                    $komisinominal = $totalbalance * $jmlkomisi / 100;
                }

                $Komisi[] = array(
                    "marketing" => $key,
                    "bulan" => $mon,
                    "omzet" => $totalbalance,
                    "komisi" => $jmlkomisi,
                    "komisi_nominal" => $komisinominal
                );
            }
        }

        return response()->json([
            'message' => 'success',
            'code' => 200,
            'data' => $Komisi
        ]);
    }

    public function Pembayaran(Request $request)
    {
        $this->validate($request, [
            "transaction_number" => "required|string",
            "tanggal_pembayaran" => "required|date",
            "jumlah_pembayaran" => "required|integer"
        ]);

        try {

            DB::transaction(function () use ($request) {
                // get data penjualan
                $penjualan = Penjualan::where("transaction_number", $request->transaction_number)->first();

                // last id pembayaran
                $lastid = Pembayaran::orderBy("id")->first();

                // format number payment
                $FormatNumberPayment = "PY" . (($lastid->id ?? 0) + 1);

                // memasukan data
                $pembayaran = Pembayaran::create([
                    "payment_number" => $FormatNumberPayment,
                    "transaction_number" => $penjualan->transaction_number,
                    "marketing_id" => $penjualan->marketing_id,
                    "date" => $request->tanggal_pembayaran,
                    "payment_amount" => $request->jumlah_pembayaran,
                ]);

                $pembayaranrekap = PembayaranRekap::firstOrNew(['payment_number' => $pembayaran->payment_number]);

                // Hitung sisa pembayaran
                $SisaPembayaran = intval(($pembayaranrekap->remaining_payment ?? $penjualan->grand_total) - $pembayaran->payment_amount);

                // Update data pembayaran
                $pembayaranrekap->payment_number = $pembayaran->payment_number;
                $pembayaranrekap->date = $pembayaran->date;
                $pembayaranrekap->payment_amount += $pembayaran->payment_amount;
                $pembayaranrekap->remaining_payment = $SisaPembayaran;

                // Simpan perubahan
                $pembayaranrekap->save();
            });




            return response()->json([
                'message' => 'success',
                'code' => 200,
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $th->getMessage(),  // Mengirimkan pesan error yang lebih jelas
                'code' => 500,
            ], 500);
        }
    }
}
