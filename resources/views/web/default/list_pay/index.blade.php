@extends(getTemplate() . '.layouts.siahkolah')

@push('styles_top')
@endpush

@section('content')
    <section class="cart-banner position-relative text-center">
        <h1 class="font-30 text-white font-weight-bold">لیست آماده پرداخت</h1>
        <div class="mt-20">
            @foreach ($webinars as $webinar)
                <a href="/course/{{ $webinar->slug }}" target="_blank">
                    <p>
                        <small class="text-white">x1 {{ $webinar->title }}</small>
                    </p>
                </a>
            @endforeach
        </div>

        <span class="payment-hint font-20 text-white d-block">{{ handlePrice($price) }}
        </span>
    </section>
    <section class="container mt-45">



        @if ($errors->any())
            <div class="alert alert-danger text-white">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/list_pay/pay/{{ $sale_link->id }}" method="post" class=" mt-25">
            {{ csrf_field() }}

            @if (!auth()->user())
                <div class="row mb-30">
                    <div class="col-md-6 mb-3">
                        <label>نام و نام خانوادگی :</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>ایمیل :</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>شماره همراه :</label>
                        <input type="number" name="mobile" class="form-control" value="{{ old('mobile') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>رمزعبور :</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>
            @endif



            <input type="text" name="list_id" value="" hidden>
            @isset($prepay_id)
                <input type="text" name="prepay_id" value="{{ $prepay_id }}" hidden>
            @endisset
            <h2 class="section-title">یک پرتال پرداخت انتخاب کنید</h2>



            @include('web.default.components.pay_cards')





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


            @include('web.default.includes.offline_pay')


            {{-- 
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
            </div> --}}


            <div class="d-flex align-items-center justify-content-between mt-45">
                <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_amount') }}
                    {{ handlePrice($price) }}</span>
                <div>
                    @if (isset($sale_link))
                        @php
                            $products = json_decode($sale_link->products);
                        @endphp
                        @if (count($products) == 1)
                            <a href="/prepay/{{ $products[0] }}" type="button"
                                class="btn btn-outline-danger btn-sm  btn-d">
                                پیش پرداخت</a>
                        @endif
                    @endif
                    <button type="button" id="paymentSubmit" disabled class="btn btn-sm btn-primary">پرداخت نقدی</button>
                </div>
            </div>
        </form>


    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/payment.min.js"></script>
@endpush
