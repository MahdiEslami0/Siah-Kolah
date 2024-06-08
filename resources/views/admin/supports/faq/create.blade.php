@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.new_ticket') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.faqs') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <form
                                        action="{{ getAdminPanelUrl() }}/supports/faq/{{ !empty($faq) ? $faq->id . '/update' : 'store' }}"
                                        method="Post">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.title') }}</label>
                                            <input type="text" name="title"
                                                class="form-control  @error('title') is-invalid @enderror"
                                                value="{{ !empty($faq) ? $faq->title : old('title') }}" />
                                            @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>والد</label>
                                            <select name="parent" class="form-control" id="">
                                                <option value="">انتخاب کنید</option>
                                                @foreach ($faqs as $item)
                                                    @if (isset($faq))
                                                        <option value="{{ $item->id }}"
                                                            @if ($item->id == $faq->parent_id) selected @endif>
                                                            {{ $item->title }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $item->id }}">{{ $item->title }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('order')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>ترتیب</label>
                                            <input type="text" name="order"
                                                class="form-control  @error('order') is-invalid @enderror"
                                                value="{{ !empty($faq) ? $faq->order : old('order') }}" />
                                            @error('order')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>توضیحات</label>

                                            <textarea name="description" class="form-control" cols="30" rows="10">
                                                @isset($faq)
{{ $faq->description }}
@endisset
                                            </textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-4 mt-2 mt-md-0">
                                            <button class="btn btn-primary w-100">{{ trans('admin/main.submit') }}</button>
                                        </div>
                                </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
@endpush
