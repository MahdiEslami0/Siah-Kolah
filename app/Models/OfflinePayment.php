<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflinePayment extends Model
{
    public static $waiting = 'waiting';
    public static $approved = 'approved';
    public static $reject = 'reject';

    public $timestamps = false;

    protected $guarded = ['id'];


    public function type()
    {
        switch ($this->type) {
            case 'prepay':
                return 'پیش واریز';
                break;
            case 'complete_prepay':
                return 'تکمیل وجه پیش خرید';
                break;
            case 'cart':
                return 'پرداخت سبدخرید';
                break;
            case 'list_pay':
                return 'پرداخت سبدخرید';
                break;
            default:
                return 'شارژ حساب';
                break;
        }
    }


    public function products()
    {
        $data = null;
        if ($this->type == 'list_pay') {
            $sale_link = sale_link::where('id', $this->type_id)->first();
            if (isset($sale_link)) {
                $products =  json_decode($sale_link->products);
                $data = [];
                foreach ($products as  $product) {
                    $Webinar = Webinar::where('id', $product)->first();
                    $data[] = $Webinar->title ?? null;
                }
                return $data;
            }
        } elseif ($this->type == 'prepay') {
            $prapay = prepayment::where('id', $this->type_id)->first();
            if (isset($prapay)) {
                $Webinar = Webinar::where('id', $prapay->webinar_id)->first();
                $data[] = $Webinar->title ?? null;
                return $data;
            }
        }
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function offlineBank()
    {
        return $this->belongsTo('App\Models\OfflineBank', 'offline_bank_id', 'id');
    }

    public function getAttachmentPath()
    {
        return '/store/' . $this->user_id . '/offlinePayments/' . $this->attachment;
    }
}
