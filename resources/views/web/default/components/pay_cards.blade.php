    <style>

    </style>

    @if (isset($sale_link))
        @php
            $selected_gates = json_decode($sale_link->gates);
        @endphp
    @endif

    <div class="row mt-20">
        @if (!empty($paymentChannels))
            @foreach ($paymentChannels as $paymentChannel)
                @if (isset($selected_gates) && in_array($paymentChannel->id, $selected_gates))
                    <div class="col-6  mb-40 charge-account-radio">
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
                @else
                    <div class="col-6  mb-40 charge-account-radio">
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
                @endif
            @endforeach
        @endif

        @if (isset($selected_gates) && in_array('offline', $selected_gates))
            <div class="col-6  mb-40 charge-account-radio">
                <input type="radio" name="gateway" id="cart" value="cart" onclick="showHideDiv(this.value)">
                <label for="cart"
                    class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                    style="height: 250px">
                    <img src="/assets/default/img/activity/cart.png" width="90" height="90" alt="">
                    <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                        کارت به کارت
                    </p>
                </label>
            </div>
        @else
            <div class="col-6  mb-40 charge-account-radio">
                <input type="radio" name="gateway" id="cart" value="cart" onclick="showHideDiv(this.value)">
                <label for="cart"
                    class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center"
                    style="height: 250px">
                    <img src="/assets/default/img/activity/cart.png" width="90" height="90" alt="">
                    <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                        کارت به کارت
                    </p>
                </label>
            </div>
        @endif

        {{-- @auth
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
                @endauth --}}

    </div>
