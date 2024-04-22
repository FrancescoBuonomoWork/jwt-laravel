<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'amount',
        'payment_method',
        'exipered_date',
        'company_id',
        'token'
    ];
    //  /**
    //  * The attributes that should be used as aliases.
    //  *
    //  * @var array
    //  */
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::bootAttributeNames();
    // }

    // protected static function bootAttributeNames()
    // {
    //     static::retrieved(function ($model) {
    //         $model->setAppends([]);
    //     });

    //     static::saved(function ($model) {
    //         $model->setAppends([]);
    //     });

    //     static::setAttributeNames([
    //         'name' => 'Nome',
    //         'description' => 'Descrizione',
    //         'amount' => 'Importo',
    //         'payment_method' => 'Metodo di pagamento',
    //         'exipered_date' => 'Data di scadenza'
    //     ]);
    // }
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
}
