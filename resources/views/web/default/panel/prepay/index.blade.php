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
                                            $webinar = App\Models\Webinar::where('id', $item->webinar_id)->first();
                                            $have_pay = $webinar->price - $item->amount - $item->pay;
                                            $created_at = Carbon\Carbon::parse($item->created_at);
                                            $now = Carbon\Carbon::now();
                                            $expiry_time = $created_at->addHours(72);
                                            $days_left = $now->diffInDays($expiry_time);
                                        @endphp
                                        <tr>
                                            <td class="text-left">
                                                <a target="_blank" href="/course/{{ $webinar->slug }}">
                                                    {{ $webinar->title }}</a>
                                            </td>
                                            <td class="text-left">

                                                @if ($item->status == 'done')
                                                    ✅
                                                @elseif($item->status == 'refunded')
                                                    🔙
                                                @elseif($item->status == 'refund_request')
                                                    ⏳
                                                @else
                                                    {{ $days_left }}
                                                    روز
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
                                                تومان</td>
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
                                                </div>
                                            </td>
                                        </tr>
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
