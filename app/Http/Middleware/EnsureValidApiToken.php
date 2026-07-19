<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return $this->unauthenticated();
        }

        $client = ApiClient::query()
            ->where('token_hash', ApiClient::hashToken($token))
            ->first();

        if (! $client || ! $client->is_active) {
            return $this->unauthenticated();
        }

        $client->forceFill(['last_used_at' => now()])->save();

        return $next($request);
    }

    private function unauthenticated(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
