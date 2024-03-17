<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\prepayment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class PrepayController extends Controller
{
    public function index(Request $request)
    {
        $query = Prepayment::query();

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('webinar_ids')) {
            $query->whereIn('webinar_id', $request->webinar_ids);
        }

        if ($request->filled('student_ids')) {
            $query->whereIn('student_id', $request->student_ids);
        }

        $prepays = $query->paginate(10);

        $data = [
            'pageTitle' => 'پیش واریز',
            'prepays' => $prepays
        ];

        return view('admin.prepay.index', $data);
    }

    public function update(prepayment $prepayment, Request $request)
    {
        $prepayment->status = $request->prepay_status;
        if ($request->prepay_status == 'done') {
            $sale  = Sale::where('webinar_id', $prepayment->webinar_id)->where('buyer_id',  $prepayment->user_id)->first();
            if ($sale == null) {
                Sale::create([
                    'order_id' => null,
                    'webinar_id' =>  $prepayment->webinar_id,
                    'type' => 'webinar',
                    'amount' =>  $prepayment->amount +  $prepayment->pay ?? 0,
                    'total_amount' => $prepayment->amount +  $prepayment->pay ?? 0,
                    'buyer_id' =>  $prepayment->user_id,
                    'created_at' => time()
                ]);
            }
        }
        $sale  = Sale::where('webinar_id', $prepayment->webinar_id)->where('buyer_id',  $prepayment->user_id)->first();

        if ($request->prepay_status == 'done' && $sale == null) {
            Sale::create([
                'order_id' => null,
                'webinar_id' =>  $prepayment->webinar_id,
                'type' => 'webinar',
                'amount' =>  $prepayment->amount +  $prepayment->pay ?? 0,
                'total_amount' => $prepayment->amount +  $prepayment->pay ?? 0,
                'buyer_id' =>  $prepayment->user_id,
                'created_at' => time()
            ]);
        }
        if ($request->prepay_status == 'refunded' && $sale != null) {
            $sale->delete();
        }
        $prepayment->save();
        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'ویرایش شد',
            'status' => 'success'
        ];
        return redirect()->back()->with(['toast' => $toastData]);
    }
}
