<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // para generar UUID
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TipoAbono;


// Su padre es Eloquent Model -- dota de metodos para llamarlos en el controlador, de acceso a la BD
// En si, el modelo viene "casi hecho".

class Abono extends Model {

    use HasUuids; // para generar UUID

    protected $table = 'abonos'; // indica explícitamente qué tabla representa este modelo

    protected $primaryKey = 'id';     // indica campo al que se asigna UUID generado por HasUUID
    public $incrementing = false;     // hace $primaryKey NO AUTOINCREMENT
    protected $keyType = 'string';    // hace $primaryKey tipo string

    public $timestamps = false;  // indica que la tabla no tiene created_at ni updated_at



    // Seguridad: indica los nombres de los campos de la BD que se pueden rellenar 
    // por el usuario mediante Abono::create($request->all())
    // Si se intenta rellenar otros campos de la BD por usuario malintencionado, lo impide.
    protected $fillable = [
        // 'id',  // omito id pues no lo voy a autogenerar yo ni lo  inserta el usuario; lo hace use HasUuide
        'fecha', 'abonado', 'edad', 'telefono', 'cuenta_bancaria', 'tipo', 'asiento', 'precio', 
    ];



    // Aplicar, a nivel de código, una relación entre las tablas abonos y tipo_abonos
    public function tipoAbono(): BelongsTo{
        // "Este Abono pertenece a un TipoAbono, y la relación se 
        // guarda en la columna 'tipo' de la tabla 'abonos'."
        return $this->belongsTo(TipoAbono::class, 'tipo');
    }
}
