<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OfflineBank;
use App\Models\PaymentChannel;
use App\Models\sale_link;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayListController extends Controller
{
    public function index($id)
    {
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
        if (is_numeric($id)) {
            $sale_link = sale_link::where('id', $id)->first();
        } else {
            $sale_link = sale_link::where('slug', $id)->first();
        }
        $products = json_decode($sale_link->products);
        $prices = [];
        $webinars = [];
        foreach ($products as $product) {
            $webinar = Webinar::where('id', $product)->first();
            $webinars[] = $webinar;
            $prices[] = $webinar->price;
        }
        if (isset($sale_link->price) &&   $sale_link->price > 0) {
            $amount = $sale_link->price;
        } else {
            $amount = 0;
            foreach ($prices as  $price) {
                $amount += $price;
            }
        }

        $offlineBanks = OfflineBank::get();
        $data = [
            'pageTitle' => 'لیست پرداخت',
            'userCharge' =>  $charge,
            'paymentChannels' =>  $paymentChannels,
            'action' => 'prepay',
            'price' => $amount,
            'id' => $id,
            'webinars' => $webinars,
            'offlineBanks' => $offlineBanks,
            'sale_link' => $sale_link,
            'products' => $products
        ];
        return view(getTemplate() . '.list_pay.index', $data);
    }
}
