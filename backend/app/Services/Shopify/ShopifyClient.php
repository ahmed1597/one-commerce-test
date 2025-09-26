<?php 
namespace App\Services\Shopify;


use GuzzleHttp\Client;


class ShopifyClient
{
   public function __construct(private Client $http = new Client()) {}


    public function fetchProducts(?string $updatedAtMin = null): array
    {
        $shop = config('services.shopify.shop');
        $token = config('services.shopify.token');
        $version = config('services.shopify.version');


        $endpoint = "https://{$shop}/admin/api/{$version}/products.json";
        $query = [ 'limit' => 250, 'fields' => 'id,title,body_html,handle,vendor,product_type,variants,images,updated_at,status' ];
        if ($updatedAtMin) $query['updated_at_min'] = $updatedAtMin;


        $all = [];
        $url = $endpoint;
        $headers = [ 'X-Shopify-Access-Token' => $token, 'Accept' => 'application/json' ];


        do {
            $resp = $this->http->get($url, ['headers' => $headers, 'query' => $query]);
            $data = json_decode((string) $resp->getBody(), true);
            $all = array_merge($all, $data['products'] ?? []);


            $link = $resp->getHeader('Link')[0] ?? '';
            $next = $this->parseNextLink($link);
            $url = $next ? $endpoint . '?page_info=' . $next . '&limit=250' : null;
            $query = []; 
        } while ($url);


        return $all;
    }


    private function parseNextLink(string $link): ?string
    {
        if (!$link) return null;
        if (preg_match('/<[^>]*[?&]page_info=([^&>]+)[^>]*>; rel="next"/i', $link, $m)) {
        return $m[1];
        }
        return null;
    }
}