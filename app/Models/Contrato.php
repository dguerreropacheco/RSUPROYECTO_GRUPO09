<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Contrato extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contratos';

    protected $fillable = [
        'personal_id',
        'tipo_contrato',
        'fecha_inicio',
        'fecha_fin',
        'salario',
        'departamento_id',  // ← FK a departamentos
        'periodo_prueba',
        'observaciones',
        'motivo_terminacion',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'deleted_at' => 'datetime',  // ← IMPORTANTE para SoftDeletes
        'activo' => 'boolean',
        'salario' => 'decimal:2',
    ];

    // Relaciones
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        $hoy = Carbon::today();
        return $query->where('activo', true)
                     ->where('fecha_inicio', '<=', $hoy)
                     ->where(function ($q) use ($hoy) {
                         $q->whereNull('fecha_fin')
                           ->orWhere('fecha_fin', '>=', $hoy);
                     });
    }

    public function scopePermanentes($query)
    {
        return $query->where('tipo_contrato', 'permanente');
    }

    public function scopeTemporales($query)
    {
        return $query->where('tipo_contrato', 'temporal');
    }

    // Accessors
    public function getEsPermanenteAttribute()
    {
        return $this->tipo_contrato === 'permanente';
    }

    public function getEsTemporalAttribute()
    {
        return $this->tipo_contrato === 'temporal';
    }

    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_fin) {
            return null; // Permanente
        }
        
        $hoy = Carbon::today();
        $fin = Carbon::parse($this->fecha_fin);
        
        return $hoy->diffInDays($fin, false);
    }

    public function getEstaVigenteAttribute()
    {
        $hoy = Carbon::today();
        $inicioValido = Carbon::parse($this->fecha_inicio)->lte($hoy);
        $finValido = !$this->fecha_fin || Carbon::parse($this->fecha_fin)->gte($hoy);
        
        return $this->activo && $inicioValido && $finValido;
    }

    public function getSalarioFormateadoAttribute()
    {
        return $this->salario ? 'S/ ' . number_format($this->salario, 2) : 'No especificado';
    }

    // Métodos auxiliares
    public function tieneDerechodVacaciones()
    {
        return $this->tipo_contrato === 'permanente';
    }

    public function enPeriodoPrueba()
    {
        if (!$this->periodo_prueba) {
            return false;
        }

        $fechaFinPrueba = Carbon::parse($this->fecha_inicio)->addMonths($this->periodo_prueba);
        return Carbon::today()->lte($fechaFinPrueba);
    }
}