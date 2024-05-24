<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">


<head>
    @include('web.default.includes.metas')
    <title>
        {{ $pageTitle ?? '' }} | آکادمی سیاه کلاه
    </title>

    <!-- General CSS File -->
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet"
        href="/assets/default/css/app.css?ver={{ filemtime(public_path('/assets/default/css/app.css')) }}">

    <link rel="stylesheet" href="/assets/default/css/rtl-app.css">

    <style>
        .btn-green {
            background-color: #2ecc41;
            color: #fff
        }

        .btn-green:hover {
            background-color: white;
            border: solid #2ecc41 1px;
        }

        .btn-outline-green {
            background-color: white;
            border: solid #2ecc41 1px;
            color: #2ecc41
        }

        .btn-outline-green:hover {
            background-color: #2ecc41;
            color: #fff
        }

        .btn-primary {
            background-color: #ffce02 !important
        }

        .cart-banner{
            background-color:black;
            border-radius:20px
        }

        .container {
            max-width: 800px;
        }
    </style>
    @stack('styles_top')
    @stack('scripts_top')

    <style>
        {!! !empty(getCustomCssAndJs('css')) ? getCustomCssAndJs('css') : '' !!} {!! getThemeFontsSettings() !!} {!! getThemeColorsSettings() !!}
    </style>


    @if (!empty($generalSettings['preloading']) and $generalSettings['preloading'] == '1')
        @include('admin.includes.preloading')
    @endif
</head>

<body class="rtl">


    <div class="container">
        <div class="text-center mt-30">
            <img src="https://minio-tosanscp-2ytxtou5.darkube.app/zilink/resized/200x200/e283387d1616c8d0679dc106e132b20d.png"
                class="img-thumbnail rounded-circle">
            <h2 class="mt-2 text-secondary font-weight-bold">علی سیاه کلاه</h2>
            <p class="slide-hint text-gray">کارآفرین و منتور بین المللی </p>
        </div>
        <hr>

        <section>

            @yield('content')

        </section>

        @include('land.components.login')

        <section class="mt-30">
            <h2 class="section-title">مشاوره :</h2>
            <button class="btn btn-warning w-100 mt-3">شک داری ؟ صبحت با کارشناس</button>
            <button class="btn btn-warning w-100 mt-3">رمز چالش</button>
        </section>


        <section class="mt-30">
            <h2 class="section-title">درباره آکادمی :</h2>
            <button class="btn btn-dark w-100 mt-3">داستان موفقیت دانشجویان</button>
        </section>


        <section class="mt-30">
            <h2 class="section-title">پشتیبانی :</h2>
            <button class="btn btn-green w-100 mt-3">پشتیبانی تلگرام</button>
            <div style="border:solid black;padding:15px;border-radius:10px" class="mt-10">
                <h4>اطلاعات تماس :</h4>
                <div class="d-flex mt-5" style="gap: 10px">
                    <div>0912000000</div>
                    <div>-</div>
                    <div>02144098765</div>
                    <div>-</div>
                    <div>02144098765</div>
                </div>
            </div>
        </section>


        <section class="mt-30">
            <h2 class="section-title">هدیه :</h2>
            <button class="btn btn-danger w-100 mt-3">دوره ویکتوری</button>
        </section>


        <footer class="my-30">
            <hr>
            <p class="font-14">
                تمامی حقوق این سایت متعلق به موسسه هوشمند یار اکسیر است. این سایت در زمینه آموزش فریلنسری و تحت قوانین
                جمهوری اسلامی ایران فعالیت می‌کند.</p>
        </footer>
    </div>


    <!-- Template JS File -->
    <script src="/assets/default/js/app.js"></script>
    <script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>
    <script type="text/javascript" src="/assets/default/vendors/simplebar/simplebar.min.js"></script>




    <link rel="stylesheet" href="/assets/default/css/rtl-app.css">

    @stack('styles_top')

    @stack('scripts_top')
    <script>
        $(document).ready(function() {
            $('#webinar_id').val('2023');
            $('#buy_button').on('click', function(event) {
                $('#login_modal').modal({
                    show: true
                })
                $('#buy_type').val('add_cart')
            })
            $('#prepay_button').on('click', function(event) {
                $('#login_modal').modal({
                    show: true
                })
                $('#buy_type').val('prepay')
            })
        });
    </script>


    <style>
        {!! !empty(getCustomCssAndJs('css')) ? getCustomCssAndJs('css') : '' !!} {!! getThemeFontsSettings() !!} {!! getThemeColorsSettings() !!}
    </style>


    @if (empty($justMobileApp) and checkShowCookieSecurityDialog())
        @include('web.default.includes.cookie-security')
    @endif


    <script>
        var deleteAlertTitle = '{{ trans('public.are_you_sure') }}';
        var deleteAlertHint = '{{ trans('public.deleteAlertHint') }}';
        var deleteAlertConfirm = '{{ trans('public.deleteAlertConfirm') }}';
        var deleteAlertCancel = '{{ trans('public.cancel') }}';
        var deleteAlertSuccess = '{{ trans('public.success') }}';
        var deleteAlertFail = '{{ trans('public.fail') }}';
        var deleteAlertFailHint = '{{ trans('public.deleteAlertFailHint') }}';
        var deleteAlertSuccessHint = '{{ trans('public.deleteAlertSuccessHint') }}';
        var forbiddenRequestToastTitleLang = '{{ trans('public.forbidden_request_toast_lang') }}';
        var forbiddenRequestToastMsgLang = '{{ trans('public.forbidden_request_toast_msg_lang') }}';
    </script>

    @if (session()->has('toast'))
        <script>
            (function() {
                "use strict";

                $.toast({
                    heading: '{{ session()->get('toast')['title'] ?? '' }}',
                    text: '{{ session()->get('toast')['msg'] ?? '' }}',
                    bgColor: '@if (session()->get('toast')['status'] == 'success') #43d477 @else #f63c3c @endif',
                    textColor: 'white',
                    hideAfter: 10000,
                    position: 'bottom-right',
                    icon: '{{ session()->get('toast')['status'] }}'
                });
            })(jQuery)
        </script>
    @endif

    @stack('styles_bottom')
    @stack('scripts_bottom')

    <script src="/assets/default/js/parts/main.min.js"></script>

    <script>
        @if (session()->has('registration_package_limited'))
            (function() {
                "use strict";

                handleLimitedAccountModal('{!! session()->get('registration_package_limited') !!}')
            })(jQuery)

            {{ session()->forget('registration_package_limited') }}
        @endif

        {!! !empty(getCustomCssAndJs('js')) ? getCustomCssAndJs('js') : '' !!}
    </script>

    <script>
        function convertToPersianNumbersRecursive(element) {
            const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

            function replaceNumbersInTextNode(node) {
                const text = node.textContent;
                node.textContent = text.replace(/\d/g, digit => persianNumbers[digit]);
            }
            element.childNodes.forEach(child => {
                if (child.nodeType === Node.TEXT_NODE) {
                    replaceNumbersInTextNode(child);
                } else if (child.nodeType === Node.ELEMENT_NODE) {
                    convertToPersianNumbersRecursive(child);
                }
            });
        }
        convertToPersianNumbersRecursive(document.body);
    </script>

</body>

</html>
