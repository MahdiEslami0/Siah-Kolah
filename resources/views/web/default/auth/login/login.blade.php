@extends(getTemplate() . '.layouts.app')

@section('content')
    <div class="container">
        @if (!empty(session()->has('msg')))
            <div class="alert alert-info alert-dismissible fade show mt-30" role="alert">
                {{ session()->get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif


        <div style="display: flex;justify-content:center">
            <div class="row login-container shadow-lg">
                <div class="col-12">
                    <div class="login-card">
                        <h1 class="font-20 font-weight-bold">{{ trans('auth.login_h1') }}</h1>

                        <form class="mt-25" method="POST" action="/login" id="loginForm">
                            @csrf
                            <input type="text" name="login_method" id="login_method" hidden>
                            <div class="d-flex mb-30">
                                <div class="w-100">
                                    <button class="btn w-100" style="border-radius: 0px 10px 10px 0px;" type="button"
                                        id="by_password">
                                        <span style="font-size: 11px">
                                            ورود با
                                            پسورد
                                        </span>
                                    </button>
                                </div>
                                <div class="w-100">
                                    <button class=" btn  w-100" style="border-radius: 10px 0px 0px 10px;" type="button"
                                        id="by_mobile">
                                        <span style="font-size: 11px">
                                            ورود با
                                            شماره
                                            همراه
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div id="inputs">
                                <div class="form-group" id="mobileInput">
                                    <label class="input-label" for="mobile">شماره همراه :</label>
                                    <input type="number" placeholder="*********09"
                                        class="form-control @error('mobile') is-invalid @enderror" name="mobile">
                                    @error('mobile')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group" id="emailInput">
                                    <label class="input-label" for="email">ایمیل | شماره همراه :</label>
                                    <input type="text" placeholder="ایمیل | شماره همراه"
                                        class="form-control @error('email') is-invalid @enderror" name="email">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group" id="passwordInput">
                                    <label class="input-label" for="password">رمز عبور :</label>
                                    <input type="password" placeholder="رمز عبور"
                                        class="form-control @error('password') is-invalid @enderror" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <span>{{ trans('auth.login') }}</span>
                            </button>
                        </form>
                        <div class="mt-30 text-center">
                            <span>{{ trans('auth.dont_have_account') }}</span>
                            <a href="/register" class="text-secondary font-weight-bold">{{ trans('auth.signup') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var loginForm = document.getElementById('loginForm');
        var mobileInput = document.getElementById('mobileInput');
        var passwordInput = document.getElementById('passwordInput');
        var emailInput = document.getElementById('emailInput');
        var byMobileBtn = document.getElementById('by_mobile');
        var byPasswordBtn = document.getElementById('by_password');

        function byMobile() {
            console.log('byMobile');
            document.getElementById('login_method').value = 'by_mobile';
            mobileInput.classList.remove('hidden');
            passwordInput.classList.add('hidden');
            emailInput.classList.add('hidden');
            byMobileBtn.classList.add('login-btn-active');
            byPasswordBtn.classList.remove('login-btn-active');
        }

        function byPassword() {
            console.log('byPassword');
            document.getElementById('login_method').value = 'by_password';
            mobileInput.classList.add('hidden');
            passwordInput.classList.remove('hidden');
            emailInput.classList.remove('hidden');
            byMobileBtn.classList.remove('login-btn-active');
            byPasswordBtn.classList.add('login-btn-active');
        }
        byMobileBtn.addEventListener('click', byMobile);
        byPasswordBtn.addEventListener('click', byPassword);
        byMobile()
    </script>


    @error('email')
        <script>
            byPassword();
        </script>
    @enderror
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

    .login-btn {
        padding: 10px;
        border: #ffce02 solid 1px !important;
        color: #ffce02 !important;
        border: none;
    }

    .login-btn-active {
        background-color: #ffce02 !important;
        color: white !important;
    }

    .hidden {
        display: none;
    }
</style>
