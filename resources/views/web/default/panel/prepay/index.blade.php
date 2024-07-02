@extends(getTemplate() . '.panel.layouts.panel_layout')
{{-- 
@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush --}}

@section('content')
    @if (count($prepay) > 0)
        <section>
            <h2 class="section-title">{{ $pageTitle }}</h2>
            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('public.title') }}</th>
                                        <th>مهلت پرداخت</th>
                                        <th>پرداختی</th>
                                        <th>باقی مانده</th>
                                        <th>وضعیت</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    @foreach ($prepay as $item)
                                        @php
                                            $Order = App\Models\Order::where('id', $item->order_id)->first();
                                            $order_items = App\Models\OrderItem::where('order_id', $Order->id)->get();
                                            $have_pay = $Order->amount - $Order->total_amount;
                                            $created_at = \Carbon\Carbon::parse($item->created_at);
                                            $expiry_time = $created_at->addHours(48)->toIso8601String();
                                        @endphp
                                        <tr>
                                            <td class="text-left" style="max-width:100px">
                                                <span style="font-size: 12px">
                                                    @foreach ($order_items as $order_item)
                                                        <a class="text-danger" target="_blank"
                                                            href="/course/{{ $order_item->webinar->slug }}">
                                                            {{ $order_item->webinar->title }}</a>
                                                        |
                                                    @endforeach
                                                </span>
                                            </td>
                                            <td class="text-left" style="width: 15%">
                                                @if ($item->status == 'pending')
                                                    <span id="countdown_{{ $item->id }}"
                                                        style="font-size: 12px;
                    color: #333;
                    background-color: #f5f5f5;
                    padding: 5px 20px;
                    border: 1px solid #ccc;
                    border-radius: 5px;"></span>
                                                @endif
                                            </td>
                                            <td class="text-left" style="color:rgb(1, 185, 1)">
                                                @if ($item->status == 'done')
                                                    {{ handlePrice($item->amount + $item->pay, false) }}
                                                @else
                                                    {{ handlePrice($item->amount, false) }}
                                                @endif
                                                تومان
                                            </td>
                                            <td class="text-left" style="color:rgb(231, 6, 6)">
                                                {{ handlePrice($have_pay, false) }}
                                                تومان
                                            </td>
                                            <td class="text-left">
                                                @switch($item->status)
                                                    @case('done')
                                                        پرداخت شده
                                                    @break

                                                    @case('pending')
                                                        در انتظار تکمیل وجه
                                                    @break

                                                    @case('refund_request')
                                                        درحال لغو
                                                    @break

                                                    @case('refunded')
                                                        لغو شد
                                                    @break

                                                    @default
                                                @endswitch
                                            </td>
                                            <td class="text-left">
                                                <div class="d-flex gap-10">
                                                    @if ($expiry_time > now())
                                                        <a target="_blank" href="/prepay/{{ $item->id }}/pay">
                                                            <button class="btn btn-primary btn-sm"
                                                                @if ($item->status == 'done' || $item->status == 'refunded' || $item->status == 'refund_request') disabled @endif>
                                                                @switch($item->status)
                                                                    @case('done')
                                                                        پرداخت شده
                                                                    @break

                                                                    @case('pending')
                                                                        پرداخت
                                                                    @break

                                                                    @case('refund_request')
                                                                        لغو
                                                                    @break

                                                                    @case('refunded')
                                                                        لغو
                                                                    @break

                                                                    @default
                                                                @endswitch
                                                            </button>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <script>
                                            // Countdown timer for expiry_time
                                            var expiryTime_{{ $item->id }} =
                                            "{{ $expiry_time }}"; // Make sure this is in a format that JavaScript's Date can parse
                                            var countdownDate_{{ $item->id }} = new Date(expiryTime_{{ $item->id }}).getTime();

                                            // Update the countdown every 1 second
                                            var x_{{ $item->id }} = setInterval(function() {
                                                var now_{{ $item->id }} = new Date().getTime();
                                                var distance_{{ $item->id }} = countdownDate_{{ $item->id }} - now_{{ $item->id }};

                                                // Time calculations for days, hours, minutes and seconds
                                                var days_{{ $item->id }} = Math.floor(distance_{{ $item->id }} / (1000 * 60 * 60 * 24));
                                                var hours_{{ $item->id }} = Math.floor((distance_{{ $item->id }} % (1000 * 60 * 60 * 24)) / (
                                                    1000 * 60 * 60));
                                                var minutes_{{ $item->id }} = Math.floor((distance_{{ $item->id }} % (1000 * 60 * 60)) / (
                                                    1000 * 60));
                                                var seconds_{{ $item->id }} = Math.floor((distance_{{ $item->id }} % (1000 * 60)) / 1000);

                                                // Display the result in the element with id="countdown_{{ $item->id }}"
                                                document.getElementById("countdown_{{ $item->id }}").innerHTML = days_{{ $item->id }} +
                                                    " روز " + hours_{{ $item->id }} +
                                                    " ساعت " +
                                                    minutes_{{ $item->id }} + " دقیقه " + seconds_{{ $item->id }} + " ثانیه ";

                                                // If the countdown is over, write some text
                                                if (distance_{{ $item->id }} < 0) {
                                                    clearInterval(x_{{ $item->id }})
                                                    document.getElementById("countdown_{{ $item->id }}").innerHTML = "زمان تمام شده";
                                                }
                                            }, 1000);
                                        </script>
                                    @endforeach




                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    @else
        @include(getTemplate() . '.includes.no-result', [
            'file_name' => 'financial.png',
            'title' => 'بدون پیش واریز',
            'hint' => 'پیش واریزی برای شما یافت نشد',
        ])
    @endif
@endsection
