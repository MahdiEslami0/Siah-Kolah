@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">


            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">
                        <div class="row">


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="fsdate" class="text-center form-control" name="from"
                                            value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="lsdate" class="text-center form-control" name="to"
                                            value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.status') }}</label>
                                    <select name="status" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_status') }}</option>
                                        <option value="done" @if (request()->get('status') == 'done') selected @endif>
                                            پرداخت شده
                                        </option>
                                        <option value="pending" @if (request()->get('status') == 'pending') selected @endif>
                                            در انتظار تکمیل وجه
                                        </option>
                                        <option value="refund_request" @if (request()->get('status') == 'refund_request') selected @endif>
                                            درحال لغو
                                        </option>
                                        <option value="refunded" @if (request()->get('status') == 'refunded') selected @endif>
                                            لغو شد
                                        </option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.class') }}</label>
                                    <select name="webinar_ids" class="form-control search-webinar-select2"
                                        data-placeholder="Search classes">

                                        @if (!empty($webinars) and $webinars->count() > 0)
                                            @foreach ($webinars as $webinar)
                                                <option value="{{ $webinar->id }}" selected>{{ $webinar->title }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.student') }}</label>
                                    <select name="student_ids" data-search-option="just_student_role"
                                        class="form-control search-user-select2" data-placeholder="Search students">

                                        @if (!empty($students) and $students->count() > 0)
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}" selected>{{ $student->full_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group mt-1">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100"
                                        value="{{ trans('admin/main.show_results') }}">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </section>


            <div class="card">
                <div class="card-header">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped font-14">
                                <tr>
                                    <th>#</th>
                                    <th>دانشجو</th>
                                    <th>دوره</th>
                                    <th>مهلت</th>
                                    <th>پرداختی</th>
                                    <th>باقی مانده</th>
                                    <th>وضعیت</th>
                                    <th>تاریخ ایجاد</th>
                                    <th>عملیات</th>
                                </tr>
                                <tr>
                                    @foreach ($prepays as $prepay)
                                        @php
                                            $webinar = App\Models\Webinar::where('id', $prepay->webinar_id)->first();
                                            $have_pay = $webinar->price - $prepay->amount - $prepay->pay;
                                            $created_at = Carbon\Carbon::parse($prepay->created_at);
                                            $now = Carbon\Carbon::now();
                                            $expiry_time = $created_at->addHours(48);
                                            $days_left = $now->diffInDays($expiry_time);
                                        @endphp
                                        <td>{{ $prepay->id }}</td>
                                        <td>{{ $prepay->user->full_name }}</td>
                                        <td>{{ $prepay->webinar->title }}</td>
                                        <td>{{ $days_left }} روز باقی مانده</td>
                                        <td>{{ handlePrice($prepay->amount + $prepay->pay) }}</td>
                                        <td>{{ handlePrice($have_pay) }}</td>
                                        <td>
                                            @switch($prepay->status)
                                                @case('done')
                                                    <span class="text-success">پرداخت شده</span>
                                                @break

                                                @case('pending')
                                                    <span class="text-info"> در انتظار تکمیل وجه
                                                    </span>
                                                @break

                                                @case('refund_request')
                                                    <span class="text-warning"> درحال لغو
                                                    </span>
                                                @break

                                                @case('refunded')
                                                    <span class="text-danger"> لغو شد
                                                    </span>
                                                @break

                                                @default
                                            @endswitch
                                        </td>
                                        <td>{{ \Morilog\Jalali\Jalalian::forge($prepay->created_at)->format('%B %d، %Y | %H:%m') }}
                                        </td>
                                        <td>
                                            <i class="fas fa-eye" style="cursor: pointer"
                                                onclick="open_modal('{{ $prepay->id }}','{{ $prepay->user->full_name }}','{{ $prepay->status }}')"></i>
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        {{ $prepays->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </section>


    <div class="modal  fade" id="modal_prepay" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5"></h5>
                </div>
                <div class="modal-body">
                    <form id="prepay_form">
                        @csrf
                        <Label>وضعیت :</Label>
                        <select name="prepay_status" class="form-control" id="prepay_status">
                            <option value="done">پرداخت شده</option>
                            <option value="pending">در انتظار تکمیل وجه</option>
                            <option value="refund_request">درحال لغو</option>
                            <option value="refunded">لغو شد</option>
                        </select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary mx-1" form="prepay_form">ذخیره</button>
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">لغو</button> --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        function open_modal(id, name, status) {
            $('#modal_prepay').modal('show');
            $('.modal-title').html(name);
            $('#prepay_form').attr('action', '/admin/prepay/' + id);
            $('#prepay_form').attr('method', 'post');
            $('#prepay_status').val(status);
        }
    </script>
@endsection
