<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cidades;

class MobileController extends Controller
{
    public function index(){

        \Session::put('mobile',true);

        $cidades = Cidades::all();

        return view('mobile.index')->with([
            'cidades' => $cidades
        ]);

    }

    public function selecionarCidade(Request $request){

        \Validator::make($request->all(),[
           'cidade' => 'required'
        ])->validate();

        return redirect()->route('selecionar-cidade',$request->cidade);

    }
}
