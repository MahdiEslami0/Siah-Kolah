@extends(getTemplate() . '.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    @php
        $registerMethod = getGeneralSettings('register_method') ?? 'mobile';
        $showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;
        $showCertificateAdditionalInRegister = getFeaturesSettings('show_certificate_additional_in_register') ?? false;
        $selectRolesDuringRegistration = getFeaturesSettings('select_the_role_during_registration') ?? null;
    @endphp

    <div class="container">


        <div style="display: flex;justify-content:center">
            <div class="row login-container">
                <div class="col-12">
                    <div class="login-card">
                        <h1 class="font-20 font-weight-bold">{{ trans('auth.signup') }}</h1>

                        <form method="post" action="/register" class="mt-35">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">



                            <div class="form-group">
                                <label class="input-label" for="full_name">{{ trans('auth.full_name') }}:</label>
                                <input name="full_name" type="text" value="{{ old('full_name') }}"
                                    class="form-control @error('full_name') is-invalid @enderror">
                                @error('full_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label class="input-label" for="mobile">{{ trans('auth.mobile') }}:</label>
                                <input name="mobile" type="number" value="{{ old('mobile') }}"
                                    class="form-control @error('mobile') is-invalid @enderror">
                                @error('mobile')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="input-label" for="email">{{ trans('auth.email') }}:</label>
                                <input name="email" type="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>




                            <button type="submit"
                                class="btn btn-primary btn-block mt-2">{{ trans('auth.signup') }}</button>
                        </form>

                        <div class="text-center mt-20">
                            <span class="text-secondary">
                                {{ trans('auth.already_have_an_account') }}
                                <a href="/login" class="text-secondary font-weight-bold">{{ trans('auth.login') }}</a>
                            </span>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection


<style>
    .login-container {
        width: 600px;
    }

    .login-card {
        padding: 40px 10px;
    }

    @media (max-width: 767px) {
        .login-container {
            width: 100%;
        }

        .login-card {
            padding: 40px 10px;
        }
    }
</style>



@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/js/custom.js"></script>
@endpush
