<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;

/**
 * Panel "Console Access": terminal web para el rol oculto super_system.
 *
 * Permite navegar por el servidor (cd / ls) y ejecutar cualquier comando
 * (git pull, artisan, etc.) sin necesidad de SSH. Mantiene el directorio
 * actual en sesión y registra TODO en storage/logs/consola.log.
 *
 * El acceso está restringido por middleware ['auth','role:super_system'].
 */
class ConsolaController extends Controller
{
    /** Tiempo máximo (segundos) por comando. */
    private const TIMEOUT = 120;

    /**
     * Comandos DESTRUCTIVOS que pueden borrar toda la base de datos o archivos.
     * Se bloquean por defecto; para forzarlos hay que anteponer "FORCE " al comando,
     * tras verificar el directorio/.env correcto. Esto evita catástrofes como correr
     * `migrate:fresh` apuntando a la base equivocada.
     */
    private const DANGEROUS = '/(\bmigrate:(fresh|reset|refresh|rollback)\b|\bdb:wipe\b|\bdrop\s+database\b|\bdrop\s+table\b|\btruncate\b|\brm\s+-[rf])/i';

    public function index()
    {
        $cwd = $this->currentDir();

        return view('admin.consola.index', [
            'cwd' => $cwd,
        ]);
    }

    public function execute(Request $request)
    {
        $request->validate([
            'command' => 'required|string|max:4000',
        ]);

        $command = trim($request->input('command'));
        $cwd     = $this->currentDir();

        // Confirmación explícita: anteponer "FORCE " permite ejecutar un comando destructivo.
        $forced = false;
        if (stripos($command, 'FORCE ') === 0) {
            $command = trim(substr($command, 6));
            $forced  = true;
        }

        $this->audit(($forced ? 'FORCE ' : '') . $command, $cwd);

        // Escudo anti-catástrofes: bloquea comandos que borran datos hasta confirmar.
        if (! $forced && preg_match(self::DANGEROUS, $command)) {
            return response()->json([
                'cwd'    => $cwd,
                'output' => "⛔ COMANDO BLOQUEADO POR SEGURIDAD\n\n"
                    . "Este comando puede BORRAR datos de la base/archivos del proyecto en:\n"
                    . "    {$cwd}\n\n"
                    . "Antes de ejecutarlo, verifica que estás en el proyecto correcto y revisa su base de datos:\n"
                    . "    cat .env | grep DB_DATABASE\n\n"
                    . "Si de verdad quieres ejecutarlo, vuelve a escribirlo anteponiendo FORCE:\n"
                    . "    FORCE {$command}",
                'error'  => true,
            ]);
        }

        // Comandos especiales que se manejan en PHP (no en el shell).
        if (preg_match('/^cd(?:\s+(.*))?$/i', $command, $m)) {
            $target = isset($m[1]) ? trim($m[1]) : '';
            $new    = $this->resolvePath($cwd, $target);

            if ($new === null) {
                return response()->json([
                    'cwd'    => $cwd,
                    'output' => 'cd: no existe el directorio: ' . ($target !== '' ? $target : '~'),
                    'error'  => true,
                ]);
            }

            session(['consola_cwd' => $new]);

            return response()->json(['cwd' => $new, 'output' => '', 'error' => false]);
        }

        if (strtolower($command) === 'pwd') {
            return response()->json(['cwd' => $cwd, 'output' => $cwd, 'error' => false]);
        }

        // Solo en Windows: cmd.exe no tiene `ls`, así que lo emulamos por comodidad.
        // En Linux se usa el `ls` real del sistema, y el `dir` nativo de Windows
        // también pasa sin tocar: NO se limita ningún comando del servidor.
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0 && preg_match('/^(ls|ll)\b(.*)$/i', $command, $m)) {
            return response()->json($this->listDirectory($cwd, trim($m[2])));
        }

        if (in_array(strtolower($command), ['clear', 'cls'], true)) {
            return response()->json(['cwd' => $cwd, 'output' => '', 'error' => false, 'clear' => true]);
        }

        [$out, $err] = $this->runShell($command, $cwd);

        $combined = rtrim($out);
        if (trim($err) !== '') {
            $combined .= ($combined !== '' ? "\n" : '') . rtrim($err);
        }

        return response()->json([
            'cwd'    => $cwd,
            'output' => $combined,
            'error'  => trim($err) !== '' && trim($out) === '',
        ]);
    }

    /** Directorio actual de la sesión (por defecto, la raíz del proyecto). */
    private function currentDir(): string
    {
        $cwd = session('consola_cwd', base_path());

        if (! is_string($cwd) || ! is_dir($cwd)) {
            $cwd = base_path();
            session(['consola_cwd' => $cwd]);
        }

        return $cwd;
    }

    /** Resuelve un destino de `cd` (absoluto o relativo) a una ruta real existente. */
    private function resolvePath(string $cwd, string $target): ?string
    {
        if ($target === '' || $target === '~') {
            return base_path();
        }

        $isAbsolute = (bool) preg_match('#^([a-zA-Z]:[\\\\/]|[\\\\/])#', $target);
        $path       = $isAbsolute ? $target : rtrim($cwd, '/\\') . DIRECTORY_SEPARATOR . $target;

        $real = realpath($path);

        return ($real !== false && is_dir($real)) ? $real : null;
    }

    /** Lista el contenido de un directorio (built-in de ls/dir), igual en Windows y Linux. */
    private function listDirectory(string $cwd, string $args): array
    {
        // Se ignoran las banderas (-l, -la, /w, etc.); el primer argumento que no sea
        // bandera se toma como ruta destino.
        $target = '';
        foreach (preg_split('/\s+/', $args, -1, PREG_SPLIT_NO_EMPTY) as $tok) {
            if ($tok[0] === '-' || $tok[0] === '/') {
                continue;
            }
            $target = trim($tok, "\"'");
            break;
        }

        $dir = $target === '' ? $cwd : $this->resolvePath($cwd, $target);

        if ($dir === null || ! is_dir($dir)) {
            return ['cwd' => $cwd, 'output' => 'ls: no existe el directorio: ' . ($target !== '' ? $target : $cwd), 'error' => true];
        }

        $entries = @scandir($dir);
        if ($entries === false) {
            return ['cwd' => $cwd, 'output' => 'ls: no se pudo leer el directorio: ' . $dir, 'error' => true];
        }

        $lines = [];
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') {
                continue;
            }
            $full  = $dir . DIRECTORY_SEPARATOR . $e;
            $isDir = is_dir($full);
            $size  = $isDir ? '<DIR>' : (string) (@filesize($full) ?: 0);
            $mtime = @date('Y-m-d H:i', @filemtime($full) ?: 0);
            $lines[] = sprintf('%s  %12s  %s  %s', $isDir ? 'd' : '-', $size, $mtime, $e . ($isDir ? '/' : ''));
        }

        return ['cwd' => $cwd, 'output' => $lines ? implode("\n", $lines) : '(directorio vacío)', 'error' => false];
    }

    /** Ejecuta el comando. Usa Symfony Process; si proc_open está deshabilitado, cae a shell_exec. */
    private function runShell(string $command, string $cwd): array
    {
        $env = $this->buildEnv();

        if (function_exists('proc_open')) {
            try {
                $process = Process::fromShellCommandline($command, $cwd, $env, null, self::TIMEOUT);
                $process->run();

                return [$process->getOutput(), $process->getErrorOutput()];
            } catch (\Throwable $e) {
                return ['', $e->getMessage()];
            }
        }

        if (function_exists('shell_exec')) {
            $prev = getcwd();
            // Aplica el mismo entorno aislado: setea PATH y ELIMINA (putenv sin '=')
            // las variables del .env de esta app, para no contaminar el proyecto hijo.
            foreach ($env as $key => $val) {
                putenv($val === false ? $key : "{$key}={$val}");
            }
            @chdir($cwd);
            $out = @shell_exec($command . ' 2>&1');
            @chdir($prev !== false ? $prev : $cwd);

            return [$out ?? '', ''];
        }

        return ['', 'No se pudo ejecutar: proc_open y shell_exec están deshabilitados en este servidor.'];
    }

    /**
     * Construye el entorno del proceso con un PATH aumentado para que se encuentren
     * git, php, composer, etc. — incluso si el servidor web no los tiene en su PATH.
     * Añade rutas típicas de Laragon (Windows), el directorio del binario de PHP, y
     * las que definas en .env como CONSOLA_EXTRA_PATHS (separadas por ; en Windows o : en Linux).
     */
    private function buildEnv(): array
    {
        $isWindows = strncasecmp(PHP_OS, 'WIN', 3) === 0;

        $path = getenv('PATH');
        if ($path === false || $path === '') {
            $path = getenv('Path') ?: '';
        }

        $extra = [];

        // Directorio del binario de PHP (para que 'php artisan' funcione).
        if (defined('PHP_BINARY') && PHP_BINARY) {
            $extra[] = dirname(PHP_BINARY);
        }

        // Rutas típicas de Laragon en Windows (git, composer, etc.).
        if ($isWindows) {
            $extra[] = 'C:\\laragon\\bin\\git\\bin';
            $extra[] = 'C:\\laragon\\bin\\git\\cmd';
            $extra[] = 'C:\\laragon\\bin\\git\\mingw64\\bin';
            $extra[] = 'C:\\laragon\\bin\\composer';
        }

        // Rutas personalizadas desde .env.
        if ($cfg = env('CONSOLA_EXTRA_PATHS')) {
            foreach (explode(PATH_SEPARATOR, $cfg) as $d) {
                $extra[] = $d;
            }
        }

        foreach ($extra as $d) {
            $d = trim($d);
            if ($d !== '' && is_dir($d) && stripos($path, $d) === false) {
                $path .= PATH_SEPARATOR . $d;
            }
        }

        $env = ['PATH' => $path];

        // CRÍTICO — aislamiento del .env por proyecto.
        // Laravel publica las variables de su .env como variables de entorno del
        // proceso (putenv), y Symfony Process las HEREDA al proceso hijo. Como el
        // .env de Laravel es inmutable, un `php artisan` ejecutado en OTRO proyecto
        // NO puede sobrescribir esas variables ya presentes y termina usando la BASE
        // DE DATOS de ESTA app (la consola) en lugar de la suya.
        // Eso provocó que `migrate:fresh` corriera contra la BD equivocada.
        // Solución: eliminar (unset) en el hijo todas las claves del .env de esta app,
        // para que cada proyecto lea su PROPIO .env.
        foreach ($this->hostEnvKeys() as $key) {
            $env[$key] = false; // En Symfony Process, false elimina la variable en el hijo.
        }

        return $env;
    }

    /** Claves del .env de ESTA app, que NO deben filtrarse a los procesos hijos. */
    private function hostEnvKeys(): array
    {
        $keys    = [];
        $envFile = base_path('.env');

        if (is_file($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = ltrim($line);
                if ($line === '' || $line[0] === '#') {
                    continue;
                }
                if (preg_match('/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)\s*=/', $line, $m)) {
                    $keys[] = $m[1];
                }
            }
        }

        // Respaldo (por si el .env no es legible): las variables que más daño causan.
        return array_values(array_unique(array_merge($keys, [
            'APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL',
            'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
            'CACHE_DRIVER', 'SESSION_DRIVER', 'QUEUE_CONNECTION', 'BROADCAST_DRIVER', 'FILESYSTEM_DISK',
        ])));
    }

    /** Registra cada comando ejecutado para auditoría. */
    private function audit(string $command, string $cwd): void
    {
        $user = Auth::user();

        $line = sprintf(
            "[%s] user=%s(#%s) ip=%s cwd=%s :: %s\n",
            now()->toDateTimeString(),
            $user->name ?? '?',
            $user->id ?? '?',
            request()->ip(),
            $cwd,
            $command
        );

        @file_put_contents(storage_path('logs/consola.log'), $line, FILE_APPEND);
    }
}
