@extends(getTemplate() . '.layouts.app')

@push('styles_top')
@endpush

@section('content')
    <section class="cart-banner position-relative text-center">
        <h1 class="font-30 text-white font-weight-bold"> برای {{ $pageTitle }} {{ $webinar->title }}</h1>
        <span class="payment-hint font-20 text-white d-block">{{ handlePrice($price) }}
        </span>
    </section>
    <section class="container mt-45">



        <h2 class="section-title">یک پرتال پرداخت انتخاب کنید</h2>


        <form action="/payments/{{ $action }}" method="post" class=" mt-25">
            {{ csrf_field() }}

            <input type="text" name="webinar_id" value="{{ $webinar->id }}" hidden>
            @isset($prepay_id)
                <input type="text" name="prepay_id" value="{{ $prepay_id }}" hidden>
            @endisset
            <div class="row">
                @if (!empty($paymentChannels))
                    @foreach ($paymentChannels as $paymentChannel)
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
                    @endforeach
                @endif

                <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                    <input type="radio" @if (empty($userCharge) or $price > $userCharge) disabled @endif name="gateway" id="offline"
                        value="credit">
                    <label for="offline"
                        class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                        style="height: 250px">
                        <img src="/assets/default/img/activity/pay.svg" width="120" height="60" alt="">

                        <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                            کیف پول (پرداخت آفلاین)
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


            <div class="d-flex align-items-center justify-content-between mt-45">
                <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_amount') }}
                    {{ handlePrice($price) }}</span>
                <button type="button" id="paymentSubmit" disabled
                    class="btn btn-sm btn-primary">{{ trans('public.start_payment') }}</button>
            </div>
        </form>


    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/payment.min.js"></script>
@endpush
