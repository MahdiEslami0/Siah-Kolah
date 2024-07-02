<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\snappay;
use App\User;
use Illuminate\Http\Request;

class SnapPayController extends Controller
{
    public function sale_info(Request $request)
    {
        $snappay =  snappay::where('uuid', $request->uuid)->first();
        if (isset($snappay)) {
            $user = User::where('id', $snappay->user_id)->select('id', 'full_name', 'email', 'mobile')->first();
            $order_items = OrderItem::where('order_id', $snappay->order_id)->get();
            $items = [];
            $price = 0;
            foreach ($order_items as $order_item) {
                $items[] = [
                    'id' => $order_item->id,
                    'title' => $order_item->webinar->title,
                    'price' => $order_item->webinar->price
                ];
                $price += $order_item->webinar->price;
            }
            return response()->json([
                'status' => 'success',
                'data' => [
                    'price' =>   $price,
                    'user' =>  $user,
                    'order_items' => $items
                ]
            ], 200);
        } else {
            return response()->json([
                'key' => 'error',
                'message' => 'درخواست اسنپ پی با این کد یافت نشد'
            ], 500);
        }
    }


    public function confirm_pay(Request $request)
    {
        $snappay =  snappay::where('uuid', $request->uuid)->first();
        if (isset($snappay)) {
            $order = Order::where('id', $snappay->order_id)->first();
            if ($request->status == 'confirm') {
                $order_items = OrderItem::where('order_id', $snappay->order_id)->get();
                foreach ($order_items as $order_item) {
                    Sale::createSales($order_item, $order->payment_method);
                }
                $snappay->status = 'pay';
                $order->status = 'paid';
            } else {
                $snappay->status = 'cancel';
                $order->status = 'fail';
            }
            $snappay->save();
            $order->save();
            return response()->json([
                'status' => 'success',
                'message' => 'انجام شد'
            ], 200);
        } else {
            return response()->json([
                'key' => 'error',
                'message' => 'درخواست اسنپ پی با این کد یافت نشد'
            ], 500);
        }
    }
}
