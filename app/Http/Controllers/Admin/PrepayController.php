<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\prepayment;
use Illuminate\Http\Request;

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
        $prepayment->save();
        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'ویرایش شد',
            'status' => 'success'
        ];
        return redirect()->back()->with(['toast' => $toastData]);
    }
}
