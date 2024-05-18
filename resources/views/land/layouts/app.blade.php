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


    @yield('content')


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
