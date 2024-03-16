<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
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
            'webinar' => $webinar,
            'userCharge' => $user->getAccountingCharge(),
            'paymentChannels' =>  $paymentChannels
        ];
        return view(getTemplate() . '.prepay.index', $data);
    }
}
