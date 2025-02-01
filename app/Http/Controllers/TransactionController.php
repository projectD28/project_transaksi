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
    public function Penjualan()  {
        $sales = Penjualan::with("marketing")->get();

        return response()->json([
            'message' => 'success',
            'code' => 200,
            'data' => $sales
        ]);
    }

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



   
}
