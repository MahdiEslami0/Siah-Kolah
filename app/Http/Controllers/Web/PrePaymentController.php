<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use App\Models\prepayment;
use App\Models\Webinar;
use Auth;
use Illuminate\Http\Request;

class PrePaymentController extends Controller
{
    public function index($id)
    {
        $webinar = Webinar::where('id', $id)->first();
        $user = Auth::user();
        $paymentChannels = PaymentChannel::where('status', 'active')->get();
        foreach ($paymentChannels as $paymentChannel) {
            if ($paymentChannel->class_name == 'Razorpay' and (!$isMultiCurrency or in_array(currency(), $paymentChannel->currencies))) {
                $razorpay = true;
            }
        }
        $data = [
            'pageTitle' => 'پیش واریز',
            'webinar' => $webinar,
            'userCharge' => $user->getAccountingCharge(),
            'paymentChannels' =>  $paymentChannels,
            'action' => 'prepay',
            'price' => $webinar->price * 0.1
        ];
        return view(getTemplate() . '.prepay.index', $data);
    }

    public function pay(prepayment $prepayment)
    {
        // dd($prepayment);
        $webinar = Webinar::where('id', $prepayment->webinar_id)->first();
        $user = Auth::user();
        $paymentChannels = PaymentChannel::where('status', 'active')->get();
        foreach ($paymentChannels as $paymentChannel) {
            if ($paymentChannel->class_name == 'Razorpay' and (!$isMultiCurrency or in_array(currency(), $paymentChannel->currencies))) {
                $razorpay = true;
            }
        }
        $data = [
            'pageTitle' => 'تکمیل واریز',
            'webinar' => $webinar,
            'userCharge' => $user->getAccountingCharge(),
            'paymentChannels' =>  $paymentChannels,
            'action' => 'complete_prepay',
            'price' => $webinar->price - ($webinar->price * 0.1)
        ];
        return view(getTemplate() . '.prepay.index', $data);
    }
}
