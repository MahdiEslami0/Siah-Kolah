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
                        <h1 class="font-20 font-weight-bold mt-20">{{ $pageTitle }}</h1>
                        <p class="mt-3 font-12">
                            رمز یکبار مصرف به {{ $user->mobile }} ارسال شده است تا به دست شما خواهد رسید
                        </p>
                        <form class="mt-25" method="POST" action="/register/otp">
                            @csrf
                            <div class="form-group">
                                <label class="input-label" for="mobile">کد تایید :</label>
                                <input required
                                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    type="number" name="code" class="form-control" maxlength="4">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <span>بررسی</span>
                            </button>
                        </form>

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
