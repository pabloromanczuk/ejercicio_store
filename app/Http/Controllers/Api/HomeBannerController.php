<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeBannerResource;
use App\Models\HomeBanner;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomeBannerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return HomeBannerResource::collection(HomeBanner::activos()->get());
    }
}
