<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProductController, SyncController, WebhookController};

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);


Route::post('/sync/products', [SyncController::class, 'sync']);


Route::post('/webhooks/shopify/products/create', [WebhookController::class, 'productCreate']);
Route::post('/webhooks/shopify/products/update', [WebhookController::class, 'productUpdate']);
Route::post('/webhooks/shopify/products/delete', [WebhookController::class, 'productDelete']);