<!-- Modal -->
<div class="modal fade" id="login_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="login-card">
                            <form class="mt-25" method="POST" action="/login" id="loginForm" name="loginForm">
                                @csrf
                                <input type="text" name="login_method" value="by_mobile" hidden>
                                <input type="text" name="method" value="ajax" hidden>
                                <div>
                                    <div class="form-group" id="mobileInput">
                                        <label class="input-label" for="mobile">شماره همراه :</label>
                                        <input type="number" placeholder="*********09" class="form-control"
                                            name="mobile">
                                    </div>
                                </div>
                                <button type="submit" form="loginForm" class="btn btn-warning w-100">
                                    <span>ادامه</span>
                                </button>
                            </form>
                            <form class="mt-25" method="POST" action="/login/otp" id="otpForm" name="otpForm">
                                <p class="text-warning my-10" role="alert" id="message">
                                </p>
                                @csrf
                                <input type="text" hidden name="buy_type" id="buy_type" value="add_cart">
                                <input type="text" hidden name="webinar_id" id="webinar_id" value="2024">
                                <div>
                                    <div class="form-group" id="mobileInput">
                                        <label class="input-label" for="mobile">کد تایید :</label>
                                        <input type="number" placeholder="****" class="form-control" name="code">
                                    </div>
                                </div>
                                <button type="submit" form="otpForm" class="btn btn-warning w-100">
                                    <span>بررسی</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_top')
    <script>
        $(document).ready(function() {
            $('#otpForm').hide();
            var key = '';
            var message = '';
            var buttonText = 'درحال بررسی ...';
            $('#loginForm').off('submit');
            $('#loginForm').on('submit', function(event) {
                event.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var method = form.attr('method');
                var formData = form.serialize();
                var button = form.find('button[type="submit"]');
                button.prop('disabled', false).text(buttonText);
                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        key = response.key;
                        $('#message').html('کد تایید ارسال شد');
                        $('#loginForm').hide();
                        $('#otpForm').show();
                    },
                    error: function(xhr, status, error) {
                        message = xhr.responseJSON.message;
                        $('#message').html(message);
                        $('#loginForm').hide();
                        $('#otpForm').show();
                    },
                    complete: function() {
                        button.prop('disabled', false).text('ادامه');
                    }
                });
            });
        });
        $('#loginForm').on('submit', function(event) {
            event.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var formData = form.serialize();
            var button = form.find('button[type="submit"]');
            button.prop('disabled', true).text(buttonText);
            $.ajax({
                type: method,
                url: url,
                data: formData,
                success: function(response) {
                    key = response.key
                    $('#message').html('کد تایید ارسال شد')
                    $('#loginForm').hide();
                    $('#otpForm').show();
                },
                error: function(xhr, status, error) {
                    message = xhr.responseJSON.message
                    $('#message').html(message)
                    $('#loginForm').hide();
                    $('#otpForm').show();
                },
                complete: function() {
                    button.prop('disabled', false).text('بررسی');
                }
            });
        });
    </script>
@endpush
