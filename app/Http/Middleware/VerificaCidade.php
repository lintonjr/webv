<?php

namespace App\Http\Middleware;

use Closure;

class VerificaCidade
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

        if (empty(session('codigo_cidade'))) {
            if (
                    !in_array(\Request::route()->getName(), array('mobile','mobile-selecionar-cidade',"index", "selecionar-cidade", "get-impresso", "modelo-declaracao", "modelo-carta", "modelo-contrato", "modelo-cpt", "modelo-proposta"))
            ) {
                return redirect('/');
            }
        }
        return $next($request);
    }
}
