@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>سوالات متداول</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">سوالات متداول</div>
            </div>
        </div>


        <section class="card">
            <div class="card-body">
                <div class="table-responsive text-center">
                    <table class="table table-striped font-14">

                        <tr>
                            <th>{{ trans('admin/main.title') }}</th>
                            <th>ترتیب</th>
                            <th>{{ trans('admin/main.action') }}</th>
                        </tr>

                        @foreach ($faqs as $faq)
                            <tr>
                                <td>
                                    {{ $faq->title }}
                                </td>
                                <td>
                                    {{ $faq->order }}
                                </td>
                                <td>
                                    <a href="{{ getAdminPanelUrl() }}/supports/faq/edit/{{ $faq->id }}">
                                        <button class="btn btn-primary">مشاهده</button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </table>
                </div>
            </div>

            <div class="card-footer text-center">
                {{ $faqs->appends(request()->input())->links() }}
            </div>
        </section>


    </section>
@endsection

@push('scripts_bottom')
@endpush
