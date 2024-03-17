<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\prepayment;
use App\Models\Webinar;
use Auth;
use Illuminate\Http\Request;

class PrepayController extends Controller
{
    public function index()
    {
        $prepay = prepayment::where('user_id', Auth::user()->id)->get();
        $data = [
            'pageTitle' => 'پیش واریز ها',
            'prepay' => $prepay
        ];
        return view(getTemplate() . '.panel.prepay.index', $data);
    }
}
