<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $primaryKey = 'id';

    protected $fillable = [
        "payment_number",
        "transaction_number",
        "marketing_id",
        "date",
        "transaction_number",
        "payout_percentage",
        "payment_amount"
    ];
}
