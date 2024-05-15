<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array
     */
    public function hosts()
    {
        return [
            '18.133.205.228',
            '35.177.236.113',
            '3.8.44.1',
            '3.11.81.131',
            '197.248.0.196',
            '197.248.0.197',
            '41.210.130.212',
            '41.210.131.180',
            '154.113.6.117'
        ];
    }
}
