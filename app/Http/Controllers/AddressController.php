<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Get sub addresses of a specific address (province, district, or commune).
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getSubAddresses(Request $request, $id)
    {
        if (!$request->ajax()) {
            return back();
        }

        $addresses = Address::where('sub_of', $id)->orderBy('name')->get();
        return response()->json([
            'addresses' => $addresses
        ]);
    }
}
