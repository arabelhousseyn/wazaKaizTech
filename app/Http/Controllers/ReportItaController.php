<?php

namespace App\Http\Controllers;

use App\Models\ReportIta;
use App\Models\ReportItaImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ReportItaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // $validator = Validator::make($request->all(), [
        //     'position' => 'required',
        //     'adress' => 'required',
        //     'markVehicle' => 'required',
        //     'description' => 'required'
        // ]);

        // if($validator->fails())
        // {
        //     return response()->json(['success' => false], 200);
        // }

        // if($validator->validated())
        // {
        //     $position = explode(';',$request->position);

        //     $reportIta = ReportIta::create([
        //         'lat' => doubleval($position[0]),
        //         'long' => doubleval($position[1]),
        //         'adress' => $request->adress,
        //         'markVehicle' => $request->markVehicle,
        //         'description' => $request->description
        //     ]);

        //     if($reportIta)
        //     {
        //         $images = explode(';ibaa;',$request->images);
        //         foreach ($variable as $key => $value) {
        //             # code...
        //         }
        //     }
        //     return response()->json(['success' => false], 200);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReportIta  $reportIta
     * @return \Illuminate\Http\Response
     */
    public function show(ReportIta $reportIta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ReportIta  $reportIta
     * @return \Illuminate\Http\Response
     */
    public function edit(ReportIta $reportIta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReportIta  $reportIta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReportIta $reportIta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReportIta  $reportIta
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReportIta $reportIta)
    {
        //
    }
}
