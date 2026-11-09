<?php

namespace App\Observers;

use App\Models\Credito;
use App\Models\Ingreso;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Mantiene el estado del crédito al día cada vez que se registra, revierte o
 * restablece un pago: al liquidarse la última cuota pasa a "terminado" y, si se
 * revierte un pago de un crédito terminado, vuelve a "pagado" (Activo).
 *
 * La sincronización se hace una sola vez por crédito tras el commit de la
 * transacción (los pagos grupales crean varios ingresos en un mismo request).
 */
class IngresoObserver
{
    /** @var array<int, true> créditos pendientes de sincronizar en este request */
    private static array $pendientes = [];

    public function created(Ingreso $ingreso): void
    {
        $this->programar($ingreso);
    }

    public function deleted(Ingreso $ingreso): void
    {
        $this->programar($ingreso);
    }

    public function restored(Ingreso $ingreso): void
    {
        $this->programar($ingreso);
    }

    private function programar(Ingreso $ingreso): void
    {
        $id = (int) $ingreso->prestamo_id;
        if (!$id || isset(self::$pendientes[$id])) {
            return;
        }
        self::$pendientes[$id] = true;

        // Si hay transacción abierta espera al commit (no corre si hay rollback);
        // si no la hay, se ejecuta de inmediato.
        DB::afterCommit(function () use ($id) {
            unset(self::$pendientes[$id]);
            try {
                Credito::find($id)?->sincronizarEstadoPorPagos();
            } catch (\Throwable $e) {
                Log::error('No se pudo sincronizar el estado del crédito', [
                    'credito_id' => $id,
                    'error'      => $e->getMessage(),
                ]);
            }
        });
    }
}
