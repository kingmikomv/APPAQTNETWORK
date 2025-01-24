<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyXenditSignature
{
    public function handle(Request $request, Closure $next): Response
{
    // Ambil X-CALLBACK-TOKEN dari header
    $callbackToken = $request->header('x-callback-token');

    // Tetapkan Secret Key langsung di kode
    $secretKey = 'B9kqMxq7DnSO6EliPpYh9Q8kbaKWUl9yO6agmemPdxaPZaIM';

    // Data payload dari body request
    $payload = $request->getContent();

    // Buat HMAC hash untuk verifikasi signature
    $computedSignature = hash_hmac('sha256', $payload, $secretKey);

    // Bandingkan token yang diterima dengan token yang dihitung
    if ($callbackToken !== $computedSignature) {
        // Jika token tidak cocok, kembalikan respon error
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Jika token valid, teruskan request
    return $next($request);
}

}
