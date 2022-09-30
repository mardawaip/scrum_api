<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $whitelist = [
            'http://localhost:3000',
            'http://localhost:3001',
            'http://localhost:8000',
            'http://localhost',
            'https://uks.mardawa.id',
            'https://beuks.mardawa.id',
            'https://manajemenuks.mardawa.id',
            'https://uks.kemdikbud.go.id',
            'http://172.29.102.176:3000',
            'http://10.100.0.22:8082',
            'https://manajemen-uks.kemdikbud.go.id',
            null
        ];
        
        $origin = $request->header('Origin');

        if(in_array($origin, $whitelist))
        return $next($request)->withHeaders(['Access-Control-Allow-Origin'=>$origin, 'Access-Control-Allow-Methods'=>'GET, POST, PATCH, PUT, DELETE, OPTIONS', 'Access-Control-Allow-Headers'=>'Content-Type, Accept, Authorization, X-Requested-With, Application', 'Access-Control-Max-Age'=>86400]);
        return Response()->json(['status'=>'Domain Tidak terdaftar']);

    }
}
