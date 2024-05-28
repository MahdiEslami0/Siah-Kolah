@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <style>
        .accordion-item {
            border: 1px solid #ddd;
        }

        .accordion-header {
            background-color: #f5f5f5;
            padding: 20px;
            cursor: pointer;
            font-weight: 600
        }

        .accordion-content {
            padding: 20px;
            display: none;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease-out;
        }

        .accordion-content.active {
            display: block;
            max-height: 1000px;
            /* Adjust as needed */
        }
    </style>
@endpush

@php
    $faqs = App\Models\SupportFaq::get();
@endphp

@section('content')
    <section class="my-30">
        <h2 class="section-title">سوالات متداول</h2>
        <div class="mt-25 rounded-sm shadow py-20 px-10 px-lg-25 bg-white">
            <div class="accordion">
                @foreach ($faqs as $faq)
                    <div class="accordion-item mb-10">
                        <div class="accordion-header shadow-lg">
                            {{ $faq->title }}
                        </div>
                        <div class="accordion-content">
                            {{ $faq->descriptiona }}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <form method="post" action="/panel/support/store">
        {{ csrf_field() }}

        <h2 class="section-title">{{ trans('panel.create_support_message') }}</h2>

        <div class="mt-25 rounded-sm shadow py-20 px-10 px-lg-25 bg-white">

            <div class="form-group">
                <label class="input-label">{{ trans('site.subject') }}</label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="form-control @error('title')  is-invalid @enderror" />
                @error('title')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>



            <div class="form-group">
                <label class="input-label d-block">{{ trans('panel.department') }}</label>
                <select name="department_id" id="departments"
                    class="form-control select2 @error('department_id')  is-invalid @enderror" data-allow-clear="false"
                    data-search="false">
                    <option selected disabled></option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->title }}</option>
                    @endforeach
                </select>

                @error('department_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div id="courseInput" class="form-group @if (!$errors->has('webinar_id')) d-none @endif">
                <label class="input-label d-block">{{ trans('product.course') }}</label>
                <select name="webinar_id" class="form-control select2 @error('webinar_id')  is-invalid @enderror">
                    <option value="" selected disabled>{{ trans('panel.select_course') }}</option>

                    @foreach ($webinars as $webinar)
                        <option value="{{ $webinar->id }}">{{ $webinar->title }} -
                            {{ $webinar->creator->full_name }}</option>
                    @endforeach
                </select>
                @error('webinar_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label d-block">{{ trans('site.message') }}</label>
                <textarea name="message" class="form-control" rows="15">{{ old('message') }}</textarea>
            </div>

            <div class="row">
                <div class="col-12 col-lg-8 d-flex align-items-center">
                    <div class="form-group">
                        <label class="input-label">{{ trans('panel.attach_file') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="button" class="input-group-text panel-file-manager" data-input="attach"
                                    data-preview="holder">
                                    <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                                </button>
                            </div>
                            <input type="text" name="attach" id="attach" value="{{ old('attach') }}"
                                class="form-control" />
                        </div>
                    </div>

                    <button type="submit"
                        class="btn btn-primary btn-sm ml-40 mt-10">{{ trans('site.send_message') }}</button>
                </div>
            </div>
        </div>
        </section>
    </form>
@endsection

@push('scripts_bottom')
    <script>
        const accordionItems = document.querySelectorAll(".accordion-item");

        accordionItems.forEach(function(item) {
            const header = item.querySelector(".accordion-header");
            const content = item.querySelector(".accordion-content");

            header.addEventListener("click", function() {
                if (content.classList.contains("active")) {
                    content.classList.remove("active");
                } else {
                    // Close all other accordion items
                    accordionItems.forEach(function(otherItem) {
                        const otherContent = otherItem.querySelector(
                            ".accordion-content");
                        if (otherContent !== content && otherContent.classList.contains(
                                "active")) {
                            otherContent.classList.remove("active");
                        }
                    });
                    content.classList.add("active");
                }
            });
        });
    </script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/js/panel/conversations.min.js"></script>
@endpush
