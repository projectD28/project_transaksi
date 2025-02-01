<?php

namespace App\Http\Controllers;


use App\Models\Pembayaran;
use App\Models\PembayaranRekap;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PaymentController extends Controller
{

    public function Pembayaran()
    {
        $payment = Pembayaran::get();

        return response()->json([
            'message' => 'success',
            'code' => 200,
            'data' => $payment
        ]);
    }

    public function BuatPembayaran(Request $request)
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

                $pembayaranrekap = PembayaranRekap::firstOrNew(['transaction_number' => $pembayaran->transaction_number]);

                // Hitung sisa pembayaran
                $SisaPembayaran = intval(($pembayaranrekap->remaining_payment ?? $penjualan->grand_total) - $pembayaran->payment_amount);

                // Update data pembayaran
                $pembayaranrekap->transaction_number = $pembayaran->transaction_number;
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
