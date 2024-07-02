<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OfflineBank;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\prepayment;
use App\Models\Webinar;
use Auth;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class PrePaymentController extends Controller
{
    public function index($id)
    {
        $productIds = explode(',', $id);
        $webinars = Webinar::whereIn('id', $productIds)->get();
        $user = auth()->user();
        $paymentChannels = PaymentChannel::where('status', 'active')->get();
        $offlinebanks =  OfflineBank::get();
        $price = 0;
        foreach ($webinars as $webinar) {
            $price += $webinar->price;
        }
        $data = [
            'pageTitle' => 'پیش واریز',
            'webinar' => $webinars,
            'userCharge' => $user->getAccountingCharge(),
            'paymentChannels' =>  $paymentChannels,
            'action' => 'prepay',
            'price' => $price * 0.1,
            'offlineBanks' => $offlinebanks
        ];
        return view(getTemplate() . '.prepay.index', $data);
    }

    public function pay(prepayment $prepayment)
    {
        $order_items = OrderItem::where('order_id', $prepayment->order_id)->get();
        $user = FacadesAuth::user();
        $paymentChannels = PaymentChannel::where('status', 'active')->get();

        $offlinebanks =  OfflineBank::get();
        $price = 0;
        $webinars = [];
        foreach ($order_items as $order_item) {
            $webinar = Webinar::where('id', $order_item->webinar_id)->first();
            $webinars[] =  $webinar;
            $price += $webinar->price;
        }
        $data = [
            'pageTitle' => 'تکمیل واریز',
            'webinar' => $webinar,
            'userCharge' => $user->getAccountingCharge(),
            'paymentChannels' =>  $paymentChannels,
            'action' => 'complete_prepay',
            'prepay_id' => $prepayment->id,
            'price' =>  $price - $prepayment->amount,
            'offlineBanks' => $offlinebanks,
            'webinar' =>  $webinars
        ];
        return view(getTemplate() . '.prepay.index', $data);
    }
}
