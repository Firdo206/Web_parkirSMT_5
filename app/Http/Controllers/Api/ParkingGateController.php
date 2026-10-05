<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParkingGateState;
use Illuminate\Http\Request;

class ParkingGateController extends Controller{
    public function trigger(Request $request){
        $gate=$request->input('gate','gate_utama');

        $state=ParkingGateState::updateOrCreate(
            ['gate'=>$gate],
            [
                'trigger'=>true,
                'izin'=>'MENUNGGU',
                'status'=>'MENUNGGU VERIFIKASI',
            ]
        );

        return response()->json([
            'success'=>true,
            'message'=>'Trigger parkir diterima.',
            'data'=>$state,
        ]);
    }

    public function command(Request $request){
        $gate=$request->input('gate','gate_utama');

        $state=ParkingGateState::firstOrCreate(
            ['gate'=>$gate],
            [
                'trigger'=>false,
                'izin'=>'SIAGA',
                'status'=>'SIAGA',
            ]
        );

        return response()->json([
            'success'=>true,
            'data'=>$state,
        ]);
    }

    public function reset(Request $request){
        $gate=$request->input('gate','gate_utama');

        $state=ParkingGateState::updateOrCreate(
            ['gate'=>$gate],
            [
                'trigger'=>false,
                'izin'=>'SIAGA',
                'status'=>'SIAGA',
            ]
        );

        return response()->json([
            'success'=>true,
            'message'=>'Status gate direset.',
            'data'=>$state,
        ]);
    }

    public function verificationStatus(Request $request){
        $gate=$request->input('gate','gate_utama');

        $state=ParkingGateState::firstOrCreate(
            ['gate'=>$gate],
            [
                'trigger'=>false,
                'izin'=>'SIAGA',
                'status'=>'SIAGA',
            ]
        );

        return response()->json([
            'success'=>true,
            'data'=>$state,
        ]);
    }
}