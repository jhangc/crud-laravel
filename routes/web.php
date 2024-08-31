<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\IniciarOpeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\IngresoExtraController;
use App\Http\Controllers\BovedaController;
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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route::get('/', function () { return view('admin'); });

Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.index')->middleware('auth');
Route::get('/admin/credito/aprobar', [App\Http\Controllers\AdminController::class, 'aprobar'])->name('admin.aprobar')->middleware('auth');
Route::get('/admin/credito/rechazar', [App\Http\Controllers\AdminController::class, 'rechazar'])->name('admin.rechazar')->middleware('auth');
Route::get('/admin/credito/guardar', [App\Http\Controllers\AdminController::class, 'guardar'])->name('admin.guardar')->middleware('auth');
Route::get('/admin/credito/observar', [App\Http\Controllers\AdminController::class, 'observar'])->name('admin.observar')->middleware('auth');

Route::get('/admin/usuarios', [App\Http\Controllers\UsuarioController::class, 'index'])->name('usuarios.index')->middleware('auth','can:usuarios.index');
Route::get('/admin/usuarios/create', [App\Http\Controllers\UsuarioController::class, 'create'])->name('usuarios.create')->middleware('auth');
Route::post('/admin/usuarios', [App\Http\Controllers\UsuarioController::class, 'store'])->name('usuarios.store')->middleware('auth');
Route::get('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'show'])->name('usuarios.show')->middleware('auth');
Route::get('/admin/usuarios/{id}/edit', [App\Http\Controllers\UsuarioController::class, 'edit'])->name('usuarios.edit')->middleware('auth');
Route::put('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'update'])->name('usuarios.update')->middleware('auth');
Route::delete('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('auth');

Route::get('/admin/creditos', [App\Http\Controllers\creditoController::class, 'index'])->name('creditos.index')->middleware('auth');

Route::get('/admin/creditos/simulador', [App\Http\Controllers\creditoController::class, 'viewSimulador'])->name('creditos.simulador')->middleware('auth');
//ir al credito nuevo
Route::get('/admin/creditos/createnuevo', [App\Http\Controllers\creditoController::class, 'createnuevo'])->name('creditos.createnuevo')->middleware('auth');
Route::get('/admin/creditos/proyecciones/{id}', [App\Http\Controllers\creditoController::class, 'proyecciones'])->name('creditos.proyecciones')->middleware('auth');
Route::post('/admin/creditos/store', [App\Http\Controllers\creditoController::class, 'store'])->name('creditos.store')->middleware('auth');
Route::get('/admin/creditos/aprobar', [App\Http\Controllers\creditoController::class, 'viewaprobar'])->name('creditos.aprobar')->middleware('auth');
Route::get('/admin/creditos/supervisar', [App\Http\Controllers\creditoController::class, 'viewsupervisar'])->name('creditos.supervisar')->middleware('auth');
Route::get('/admin/creditos/{id}/edit', [App\Http\Controllers\creditoController::class, 'edit'])->name('creditos.edit')->middleware('auth');
Route::put('/admin/creditos/{id}', [App\Http\Controllers\creditoController::class, 'update'])->name('creditos.update')->middleware('auth');
Route::delete('/admin/creditos/{id}', [App\Http\Controllers\creditoController::class, 'destroy'])->name('creditos.destroy')->middleware('auth');

Route::get('/admin/creditos/comercio', [App\Http\Controllers\creditoController::class, 'comercio'])->name('creditos.comercio')->middleware('auth');
Route::get('/admin/creditos/produccion', [App\Http\Controllers\creditoController::class, 'produccion'])->name('creditos.produccion')->middleware('auth');
Route::get('/admin/creditos/servicio', [App\Http\Controllers\creditoController::class, 'servicio'])->name('creditos.servicio')->middleware('auth');
Route::get('/admin/creditos/grupal', [App\Http\Controllers\creditoController::class, 'grupal'])->name('creditos.grupal')->middleware('auth');
Route::get('/admin/creditos/agricola', [App\Http\Controllers\creditoController::class, 'agricola'])->name('creditos.agricola')->middleware('auth');

//traer descricpones
Route::get('/admin/credito/descripcion', [App\Http\Controllers\creditoController::class, 'getdescripciones'])->name('creditos.getdescripciones')->middleware('auth');
Route::get('/admin/cronograma/{id}/cuotas', [App\Http\Controllers\CronogramaController::class, 'vercuota'])->name('credito.cuotas')->middleware('auth');
Route::get('/admin/creditoinfo/{id}/', [App\Http\Controllers\creditoController::class, 'show'])->name('credito.info')->middleware('auth');

Route::get('/admin/creditos/buscardni', [App\Http\Controllers\clienteController::class, 'buscarPorDocumento'])->name('creditos.buscardni')->middleware('auth');
Route::get('/admin/creditos/agregardni', [App\Http\Controllers\clienteController::class, 'agregarpordni'])->name('creditos.agregardni')->middleware('auth');
// Route::get('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'show'])->name('usuarios.show')->middleware('auth');



Route::get('/admin/clientes', [App\Http\Controllers\clienteController::class, 'index'])->name('clientes.index')->middleware('auth');
Route::get('/admin/clientes/create', [App\Http\Controllers\clienteController::class, 'create'])->name('clientes.create')->middleware('auth');
Route::post('/admin/clientes', [App\Http\Controllers\clienteController::class, 'store'])->name('clientes.store')->middleware('auth');
Route::get('/admin/clientes/{id}/edit', [App\Http\Controllers\clienteController::class, 'edit'])->name('clientes.edit')->middleware('auth');
Route::put('/admin/clientes/{id}', [App\Http\Controllers\clienteController::class, 'update'])->name('clientes.update')->middleware('auth');
Route::delete('/admin/clientes/{id}', [App\Http\Controllers\clienteController::class, 'destroy'])->name('clientes.destroy')->middleware('auth');

Route::get('/admin/clientes/evaluar', [App\Http\Controllers\clienteController::class, 'viewevaluar'])->name('clientes.evaluar')->middleware('auth');
Route::get('/admin/clientes/ratios', [App\Http\Controllers\clienteController::class, 'viewratios'])->name('clientes.ratios')->middleware('auth');

Route::get('/admin/caja/arqueo', [App\Http\Controllers\creditoController::class, 'viewarqueo'])->name('caja.arqueo')->middleware('auth');
Route::get('/admin/caja/habilitar', [App\Http\Controllers\creditoController::class, 'viewhabilitarcaja'])->name('caja.abrir1')->middleware('auth');
Route::get('/admin/caja/pagarcredito', [App\Http\Controllers\creditoController::class, 'viewpagarcredito'])->name('caja.pagarcredito')->middleware('auth');
Route::get('/admin/caja/pagares', [App\Http\Controllers\creditoController::class, 'viewpagares'])->name('caja.pagares')->middleware('auth');
Route::get('/admin/caja/pagar/{id}', [App\Http\Controllers\creditoController::class, 'pagar'])->name('caja.pagar')->middleware('auth');
Route::get('/admin/caja/cobrar', [App\Http\Controllers\creditoController::class, 'viewcobrar'])->name('caja.cobrar')->middleware('auth');
Route::get('/admin/caja/ultima-transaccion/{caja}', [App\Http\Controllers\creditoController::class, 'ultimaTransaccion'])->name('caja.ultimaTransaccion')->middleware('auth');
Route::post('/admin/caja/abrir', [App\Http\Controllers\creditoController::class, 'abrirCaja'])->name('caja.abrir')->middleware('auth');
Route::post('/admin/caja/guardar-arqueo', [App\Http\Controllers\creditoController::class, 'guardarArqueo'])->name('caja.guardarArqueo')->middleware('auth');




Route::get('/admin/cobranza/cargarcompromiso', [App\Http\Controllers\creditoController::class, 'viewcargarcompromiso'])->name('cobranza.cargarcompromiso')->middleware('auth');
Route::get('/admin/cobranza/carta', [App\Http\Controllers\creditoController::class, 'viewcarta'])->name('cobranza.carta')->middleware('auth');
Route::get('/admin/cobranza/generarcompromiso', [App\Http\Controllers\creditoController::class, 'viewgenerarcompromiso'])->name('cobranza.generarcompromiso')->middleware('auth');
Route::get('/admin/cobranza/generarnotificacion', [App\Http\Controllers\creditoController::class, 'viewgenerarnotificacion'])->name('cobranza.generarnotificacion')->middleware('auth');

Route::get('/admin/reportes/creditoindividual', [App\Http\Controllers\ReporteController::class, 'viewreportecreditoindividual'])->name('reporte.creditoindividual')->middleware('auth');
Route::get('/admin/reportes/creditogrupal', [App\Http\Controllers\ReporteController::class, 'viewreportecreditogrupal'])->name('reporte.creditogrupal')->middleware('auth');

Route::get('/admin/reportes/prestamosactivos', [App\Http\Controllers\ReporteController::class, 'viewprestamosactivos'])->name('reporte.prestamosactivos')->middleware('auth');
Route::get('/admin/reportes/prestamosvencidos', [App\Http\Controllers\ReporteController::class, 'viewprestamosvencidos'])->name('reporte.prestamosvencidos')->middleware('auth');

Route::get('/admin/transacciones/egresos', [App\Http\Controllers\creditoController::class, 'viewegresos'])->name('transacciones.egresos')->middleware('auth');
Route::get('/admin/transacciones/ingresos', [App\Http\Controllers\creditoController::class, 'viewingresos'])->name('transacciones.ingresos')->middleware('auth');
//rutas de  update
Route::post('/admin/creditos/updatecomercio/{id}', [App\Http\Controllers\UpdateController::class, 'updatecomercio'])->name('creditos.updatecomercio')->middleware('auth');
Route::post('/admin/creditos/updategrupal/{id}', [App\Http\Controllers\UpdateController::class, 'updateCreditoGrupal'])->name('creditos.updatecreditogrupal')->middleware('auth');
Route::post('/admin/creditos/updateservicio/{id}', [App\Http\Controllers\UpdateController::class, 'updateCreditoServicio'])->name('creditos.updateCreditoServicio')->middleware('auth');
Route::post('/admin/creditos/updateprodcuccion/{id}', [App\Http\Controllers\UpdateController::class, 'updateCreditoProduccion'])->name('creditos.updateCreditoProduccion')->middleware('auth');
Route::post('/admin/creditos/updateagricola/{id}', [App\Http\Controllers\UpdateController::class, 'updateCreditoagricola'])->name('creditos.updateCreditoAgricola')->middleware('auth');

Route::get('/generar-pdf/{id}', [PDFController::class, 'generatePDF'])->name('generar-pdf');
Route::get('/generar-cronograma/{id}', [PDFController::class, 'generatecronogramaPDF'])->name('generar-cronograma');
Route::get('/generar-cronogramaindividual/{id}', [PDFController::class, 'generatecronogramaindividualPDF'])->name('generar-cronograma-individual');
Route::get('/generar-cronogramagrupal/{id}', [PDFController::class, 'generatecronogramagrupalPDF'])->name('generar-cronograma-grupal');

Route::get('/generar-contratogrupal/{id}', [PDFController::class, 'generatecontratogrupalPDF'])->name('generar-contrato-grupal');
Route::get('/generar-contratoindividual/{id}', [PDFController::class, 'generatecrontratoindividualPDF'])->name('generar-contrato-individual');

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


Route::get('/getProvincias/{dep_id}', [App\Http\Controllers\clienteController::class, 'getProvincias'])->name('clientes.getProvincias')->middleware('auth');
Route::get('/getDistritos/{prov_id}', [App\Http\Controllers\clienteController::class, 'getDistritos'])->name('clientes.getDistritos')->middleware('auth');

//para exportar excel
Route::get('/admin/reportes/clientes/export', [App\Http\Controllers\ExcelController::class, 'export'])->name('reporte.clientes.export')->middleware('auth');
Route::get('/admin/reportes/credito/exportcreditoactivo', [App\Http\Controllers\ExcelController::class, 'exportPrestamosActivos'])->name('reporte.prestamos.exportactivos')->middleware('auth');
Route::get('/admin/reportes/credito/exportarcreditosindividual', [App\Http\Controllers\ExcelController::class, 'exportarCreditoIndividual'])->name('reporte.clientes.exportcreditoindividual')->middleware('auth');
Route::get('/admin/reportes/credito/exportarcreditosgrupal', [App\Http\Controllers\ExcelController::class, 'exportarCreditoGrupal'])->name('reporte.clientes.exportcreditogrupal')->middleware('auth');

Route::get('/admin/creditos/vercuotaspago/{id}', [App\Http\Controllers\creditoController::class, 'verpagocuota'])->name('creditos.verpagocuota')->middleware('auth');
Route::post('/admin/creditos/pagocuota', [App\Http\Controllers\creditoController::class, 'pagocuota'])->name('creditos.pagocuota')->middleware('auth');
Route::post('/admin/creditos/pagocuotagrupal', [App\Http\Controllers\creditoController::class, 'pagoGrupal'])->name('creditos.pagogrupal')->middleware('auth');
Route::get('/admin/generar-ticket-pago/{id}', [PDFController::class, 'generarTicketDePago'])->name('generar.ticket.pago');
Route::get('/admin/generar-ticket-pagogrupal/{array}', [PDFController::class, 'generarTicketDePagogrupal'])->name('generar.ticket.pagogrupal');

Route::get('/storage/foto/{filename}', [FileController::class, 'getFoto'])->name('getFoto');
Route::get('/storage/pdf/{filename}', [FileController::class, 'getPdf'])->name('getPdf');

Route::get('/admin/creditos/ingresosday', [App\Http\Controllers\AdminController::class, 'ingresosday'])->name('creditos.ingresosday')->middleware('auth');
Route::get('/admin/creditos/egresosday', [App\Http\Controllers\AdminController::class, 'updateCreditoagricola'])->name('creditos.updateCreditoAgricola')->middleware('auth');
Route::get('/admin/caja/obtener-transacciones/{id}', [App\Http\Controllers\AdminController::class, 'obtenerTransaccionesCaja'])->name('creditos.obtenerTransaccionesCaja')->middleware('auth');
Route::get('/admin/caja/resetcaja/{id}', [App\Http\Controllers\AdminController::class, 'resetCaja'])->name('creditos.resetCaja')->middleware('auth');

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
Route::post('/admin/credito/verpagototal', [App\Http\Controllers\creditoController::class, 'verpagototalindividual'])->name('credito.verpagototalindividual')->middleware('auth');
Route::post('/admin/credito/verpagototalgrupal', [App\Http\Controllers\creditoController::class, 'verpagototalgrupal'])->name('credito.verpagototalgrupal')->middleware('auth');
Route::post('/credito/confirmar-pago-individual', [App\Http\Controllers\creditoController::class, 'confirmarPagoIndividual'])->name('credito.confirmarPagoIndividual')->middleware('auth');
Route::post('/credito/confirmar-pago-grupal', [App\Http\Controllers\creditoController::class, 'confirmarPagoGrupal'])->name('credito.confirmarPagoGrupal')->middleware('auth');



