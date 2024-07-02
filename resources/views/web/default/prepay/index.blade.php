@extends(getTemplate() . '.layouts.siahkolah')

@push('styles_top')
@endpush

@section('content')
    <section class="cart-banner position-relative text-center">
        <h1 class="font-30 text-white font-weight-bold" style="margin-bottom: 10px"> برای {{ $pageTitle }}
        </h1>
        <span style="font-size: 12px;" class="text-white">(
            @foreach ($webinar as $item)
                <a href="/course/{{ $item->slug }}" target="_blank" class="text-danger">{{ $item->title }}</a> ,
            @endforeach)
        </span>
        {{-- <span class="payment-hint font-20 text-white d-block">{{ handlePrice($price) }}
        </span> --}}
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
        <form action="/payments/{{ $action }}" method="post" class="mt-25" enctype="multipart/form-data">
            {{ csrf_field() }}

            @if ($action != 'complete_prepay')
                <div class="row mb-30">
                    <div class="col-md-4">
                        <label>مبلغ : <small class="text-danger">(حداقل : 50,000 تومان) </small>
                        </label>
                        <input type="number" min="50000" onkeyup="imposeMinMax(this)"
                            value="50000" class="form-control" name="amount">
                    </div>
                </div>
            @endif

            @php
                $ids = [];
                foreach ($webinar as $item) {
                    $ids[] = $item->id;
                }
            @endphp

            <input type="text" name="webinar_id[]" value="{{ json_encode($ids) }}" hidden>

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
