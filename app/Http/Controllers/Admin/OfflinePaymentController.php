<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OfflinePaymentsExport;
use App\Http\Controllers\Controller;
use App\Mixins\Cashback\CashbackAccounting;
use App\Models\Accounting;
use App\Models\OfflineBank;
use App\Models\OfflinePayment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\prepayment;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Role;
use App\Models\Sale;
use App\Models\sale_link;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OfflinePaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_offline_payments_list');

        $pageType = $request->get('page_type', 'requests'); //requests or history

        $query = OfflinePayment::query();
        if ($pageType == 'requests') {
            $query->where('status', OfflinePayment::$waiting);
        } else {
            $query->where('status', '!=', OfflinePayment::$waiting);
        }

        $query = $this->filters($query, $request);

        $offlinePayments = $query->paginate(10);

        $offlinePayments->appends([
            'page_type' => $pageType
        ]);

        $roles = Role::all();

        $offlineBanks = OfflineBank::query()
            ->orderBy('created_at', 'desc')
            ->with([
                'specifications'
            ])
            ->get();

        $data = [
            'pageTitle' => trans('admin/main.offline_payments_title') . (($pageType == 'requests') ? 'Requests' : 'History'),
            'offlinePayments' => $offlinePayments,
            'pageType' => $pageType,
            'roles' => $roles,
            'offlineBanks' => $offlineBanks,
        ];

        $user_ids = $request->get('user_ids', []);

        if (!empty($user_ids)) {
            $data['users'] = User::select('id', 'full_name')
                ->whereIn('id', $user_ids)->get();
        }

        return view('admin.financial.offline_payments.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $user_ids = $request->get('user_ids', []);
        $role_id = $request->get('role_id', null);
        $account_type = $request->get('account_type', null);
        $sort = $request->get('sort', null);
        $status = $request->get('status', null);

        if (!empty($search)) {
            $ids = User::where('full_name', 'like', "%$search%")->pluck('id')->toArray();
            $user_ids = array_merge($user_ids, $ids);
        }

        if (!empty($role_id)) {
            $role = Role::where('id', $role_id)->first();

            if (!empty($role)) {
                $ids = $role->users()->pluck('id')->toArray();
                $user_ids = array_merge($user_ids, $ids);
            }
        }

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($user_ids) and count($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        if (!empty($account_type)) {
            $query->where('offline_bank_id', $account_type);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'pay_date_asc':
                    $query->orderBy('pay_date', 'asc');
                    break;
                case 'pay_date_desc':
                    $query->orderBy('pay_date', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function reject($id)
    {
        $this->authorize('admin_offline_payments_reject');

        $offlinePayment = OfflinePayment::findOrFail($id);
        $offlinePayment->update(['status' => OfflinePayment::$reject]);

        $notifyOptions = [
            '[amount]' => handlePrice($offlinePayment->amount),
        ];
        sendNotification('offline_payment_rejected', $notifyOptions, $offlinePayment->user_id);

        return back();
    }

    protected function charge($offlinePayment)
    {
        Accounting::create([
            'creator_id' => auth()->user()->id,
            'user_id' => $offlinePayment->user_id,
            'amount' => $offlinePayment->amount,
            'type' => Accounting::$addiction,
            'type_account' => Accounting::$asset,
            'description' => trans('admin/pages/setting.notification_offline_payment_approved'),
            'created_at' => time(),
        ]);


        $notifyOptions = [
            '[amount]' => handlePrice($offlinePayment->amount),
        ];
        sendNotification('offline_payment_approved', $notifyOptions, $offlinePayment->user_id);

        $accountChargeReward = RewardAccounting::calculateScore(Reward::ACCOUNT_CHARGE, $offlinePayment->amount);
        RewardAccounting::makeRewardAccounting($offlinePayment->user_id, $accountChargeReward, Reward::ACCOUNT_CHARGE);

        $chargeWalletReward = RewardAccounting::calculateScore(Reward::CHARGE_WALLET, $offlinePayment->amount);
        RewardAccounting::makeRewardAccounting($offlinePayment->user_id, $chargeWalletReward, Reward::CHARGE_WALLET);

        if (!empty($offlinePayment->user)) {
            $order = new Order();
            $order->total_amount = $offlinePayment->amount;
            $order->user_id = $offlinePayment->user_id;

            $cashbackAccounting = new CashbackAccounting($offlinePayment->user);
            $cashbackAccounting->rechargeWallet($order);
        }
    }

    public function approved($id)
    {
        $this->authorize('admin_offline_payments_approved');

        $offlinePayment = OfflinePayment::findOrFail($id);

        if ($offlinePayment->type == 'prepay') {
            prepayment::create([
                'webinar_id' =>  $offlinePayment->type_id,
                'user_id' => $offlinePayment->user_id,
                'amount' =>  $offlinePayment->amount,
                'status' => 'pending',
            ]);
        } elseif ($offlinePayment->type == 'complete_prepay') {
            $prepayment = prepayment::where('id', $offlinePayment->type_id)->first();
            $prepayment->status = 'done';
            $prepayment->pay = $offlinePayment->amount;
            $prepayment->save();
            Sale::create([
                'buyer_id' =>  $offlinePayment->user_id,
                'order_id' => $offlinePayment->id,
                'webinar_id' => $prepayment->webinar_id,
                'amount' => $offlinePayment->amount,
                'created_at' => time()
            ]);
        } elseif ($offlinePayment->type == 'cart') {
            $order = order::where('id', $offlinePayment->type_id)->first();
            $order_items = OrderItem::where('order_id', $order->id)->get();
            foreach ($order_items as $item) {
                Sale::create([
                    'buyer_id' =>  $offlinePayment->user_id,
                    'order_id' => $offlinePayment->id,
                    'webinar_id' => $item->webinar_id,
                    'amount' => $item->amount,
                    'created_at' => time()
                ]);
            }
        } elseif ($offlinePayment->type == 'list_pay') {
            $list = sale_link::where('id', $offlinePayment->type_id)->first();
            $products =  json_decode($list->products);
            foreach ($products as $product) {
                $webianr = Webinar::where('id', $product)->first();
                Sale::create([
                    'buyer_id' =>  $offlinePayment->user_id,
                    'order_id' => $offlinePayment->id,
                    'webinar_id' => $product,
                    'amount' =>  $webianr->price,
                    'created_at' => time()
                ]);
            }
        } else {
            $this->charge($offlinePayment);
        }
        $offlinePayment->update(['status' => OfflinePayment::$approved]);

        return back();
    }

    public function exportExcel(Request $request)
    {
        $pageType = $request->get('page_type', 'requests'); //requests or history

        $query = OfflinePayment::query();
        if ($pageType == 'requests') {
            $query->where('status', OfflinePayment::$waiting);
        } else {
            $query->where('status', '!=', OfflinePayment::$waiting);
        }

        $query = $this->filters($query, $request);

        $offlinePayments = $query->get();

        $export = new OfflinePaymentsExport($offlinePayments);

        return Excel::download($export, 'offline_payment_' . $pageType . '.xlsx');
    }
}
