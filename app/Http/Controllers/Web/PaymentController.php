<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mixins\Cashback\CashbackAccounting;
use App\Models\Accounting;
use App\Models\BecomeInstructor;
use App\Models\Cart;
use App\Models\OfflinePayment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\otp;
use App\Models\PaymentChannel;
use App\Models\prepayment;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ReserveMeeting;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Sale;
use App\Models\sale_link;
use App\Models\TicketUser;
use App\Models\Webinar;
use App\PaymentChannels\ChannelManager;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cookie;
use Http;
use Illuminate\Support\Facades\Http as FacadesHttp;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PaymentController extends Controller
{
    protected $order_session_key = 'payment.order_id';

    private function handleUploadAttachment($user, $file)
    {
        $storage = Storage::disk('public');

        $path = '/' . $user->id . '/offlinePayments';

        if (!$storage->exists($path)) {
            $storage->makeDirectory($path);
        }

        $img = Image::make($file);
        $name = time() . '.' . $file->getClientOriginalExtension();

        $path = $path . '/' . $name;

        $storage->put($path, (string)$img->encode());

        return $name;
    }


    protected function offline($request, $amount, $type, $type_id)
    {
        $rules = [
            // 'amount' => 'required|numeric|min:0',
            'gateway' => 'required',
            'account' => 'required',
            'referral_code' => 'required',
            'date' => 'required',
            'attachment' => 'image|mimes:jpeg,png,jpg|max:10240'
        ];

        // if (!empty($request->file('attachment'))) {
        //     $rules['attachment'] = 'image|mimes:jpeg,png,jpg|max:10240';
        // }

        $this->validate($request, $rules);

        $attachment = null;
        $userAuth = auth()->user();
        if (!empty($request->file('attachment'))) {
            $attachment = $this->handleUploadAttachment($userAuth, $request->file('attachment'));
        }
        $date = $request->date;
        OfflinePayment::create([
            'user_id' => $userAuth->id,
            'amount' => $amount,
            'offline_bank_id' => $request->account,
            'reference_number' => $request->referral_code,
            'status' => OfflinePayment::$waiting,
            'pay_date' => $date,
            'attachment' => $attachment,
            'created_at' => time(),
            'type' => $type,
            'type_id' => $type_id
        ]);
        $notifyOptions = [
            '[amount]' => handlePrice($amount),
            '[u.name]' => $userAuth->full_name
        ];
        sendNotification('offline_payment_request', $notifyOptions, $userAuth->id);
        sendNotification('new_offline_payment_request', $notifyOptions, 1);
        $sweetAlertData = [
            'msg' => trans('financial.offline_payment_request_success_store'),
            'status' => 'success'
        ];
        return back()->with(['sweetalert' => $sweetAlertData]);
    }

    public function prepay(Request $request)
    {
        $webinarIds = json_decode($request->input('webinar_id')[0]);
        $Webinars = Webinar::WhereIn('id', $webinarIds)->get();
        $price = 0;
        foreach ($Webinars as $Webinar) {
            $price += $Webinar->price;
        }
        if ($request->gateway == 'cart') {
            $gateway = 'payment_channel';
        } else {
            $gateway = $request->gateway;
        }
        if ('50000' <= $request->amount) {
            $order = order::create([
                'user_id' => auth()->user()->id,
                'status' => 'pending',
                'payment_method' => $gateway,
                'amount' => $price,
                'total_amount' => $request->amount,
                'prepay' => 'pending',
                'created_at' => time()
            ]);
            foreach ($Webinars as $Webinar) {
                OrderItem::create([
                    'user_id' => auth()->user()->id,
                    'order_id' => $order->id,
                    'webinar_id' => $Webinar->id,
                    'amount' => $Webinar->price,
                    'total_amount' => $Webinar->price
                ]);
            }
            return $this->paymentRequest($request, $request->gateway, $order->id, 'prepay', $request->webinar_id);
        } else {
            return redirect()->back();
        }
    }

    public function complete_prepay(Request $request)
    {
        if ($request->gateway == 'cart') {
            $gateway = 'payment_channel';
        } else {
            $gateway = $request->gateway;
        }
        $prepay = prepayment::where('id', $request->prepay_id)->first();
        $webinarIds = json_decode($request->input('webinar_id')[0]);
        $Webinars = Webinar::WhereIn('id', $webinarIds)->get();
        $price = 0;
        foreach ($Webinars as $Webinar) {
            $price += $Webinar->price;
        }
        $order = order::create([
            'user_id' => auth()->user()->id,
            'status' => 'pending',
            'payment_method' =>  $gateway,
            'amount' => $price - $prepay->amount,
            'total_amount' => $price  -  $prepay->amount,
            'prepay' => 'complete',
            'prepay_id' => $request->prepay_id,
            'created_at' => time()
        ]);
        foreach ($Webinars as $Webinar) {
            OrderItem::create([
                'user_id' => auth()->user()->id,
                'order_id' => $order->id,
                'webinar_id' => $Webinar->id,
                'amount' => $Webinar->price,
                'total_amount' => $Webinar->price
            ]);
        }
        return $this->paymentRequest($request, $request->gateway, $order->id, 'complete_prepay', $request->prepay_id);
    }

    public function list_pay($id, Request $request)
    {
        if ($request->gateway == 'cart') {
            $gateway = 'payment_channel';
        } else {
            $gateway = $request->gateway;
        }
        $sale_link = sale_link::where('id', $id)->first();
        $products = json_decode($sale_link->products);
        $prices = [];
        foreach ($products as $product) {
            $Webinar = Webinar::where('id', $product)->first();
            $prices[] = $Webinar->price;
        }
        if (isset($sale_link->price) &&   $sale_link->price > 0) {
            $amount = $sale_link->price;
        } else {
            $amount = 0;
            foreach ($prices as  $price) {
                $amount += $price;
            }
        }
        if (auth()->user()) {
            $user_id = auth()->user()->id;
        } else {
            if (isset($request->login_method)) {
                if ($request->login_method == 'by_password') {
                    $check_user_email = user::where('email', $request->email)->first();
                    if (isset($check_user_email)) {
                        $rules = [
                            'full_name' => 'required',
                            'email' => 'required|email',
                            'password' => 'required',
                        ];
                        $validator = Validator::make($request->all(), $rules);
                        if ($validator->fails()) {
                            throw new ValidationException($validator);
                        }
                        $credentials = $request->only('email', 'password');
                        if (Auth::attempt($credentials)) {
                            $user = $check_user_email;
                        } else {
                            $toastData = [
                                'title' => trans('public.request_failed'),
                                'msg' => 'حساب کاربری با این ایمیل وجود دارد , ولی رمزعبور اشتباه است',
                                'status' => 'danger'
                            ];
                            return redirect()->back()->with(['toast' => $toastData]);
                        }
                    } else {
                        $rules = [
                            'full_name' => 'required',
                            'email' => 'required|email',
                            'password' => 'required|min:8',
                        ];
                        $validator = Validator::make($request->all(), $rules);
                        if ($validator->fails()) {
                            throw new ValidationException($validator);
                        }
                        $user =  User::create([
                            'full_name' => $request->full_name,
                            'email' => $request->email,
                            'role_id' => 1,
                            'role_name' => 'user',
                            'created_at' => time()
                        ]);
                        Auth::login($user);
                        $user_id = $user->id;
                    }
                } elseif ($request->login_method == 'by_mobile') {
                    if (isset($request->uuid)) {
                        $otp = Otp::where('key', $request->uuid)->first();
                        if ($request->action == 'login') {
                            $user = User::where('id', $otp->user_id)->first();
                            if ($request->code == $otp->code) {
                                Auth::login($user);
                                $otp->delete();
                                $user_id = $user->id;
                                return redirect()->intended('/dashboard');
                            } else {
                                $otp->try = $otp->try + 1;
                                $otp->save();
                                $toastData = [
                                    'title' => trans('public.request_failed'),
                                    'msg' => 'کد تایید اشتباه است',
                                    'status' => 'danger'
                                ];
                                return redirect()->back()->with(['toast' => $toastData]);
                            }
                        } else {
                            if ($otp->try >= 3) {
                                $otp->delete();
                                $toastData = [
                                    'title' => 'خطا',
                                    'msg' => 'تلاش بیش ازحد مجاز',
                                    'status' => 'danger'
                                ];
                                return redirect(url('/list_pay/' . $id))->with(['toast' => $toastData]);
                            }
                        }
                    } else {
                        $check_user = User::where('mobile', $request->mobile)->first();
                        if (isset($check_user)) {
                            $otp = Otp::where('user_id', $check_user->id)->first();
                            $key = uuid_create();
                            $code = rand(1000, 9999);
                            if ($otp && $otp->created_at->diffInMinutes(now()) < 3) {
                                $toastData = [
                                    'title' => 'کد قبلا ارسال شده است',
                                    'msg' => ($otp->created_at->diffInMinutes(now()) - 3) * -1 . ' دقیقه دیگر دوباره تلاش کنید ',
                                    'status' => 'error'
                                ];
                            } else {
                                if (isset($otp)) {
                                    $otp->delete();
                                }
                                Otp::create([
                                    'code' =>  $code,
                                    'key' => $key,
                                    'user_id' => $check_user->id,
                                    'try' => 0
                                ]);
                                $toastData = [
                                    'title' => "موفق",
                                    'msg' => 'کد تایید برای شما پیامک شد',
                                    'status' => 'success'
                                ];
                                // Send SMS with verification code (replace with your SMS provider)
                                // Example: Kavenegar SMS API
                                FacadesHttp::get('http://api.kavenegar.com/v1/2F4E5079575663783031503968356E4E516851634C2F566C6B435A5A7254532B434E3676596443563068733D/verify/lookup.json', [
                                    'receptor' =>  $request->mobile,
                                    'token' => $code,
                                    'template' => 'verify'
                                ]);
                            }
                            return redirect()->to(url('/list_pay/' . $id . '?uuid=' . $key))->with(['toast' => $toastData]);
                        } else {
                            $rules = [
                                'full_name' => 'required',
                                'mobile' => 'required|numeric|regex:/^[0][9][0-9]{9}$/',
                            ];
                            $validator = Validator::make($request->all(), $rules);
                            if ($validator->fails()) {
                                throw new ValidationException($validator);
                            }

                            $user =  User::create([
                                'full_name' => $request->full_name,
                                'mobile' => $request->mobile,
                                'role_id' => 1,
                                'role_name' => 'user',
                                'created_at' => time()
                            ]);
                            Auth::login($user);
                            $user_id = $user->id;
                        }
                    }
                }
            } else {
                $toastData = [
                    'title' => 'خطا',
                    'msg' => 'روش ورود مشخص نشده است',
                    'status' => 'danger'
                ];
                return redirect()->back()->with(['toast' => $toastData]);
            }
        }
        $order = order::create([
            'user_id' => $user_id,
            'status' => 'pending',
            'payment_method' => $gateway,
            'amount' => $amount,
            'total_amount' => $amount,
            'created_at' => time(),
            'list_id' => $id
        ]);
        foreach ($products as $product) {
            $Webinar = Webinar::where('id', $product)->first();
            OrderItem::create([
                'user_id' => auth()->user()->id,
                'order_id' => $order->id,
                'webinar_id' => $Webinar->id,
                'amount' => $Webinar->price,
                'total_amount' => $Webinar->price
            ]);
        }
        return $this->paymentRequest($request, $request->gateway, $order->id, 'list_pay', $id);
    }

    public function paymentRequest(Request $request, ?string $gateway = null, ?int $order_id = null, $type = 'cart', $type_id = null)
    {
        $this->validate($request, [
            'gateway' => 'required'
        ]);

        $user = auth()->user();
        $gateway = $request->input('gateway') ?? $gateway;
        $orderId = $request->input('order_id') ?? $order_id;

        if ($type_id == null) {
            $type_id =  $orderId;
        }

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if ($order->type === Order::$meeting) {
            $orderItem = OrderItem::where('order_id', $order->id)->first();
            $reserveMeeting = ReserveMeeting::where('id', $orderItem->reserve_meeting_id)->first();
            $reserveMeeting->update(['locked_at' => time()]);
        }

        if ($gateway == 'cart') {
            $this->offline($request, $order->total_amount, $type, $type_id);
            $toastData = [
                'title' => 'پرداخت موفق',
                'msg' => 'پرداخت ثبت شد و به زودی بررسی میشود',
                'status' => 'success'
            ];
            Cart::where('creator_id', auth()->user()->id)->delete();
            return redirect()->to('/panel/financial/account')->with(['toast' => $toastData])->withCookie(Cookie::forget('carts'));
            exit;
        }

        if ($gateway === 'credit') {

            if ($user->getAccountingCharge() < $order->total_amount) {
                $order->update(['status' => Order::$fail]);

                session()->put($this->order_session_key, $order->id);

                return redirect('/payments/status');
            }

            $order->update([
                'payment_method' => Order::$credit
            ]);

            $this->setPaymentAccounting($order, 'credit');

            $order->update([
                'status' => Order::$paid
            ]);

            session()->put($this->order_session_key, $order->id);

            return redirect('/payments/status');
        }

        $paymentChannel = PaymentChannel::where('id', $gateway)
            ->where('status', 'active')
            ->first();

        if (!$paymentChannel) {
            $toastData = [
                'title' => trans('cart.fail_purchase'),
                'msg' => trans('public.channel_payment_disabled'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        $order->payment_method = Order::$paymentChannel;
        $order->save();


        try {
            $channelManager = ChannelManager::makeChannel($paymentChannel);
            $redirect_url = $channelManager->paymentRequest($order);

            if (in_array($paymentChannel->class_name, PaymentChannel::$gatewayIgnoreRedirect)) {
                return $redirect_url;
            }

            return Redirect::away($redirect_url);
        } catch (\Exception $exception) {

            $toastData = [
                'title' => trans('cart.fail_purchase'),
                'msg' => trans('cart.gateway_error'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }
    }

    public function paymentVerify(Request $request, $gateway)
    {
        $paymentChannel = PaymentChannel::where('class_name', $gateway)
            ->where('status', 'active')
            ->first();
        try {
            $channelManager = ChannelManager::makeChannel($paymentChannel);
            $order = $channelManager->verify($request);
            return $this->paymentOrderAfterVerify($order);
        } catch (\Exception $exception) {
            $toastData = [
                'title' => trans('cart.fail_purchase'),
                'msg' => trans('cart.gateway_error'),
                'status' => 'error'
            ];
            return redirect('cart')->with(['toast' => $toastData]);
        }
    }

    /*
     * | this methode only run for payku.result
     * */
    public function paykuPaymentVerify(Request $request, $id)
    {
        $paymentChannel = PaymentChannel::where('class_name', PaymentChannel::$payku)
            ->where('status', 'active')
            ->first();

        try {
            $channelManager = ChannelManager::makeChannel($paymentChannel);

            $request->request->add(['transaction_id' => $id]);

            $order = $channelManager->verify($request);

            return $this->paymentOrderAfterVerify($order);
        } catch (\Exception $exception) {
            $toastData = [
                'title' => trans('cart.fail_purchase'),
                'msg' => trans('cart.gateway_error'),
                'status' => 'error'
            ];
            return redirect('cart')->with(['toast' => $toastData]);
        }
    }

    private function paymentOrderAfterVerify($order)
    {
        // dd('sadasd');
        if (!empty($order)) {
            if ($order->status == Order::$paying) {
                $this->setPaymentAccounting($order);

                $order->update(['status' => Order::$paid]);
            } else {
                if ($order->type === Order::$meeting) {
                    $orderItem = OrderItem::where('order_id', $order->id)->first();
                    if ($orderItem && $orderItem->reserve_meeting_id) {
                        $reserveMeeting = ReserveMeeting::where('id', $orderItem->reserve_meeting_id)->first();
                        if ($reserveMeeting) {
                            $reserveMeeting->update(['locked_at' => null]);
                        }
                    }
                }
            }
            session()->put($this->order_session_key, $order->id);
            return redirect('/payments/status?order_id=' . $order->id);
        } else {
            $toastData = [
                'title' => trans('cart.fail_purchase'),
                'msg' => trans('cart.gateway_error'),
                'status' => 'error'
            ];
            return redirect('cart')->with($toastData);
        }
    }

    public function setPaymentAccounting($order, $type = null)
    {
        $cashbackAccounting = new CashbackAccounting();

        if ($order->is_charge_account) {
            Accounting::charge($order);

            $cashbackAccounting->rechargeWallet($order);
        } else {
            foreach ($order->orderItems as $orderItem) {
                // $sale = Sale::createSales($orderItem, $order->payment_method);

                if (!empty($orderItem->reserve_meeting_id)) {
                    $reserveMeeting = ReserveMeeting::where('id', $orderItem->reserve_meeting_id)->first();


                    $reserver = $reserveMeeting->user;

                    if ($reserver) {
                        $this->handleMeetingReserveReward($reserver);
                    }
                }

                if (!empty($orderItem->gift_id)) {
                    $gift = $orderItem->gift;

                    $gift->update([
                        'status' => 'active'
                    ]);

                    $gift->sendNotificationsWhenActivated($orderItem->total_amount);
                }

                if (!empty($orderItem->subscribe_id)) {
                    Accounting::createAccountingForSubscribe($orderItem, $type);
                } elseif (!empty($orderItem->promotion_id)) {
                    Accounting::createAccountingForPromotion($orderItem, $type);
                } elseif (!empty($orderItem->registration_package_id)) {
                    Accounting::createAccountingForRegistrationPackage($orderItem, $type);

                    if (!empty($orderItem->become_instructor_id)) {
                        BecomeInstructor::where('id', $orderItem->become_instructor_id)
                            ->update([
                                'package_id' => $orderItem->registration_package_id
                            ]);
                    }
                } elseif (!empty($orderItem->installment_payment_id)) {
                    Accounting::createAccountingForInstallmentPayment($orderItem, $type);

                    // $this->updateInstallmentOrder($orderItem, $sale);
                } else {
                    // webinar and meeting and product and bundle

                    Accounting::createAccounting($orderItem, $type);
                    TicketUser::useTicket($orderItem);

                    if (!empty($orderItem->product_id)) {
                        // $this->updateProductOrder($sale, $orderItem);
                    }
                }
            }

            // Set Cashback Accounting For All Order Items
            $cashbackAccounting->setAccountingForOrderItems($order->orderItems);
        }

        Cart::emptyCart($order->user_id);
    }

    public function payStatus(Request $request)
    {
        if (!empty(session()->get($this->order_session_key))) {
            $orderId = session()->get($this->order_session_key);
            session()->forget($this->order_session_key);
        } else {
            return redirect()->to('/panel');
        }
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->user()->id)
            ->first();
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        if (!empty($order)) {
            if (isset($order->list_id)) {
                foreach ($orderItems as  $orderItem) {
                    Sale::createSales($orderItem, $order->payment_method);
                }
                $button = 'مشاهده دوره ها';
                $href = "/panel/webinars/purchases";
            } elseif ($order->prepay == 'pending') {
                prepayment::create([
                    'webinar_id' =>  $order->webinar_id,
                    'user_id' => auth()->user()->id,
                    'amount' =>  $order->total_amount,
                    'status' => 'pending',
                    'order_id' => $order->id
                ]);
                if ($order->payment_method == 'credit') {
                    Accounting::create([
                        'user_id' => auth()->user()->id,
                        'amount' => $order->total_amount,
                        'type_account' => 'asset',
                        'type' => 'deduction',
                        'store_type' => 'automatic',
                        'description' => 'پرداخت پیش واریز',
                        'created_at' => time()
                    ]);
                }
                $button = "پیش واریز ها";
                $href = "/panel/webinars/prepay";
            } elseif ($order->prepay == 'complete') {
                $prepayment = prepayment::where('id', $order->prepay_id)->first();
                $prepayment->status = 'done';
                $prepayment->pay =  $order->total_amount;
                $prepayment->save();
                $total_amount = $prepayment->amount + $order->total_amount;
                foreach ($orderItems as  $orderItem) {
                    Sale::createSales($orderItem, $order->payment_method);
                }
                if ($order->payment_method == 'credit') {
                    Accounting::create([
                        'user_id' => auth()->user()->id,
                        'amount' => $order->total_amount,
                        'type_account' => 'asset',
                        'type' => 'deduction',
                        'store_type' => 'automatic',
                        'description' => 'تکمیل پرداخت پیش واریز',
                        'created_at' => time()
                    ]);
                }
                $button = "مشاهده دوره ها";
                $href = "/panel/webinars/purchases";
            } else {
                $button = 'مشاهده دوره ها';
                $href = "/panel/webinars/purchases";
            }
            $data = [
                'pageTitle' => trans('public.cart_page_title'),
                'order' => $order,
                'button' => $button,
                'href' => $href,
            ];
            return view('web.default.cart.status_pay', $data);
        } else {
            return redirect('/panel');
        }
    }

    private function handleMeetingReserveReward($user)
    {
        if ($user->isUser()) {
            $type = Reward::STUDENT_MEETING_RESERVE;
        } else {
            $type = Reward::INSTRUCTOR_MEETING_RESERVE;
        }

        $meetingReserveReward = RewardAccounting::calculateScore($type);

        RewardAccounting::makeRewardAccounting($user->id, $meetingReserveReward, $type);
    }

    private function updateProductOrder($sale, $orderItem)
    {
        $product = $orderItem->product;

        $status = ProductOrder::$waitingDelivery;

        if ($product and $product->isVirtual()) {
            $status = ProductOrder::$success;
        }

        ProductOrder::where('product_id', $orderItem->product_id)
            ->where(function ($query) use ($orderItem) {
                $query->where(function ($query) use ($orderItem) {
                    $query->whereNotNull('buyer_id');
                    $query->where('buyer_id', $orderItem->user_id);
                });

                $query->orWhere(function ($query) use ($orderItem) {
                    $query->whereNotNull('gift_id');
                    $query->where('gift_id', $orderItem->gift_id);
                });
            })
            ->update([
                'sale_id' => $sale->id,
                'status' => $status,
            ]);

        if ($product and $product->getAvailability() < 1) {
            $notifyOptions = [
                '[p.title]' => $product->title,
            ];
            sendNotification('product_out_of_stock', $notifyOptions, $product->creator_id);
        }
    }

    private function updateInstallmentOrder($orderItem, $sale)
    {
        $installmentPayment = $orderItem->installmentPayment;

        if (!empty($installmentPayment)) {
            $installmentOrder = $installmentPayment->installmentOrder;

            $installmentPayment->update([
                'sale_id' => $sale->id,
                'status' => 'paid',
            ]);

            /* Notification Options */
            $notifyOptions = [
                '[u.name]' => $installmentOrder->user->full_name,
                '[installment_title]' => $installmentOrder->installment->main_title,
                '[time.date]' => dateTimeFormat(time(), 'j M Y - H:i'),
                '[amount]' => handlePrice($installmentPayment->amount),
            ];

            if ($installmentOrder and $installmentOrder->status == 'paying' and $installmentPayment->type == 'upfront') {
                $installment = $installmentOrder->installment;

                if ($installment) {
                    if ($installment->needToVerify()) {
                        $status = 'pending_verification';

                        sendNotification("installment_verification_request_sent", $notifyOptions, $installmentOrder->user_id);
                        sendNotification("admin_installment_verification_request_sent", $notifyOptions, 1); // Admin
                    } else {
                        $status = 'open';

                        sendNotification("paid_installment_upfront", $notifyOptions, $installmentOrder->user_id);
                    }

                    $installmentOrder->update([
                        'status' => $status
                    ]);

                    if ($status == 'open' and !empty($installmentOrder->product_id) and !empty($installmentOrder->product_order_id)) {
                        $productOrder = ProductOrder::query()->where('installment_order_id', $installmentOrder->id)
                            ->where('id', $installmentOrder->product_order_id)
                            ->first();

                        $product = Product::query()->where('id', $installmentOrder->product_id)->first();

                        if (!empty($product) and !empty($productOrder)) {
                            $productOrderStatus = ProductOrder::$waitingDelivery;

                            if ($product->isVirtual()) {
                                $productOrderStatus = ProductOrder::$success;
                            }

                            $productOrder->update([
                                'status' => $productOrderStatus
                            ]);
                        }
                    }
                }
            }


            if ($installmentPayment->type == 'step') {
                sendNotification("paid_installment_step", $notifyOptions, $installmentOrder->user_id);
                sendNotification("paid_installment_step_for_admin", $notifyOptions, 1); // For Admin
            }
        }
    }
}
