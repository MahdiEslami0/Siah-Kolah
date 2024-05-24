@extends(getTemplate() . '.layouts.siahkolah')

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

            @include('web.default.components.pay_cards')



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
