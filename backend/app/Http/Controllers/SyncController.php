<?php
namespace App\Http\Controllers;

use App\Services\Shopify\SyncProductsAction;
use Illuminate\Http\JsonResponse;


class SyncController extends Controller
{
    public function __construct(private SyncProductsAction $action) {}
    public function sync(): JsonResponse
    {
        $result = $this->action->handle();
        return response()->json(['message' => 'Sync completed', 'synced' => $result['count']]);
    }
}