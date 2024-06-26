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
                @if (request()->has('uuid'))
                    <input type="text" name="uuid" class="form-control" value="{{ request('uuid') }}" hidden>
                    <input type="text" name="action" class="form-control" value="login" hidden>
                    <div class="row mb-30 mt-10">
                        <div class="col-md-6 mb-3">
                            <label>کد تایید :</label>
                            <input type="number" name="code" class="form-control" value="{{ old('code') }}">
                        </div>
                    </div>
                @else
                    <div class="row mb-30">
                        <div class="col-md-6 mb-3">
                            <label>نام و نام خانوادگی :</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control">
                        </div>
                        {{-- <div class="col-md-6 mb-3">
                        <label>ایمیل :</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div> --}}
                        <div class="col-md-6 mb-3">
                            <label>شماره همراه :</label>
                            <input type="number" name="mobile" class="form-control" value="{{ old('mobile') }}">
                        </div>
                        {{-- <div class="col-md-6 mb-3">
                        <label>رمزعبور :</label>
                        <input type="password" name="password" class="form-control">
                    </div> --}}
                    </div>
                @endif
            @endif



            <input type="text" name="list_id" value="" hidden>
            @isset($prepay_id)
                <input type="text" name="prepay_id" value="{{ $prepay_id }}" hidden>
            @endisset


            @if (isset($sale_link))
                @php
                    $products = json_decode($sale_link->products);
                @endphp
                @if (count($products) == 1)
                    <div id="select_pay">
                        <h2 class="section-title">نوع پرداخت را انتخاب کنید</h2>
                        <div class="row mt-45">
                            <div class="col-6">
                                <a href="/prepay/{{ $products[0] }}">
                                    <div class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                                        style="border: 3px solid var(--primary);
                                        transition: all 0.3s ease;
                                        box-shadow: 0 10px 30px 0 rgba(119, 122, 120, 0.3);
                                    }">
                                        <img src="/assets/default/img/no-results/financial.png" width="90"
                                            height="90">

                                        <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                                            پیش واریز
                                        </p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a onclick="show_pay_online()">
                                    <div class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                                        style="border: 3px solid var(--primary);
                                        transition: all 0.3s ease;
                                        box-shadow: 0 10px 30px 0 rgba(119, 122, 120, 0.3);
                                    }">
                                        <img src="/assets/default/img/no-results/offer.png" width="90" height="90"
                                            alt="">
                                        <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                                            پرداخت نقدی
                                        </p>
                                    </div>
                                </a>
                            </div>

                        </div>
                    </div>
                @endif
            @endif


            <div id="pay_online">
                <div class="row justify-content-between">
                    <div>
                        <h2 class="section-title">یک پرتال پرداخت انتخاب کنید</h2>
                    </div>
                    @if (count($products) == 1)
                        @if (count($products) == 1)
                            <div class="d-flex align-items-center" style="gap: 5px" onclick="show_select_pay()">
                                <div>
                                    <a>
                                        پیش وارزی
                                    </a>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                                    </svg>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                @include('web.default.components.pay_cards')
                <div class="d-flex align-items-center justify-content-between mt-45">
                    <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_amount') }}
                        {{ handlePrice($price) }}</span>
                    <div>
                        <button type="button" id="paymentSubmit" disabled class="btn btn-sm btn-primary">پرداخت
                            نقدی</button>
                    </div>
                </div>
            </div>



            @if (isset($sale_link))
                @if (count($products) == 1)
                    <script>
                        document.getElementById('pay_online').style.display = 'none';

                        function show_pay_online() {
                            document.getElementById('pay_online').style.display = 'block';
                            document.getElementById('select_pay').style.display = 'none';
                        }

                        function show_select_pay() {
                            document.getElementById('pay_online').style.display = 'none';
                            document.getElementById('select_pay').style.display = 'block';
                        }
                    </script>
                @endif
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


        </form>


    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/payment.min.js"></script>
@endpush
