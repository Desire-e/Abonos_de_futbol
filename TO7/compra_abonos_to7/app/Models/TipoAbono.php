<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Abono;


class TipoAbono extends Model {

    use HasUuids;

    protected $table = "tipo_abonos"; // Indica explícitamente qué tabla representa este modelo

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;  // indica que la tabla no tiene created_at ni updated_at


    protected $fillable = ['descripcion', 'precio', 'codigo', 'icono' ]; 



    public function abonos(): HasMany {
        return $this->hasMany(Abono::class, 'tipo');
    }

}
