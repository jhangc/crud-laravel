<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\apisnetController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\IniciarOpeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\IngresoExtraController;
use App\Http\Controllers\BovedaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CrediJoyaController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\CtsUsuarioController;
use App\Http\Controllers\CuentasController;
use App\Http\Controllers\DepositoCtsController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\GoldPriceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InicioDesembolsoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReprogramacionController;
use App\Http\Controllers\UpdateController;
use App\Models\InicioDesembolso;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//  return view('welcome');
//});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/admin/credito/estado', [UsuarioController::class, 'actualizarCreditosTerminados'])->name('creditos.actualizarTerminados');
//Route::get('/', function () { return view('admin'); });

Route::get('/', [AdminController::class, 'index'])->name('admin.index')->middleware('auth');
Route::get('/admin/credito/aprobar', [AdminController::class, 'aprobar'])->name('admin.aprobar')->middleware('auth');
Route::get('/admin/credito/rechazar', [AdminController::class, 'rechazar'])->name('admin.rechazar')->middleware('auth');
Route::get('/admin/credito/guardar', [AdminController::class, 'guardar'])->name('admin.guardar')->middleware('auth');
Route::get('/admin/credito/observar', [AdminController::class, 'observar'])->name('admin.observar')->middleware('auth');

Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index')->middleware('auth', 'can:usuarios.index');
Route::get('/admin/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create')->middleware('auth');
Route::post('/admin/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store')->middleware('auth');
Route::get('/admin/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show')->middleware('auth');
Route::get('/admin/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit')->middleware('auth');
Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update')->middleware('auth');
Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('auth');

Route::get('/admin/creditos', [CreditoController::class, 'index'])->name('creditos.index')->middleware('auth');

Route::get('/admin/creditos/simulador', [CreditoController::class, 'viewSimulador'])->name('creditos.simulador')->middleware('auth');
//ir al credito nuevo
Route::get('/admin/creditos/createnuevo', [CreditoController::class, 'createnuevo'])->name('creditos.createnuevo')->middleware('auth');
Route::get('/admin/creditos/proyecciones/{id}', [CreditoController::class, 'proyecciones'])->name('creditos.proyecciones')->middleware('auth');
Route::post('/admin/creditos/store', [CreditoController::class, 'store'])->name('creditos.store')->middleware('auth');
Route::get('/admin/creditos/aprobar', [CreditoController::class, 'viewaprobar'])->name('creditos.aprobar')->middleware('auth');
Route::get('/admin/creditos/supervisar', [CreditoController::class, 'viewsupervisar'])->name('creditos.supervisar')->middleware('auth');
Route::get('/admin/creditos/{id}/edit', [CreditoController::class, 'edit'])->name('creditos.edit')->middleware('auth');
Route::put('/admin/creditos/{id}', [CreditoController::class, 'update'])->name('creditos.update')->middleware('auth');
Route::delete('/admin/creditos/{id}', [CreditoController::class, 'destroy'])->name('creditos.destroy')->middleware('auth');

Route::get('/admin/creditos/comercio', [CreditoController::class, 'comercio'])->name('creditos.comercio')->middleware('auth');
Route::get('/admin/creditos/produccion', [CreditoController::class, 'produccion'])->name('creditos.produccion')->middleware('auth');
Route::get('/admin/creditos/servicio', [CreditoController::class, 'servicio'])->name('creditos.servicio')->middleware('auth');
Route::get('/admin/creditos/grupal', [CreditoController::class, 'grupal'])->name('creditos.grupal')->middleware('auth');
Route::get('/admin/creditos/agricola', [CreditoController::class, 'agricola'])->name('creditos.agricola')->middleware('auth');
Route::get('/admin/creditos/joya', [CreditoController::class, 'joya'])->name('creditos.joya')->middleware('auth');
//traer descricpones
Route::get('/admin/credito/descripcion', [CreditoController::class, 'getdescripciones'])->name('creditos.getdescripciones')->middleware('auth');
Route::get('/admin/cronograma/{id}/cuotas', [CronogramaController::class, 'vercuota'])->name('credito.cuotas')->middleware('auth');
Route::get('/admin/creditoinfo/{id}/', [CreditoController::class, 'show'])->name('credito.info')->middleware('auth');

Route::get('/admin/creditos/buscardni', [ClienteController::class, 'buscarPorDocumento'])->name('creditos.buscardni')->middleware('auth');
Route::get('/admin/creditos/agregardni', [ClienteController::class, 'agregarpordni'])->name('creditos.agregardni')->middleware('auth');
// Route::get('/admin/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show')->middleware('auth');



Route::get('/admin/clientes', [ClienteController::class, 'index'])->name('clientes.index')->middleware('auth');
Route::get('/admin/clientes/create', [ClienteController::class, 'create'])->name('clientes.create')->middleware('auth');
Route::post('/admin/clientes', [ClienteController::class, 'store'])->name('clientes.store')->middleware('auth');
Route::get('/admin/clientes/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit')->middleware('auth');
Route::put('/admin/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update')->middleware('auth');
Route::delete('/admin/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy')->middleware('auth');

Route::get('/admin/clientes/evaluar', [ClienteController::class, 'viewevaluar'])->name('clientes.evaluar')->middleware('auth');
Route::get('/admin/clientes/ratios', [ClienteController::class, 'viewratios'])->name('clientes.ratios')->middleware('auth');

Route::get('/admin/caja/arqueo', [CreditoController::class, 'viewarqueo'])->name('caja.arqueo')->middleware('auth');
Route::get('/admin/caja/habilitar', [CreditoController::class, 'viewhabilitarcaja'])->name('caja.abrir1')->middleware('auth');
Route::get('/admin/caja/pagarcredito', [CreditoController::class, 'viewpagarcredito'])->name('caja.pagarcredito')->middleware('auth');
Route::get('/admin/caja/pagares', [CreditoController::class, 'viewpagares'])->name('caja.pagares')->middleware('auth');
Route::get('/admin/caja/pagar/{id}', [CreditoController::class, 'pagar'])->name('caja.pagar')->middleware('auth');
Route::get('/admin/caja/cobrar', [CreditoController::class, 'viewcobrar'])->name('caja.cobrar')->middleware('auth');
Route::get('/admin/caja/ultima-transaccion/{caja}', [CreditoController::class, 'ultimaTransaccion'])->name('caja.ultimaTransaccion')->middleware('auth');
Route::post('/admin/caja/abrir', [CreditoController::class, 'abrirCaja'])->name('caja.abrir')->middleware('auth');
Route::post('/admin/caja/guardar-arqueo', [CreditoController::class, 'guardarArqueo'])->name('caja.guardarArqueo')->middleware('auth');


//CTS
Route::get('/admin/depositos-cts', [DepositoCtsController::class, 'index'])->name('depositos-cts.index')->middleware('auth');
Route::post('/admin/depositos-cts', [DepositoCtsController::class, 'store'])->name('depositos-cts.store')->middleware('auth');
Route::get('/admin/depositos-cts/{id}/edit', [DepositoCtsController::class, 'edit'])->name('depositos-cts.edit')->middleware('auth');
Route::delete('/admin/depositos-cts/{id}', [DepositoCtsController::class, 'destroy'])->name('depositos-cts.destroy')->middleware('auth');
Route::get('/admin/cts/depositoticket/{id}', [DepositoCtsController::class, 'ticket'])->name('depositos-cts.ticket')->middleware('auth');


Route::get('/admin/cts/ver-saldo', [CtsUsuarioController::class, 'index'])->name('cuenta-cts.index')->middleware('auth');
Route::post('/admin/cts/solicitud-pago', [DepositoCtsController::class, 'storeSolicitud'])->name('solicitud-cts.store')->middleware('auth');
Route::get('/admin/desembolso-cts', [DepositoCtsController::class, 'desembolsar'])->name('desembolso-cts.index')->middleware('auth');
Route::get('/admin/cts/pagar-desembolso/{id}', [DepositoCtsController::class, 'pagarDesembolso'])->name('desembolso-cts.pagar')->middleware('auth');

Route::get('/admin/cts/permisos', [InicioDesembolsoController::class, 'index'])->name('permiso-cts.index')->middleware('auth');
Route::post('/admin/cts/guardarpermiso', [InicioDesembolsoController::class, 'store'])->name('permiso-cts.store')->middleware('auth');
Route::get('/admin/cts/cerrarpermiso/{id}', [InicioDesembolsoController::class, 'cerrar'])->name('permiso-cts.cerrar')->middleware('auth');


Route::get('/admin/cobranza/cargarcompromiso', [CreditoController::class, 'viewcargarcompromiso'])->name('cobranza.cargarcompromiso')->middleware('auth');
Route::get('/admin/cobranza/carta', [CreditoController::class, 'viewcarta'])->name('cobranza.carta')->middleware('auth');
Route::get('/admin/cobranza/generarcompromiso', [CreditoController::class, 'viewgenerarcompromiso'])->name('cobranza.generarcompromiso')->middleware('auth');
Route::get('/admin/cobranza/generarnotificacion', [CreditoController::class, 'viewgenerarnotificacion'])->name('cobranza.generarnotificacion')->middleware('auth');

Route::get('/admin/reportes/creditoindividual', [ReporteController::class, 'viewreportecreditoindividual'])->name('reporte.creditoindividual')->middleware('auth');
Route::get('/admin/reportes/creditogrupal', [ReporteController::class, 'viewreportecreditogrupal'])->name('reporte.creditogrupal')->middleware('auth');
Route::get('/admin/reportes/interesesmensual', [ReporteController::class, 'viewreporteinteresesmensual'])->name('ReporteController.interesesmensual')->middleware('auth');

Route::get('/admin/reportes/prestamosactivos', [ReporteController::class, 'viewprestamosactivos'])->name('reporte.prestamosactivos')->middleware('auth');
Route::get('/admin/reportes/prestamosvencidos', [ReporteController::class, 'viewprestamosvencidos'])->name('reporte.prestamosvencidos')->middleware('auth');

Route::get('/admin/transacciones/egresos', [CreditoController::class, 'viewegresos'])->name('transacciones.egresos')->middleware('auth');
Route::get('/admin/transacciones/ingresos', [CreditoController::class, 'viewingresos'])->name('transacciones.ingresos')->middleware('auth');
//rutas de  update
Route::post('/admin/creditos/updatecomercio/{id}', [UpdateController::class, 'updatecomercio'])->name('creditos.updatecomercio')->middleware('auth');
Route::post('/admin/creditos/updategrupal/{id}', [UpdateController::class, 'updateCreditoGrupal'])->name('creditos.updatecreditogrupal')->middleware('auth');
Route::post('/admin/creditos/updateservicio/{id}', [UpdateController::class, 'updateCreditoServicio'])->name('creditos.updateCreditoServicio')->middleware('auth');
Route::post('/admin/creditos/updateprodcuccion/{id}', [UpdateController::class, 'updateCreditoProduccion'])->name('creditos.updateCreditoProduccion')->middleware('auth');
Route::post('/admin/creditos/updateagricola/{id}', [UpdateController::class, 'updateCreditoagricola'])->name('creditos.updateCreditoAgricola')->middleware('auth');

Route::get('/generar-pdf/{id}', [PDFController::class, 'generatePDF'])->name('generar-pdf');
Route::get('/generar-cronograma/{id}', [PDFController::class, 'generatecronogramaPDF'])->name('generar-cronograma');
Route::get('/generar-cronogramaindividual/{id}', [PDFController::class, 'generatecronogramaindividualPDF'])->name('generar-cronograma-individual');
Route::get('/generar-cronogramagrupal/{id}', [PDFController::class, 'generatecronogramagrupalPDF'])->name('generar-cronograma-grupal');

Route::get('/generar-contratogrupal/{id}', [PDFController::class, 'generatecontratogrupalPDF'])->name('generar-contrato-grupal');
Route::get('/generar-contratoindividual/{id}', [PDFController::class, 'generatecrontratoindividualPDF'])->name('generar-contrato-individual');

Route::get('/generar-cronogramacredijoya/{id}', [PDFController::class, 'generatecronogramaindividualC'])->name('generar-cronograma-credijoya');
Route::get('/generar-contratocredijoya/{id}', [PDFController::class, 'generatecrontratoindividualC'])->name('generar-contrato-credijoya');
Route::get('/generar-info-joya', [PDFController::class, 'genrarInfoC'])->name('generar-info-joya');

Route::get('/generar-cartilla/{id}', [PDFController::class, 'generatecartillaPDF'])->name('generar-cartilla');
Route::get('/generar-pagare/{id}', [PDFController::class, 'generatepagarePDF'])->name('generar-pagare');

Route::get('/generar-ticket-desembolso/{id}', [PDFController::class, 'generateticket'])->name('generar-ticket');

Route::get('/generar-carta-cobranza/{id}', [PDFController::class, 'generatecartacobranzaPDF'])->name('carta-cobranza-pdf');
Route::get('/generar-carta-cobranza-grupal/{id}', [PDFController::class, 'generatecartacobranzagrupalPDF'])->name('carta-cobranza-grupal-pdf');

Route::get('/generar-detalle-cliente/{id}', [PDFController::class, 'generatedetalleclientePDF'])->name('detalle-cliente-pdf');


Route::get('/inicio-operaciones', [IniciarOpeController::class, 'index'])->name('inicio_operaciones.index')->middleware('auth');
Route::post('/inicio-operaciones', [IniciarOpeController::class, 'store'])->name('inicio_operaciones.store')->middleware('auth');
Route::post('/inicio-operaciones/start/{id}', [IniciarOpeController::class, 'start'])->name('inicio_operaciones.start')->middleware('auth');
Route::post('/inicio-operaciones/close', [IniciarOpeController::class, 'close'])->name('inicio_operaciones.close')->middleware('auth');


Route::get('/getProvincias/{dep_id}', [ClienteController::class, 'getProvincias'])->name('clientes.getProvincias')->middleware('auth');
Route::get('/getDistritos/{prov_id}', [ClienteController::class, 'getDistritos'])->name('clientes.getDistritos')->middleware('auth');

//para exportar excel
Route::get('/admin/reportes/clientes/export', [ExcelController::class, 'export'])->name('reporte.clientes.export')->middleware('auth');
Route::get('/admin/reportes/credito/exportcreditoactivo', [ExcelController::class, 'exportPrestamosActivos'])->name('reporte.prestamos.exportactivos')->middleware('auth');
Route::get('/admin/reportes/credito/exportarcreditosindividual', [ExcelController::class, 'exportarCreditoIndividual'])->name('reporte.clientes.exportcreditoindividual')->middleware('auth');
Route::get('/admin/reportes/credito/exportarcreditosgrupal', [ExcelController::class, 'exportarCreditoGrupal'])->name('reporte.clientes.exportcreditogrupal')->middleware('auth');

Route::get('/admin/creditos/vercuotaspago/{id}', [CreditoController::class, 'verpagocuota'])->name('creditos.verpagocuota')->middleware('auth');
Route::post('/admin/creditos/pagocuota', [CreditoController::class, 'pagocuota'])->name('creditos.pagocuota')->middleware('auth');
Route::post('/admin/creditos/pagocuotagrupal', [CreditoController::class, 'pagoGrupal'])->name('creditos.pagogrupal')->middleware('auth');
Route::get('/admin/generar-ticket-pago/{id}/{diferencia}', [PDFController::class, 'generarTicketDePago'])->name('generar.ticket.pago');
Route::get('/admin/generar-ticket-pagogrupal/{array}', [PDFController::class, 'generarTicketDePagogrupal'])->name('generar.ticket.pagogrupal');

Route::get('/storage/foto/{filename}', [FileController::class, 'getFoto'])->name('getFoto');
Route::get('/storage/pdf/{filename}', [FileController::class, 'getPdf'])->name('getPdf');

Route::get('/admin/creditos/ingresosday', [AdminController::class, 'ingresosday'])->name('creditos.ingresosday')->middleware('auth');

Route::get('/admin/caja/obtener-transacciones/{id}', [AdminController::class, 'obtenerTransaccionesCaja'])->name('creditos.obtenerTransaccionesCaja')->middleware('auth');
Route::get('/admin/caja/resetcaja/{id}', [AdminController::class, 'resetCaja'])->name('creditos.resetCaja')->middleware('auth');

// Rutas para la gestión de gastos
Route::get('/admin/gastos', [GastoController::class, 'index'])->name('gastos.index')->middleware('auth');
Route::get('/admin/gastos/{id}/edit', [GastoController::class, 'edit'])->name('gastos.edit')->middleware('auth');
Route::post('/admin/gastos', [GastoController::class, 'store'])->name('gastos.store')->middleware('auth');
Route::delete('/admin/gastos/{id}', [GastoController::class, 'destroy'])->name('gastos.destroy')->middleware('auth');
//rutas de gestion de  ingresos
Route::get('/admin/ingresos-extras', [IngresoExtraController::class, 'index'])->name('ingresos-extras.index')->middleware('auth');
Route::get('/admin/ingresos-extras/{id}', [IngresoExtraController::class, 'edit'])->name('ingresos-extras.edit')->middleware('auth');
Route::post('/admin/ingresos-extras', [IngresoExtraController::class, 'store'])->name('ingresos-extras.store')->middleware('auth');
Route::delete('/admin/ingresos-extras/{id}', [IngresoExtraController::class, 'destroy'])->name('ingresos-extras.destroy')->middleware('auth');

//reporte de cja diairo
Route::get('/admin/generar-transacciones-pdf/{caja_id}', [PDFController::class, 'generarTransaccionesPDF'])->name('caja.generarTransaccionesPDF');
Route::get('/admin/caja/generar-arqueo-pdf/{id}', [PdfController::class, 'generarArqueoPDF'])->name('pdf.generarArqueoPDF');

Route::get('/admin/boveda', [BovedaController::class, 'index'])->name('boveda.index')->middleware('auth');
Route::get('/admin/boveda/{id}/edit', [BovedaController::class, 'edit'])->name('boveda.edit')->middleware('auth');
Route::post('/admin/boveda', [BovedaController::class, 'store'])->name('boveda.store')->middleware('auth');
Route::post('/admin/boveda/{id}', [BovedaController::class, 'store'])->name('boveda.update')->middleware('auth');
Route::delete('/admin/boveda/{id}', [BovedaController::class, 'destroy'])->name('boveda.destroy')->middleware('auth');

// Movimientos de Bóveda
Route::get('/admin/boveda/{id}/movimientos', [BovedaController::class, 'movimientos'])->name('boveda.movimientos')->middleware('auth');
Route::post('/admin/boveda/{id}/movimientos', [BovedaController::class, 'agregarMovimiento'])->name('boveda.movimientos.store')->middleware('auth');
Route::get('/admin/boveda/{id}/movimientos/{movimientoId}/edit', [BovedaController::class, 'editarMovimiento'])->name('boveda.movimientos.edit')->middleware('auth');
Route::post('/admin/boveda/{id}/movimientos/{movimientoId}', [BovedaController::class, 'actualizarMovimiento'])->name('boveda.movimientos.update')->middleware('auth');
Route::delete('/admin/boveda/{id}/movimientos/{movimientoId}', [BovedaController::class, 'eliminarMovimiento'])->name('boveda.movimientos.destroy')->middleware('auth');
//Pagos
Route::post('/admin/credito/verpagototal', [CreditoController::class, 'verpagototalindividual'])->name('credito.verpagototalindividual')->middleware('auth');
Route::post('/admin/credito/verpagototalgrupal', [CreditoController::class, 'verpagototalgrupal'])->name('credito.verpagototalgrupal')->middleware('auth');
Route::post('/credito/confirmar-pago-individual', [CreditoController::class, 'confirmarPagoIndividual'])->name('credito.confirmarPagoIndividual')->middleware('auth');
Route::post('/credito/confirmar-pago-grupal', [CreditoController::class, 'confirmarPagoGrupal'])->name('credito.confirmarPagoGrupal')->middleware('auth');

//contabilidad
Route::get('/admin/cuentas', [CuentasController::class, 'index'])->name('cuentas.index')->middleware('auth');
Route::post('/cuentas', [CuentasController::class, 'store'])->name('cuentas.store')->middleware('auth');
Route::get('/cuentas/{id}/edit', [CuentasController::class, 'edit'])->name('cuentas.edit')->middleware('auth');
Route::put('/cuentas/{id}', [CuentasController::class, 'update'])->name('cuentas.update')->middleware('auth');
Route::delete('/cuentas/{id}', [CuentasController::class, 'destroy'])->name('cuentas.destroy')->middleware('auth');

Route::post('/calcular-cuota-pendiente', [CreditoController::class, 'calcularCuotaPendiente'])->name('calcular.cuota.pendiente')->middleware('auth');
Route::post('/generarcronogram/temp/', [CreditoController::class, 'generarNuevoCronograma'])->name('generar.nuevo.cronograma');
Route::post('/generarcronogram/final', [CreditoController::class, 'amortizarCapital'])->name('amortizar.capital')->middleware('auth');
Route::get('/vernuevocronograma/{id}', [PDFController::class, 'generarNuevoCronogramaPDF'])->name('generar.pdf.nuevo cronograma');
Route::get('/admin/generar-ticket-pagototal-individual/{array}', [PDFController::class, 'Pagototalindividual'])->name('generar.pdf.Pagototalindividual');

//Reprogramacion de credito
Route::post('/solicitar/reprogramacion', [CreditoController::class, 'solicitarReprogramacion'])->name('solicitar.reprogramacion')->middleware('auth');
Route::post('/reprogramaciones/store', [ReprogramacionController::class, 'reprogramacionStore'])->name('reprogramacion.store')->middleware('auth');
Route::get('/admin/creditos/aprobarreprogramados', [ReprogramacionController::class, 'viewreprogramacion'])->name('reprogramacion.index')->middleware('auth');
Route::post('/reprogramaciones/process', [ReprogramacionController::class, 'process'])->name('reprogramacion.process')->middleware('auth');
Route::post('/generarcronogramreprogramado', [CreditoController::class, 'generarreprogramacion'])->name('reprogramacion.exitosa')->middleware('auth');
Route::get('/vernuevocronogramareprogramado/{id}', [PDFController::class, 'generarNuevoCronogramaReprogramadoPDF'])->name('generar.pdf.nuevo cronogramareprogramado')->middleware('auth');
// Vista CRUD
Route::get('/admin/precios-oro', [GoldPriceController::class, 'index'])->name('preciosoro.index')->middleware('auth');
// API CRUD
Route::get('/admin/precios-oro/list',   [GoldPriceController::class, 'list'])->name('preciosoro.list')->middleware('auth');
Route::post('/admin/precios-oro',       [GoldPriceController::class, 'store'])->name('preciosoro.store')->middleware('auth');
Route::put('/admin/precios-oro/{goldPrice}',  [GoldPriceController::class, 'update'])->name('preciosoro.update')->middleware('auth');
Route::delete('/admin/precios-oro/{goldPrice}', [GoldPriceController::class, 'destroy'])->name('preciosoro.destroy')->middleware('auth');
// Endpoint para CrediJoya (autollenarPrecioOro)
Route::get('/admin/credijoya/precio-oro', [GoldPriceController::class, 'vigente'])->middleware('auth');
Route::get('/admin/credijoya/deuda-previa', [CrediJoyaController::class, 'deudaPrevia'])->name('credijoya.deuda_previa')->middleware('auth');
Route::post('admin/credijoya/store',[CrediJoyaController::class, 'store'])->middleware('auth');
Route::post('/admin/credijoya/{id}/aprobar', [CrediJoyaController::class, 'aprobarCredijoya'])->name('credijoya.aprobar')->middleware('auth');
Route::post('/admin/credijoya/{id}/rechazar', [CrediJoyaController::class, 'rechazarCredijoya'])->name('credijoya.rechazar')->middleware('auth');
Route::post('/admin/credijoya/update/{id}', [CrediJoyaController::class, 'update'])->name('credijoya.update')->middleware('auth');

Route::get('admin/caja/credijoya/{id}/pagar', [CrediJoyaController::class,'pagar'])->name('credijoya.desembolso')->middleware('auth');
Route::get('admin/caja/credijoya/cuotas-pendientes', [CrediJoyaController::class,'cuotasPendientes'])->name('credijoya.cuotasPendientes')->middleware('auth'); // ?credito_id=XX
Route::post('admin/caja/credijoya/{id}/desembolsar', [CrediJoyaController::class,'desembolsar'])->name('credijoya.desembolsar')->middleware('auth');

Route::get('admin/credijoya/ticket-pagos/{prestamo}/{caja}/{ids}', [CrediJoyaController::class, 'ticketPagosAnterioresCJ'])->name('credijoya.ticketPagos')->middleware('auth');
Route::get('admin/credijoya/ticket-desembolso/{prestamo}', [CrediJoyaController::class, 'ticketDesembolsoCJ'])->name('credijoya.ticketDesembolso')->middleware('auth');
///
Route::get('admin/credijoya/{credito}/pago', [CrediJoyaController::class, 'createPago'])->name('pagocredijoya.create')->middleware('auth');
Route::post('admin/credijoya/{credito}/pago', [CrediJoyaController::class, 'storePago'])->name('pagocredijoya.store')->middleware('auth');
Route::get('admin/credijoya/ticket-pago/{pago}', [CrediJoyaController::class, 'ticketPago'])->name('pagocredijoya.ticket')->middleware('auth');

Route::get('admin/credijoya/devoluciones', [DevolucionController::class,'index'])->name('devoluciones.index')->middleware('auth');
Route::get('admin/credijoya/devoluciones/list', [DevolucionController::class,'list'])->name('devoluciones.list')->middleware('auth');
Route::get('admin/credijoya/devoluciones/calcular-custodia/{credito}', [DevolucionController::class,'calcularCustodia'])->name('devoluciones.calcular')->middleware('auth');
Route::post('admin/credijoya/devoluciones/pagar-custodia/{credito}', [DevolucionController::class,'pagarCustodia'])->name('devoluciones.pagar')->middleware('auth');
Route::post('admin/credijoya/devoluciones/devolver/{credito}', [DevolucionController::class,'devolver'])->name('devoluciones.devolver')->middleware('auth');
// Tickets/PDF
Route::get('admin/credijoya/devoluciones/ticket-custodia/{ingresoExtra}', [DevolucionController::class,'ticketCustodia'])->name('devoluciones.ticket.custodia')->middleware('auth');
Route::get('admin/credijoya/devoluciones/hoja-devolucion/{credito}', [DevolucionController::class,'hojaDevolucion'])->name('devoluciones.hoja')->middleware('auth');
//api
Route::get('/testapi/{dni}', [apisnetController::class, 'index'])->name('test.api')->middleware('auth');