<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/v1/*',
        'home/setlocale',
        'carts/delete',
        'carts/update',
        'checkGP',
        'auctions/update_autobid',
        'auctions/check_autobid',
        'home/setcountry',
        'auctionwinner',
        'api/request/*',
        'update_shipment'
    ];
}
