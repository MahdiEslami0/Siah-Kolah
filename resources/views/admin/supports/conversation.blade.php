@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <div class="tickets-list">
                <a class="ticket-item">
                    <div class="ticket-title">
                        <h4 class="text-primary">{{ $support->title }}</h4>
                    </div>
                    <div class="ticket-info">
                        <div class="font-weight-bold">{{ $support->user->full_name }}</div>
                        <div class="bullet"></div>
                        <div class="font-weight-bold">
                            @if ($support->status == 'open')
                                <span class="text-success">{{ trans('admin/main.open') }}</span>
                            @elseif($support->status == 'close')
                                <span class="text-danger">{{ trans('admin/main.close') }}</span>
                            @elseif($support->status == 'replied')
                                <span class="text-warning">{{ trans('admin/main.pending_reply') }}</span>
                            @else
                                <span class="text-primary">{{ trans('admin/main.replied') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>

            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a
                        href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.conversation') }}</div>
            </div>
        </div>


        <style>
            .my-message {
                width: 51%;
                float: right;
            }

            .support-message {
                width: 51%;
                float: left;
                background-color: #248fde;
                color: white;
            }

            @media (max-width: 600px) {

                .my-message,
                .support-message {
                    width: 80%;
                    margin: 0 auto;
                    display: block;
                }
            }
        </style>

        <div class="section-body">

            <div class="row">
                <div class="col-12 ">
                    <div class="card chat-box" id="mychatbox2">

                        <div class="card-body chat-content">

                            @foreach ($support->conversations as $conversations)
                                @if (isset($conversations->message))
                                    <div class="chat-item chat-{{ !empty($conversations->sender_id) ? 'right' : 'left' }}">
                                        <img
                                            src="{{ !empty($conversations->sender_id) ? $conversations->sender->getAvatar() : $conversations->supporter->getAvatar() }}">

                                        <div class="chat-details"
                                            style="{{ !empty($conversations->sender_id) ? 'float:right' : 'float:left' }}">

                                            <div class="chat-time">
                                                {{ !empty($conversations->sender_id) ? $conversations->sender->full_name : $conversations->supporter->full_name }}
                                            </div>

                                            <div class="chat-text white-space-pre-wrap">
                                                <p>{{ $conversations->message }}</p>
                                            </div>


                                            <div class="chat-time">
                                                <span
                                                    class="mr-2">{{ dateTimeFormat($conversations->created_at, 'Y M j | H:i') }}</span>

                                                @if (!empty($conversations->attach))
                                                    <a href="{{ url($conversations->attach) }}" target="_blank"
                                                        class="text-success"><i class="fa fa-paperclip"></i>
                                                        {{ trans('admin/main.open_attach') }}</a>
                                                @endif
                                            </div>
                                            @if (isset($conversations->admin_reason))
                                                <span
                                                    class="badge badge-{{ !empty($conversations->sender_id) ? 'light' : 'primary' }}">{{ $conversations->admin_reason }}</span>
                                            @endif

                                        </div>
                                    </div>
                                @endif
                                @if (isset($conversations->reason))
                                    <div class="chat-item chat-{{ !empty($conversations->sender_id) ? 'right' : 'left' }}">
                                        <img
                                            src="{{ !empty($conversations->sender_id) ? $conversations->sender->getAvatar() : $conversations->supporter->getAvatar() }}">
                                        <div class="chat-details"  style="{{ !empty($conversations->sender_id) ? 'float:right' : 'float:left' }}">
                                            <div class="chat-time">
                                                {{ !empty($conversations->sender_id) ? $conversations->sender->full_name : $conversations->supporter->full_name }}
                                                (علت ضمیمه شده)
                                            </div>
                                            <div>
                                                <span class="text-danger">{{ $conversations->reason }}</span>
                                            </div>
                                            @if (isset($conversations->admin_reason))
                                                <hr>
                                                <span
                                                    class="text-{{ !empty($conversations->sender_id) ? 'light' : 'primary' }}">{{ $conversations->admin_reason }}</span>
                                            @endif
                                            <div class="chat-time">
                                                <span
                                                    class="mr-2">{{ dateTimeFormat($conversations->created_at, 'Y M j | H:i') }}</span>
                                                @if (!empty($conversations->attach))
                                                    <a href="{{ url($conversations->attach) }}" target="_blank"
                                                        class="text-success"><i class="fa fa-paperclip"></i>
                                                        {{ trans('admin/main.open_attach') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 ">
                    <div class="card">

                        <div class="card-body">
                            <form action="{{ getAdminPanelUrl() }}/supports/{{ $support->id }}/conversation"
                                method="post">
                                {{ csrf_field() }}



                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group mt-15">
                                            <label class="input-label">{{ trans('site.message') }}</label>
                                            <textarea name="message" rows="6" class=" form-control @error('message')  is-invalid @enderror">{!! old('message') !!}</textarea>
                                            @error('message')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-6">

                                        <div class="form-group mt-15">
                                            <label class="input-label">علت (عدم نمایش به دانشجو)</label>
                                            <textarea name="reason" rows="6" class=" form-control @error('message')  is-invalid @enderror">{!! old('message') !!}</textarea>
                                            @error('reason')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label d-block">علت</label>
                                            <select name="admin_reason" class="form-control">
                                                <option value="">انتخاب کنید</option>
                                                <option value="مشكل محصول">مشكل محصول</option>
                                                <option value="مشكل اسپات پلير">مشكل اسپات پلير</option>
                                                <option value="مشكل از سمت مشترى">مشكل از سمت مشترى</option>
                                                <option value="مشكل از كارشناس فروش">مشكل از كارشناس فروش</option>
                                                <option value="مشكل از پرزنت محصول در كمپين">مشكل از پرزنت محصول در كمپين
                                                </option>
                                                <option value="مشكل از كوچ">مشكل از كوچ</option>
                                                <option value="مشكل پشتيبانى">مشكل پشتيبانى</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="input-label">اوپراتور</label>
                                            <select name="support" class="form-control">
                                                <option value=""></option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        @if (isset($support) && $support->support_id == $user->id) selected @endif>
                                                        {{ $user->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="input-label">دپارتمان</label>
                                            <select name="department" class="form-control">
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}"
                                                        @if (isset($support) && $support->department_id == $department->id) selected @endif>
                                                        {{ $department->getTitleAttribute() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.attach') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager"
                                                        data-input="attach" data-preview="holder">
                                                        Browse
                                                    </button>
                                                </div>
                                                <input type="text" name="attach" id="attach"
                                                    value="{{ old('image_cover') }}" class="form-control" />
                                                <div class="input-group-append">
                                                    <button type="button" class="input-group-text admin-file-view"
                                                        data-input="attach">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4 text-right mt-4">
                                        <button type="submit"
                                            class="btn btn-primary">{{ trans('site.send_message') }}</button>

                                        @if ($support->status != 'close')
                                            <a href="{{ getAdminPanelUrl() }}/supports/{{ $support->id }}/close"
                                                class="btn btn-danger ml-1">{{ trans('admin/main.close_conversation') }}</a>
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
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
@endpush
