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


    // Aplicar, a nivel de código, una relación entre las tablas abonos y tipo_abonos
    // - No crea la relación en la base de datos
    // - La declara en Laravel para que Eloquent sepa cómo están relacionadas
    public function abonos(): HasMany {
        return $this->hasMany(Abono::class, 'tipo');
        // Como decir: 
        // "Un TipoAbono puede estar relacionado con muchos Abono, y
        // esa relación se guarda en la columna 'tipo' de la tabla 'abonos'."

        // La tabla que tiene la foreign key -- belongsTo
        // La tabla referenciada -- hasMany / hasOne
    }

}
