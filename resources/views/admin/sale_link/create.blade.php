@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}
            </h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ $action }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="">عنوان فروش :</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $sale_link->name ?? '' }}">
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">لینک <small></small>
                                            :</label>
                                        <input type="text" class="form-control" name="slug"
                                            value="{{ $sale_link->slug ?? '' }}">
                                        @error('price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">وضعیت :</label>
                                        <select name="status" class="form-control">
                                            <option value="active" @if (isset($sale_link->status) && $sale_link->status == 'active') selected @endif>فعال
                                            </option>
                                            <option value="inactive" @if (isset($sale_link->status) && $sale_link->status == 'inactive') selected @endif>
                                                غیرفعال
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">دوره های :</label>
                                        @if (isset($sale_link))
                                            <select name="products[]" class="form-control js-example-basic-single" multiple>
                                                @php
                                                    $products = json_decode($sale_link->products);
                                                @endphp
                                                @foreach ($webinars as $webinar)
                                                    @if ($sale_link != null)
                                                        <option value="{{ $webinar->id }}"
                                                            @if (in_array($webinar->id, $products)) selected @endif>
                                                            {{ $webinar->title }}</option>
                                                    @else
                                                        <option value="{{ $webinar->id }}">
                                                            {{ $webinar->title }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @else
                                            <select name="products[]" class="form-control js-example-basic-single" multiple>
                                                @foreach ($webinars as $webinar)
                                                    <option value="{{ $webinar->id }}">
                                                        {{ $webinar->title }}</option>
                                                @endforeach
                                            </select>
                                        @endif

                                        @error('products')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">قیمت <small>(درصورت نداشتن قیمت خالی بگذارید)</small>
                                            :</label>
                                        <input type="number" class="form-control" name="price"
                                            value="{{ $sale_link->price ?? '' }}">
                                        @error('price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="">درگاه ها :</label>
                                        @if (isset($sale_link))
                                            <select name="gates[]" class="form-control js-example-basic-single" multiple>
                                                @php
                                                    $selected_gates = json_decode($sale_link->gates);
                                                @endphp
                                                <option value="offline" @if (isset($selected_gates) && in_array('offline', $selected_gates)) selected @endif>
                                                    کارت به کارت</option>
                                                @foreach ($gates as $gate)
                                                    @if ($sale_link != null)
                                                        <option value="{{ $gate->id }}"
                                                            @if (isset($selected_gates) && in_array($gate->id, $selected_gates)) selected @endif>
                                                            {{ $gate->title }}</option>
                                                    @else
                                                        <option value="{{ $gate->id }}">
                                                            {{ $gate->title }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @else
                                            <select name="gates[]" class="form-control js-example-basic-single" multiple>
                                                <option value="offline">
                                                    کارت به کارت</option>
                                                @foreach ($gates as $gate)
                                                    <option value="{{ $gate->id }}">
                                                        {{ $gate->title }}</option>
                                                @endforeach
                                            </select>
                                        @endif

                                        @error('products')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>



                                    <div class="col-md-12">
                                        <button class="btn btn-primary">ثبت</button>
                                        @if (isset($sale_link))
                                            <button class="btn btn-danger" type="button" id="deleteButton">حذف</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/roles.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
    @if (isset($sale_link))
        <script>
            $(document).ready(function() {
                $("#deleteButton").click(function() {
                    if (confirm("آیا از حذف اطمینان دارید ؟")) {
                        location.replace('delete/{{ $sale_link->id }}')
                    }
                });
            });
        </script>
    @endif
@endpush
