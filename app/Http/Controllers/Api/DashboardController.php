<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   public function home(): JsonResponse
{
    $user = Auth::user();
    $dashboard = $user->dashboard;           // ← crée automatiquement si absent
    $dashboard->refreshDashboard();          // recalcule les agrégats

    return response()->json(new DashboardResource($user));
}
}