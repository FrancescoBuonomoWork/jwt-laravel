<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'description_product', 
        'quantity', 
        'unit_price', 
    ];
    public function payment(){
        return $this->belongsTo(Payment::class);
    }
}
