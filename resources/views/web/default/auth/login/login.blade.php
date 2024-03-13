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
                        <h1 class="font-20 font-weight-bold mt-20">{{ trans('auth.login_h1') }}</h1>
                        <form class="mt-25" method="POST" action="/login">
                            @csrf
                            <div class="form-group">
                                <label class="input-label" for="mobile">شماره همراه :</label>
                                <input required type="number" placeholder="*********09" class="form-control"
                                    name="mobile">
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
