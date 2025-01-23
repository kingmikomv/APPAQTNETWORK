<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyXenditSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil Xendit-Callback-Signature dari header
        $signature = $request->header('X-CALLBACK-SIGNATURE');
        $secretKey = env('xnd_development_pvO9UBoEWcj1zWGdIVBmO0CLkiPGgIIZEHXfHL42EZIy5TA3CtuaXp39AUzB55xC'); // Simpan secret key di .env

        // Data payload
        $payload = $request->getContent();

        // Buat HMAC hash untuk verifikasi
        $computedSignature = hash_hmac('sha256', $payload, $secretKey);

        if ($signature !== $computedSignature) {
            // Jika signature tidak cocok, kembalikan respon error
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
