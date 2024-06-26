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
                            <form
                                action="{{ getAdminPanelUrl() }}/page-builder/{{ !empty($page) ? $page->id . '/update' : 'store' }}"
                                method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="">عنوان :</label>
                                        <input type="text" name="title" class="form-control"
                                            value="{{ $page->title ?? '' }}">
                                        @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="">ترتیب :</label>
                                        <input type="number" name="order" class="form-control"
                                            value="{{ $page->order ?? '' }}">
                                        @error('order')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="">لینک :</label>
                                        <input type="text" name="url" class="form-control"
                                            value="{{ $page->url ?? '' }}">
                                        @error('url')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="">نوع :</label>
                                        <select class="form-control" name="type">
                                            <option value="title">تایتل</option>
                                            <option value="button">دکمه</option>
                                            <option value="info-box">باکس توضیحات</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="">کلاس :</label>
                                        <select class="form-control" name="class">
                                            <option value="">انتخاب کنید</option>
                                            <option value="btn-warning"
                                                {{ isset($page) && $page->title == 'btn-warning' ? 'selected' : '' }}>
                                                btn-warning
                                            </option>
                                            <option value="btn-dark"
                                                {{ isset($page) && $page->title == 'btn-dark' ? 'selected' : '' }}>
                                                btn-dark</option>
                                            <option value="btn-green"
                                                {{ isset($page) && $page->title == 'btn-green' ? 'selected' : '' }}>
                                                btn-green</option>
                                            <option value="btn-danger"
                                                {{ isset($page) && $page->title == 'btn-danger' ? 'selected' : '' }}>
                                                btn-danger</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="">توضیحات :</label>
                                        <textarea name="description" id="" class="form-control" cols="30" rows="10">
                                            {{ $page->description ?? '' }}
                                        </textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <button class="btn btn-primary">ثبت</button>
                                        @if (isset($page))
                                            <a href="/admin/page-builder/delete/1">
                                                <button class="btn btn-danger" type="button" id="deleteButton">حذف</button>
                                            </a>
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
    @if (isset($page))
        <script>
            $(document).ready(function() {
                $("#deleteButton").click(function() {
                    if (confirm("آیا از حذف اطمینان دارید ؟")) {
                        location.replace('delete/{{ $page->id }}')
                    }
                });
            });
        </script>
    @endif
@endpush
