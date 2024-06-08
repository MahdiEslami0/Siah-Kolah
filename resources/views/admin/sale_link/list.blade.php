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
            <div class="card">
                <div class="card-header">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped font-14">
                                <tr>
                                    <th>#</th>
                                    <th>عنوان</th>
                                    <th>قیمت</th>
                                    <th>وضعیت</th>
                                    <th>دوره ها</th>
                                    <th>عملیات</th>
                                </tr>
                                @foreach ($sale_link as $item)
                                    @php
                                        $webinars = [];
                                        $products = json_decode($item->products);
                                        foreach ($products as $product) {
                                            $webinar = App\Models\Webinar::where('id', $product)->first();
                                            if ($webinar) {
                                                $webinars[] = $webinar;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if (isset($item->price) && $item->price > 0)
                                                @formatPrice($item->price ?? 0) تومان
                                            @else
                                                ثبت نشده
                                            @endif
                                        </td>
                                        <td>{{ $item->status }}</td>
                                        <td>
                                            @foreach ($webinars as $webinar)
                                                <a href="/course/{{ $webinar->slug }}" target="_blank">
                                                    <small>{{ $webinar->title }}</small>
                                                </a>
                                                <br>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ $item->id }}">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                            <a href="/list_pay/{{ $item->slug ?? $item->id }}?seller={{ $item->getSeller()->full_name }}"
                                                target="_blank">
                                                <i class="fas fa-link text-info"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        {{ $sale_link->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
