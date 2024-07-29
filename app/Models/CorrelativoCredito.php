<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;

class CorrelativoCredito extends Model
{
    use HasFactory;

    protected $table = 'correlativos_creditos';

    protected $fillable = ['id_prestamo', 'id_cliente', 'serie', 'correlativo'];

    // Define the relationship with the Credito model
    public function prestamo()
    {
        return $this->belongsTo(credito::class, 'id_prestamo');
    }

    // Define the relationship with the Cliente model
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public static function generateCorrelativosGrupales($idPrestamo, $serie = '0001')
    {
        DB::transaction(function () use ($idPrestamo, $serie) {
            $credito = credito::find($idPrestamo);
            if (!$credito) {
                throw new \Exception('Crédito no encontrado');
            }

            // Verificar si ya existen correlativos para este préstamo
            $existenCorrelativos = self::where('id_prestamo', $idPrestamo)->exists();
            if ($existenCorrelativos) {
                return; // Si ya existen correlativos, no generar nuevos
            }

            // Obtener el correlativo más reciente dentro de la misma serie
            $lastCorrelativo = self::where('serie', $serie)->orderBy('correlativo', 'desc')->first();
            $newCorrelativo = $lastCorrelativo ? intval($lastCorrelativo->correlativo) + 1 : 1;
            $correlativo = str_pad($newCorrelativo, 4, '0', STR_PAD_LEFT);
            $correlativoGeneral = $correlativo;

            // Guardar correlativo general
            self::create([
                'id_prestamo' => $idPrestamo,
                'id_cliente' => null,
                'serie' => $serie,
                'correlativo' => $correlativoGeneral // Guardamos el correlativo general
            ]);

            // Generar correlativos para los integrantes del grupo
            $creditoClientes = CreditoCliente::where('prestamo_id', $idPrestamo)->get();
            foreach ($creditoClientes as $index => $creditoCliente) {
                $integranteCorrelativo = $correlativoGeneral . '-' . ($index + 1);

                self::create([
                    'id_prestamo' => $idPrestamo,
                    'id_cliente' => $creditoCliente->cliente_id,
                    'serie' => $serie,
                    'correlativo' => $integranteCorrelativo // Guardamos el correlativo del integrante
                ]);
            }
        });
    }
}
