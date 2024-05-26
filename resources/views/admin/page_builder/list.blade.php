@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')
    <section class="section">

        <div class="section-header">
            <h1>گزینه ها</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">گزینه ها</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.title') }}</th>
                                        <th>ترتیب</th>
                                        <th>{{ trans('admin/main.type') }}</th>
                                        <th>class</th>
                                        <th>{{ trans('admin/main.action') }}</th>
                                    </tr>
                                    @foreach ($page as $item)
                                        <tr>
                                            <td>{{ $item->title }}</td>
                                            <td>{{ $item->order }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->class ?? '' }}</td>
                                            <td>
                                                <a href="/admin/page-builder/edit/{{ $item->id }}">
                                                    <button class="btn btn-primary">مشاهده</button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $page->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
