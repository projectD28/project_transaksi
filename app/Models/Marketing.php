<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Penjualan;

class Marketing extends Model
{
    use HasFactory;

    protected $table = 'marketing';

    protected $primaryKey = 'id';

    public function penjualan()
    {
        return  $this->hasMany(Penjualan::class, "marketing_id", 'id');
    }
}
