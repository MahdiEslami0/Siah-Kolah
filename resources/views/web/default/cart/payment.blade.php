@extends(getTemplate() . '.layouts.app')

@push('styles_top')
@endpush

@section('content')
    <section class="cart-banner position-relative text-center">
        <h1 class="font-30 text-white font-weight-bold">{{ trans('cart.checkout') }}</h1>
        <span
            class="payment-hint font-20 text-white d-block">{{ handlePrice($total) . ' ' . trans('cart.for_items', ['count' => $count]) }}</span>
    </section>

    <section class="container mt-45">

        @if (!empty($totalCashbackAmount))
            <div class="d-flex align-items-center mb-25 p-15 success-transparent-alert">
                <div class="success-transparent-alert__icon d-flex align-items-center justify-content-center">
                    <i data-feather="credit-card" width="18" height="18" class=""></i>
                </div>

                <div class="ml-10">
                    <div class="font-14 font-weight-bold ">{{ trans('update.get_cashback') }}</div>
                    <div class="font-12 ">
                        {{ trans('update.by_purchasing_this_cart_you_will_get_amount_as_cashback', ['amount' => handlePrice($totalCashbackAmount)]) }}
                    </div>
                </div>
            </div>
        @endif

        @php
            $isMultiCurrency = !empty(getFinancialCurrencySettings('multi_currency'));
            $userCurrency = currency();
            $invalidChannels = [];
        @endphp

        <h2 class="section-title">{{ trans('financial.select_a_payment_gateway') }}</h2>

        <form action="/payments/payment-request" method="post" class=" mt-25">
            {{ csrf_field() }}
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <div class="row">
                @if (!empty($paymentChannels))
                    @foreach ($paymentChannels as $paymentChannel)
                        @if (!$isMultiCurrency or !empty($paymentChannel->currencies) and in_array($userCurrency, $paymentChannel->currencies))
                            <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                                <input type="radio" name="gateway" id="{{ $paymentChannel->title }}"
                                    data-class="{{ $paymentChannel->class_name }}" value="{{ $paymentChannel->id }}">
                                <label for="{{ $paymentChannel->title }}"
                                    class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                                    style="height: 250px;">
                                    <img src="{{ $paymentChannel->image }}" width="120" height="60" alt="">

                                    <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                                        {{ trans('financial.pay_via') }}
                                        <span class="font-weight-bold font-14">{{ $paymentChannel->title }}</span>
                                    </p>
                                </label>
                            </div>
                        @else
                            @php
                                $invalidChannels[] = $paymentChannel;
                            @endphp
                        @endif
                    @endforeach
                @endif


                <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                    <input type="radio" name="gateway" id="cart" value="cart" onclick="showHideDiv(this.value)">
                    <label for="cart"
                        class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                        style="height: 250px">
                        <img src="/assets/default/img/activity/cart.png" width="120" height="120" alt="">
                        <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                            کارت به کارت
                        </p>
                    </label>
                </div>

                <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                    <input type="radio" @if (empty($userCharge) or $total > $userCharge) disabled @endif name="gateway" id="offline"
                        value="credit">
                    <label for="offline"
                        class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                        style="height: 250px">
                        <img src="/assets/default/img/activity/pay.svg" width="120" height="60" alt="">

                        <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                            کیف پول
                        </p>



                        <span class="mt-5 font-14">{{ handlePrice($userCharge) }}</span>

                        <a href="/panel/financial/account">
                            <button class="btn btn-sm btn-dark mt-2" type="button">افزایش موجودی</button>
                        </a>

                    </label>
                </div>
            </div>

            @if (!empty($invalidChannels))
                <div class="d-flex align-items-center mt-30 rounded-lg border p-15">
                    <div class="size-40 d-flex-center rounded-circle bg-gray200">
                        <i data-feather="info" class="text-gray" width="20" height="20"></i>
                    </div>
                    <div class="ml-5">
                        <h4 class="font-14 font-weight-bold text-gray">{{ trans('update.disabled_payment_gateways') }}</h4>
                        <p class="font-12 text-gray">{{ trans('update.disabled_payment_gateways_hint') }}</p>
                    </div>
                </div>

                <div class="row mt-20">
                    @foreach ($invalidChannels as $invalidChannel)
                        <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                            <div
                                class="disabled-payment-channel bg-white border rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ $invalidChannel->image }}" width="120" height="60" alt="">

                                <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                                    {{ trans('financial.pay_via') }}
                                    <span class="font-weight-bold font-14">{{ $invalidChannel->title }}</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row mt-15" id="offlineBanks">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label">{{ trans('financial.account') }}</label>
                            <select name="account" class="form-control @error('account') is-invalid @enderror">
                                <option selected disabled>{{ trans('financial.select_the_account') }}</option>

                                @foreach ($offlineBanks as $offlineBank)
                                    <option value="{{ $offlineBank->id }}"
                                        @if (!empty($editOfflinePayment) and $editOfflinePayment->offline_bank_id == $offlineBank->id) selected @endif>{{ $offlineBank->title }}
                                    </option>
                                @endforeach
                            </select>

                            @error('account')
                                <div class="invalid-feedback"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="referralCode" class="input-label">{{ trans('admin/main.referral_code') }}</label>
                            <input type="text" name="referral_code" id="referralCode"
                                value="{{ !empty($editOfflinePayment) ? $editOfflinePayment->reference_number : old('referral_code') }}"
                                class="form-control @error('referral_code') is-invalid @enderror" />
                            @error('referral_code')
                                <div class="invalid-feedback"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.date_time') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="dateRangeLabel">
                                        <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                    </span>
                                </div>
                                <input type="text" name="date"
                                    value="{{ !empty($editOfflinePayment) ? dateTimeFormat($editOfflinePayment->pay_date, 'Y-m-d H:i', false) : old('date') }}"
                                    class="form-control datetimepicker @error('date') is-invalid @enderror"
                                    aria-describedby="dateRangeLabel" />
                                @error('date')
                                    <div class="invalid-feedback"> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label">{{ trans('update.attach_the_payment_photo') }}</label>

                            <label for="attachmentFile" id="attachmentFileLabel" class="custom-upload-input-group">
                                <div class="custom-upload-input"></div>
                                <span class="custom-upload-icon text-white">
                                    <i data-feather="upload" width="18" height="18" class="text-white"></i>
                                </span>
                            </label>
                            <input type="file" name="attachment" id="attachmentFile"
                                class="form-control h-auto invisible-file-input @error('attachment') is-invalid @enderror"
                                value="" />
                            @error('attachment')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div>
                    @foreach ($offlineBanks as $offlineBank)
                        <div class="col-12 col-lg-4 mb-30 mb-lg-0">
                            <div
                                class="py-25 px-20 rounded-sm panel-shadow d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ $offlineBank->logo }}" width="120" height="60" alt="">

                                <div class="mt-15 mt-30 w-100">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span
                                            class="font-14 font-weight-500 text-secondary">{{ trans('public.name') }}:</span>
                                        <span class="font-14 font-weight-500 text-gray">{{ $offlineBank->title }}</span>
                                    </div>

                                    @foreach ($offlineBank->specifications as $specification)
                                        <div class="d-flex align-items-center justify-content-between mt-10">
                                            <span
                                                class="font-14 font-weight-500 text-secondary">{{ $specification->name }}:</span>
                                            <span
                                                class="font-14 font-weight-500 text-gray">{{ $specification->value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-45">
                <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_amount') }}
                    {{ handlePrice($total) }}</span>
                <button type="button" id="paymentSubmit" disabled
                    class="btn btn-sm btn-primary">{{ trans('public.start_payment') }}</button>
            </div>
        </form>
        {{-- 
        @if (!empty($razorpay) and $razorpay)
            <form action="/payments/verify/Razorpay" method="get">
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ env('RAZORPAY_API_KEY') }}"
                    data-amount="{{ (int) ($order->total_amount * 100) }}" data-buttontext="product_price" data-description="Rozerpay"
                    data-currency="{{ currency() }}" data-image="{{ $generalSettings['logo'] }}"
                    data-prefill.name="{{ $order->user->full_name }}" data-prefill.email="{{ $order->user->email }}"
                    data-theme.color="#43d477"></script>
            </form>
        @endif --}}
    </section>




@endsection

@push('scripts_bottom')
    <script>
        offlineBanks.style.display = "none";

        function showHideDiv(gateway) {
            var offlineBanks = document.getElementById("offlineBanks");
            if (gateway === "cart") {
                offlineBanks.style.display = "block";
            } else {
                offlineBanks.style.display = "none";
            }
        }
    </script>
    <script src="/assets/default/js/parts/payment.min.js"></script>
@endpush
