<div class="row mt-15" id="offlineBanks">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="input-label">{{ trans('financial.account') }}</label>
                <select name="account" class="form-control @error('account') is-invalid @enderror">
                    <option selected disabled>{{ trans('financial.select_the_account') }}</option>

                    @foreach ($offlineBanks as $offlineBank)
                        <option value="{{ $offlineBank->id }}" @if (!empty($editOfflinePayment) and $editOfflinePayment->offline_bank_id == $offlineBank->id) selected @endif>
                            {{ $offlineBank->title }}
                        </option>
                    @endforeach
                </select>

                @error('account')
                    <div class="invalid-feedback"> {{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="referralCode" class="input-label">{{ trans('admin/main.referral_code') }}</label>
                <input type="text" name="referral_code" id="referralCode"
                    value="{{ !empty($editOfflinePayment) ? $editOfflinePayment->reference_number : old('referral_code') }}"
                    class="form-control @error('referral_code') is-invalid @enderror" />
                @error('referral_code')
                    <div class="invalid-feedback"> {{ $message }}</div>
                @enderror
            </div>
        </div>
        @php
            $now = time();
        @endphp
        <div class="col-md-4" style="display: none">
            <div class="form-group">
                <label class="input-label">{{ trans('public.date_time') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="dateRangeLabel">
                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                        </span>
                    </div>
                    <input type="text" name="date" value="{{ $now }}"
                        class="form-control datetimepicker @error('date') is-invalid @enderror"
                        aria-describedby="dateRangeLabel" />
                    @error('date')
                        <div class="invalid-feedback"> {{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="form-group">
                <label class="input-label">{{ trans('update.attach_the_payment_photo') }}</label>

                <label for="attachmentFile" id="attachmentFileLabel" class="custom-upload-input-group">
                    <div class="custom-upload-input"></div>
                    <span class="custom-upload-icon text-white">
                        <i data-feather="upload" width="18" height="18" class="text-white"></i>
                    </span>
                </label>
                <input type="file" name="attachment" id="attachmentFile"
                    class="form-control h-auto invisible-file-input @error('attachment') is-invalid @enderror"
                    value="" />
                @error('attachment')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div> --}}
    </div>
    <div>
        @foreach ($offlineBanks as $offlineBank)
            <div class="col-12 col-lg-4 mb-30 mb-lg-0">
                <div
                    class="py-25 px-20 rounded-sm panel-shadow d-flex flex-column align-items-center justify-content-center">
                    <img src="{{ $offlineBank->logo }}" width="60" height="60" alt="">

                    <div class="mt-15 mt-30 w-100">

                        <div class="d-flex align-items-center justify-content-between">
                            <span class="font-14 font-weight-500 text-secondary">{{ trans('public.name') }}:</span>
                            <span class="font-14 font-weight-500 text-gray">{{ $offlineBank->title }}</span>
                        </div>

                        @foreach ($offlineBank->specifications as $specification)
                            <div class="d-flex align-items-center justify-content-between mt-10">
                                <span class="font-14 font-weight-500 text-secondary">{{ $specification->name }}:</span>
                                <span class="font-14 font-weight-500 text-gray">{{ $specification->value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts_bottom')
    <script>
        offlineBanks.style.display = "none";

        function showHideDiv(gateway) {
            var offlineBanks = document.getElementById("offlineBanks");
            if (gateway === "cart") {
                offlineBanks.style.display = "block";
            } else {
                offlineBanks.style.display = "none";
            }
        }
    </script>
@endpush
