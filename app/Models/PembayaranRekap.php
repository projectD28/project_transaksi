<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranRekap extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_rekap';

    protected $primaryKey = 'id';

    protected $fillable = [
        "payment_number",
        "transaction_number",
        "date",
        "payout_percentage",
        "payment_amount",
        "remaining_payment"
    ];
}
