<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;

class apisnetController extends Controller
{
   public function index($dni)
    {
        // Si prefieres, deja tu token aquí "hardcodeado" para la prueba
        $token = 'sk_9795.6RFNhNAXHqOWTaRyMsABG8iPxT1i9Fl3';
        // Cliente Guzzle
        $client = new Client([
            'base_uri' => 'https://api.decolecta.com',
            'verify'   => false, // equivalente a CURLOPT_SSL_VERIFYPEER = 0 (para pruebas)
            'timeout'  => 8,
        ]);

        // Parámetros de la petición (equivalente a tu curl_setopt_array)
        $options = [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Referer'       => 'https://apis.net.pe/consulta-dni-api',
                'Accept'        => 'application/json',
                'User-Agent'    => 'laravel/guzzle',
            ],
            'query' => [
                'numero' => $dni,
            ],
        ];

        // Llamada: GET /v1/reniec/dni?numero=XXXXXXX
        $res = $client->get('/v1/reniec/dni', $options);

        $status = $res->getStatusCode();
        $json   = json_decode($res->getBody()->getContents(), true);

        // Opcional: ver crudo
        // dd($json);

        if ($status !== 200) {
            return response()->json([
                'ok'      => false,
                'status'  => $status,
                'message' => $json['message'] ?? 'No se pudo consultar el DNI.',
                'raw'     => $json,
            ], $status ?: 500);
        }

        // Igual que tu var_dump($persona); pero en JSON para consumo desde JS
        return response()->json([
            'ok'   => true,
            'data' => $json,
        ]);
    
    }
}
