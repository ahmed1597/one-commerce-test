<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jobs\ProcessShopifyProductWebhook;

class WebhookController extends Controller
{
    protected function verify(Request $request): bool
    {
        $secret = config('services.shopify.webhook_secret');
        $hmacHeader = $request->header('X-Shopify-Hmac-Sha256', '');
        $hmac = base64_decode($hmacHeader);
        $calc = hash_hmac('sha256', $request->getContent(), $secret, true);
        return hash_equals($hmac, $calc);
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
