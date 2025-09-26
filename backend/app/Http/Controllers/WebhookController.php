<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessShopifyProductWebhook;

class WebhookController extends Controller
{
    protected function verify(Request $request): bool
    {
        $secret     = trim((string) config('services.shopify.webhook_secret', ''));
        $hmacHeader = trim((string) $request->header('X-Shopify-Hmac-Sha256', ''));
        $raw = $request->getContent();
        if ($raw === '' || $raw === null) {
            $raw = file_get_contents('php://input');
        }
        $calcB64 = base64_encode(hash_hmac('sha256', $raw, $secret, true));
        $ok = hash_equals($hmacHeader, $calcB64);
        return $ok;
     }


    public function productCreate(Request $request)
    {
        if (!$this->verify($request)) return response('Invalid HMAC', Response::HTTP_FORBIDDEN);
        dispatch(new ProcessShopifyProductWebhook('create', $request->json()->all()));
        return response('OK', 200);
    }


    public function productUpdate(Request $request)
    {
        if (!$this->verify($request)) return response('Invalid HMAC', Response::HTTP_FORBIDDEN);
        dispatch(new ProcessShopifyProductWebhook('update', $request->json()->all()));
        return response('OK', 200);
    }


    public function productDelete(Request $request)
    {
        if (!$this->verify($request)) return response('Invalid HMAC', Response::HTTP_FORBIDDEN);
        dispatch(new ProcessShopifyProductWebhook('delete', $request->json()->all()));
        return response('OK', 200);
    }
}
