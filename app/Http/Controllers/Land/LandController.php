<?php

namespace App\Http\Controllers\Land;

use App\Http\Controllers\Controller;
use App\Models\Api\Cart;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LandController extends Controller
{



    public function index()
    {
        return view('land.land');
    }




}
