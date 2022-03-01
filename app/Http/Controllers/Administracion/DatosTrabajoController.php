<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Repository\DatosTrabajoRepository;
use Illuminate\Support\Arr;
class DatosTrabajoController extends Controller
{
    public $rep;

    public function __construct(DatosTrabajoRepository $repository){
        $this->rep = $repository;
    }

    public function index(Request $request)
    {
        $this->validate($request,[
            'usuario'=>'required',
            'idDocumento'=>'required'
        ]);
        ['usuario'=> $usuario,'idDocumento'=>$idDocumento] = $request->input();
        return $this->rep->getFilterUserByDoc($usuario,$idDocumento);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
