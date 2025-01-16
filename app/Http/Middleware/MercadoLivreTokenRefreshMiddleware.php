<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class MercadoLivreTokenRefreshMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user AND $user->mercadolibre_expires_at AND $user->mercadolibre_expires_at->isPast()) {
            try {

                $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
                    'grant_type' => 'refresh_token',
                    'client_id' => env('MERCADOLIBRE_CLIENT_ID'),
                    'client_secret' => env('MERCADOLIBRE_CLIENT_SECRET'),
                    'refresh_token' => $user->mercadolibre_refresh_token,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $user->update([
                        'mercadolibre_token' => $data['access_token'],
                        'mercadolibre_refresh_token' => $data['refresh_token'] ?? $user->mercadolibre_refresh_token,
                        'mercadolibre_expires_at' => now()->addSeconds($data['expires_in']),
                    ]);
                } else {
                    logger()->error('Erro ao renovar o token do Mercado Livre.', ['response' => $response->json()]);
                }
            } catch (\Exception $e) {
                logger()->error('Erro ao renovar o token do Mercado Livre.', ['exception' => $e]);
            }
        }

        return $next($request);
    }
}
