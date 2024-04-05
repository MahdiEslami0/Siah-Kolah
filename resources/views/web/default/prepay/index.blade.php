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
        @if ($errors->any())
            <div class="alert alert-danger text-white my-35">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <h2 class="section-title">یک پرتال پرداخت انتخاب کنید</h2>
        <form action="/payments/{{ $action }}" method="post" class="mt-25">
            {{ csrf_field() }}

            @if ($action != 'complete_prepay')
                <div class="row mb-30">
                    <div class="col-md-4">
                        <label>مبلغ : <small class="text-danger">(حداقل : {{ handlePrice($price) }}) </small>
                        </label>
                        <input type="number" min="{{ $price }}" onkeyup="imposeMinMax(this)"
                            value="{{ $price }}" class="form-control" name="amount">
                    </div>
                </div>
            @endif

            <input type="text" name="webinar_id" value="{{ $webinar->id }}" hidden>
            @isset($prepay_id)
                <input type="text" name="prepay_id" value="{{ $prepay_id }}" hidden>
            @endisset
            <div class="row">
                @if (!empty($paymentChannels))
                    @foreach ($paymentChannels as $paymentChannel)
                        <div class="col-6 col-lg-4 mb-40 charge-account-radio">
                            <input type="radio" name="gateway" id="{{ $paymentChannel->title }}"
                                data-class="{{ $paymentChannel->class_name }}" value="{{ $paymentChannel->id }}"
                                onclick="showHideDiv(this.value)">
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
                    <input type="radio" @if (empty($userCharge) or $price > $userCharge) disabled @endif name="gateway" id="offline"
                        value="credit" onclick="showHideDiv(this.value)">
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


            @include('web.default.includes.offline_pay')


            <div class="d-flex align-items-center justify-content-between mt-45">
                <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_amount') }}
                    {{ handlePrice($price) }}</span>
                <button type="submit" id="paymentSubmit"
                    class="btn btn-sm btn-primary">{{ trans('public.start_payment') }}</button>
            </div>
        </form>


    </section>
@endsection

@push('scripts_bottom')
    <script>
        function imposeMinMax(el) {
            if (el.value != "") {
                if (parseInt(el.value) < parseInt(el.min)) {
                    el.value = el.min;
                }
                if (parseInt(el.value) > parseInt(el.max)) {
                    el.value = el.max;
                }
            }
        }
    </script>
    <script src="/assets/default/js/parts/payment.min.js"></script>
@endpush
