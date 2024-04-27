<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('/admin/usuarios', [App\Http\Controllers\UsuarioController::class, 'index'])->name('usuarios.index')->middleware('auth','can:usuarios.index');
Route::get('/admin/usuarios/create', [App\Http\Controllers\UsuarioController::class, 'create'])->name('usuarios.create')->middleware('auth');
Route::post('/admin/usuarios', [App\Http\Controllers\UsuarioController::class, 'store'])->name('usuarios.store')->middleware('auth');
Route::get('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'show'])->name('usuarios.show')->middleware('auth');
Route::get('/admin/usuarios/{id}/edit', [App\Http\Controllers\UsuarioController::class, 'edit'])->name('usuarios.edit')->middleware('auth');
Route::put('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'update'])->name('usuarios.update')->middleware('auth');
Route::delete('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('auth');

Route::get('/admin/creditos', [App\Http\Controllers\creditoController::class, 'index'])->name('creditos.index')->middleware('auth');
Route::get('/admin/creditos/create', [App\Http\Controllers\creditoController::class, 'create'])->name('creditos.create')->middleware('auth');
Route::post('/admin/creditos', [App\Http\Controllers\creditoController::class, 'store'])->name('creditos.store')->middleware('auth');
Route::get('/admin/creditos/aprobar', [App\Http\Controllers\creditoController::class, 'viewaprobar'])->name('creditos.aprobar')->middleware('auth');
Route::get('/admin/creditos/supervisar', [App\Http\Controllers\creditoController::class, 'viewsupervisar'])->name('creditos.supervisar')->middleware('auth');


Route::get('/admin/creditos/buscardni', [App\Http\Controllers\clienteController::class, 'buscarPorDocumento'])->name('creditos.buscardni')->middleware('auth');
Route::get('/admin/creditos/agregardni', [App\Http\Controllers\clienteController::class, 'agregarpordni'])->name('creditos.agregardni')->middleware('auth');


Route::get('/admin/clientes', [App\Http\Controllers\clienteController::class, 'index'])->name('clientes.index')->middleware('auth');
Route::get('/admin/clientes/create', [App\Http\Controllers\clienteController::class, 'create'])->name('clientes.create')->middleware('auth');
Route::post('/admin/clientes', [App\Http\Controllers\clienteController::class, 'store'])->name('clientes.store')->middleware('auth');
Route::get('/admin/clientes/{id}/edit', [App\Http\Controllers\clienteController::class, 'edit'])->name('clientes.edit')->middleware('auth');
Route::put('/admin/clientes/{id}', [App\Http\Controllers\clienteController::class, 'update'])->name('clientes.update')->middleware('auth');
Route::delete('/admin/clientes/{id}', [App\Http\Controllers\clienteController::class, 'destroy'])->name('clientes.destroy')->middleware('auth');

Route::get('/admin/clientes/evaluar', [App\Http\Controllers\clienteController::class, 'viewevaluar'])->name('clientes.evaluar')->middleware('auth');
Route::get('/admin/clientes/ratios', [App\Http\Controllers\clienteController::class, 'viewratios'])->name('clientes.ratios')->middleware('auth');

Route::get('/admin/caja/arqueo', [App\Http\Controllers\creditoController::class, 'viewarqueo'])->name('caja.arqueo')->middleware('auth');
Route::get('/admin/caja/habilitar', [App\Http\Controllers\creditoController::class, 'viewhabilitarcaja'])->name('caja.habilitar')->middleware('auth');
Route::get('/admin/caja/pagarcredito', [App\Http\Controllers\creditoController::class, 'viewpagarcredito'])->name('caja.pagarcredito')->middleware('auth');
Route::get('/admin/caja/pagares', [App\Http\Controllers\creditoController::class, 'viewpagares'])->name('caja.pagares')->middleware('auth');

Route::get('/admin/cobranza/cargarcompromiso', [App\Http\Controllers\creditoController::class, 'viewcargarcompromiso'])->name('cobranza.cargarcompromiso')->middleware('auth');
Route::get('/admin/cobranza/carta', [App\Http\Controllers\creditoController::class, 'viewcarta'])->name('caja.habilitar')->middleware('auth');
Route::get('/admin/cobranza/generarcompromiso', [App\Http\Controllers\creditoController::class, 'viewgenerarcompromiso'])->name('cobranza.generarcompromiso')->middleware('auth');
Route::get('/admin/cobranza/generarnotificacion', [App\Http\Controllers\creditoController::class, 'viewgenerarnotificacion'])->name('cobranza.generarnotificacion')->middleware('auth');

Route::get('/admin/reportes/clientes', [App\Http\Controllers\clienteController::class, 'viewreportecliente'])->name('reporte.cliente')->middleware('auth');
Route::get('/admin/reportes/prestamosactivos', [App\Http\Controllers\creditoController::class, 'viewprestamosactivos'])->name('reporte.prestamosactivos')->middleware('auth');
Route::get('/admin/reportes/prestamosvencidos', [App\Http\Controllers\creditoController::class, 'viewprestamosvencidos'])->name('reporte.prestamosvencidos')->middleware('auth');

Route::get('/admin/transacciones/egresos', [App\Http\Controllers\creditoController::class, 'viewegresos'])->name('transacciones.egresos')->middleware('auth');
Route::get('/admin/transacciones/ingresos', [App\Http\Controllers\creditoController::class, 'viewingresos'])->name('transacciones.ingresos')->middleware('auth');
