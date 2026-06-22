@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1><i class="fas fa-terminal"></i> Console Access</h1>
            <p class="text-muted mb-2">
                Terminal del servidor. Navega con <code>cd</code> / <code>ls</code> y ejecuta cualquier comando
                (git pull, php artisan migrate, etc.). Cada comando queda registrado en
                <code>storage/logs/consola.log</code>.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div id="terminal" class="consola-terminal" tabindex="0">
                <div id="consola-output"></div>
                <div class="consola-input-line">
                    <span id="consola-prompt" class="consola-prompt"></span>
                    <input type="text" id="consola-command" class="consola-input" autocomplete="off"
                        autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="escribe un comando y presiona Enter…">
                    <span id="consola-spinner" class="consola-spinner" style="display:none;">⏳</span>
                </div>
            </div>
            <small class="text-muted">↑/↓ historial · <code>clear</code> limpia la pantalla</small>
        </div>
    </div>

    <style>
        .consola-terminal {
            background: #0d1117;
            color: #d1d5da;
            font-family: "Consolas", "Courier New", monospace;
            font-size: 13px;
            line-height: 1.45;
            padding: 12px 14px;
            border-radius: 6px;
            height: 70vh;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
            border: 1px solid #30363d;
        }
        .consola-terminal:focus { outline: 1px solid #2f81f7; }
        .consola-output-block { margin: 0 0 6px 0; }
        .consola-cmd-echo { color: #58a6ff; }
        .consola-err { color: #ff7b72; }
        .consola-input-line { display: flex; align-items: center; }
        .consola-prompt { color: #3fb950; margin-right: 6px; white-space: nowrap; }
        .consola-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #d1d5da;
            font-family: inherit;
            font-size: inherit;
            outline: none;
            padding: 0;
        }
        .consola-spinner { margin-left: 8px; }
    </style>

    <script>
        (function () {
            const csrf       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const executeUrl = "{{ route('consola.execute') }}";

            const terminal = document.getElementById('terminal');
            const output   = document.getElementById('consola-output');
            const input    = document.getElementById('consola-command');
            const promptEl = document.getElementById('consola-prompt');
            const spinner  = document.getElementById('consola-spinner');

            let cwd     = @json($cwd);
            let history = [];
            let histIdx = -1;

            function renderPrompt() {
                promptEl.textContent = cwd + ' $';
            }

            function append(text, cssClass) {
                if (text === '' || text === null || text === undefined) return;
                const block = document.createElement('div');
                block.className = 'consola-output-block' + (cssClass ? ' ' + cssClass : '');
                block.textContent = text;
                output.appendChild(block);
            }

            function scrollDown() {
                terminal.scrollTop = terminal.scrollHeight;
            }

            renderPrompt();

            // Click en la terminal enfoca el input.
            terminal.addEventListener('click', function () { input.focus(); });
            input.focus();

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const cmd = input.value;
                    if (cmd.trim() === '') return;
                    runCommand(cmd);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (history.length === 0) return;
                    if (histIdx === -1) histIdx = history.length;
                    histIdx = Math.max(0, histIdx - 1);
                    input.value = history[histIdx] || '';
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (history.length === 0) return;
                    histIdx = Math.min(history.length, histIdx + 1);
                    input.value = histIdx === history.length ? '' : (history[histIdx] || '');
                }
            });

            function runCommand(cmd) {
                // Eco del comando con el prompt actual.
                append(cwd + ' $ ' + cmd, 'consola-cmd-echo');
                history.push(cmd);
                histIdx = -1;
                input.value = '';
                input.disabled = true;
                spinner.style.display = 'inline';
                scrollDown();

                fetch(executeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ command: cmd }),
                })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res) {
                    const d = res.d || {};
                    if (d.clear) {
                        output.innerHTML = '';
                    } else if (!res.ok) {
                        append((d.message || 'Error en el servidor') , 'consola-err');
                        if (d.errors && d.errors.command) append(d.errors.command.join('\n'), 'consola-err');
                    } else {
                        append(d.output || '', d.error ? 'consola-err' : '');
                    }
                    if (d.cwd) { cwd = d.cwd; renderPrompt(); }
                })
                .catch(function (err) {
                    append('Error de red: ' + err, 'consola-err');
                })
                .finally(function () {
                    input.disabled = false;
                    spinner.style.display = 'none';
                    input.focus();
                    scrollDown();
                });
            }
        })();
    </script>
@endsection
