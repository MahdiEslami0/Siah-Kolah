@extends('land.layouts.app')

@push('styles_top')
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

        .container {
            max-width: 600px;
        }
    </style>
@endpush

@push('scripts_top')
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
@endpush

@section('content')
    <div class="container">
        <div class="text-center mt-30">
            <img src="https://minio-tosanscp-2ytxtou5.darkube.app/zilink/resized/200x200/e283387d1616c8d0679dc106e132b20d.png"
                class="img-thumbnail rounded-circle">
            <h2 class="mt-2 text-secondary font-weight-bold">علی سیاه کلاه</h2>
            <p class="slide-hint text-gray">کارآفرین و منتور بین المللی </p>
        </div>
        <hr>

        <section>
            {{-- <h2 class="section-title">خرید دوره :</h2> --}}
            <div class="row align-items-center mt-30">
                <div class="col-4">
                    <img src="https://siahkolah.com/wp-content/uploads/2023/08/%D9%81%D8%B1%DB%8C%D9%84%D9%86%D8%B3%DB%8C%D9%86%DA%AF-%D8%AF%D9%84%D8%A7%D8%B1%DB%8C-%D9%85%D8%B9%D9%85%D9%88%D9%84%DB%8C-914x800.png"
                        width="100%">
                </div>
                <div class="col-8">
                    <h3>دوره فریلنسینگ دلاری + کوچینگ دلاری</h3>
                    <p class="slide-hint text-gray mt-5 font-12">
                        دوره فریلنسینگ دلاری برای کمک به شما در یافتن مشتریان جهانی و کسب درآمد دلاری صرف نظر از سطح تخصص و
                        مهارتی که دارید طراحی شده است. در این دوره یاد می‌گیرید چگونه به طور موثر با مشتریان جهانی تعامل
                        داشته باشید و توجه آن‌ها را برای گرفتن پروژه‌های دلاری جلب کنید.
                    </p>
                </div>
            </div>



            <div class="row">
                @if (auth()->user())
                    <div class="col-6">
                        <form action="/cart/store" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="2023">
                            <input type="hidden" name="item_name" value="webinar_id">
                            <button type="submit" class="btn btn-green w-100 mt-3">خرید</button>
                        </form>
                    </div>
                @else
                    <div class="col-6">
                        <button class="btn btn-green w-100 mt-3" type="button" id="buy_button">خرید</button>
                    </div>
                @endif

                @if (auth()->user())
                    <div class="col-6">
                        <a href="/prepay/2023">
                            <button class="btn  btn-outline-green w-100 mt-3">پیش واریز</button>
                        </a>
                    </div>
                @else
                    <div class="col-6">
                        <button class="btn  btn-outline-green w-100 mt-3" id="prepay_button">پیش واریز</button>
                    </div>
                @endif


            </div>
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
@endsection
