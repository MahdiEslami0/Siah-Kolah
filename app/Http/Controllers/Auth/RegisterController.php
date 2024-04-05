<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\traits\UserFormFieldsTrait;
use App\Mixins\RegistrationBonus\RegistrationBonusAccounting;
use App\Models\Affiliate;
use App\Models\otp;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Role;
use App\Models\UserMeta;
use App\User;
use Auth;
use Http;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Session;

class RegisterController extends Controller
{

    use UserFormFieldsTrait;

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(Request $request)
    {

        $data = [
            'pageTitle' => "ثبت نام",

        ];
        return view(getTemplate() . '.auth.register.register', $data);
    }

    public function register(Request $request)
    {
        // $key = uuid_create();
        // $code = rand(1000, 9999);
        $rules = [
            'full_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'mobile' => 'required|numeric|unique:users|regex:/^[0][9][0-9]{9,9}$/',
        ];
        $Validator =  Validator::make($request->all(), $rules);

        if ($Validator->fails()) {
            throw new ValidationException($Validator);
        }
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => hash::make($request->password),
            'role_id' => 1,
            'role_name' => 'user',
            'status' => 'active',
            'created_at' => time()
        ]);
        FacadesAuth::login($user);
        $toastData = [
            'title' => "موفق",
            'msg' => 'ثبت نام با موفقیت انجام شد',
            'status' => 'success'
        ];
        return redirect(url('/'))->with(['toast' => $toastData]);
        // otp::create([
        //     'code' =>  $code,
        //     'key' => $key,
        //     'user_id' => $user->id,
        //     'try' => 0
        // ]);
        // Http::get('http://api.kavenegar.com/v1/2F4E5079575663783031503968356E4E516851634C2F566C6B435A5A7254532B434E3676596443563068733D/verify/lookup.json', [
        //     'receptor' => $request->mobile,
        //     'token' => $code,
        //     'template' => 'verify'
        // ]);
        // Session::put('otp_key', $key);
        // $toastData = [
        //     'title' => "موفق",
        //     'msg' => 'کد تایید برای شما پیامک شد',
        //     'status' => 'success'
        // ];
        // return redirect(url('register/otp'))->with(['toast' => $toastData]);
    }


    public function showOtpForm(Request $request)
    {
        $key = Session::get('otp_key');
        $otp =  otp::where('key', $key)->first();
        $user = User::where('id', $otp->user_id)->first();
        $data = [
            'pageTitle' => 'بررسی کد تایید',
            'user' => $user
        ];
        return view(getTemplate() . '.auth.register.otp', $data);
    }

    public function otp(Request $request)
    {
        $key = Session::get('otp_key');
        $otp =  otp::where('key', $key)->first();
        if ($otp->try >= 3) {
            $otp->delete();
            $toastData = [
                'title' => 'خطا',
                'msg' => 'تلاش بیش ازحد مجاز',
                'status' => 'danger'
            ];
            return redirect(url('/login'))->with(['toast' => $toastData]);
        }
        $user = User::where('id', $otp->user_id)->first();
        if ($request->code == $otp->code) {
            Auth::login($user);
            $toastData = [
                'title' => 'موفق',
                'msg' => 'ورود موفق',
                'status' => 'success'
            ];
            $otp->delete();
            return redirect(url('/'))->with(['toast' => $toastData]);
        } else {
            $otp->try =  $otp->try + 1;
            $otp->save();
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => 'کد تایید اشتباه است',
                'status' => 'danger'
            ];
            return redirect()->back()->with(['toast' => $toastData]);
        }
    }

    // /**
    //  * Get a validator for an incoming registration request.
    //  *
    //  * @param array $data
    //  * @return \Illuminate\Contracts\Validation\Validator
    //  */
    // protected function validator(array $data)
    // {
    //     $registerMethod = getGeneralSettings('register_method') ?? 'mobile';

    //     if (!empty($data['mobile']) and !empty($data['country_code'])) {
    //         $data['mobile'] = ltrim($data['country_code'], '+') . ltrim($data['mobile'], '0');
    //     }

    //     $rules = [
    //         'country_code' => ($registerMethod == 'mobile') ? 'required' : 'nullable',
    //         'mobile' => (($registerMethod == 'mobile') ? 'required' : 'nullable') . '|numeric|unique:users',
    //         'email' => (($registerMethod == 'email') ? 'required' : 'nullable') . '|email|max:255|unique:users',
    //         // 'term' => 'required',
    //         'full_name' => 'required|string|min:3',
    //         'password' => 'required|string|min:6|confirmed',
    //         'password_confirmation' => 'required|same:password',
    //         'referral_code' => 'nullable|exists:affiliates_codes,code'
    //     ];

    //     if (!empty(getGeneralSecuritySettings('captcha_for_register'))) {
    //         $rules['captcha'] = 'required|captcha';
    //     }


    //     return Validator::make($data, $rules);
    // }

    // /**
    //  * Create a new user instance after a valid registration.
    //  *
    //  * @param array $data
    //  * @return
    //  */
    // protected function create(array $data)
    // {
    //     if (!empty($data['mobile']) and !empty($data['country_code'])) {
    //         $data['mobile'] = ltrim($data['country_code'], '+') . ltrim($data['mobile'], '0');
    //     }

    //     $referralSettings = getReferralSettings();
    //     $usersAffiliateStatus = (!empty($referralSettings) and !empty($referralSettings['users_affiliate_status']));

    //     if (empty($data['timezone'])) {
    //         $data['timezone'] = getGeneralSettings('default_time_zone') ?? null;
    //     }

    //     $disableViewContentAfterUserRegister = getFeaturesSettings('disable_view_content_after_user_register');
    //     $accessContent = !((!empty($disableViewContentAfterUserRegister) and $disableViewContentAfterUserRegister));

    //     $roleName = Role::$user;
    //     $roleId = Role::getUserRoleId();

    //     if (!empty($data['account_type'])) {
    //         if ($data['account_type'] == Role::$teacher) {
    //             $roleName = Role::$teacher;
    //             $roleId = Role::getTeacherRoleId();
    //         } else if ($data['account_type'] == Role::$organization) {
    //             $roleName = Role::$organization;
    //             $roleId = Role::getOrganizationRoleId();
    //         }
    //     }

    //     $user = User::create([
    //         'role_name' => $roleName,
    //         'role_id' => $roleId,
    //         'mobile' => $data['mobile'] ?? null,
    //         'email' => $data['email'] ?? null,
    //         'full_name' => $data['full_name'],
    //         'status' => User::$pending,
    //         'access_content' => $accessContent,
    //         'password' => Hash::make($data['password']),
    //         'affiliate' => $usersAffiliateStatus,
    //         'timezone' => $data['timezone'] ?? null,
    //         'created_at' => time()
    //     ]);

    //     if (!empty($data['certificate_additional'])) {
    //         UserMeta::updateOrCreate([
    //             'user_id' => $user->id,
    //             'name' => 'certificate_additional'
    //         ], [
    //             'value' => $data['certificate_additional']
    //         ]);
    //     }

    //     $this->storeFormFields($data, $user);

    //     return $user;
    // }


    // public function register(Request $request)
    // {
    //     $validate = $this->validator($request->all());
    //     if ($validate->fails()) {
    //         $errors = $validate->errors();

    //         $form = $this->getFormFieldsByType($request->get('account_type'));

    //         if (!empty($form)) {
    //             $fieldErrors = $this->checkFormRequiredFields($request, $form);

    //             if (!empty($fieldErrors) and count($fieldErrors)) {
    //                 foreach ($fieldErrors as $id => $error) {
    //                     $errors->add($id, $error);
    //                 }
    //             }
    //         }

    //         throw new ValidationException($validate);
    //     } else {
    //         $form = $this->getFormFieldsByType($request->get('account_type'));
    //         $errors = [];

    //         if (!empty($form)) {
    //             $fieldErrors = $this->checkFormRequiredFields($request, $form);

    //             if (!empty($fieldErrors) and count($fieldErrors)) {
    //                 foreach ($fieldErrors as $id => $error) {
    //                     $errors[$id] = $error;
    //                 }
    //             }
    //         }

    //         if (count($errors)) {
    //             return back()->withErrors($errors)->withInput($request->all());
    //         }
    //     }


    //     $data = $request->all();

    //     if (!empty($data['mobile']) and !empty($data['country_code'])) {
    //         $data['mobile'] = $data['country_code'] . ltrim($data['mobile'], '0');
    //     }


    //     if (!empty($data['mobile'])) {
    //         $checkIsValid = checkMobileNumber($data['mobile']);

    //         if (!$checkIsValid) {
    //             $errors['mobile'] = [trans('update.mobile_number_is_not_valid')];
    //             return back()->withErrors($errors)->withInput($request->all());
    //         }
    //     }

    //     $user = $this->create($request->all());

    //     event(new Registered($user));

    //     $notifyOptions = [
    //         '[u.name]' => $user->full_name,
    //         '[u.role]' => trans("update.role_{$user->role_name}"),
    //         '[time.date]' => dateTimeFormat($user->created_at, 'j M Y H:i'),
    //     ];
    //     sendNotification("new_registration", $notifyOptions, 1);

    //     $registerMethod = getGeneralSettings('register_method') ?? 'mobile';

    //     $value = $request->get($registerMethod);
    //     if ($registerMethod == 'mobile') {
    //         $value = $request->get('country_code') . ltrim($request->get('mobile'), '0');
    //     }

    //     $referralCode = $request->get('referral_code', null);
    //     if (!empty($referralCode)) {
    //         session()->put('referralCode', $referralCode);
    //     }

    //     $verificationController = new VerificationController();
    //     $checkConfirmed = $verificationController->checkConfirmed($user, $registerMethod, $value);

    //     $referralCode = $request->get('referral_code', null);

    //     if ($checkConfirmed['status'] == 'send') {

    //         if (!empty($referralCode)) {
    //             session()->put('referralCode', $referralCode);
    //         }

    //         return redirect('/verification');
    //     } elseif ($checkConfirmed['status'] == 'verified') {
    //         $this->guard()->login($user);

    //         $enableRegistrationBonus = false;
    //         $registrationBonusAmount = null;
    //         $registrationBonusSettings = getRegistrationBonusSettings();
    //         if (!empty($registrationBonusSettings['status']) and !empty($registrationBonusSettings['registration_bonus_amount'])) {
    //             $enableRegistrationBonus = true;
    //             $registrationBonusAmount = $registrationBonusSettings['registration_bonus_amount'];
    //         }


    //         $user->update([
    //             'status' => User::$active,
    //             'enable_registration_bonus' => $enableRegistrationBonus,
    //             'registration_bonus_amount' => $registrationBonusAmount,
    //         ]);

    //         $registerReward = RewardAccounting::calculateScore(Reward::REGISTER);
    //         RewardAccounting::makeRewardAccounting($user->id, $registerReward, Reward::REGISTER, $user->id, true);

    //         if (!empty($referralCode)) {
    //             Affiliate::storeReferral($user, $referralCode);
    //         }

    //         $registrationBonusAccounting = new RegistrationBonusAccounting();
    //         $registrationBonusAccounting->storeRegistrationBonusInstantly($user);

    //         if ($response = $this->registered($request, $user)) {
    //             return $response;
    //         }

    //         return $request->wantsJson()
    //             ? new JsonResponse([], 201)
    //             : redirect($this->redirectPath());
    //     }
    // }
}
