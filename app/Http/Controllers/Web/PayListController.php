<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use App\Models\sale_link;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayListController extends Controller
{
    public function index($id)
    {
        sale_link::where('id', $id)->first();
        $user = Auth::user();
        if ($user) {
            $charge = $user->getAccountingCharge();
        } else {
            $charge = 0;
        }
        $paymentChannels = PaymentChannel::where('status', 'active')->get();
        foreach ($paymentChannels as $paymentChannel) {
            if ($paymentChannel->class_name == 'Razorpay' and (!$isMultiCurrency or in_array(currency(), $paymentChannel->currencies))) {
                $razorpay = true;
            }
        }
        $sale_link = sale_link::where('id', $id)->first();
        $products = json_decode($sale_link->products);
        $prices = [];
        $webinars = [];
        foreach ($products as $product) {
            $webinar = Webinar::where('id', $product)->first();
            $webinars[] = $webinar;
            $prices[] = $webinar->price;
        }
        $amount = 0;
        foreach ($prices as  $price) {
            $amount += $price;
        }
        $data = [
            'pageTitle' => 'پیش واریز',
            'userCharge' =>  $charge,
            'paymentChannels' =>  $paymentChannels,
            'action' => 'prepay',
            'price' => $amount,
            'id' => $id,
            'webinars' => $webinars
        ];
        return view(getTemplate() . '.list_pay.index', $data);
    }
}