<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_name',
        'vat_number',
        'city',
        'cap',
        'province',
        'email',
        'mobile_phone',
        'user_id',
        'image'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
