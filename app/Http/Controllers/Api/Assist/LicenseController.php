<?php

namespace App\Http\Controllers\Api\Assist;

use App\Http\Controllers\Controller;
use App\Services\UsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function limits(Request $request, UsageService $usage): JsonResponse
    {
        return response()->json($usage->limitsSnapshot($request->user()));
    }
}
