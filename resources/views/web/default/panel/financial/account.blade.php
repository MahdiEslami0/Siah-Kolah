@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')

    {{-- Cashback Alert --}}
    @if (!empty($cashbackRules) and count($cashbackRules))
        @foreach ($cashbackRules as $cashbackRule)
            <div class="d-flex align-items-center mb-20 p-15 success-transparent-alert {{ $classNames ?? '' }}">
                <div class="success-transparent-alert__icon d-flex align-items-center justify-content-center">
                    <i data-feather="credit-card" width="18" height="18" class=""></i>
                </div>

                <div class="ml-10">
                    <div class="font-14 font-weight-bold ">{{ trans('update.get_cashback') }}</div>

                    <div class="font-12 ">
                        {{ trans('update.by_charging_your_wallet_will_get_amount_as_cashback', ['amount' => $cashbackRule->amount_type == 'percent' ? "%{$cashbackRule->amount}" : handlePrice($cashbackRule->amount)]) }}
                    </div>
                </div>
            </div>
        @endforeach
    @endif



    {{-- @if (!empty($registrationBonusAmount))
        <div class="mb-25 d-flex align-items-center justify-content-between p-15 bg-white panel-shadow">
            <div class="d-flex align-items-center">
                <img src="/assets/default/img/icons/money.png" alt="money" width="51" height="51">

                <div class="ml-15">
                    <span
                        class="d-block font-16 text-dark font-weight-bold">{{ trans('update.unlock_registration_bonus') }}</span>
                    <span
                        class="d-block font-14 text-gray font-weight-500 mt-5">{{ trans('update.your_wallet_includes_amount_registration_bonus_This_amount_is_locked', ['amount' => handlePrice($registrationBonusAmount)]) }}</span>
                </div>
            </div>

            <a href="/panel/marketing/registration_bonus" class="btn btn-border-gray300">{{ trans('update.view_more') }}</a>
        </div>
    @endif

    <section>
        <h2 class="section-title">{{ trans('financial.account_summary') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/36.svg" width="64" height="64" alt="">
                        <strong
                            class="font-30 text-dark-blue font-weight-bold mt-5">{{ $accountCharge ? handlePrice($accountCharge) : 0 }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('financial.account_charge') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/37.svg" width="64" height="64" alt="">
                        <strong
                            class="font-30 text-dark-blue font-weight-bold mt-5">{{ $readyPayout ? handlePrice($readyPayout) : 0 }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('financial.ready_to_payout') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/38.svg" width="64" height="64" alt="">
                        <strong
                            class="font-30 text-dark-blue font-weight-bold mt-5">{{ $totalIncome ? handlePrice($totalIncome) : 0 }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('financial.total_income') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section> --}}
    @if (\Session::has('msg'))
        <div class="alert alert-warning">
            <ul>
                <li>{!! \Session::get('msg') !!}</li>
            </ul>
        </div>
    @endif

    @php
        $showOfflineFields = false;
        if (
            $errors->has('date') or
            $errors->has('referral_code') or
            $errors->has('account') or
            !empty($editOfflinePayment)
        ) {
            $showOfflineFields = true;
        }

        $isMultiCurrency = !empty(getFinancialCurrencySettings('multi_currency'));
        $userCurrency = currency();
        $invalidChannels = [];
    @endphp



    @if ($offlinePayments->count() > 0)
        <section class="mt-40">
            <h2 class="section-title">{{ trans('financial.offline_transactions_history') }}</h2>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('financial.bank') }}</th>
                                        <th>{{ trans('admin/main.type') }}</th>
                                        <th>{{ trans('admin/main.referral_code') }}</th>
                                        <th class="text-center">{{ trans('panel.amount') }} ({{ $currency }})</th>
                                        <th class="text-center">{{ trans('update.attachment') }}</th>
                                        <th class="text-center">{{ trans('public.status') }}</th>
                                        <th class="text-right">{{ trans('public.controls') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offlinePayments as $offlinePayment)
                                        <tr>
                                            <td class="text-left">
                                                <div class="d-flex flex-column">

                                                    @if (!empty($offlinePayment->offlineBank))
                                                        <span
                                                            class="font-weight-500 text-dark-blue">{{ $offlinePayment->offlineBank->title }}</span>
                                                    @else
                                                        <span class="font-weight-500 text-dark-blue">-</span>
                                                    @endif
                                                    <span
                                                        class="font-12 text-gray">{{ dateTimeFormat($offlinePayment->pay_date, 'j M Y H:i') }}</span>
                                                </div>
                                            </td>
                                            <td class="text-left align-middle">
                                                <span>{{ $offlinePayment->type() }}</span>
                                            </td>
                                            <td class="text-left align-middle">
                                                <span>{{ $offlinePayment->reference_number }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span
                                                    class="font-16 font-weight-bold text-primary">{{ handlePrice($offlinePayment->amount, false) }}</span>
                                            </td>

                                            <td class="text-center align-middle">
                                                @if (!empty($offlinePayment->attachment))
                                                    <a href="{{ $offlinePayment->getAttachmentPath() }}" target="_blank"
                                                        class="text-primary">{{ trans('public.view') }}</a>
                                                @else
                                                    ---
                                                @endif
                                            </td>

                                            <td class="text-center align-middle">
                                                @switch($offlinePayment->status)
                                                    @case(\App\Models\OfflinePayment::$waiting)
                                                        <span class="text-warning">{{ trans('public.waiting') }}</span>
                                                    @break

                                                    @case(\App\Models\OfflinePayment::$approved)
                                                        <span class="text-primary">{{ trans('financial.approved') }}</span>
                                                    @break

                                                    @case(\App\Models\OfflinePayment::$reject)
                                                        <span class="text-danger">{{ trans('public.rejected') }}</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td class="text-right align-middle">
                                                @if ($offlinePayment->status != 'approved')
                                                    <div class="btn-group dropdown table-actions">
                                                        <button type="button" class="btn-transparent dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i data-feather="more-vertical" height="20"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a href="/panel/financial/offline-payments/{{ $offlinePayment->id }}/edit"
                                                                class="webinar-actions d-block mt-10">{{ trans('public.edit') }}</a>
                                                            <a href="/panel/financial/offline-payments/{{ $offlinePayment->id }}/delete"
                                                                data-item-id="1"
                                                                class="webinar-actions d-block mt-10 delete-action">{{ trans('public.delete') }}</a>
                                                        </div>
                                                    </div>
                                                @endif
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
            'file_name' => 'offline.png',
            'title' => trans('financial.offline_no_result'),
            'hint' => nl2br(trans('financial.offline_no_result_hint')),
        ])
    @endif
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script src="/assets/default/js/panel/financial/account.min.js"></script>

    <script>
        (function($) {
            "use strict";

            @if (session()->has('sweetalert'))
                Swal.fire({
                    icon: "{{ session()->get('sweetalert')['status'] ?? 'success' }}",
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">{{ session()->get('sweetalert')['msg'] ?? '' }}</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });
            @endif
        })(jQuery)
    </script>
@endpush
