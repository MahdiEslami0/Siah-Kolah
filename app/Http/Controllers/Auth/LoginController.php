<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\CartManagerController;
use App\Models\otp;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\UserSession;
use App\User;
use Auth;
use Http;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/panel';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $seoSettings = getSeoMetas('login');
        $pageTitle = trans('site.login_page_title');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('site.login_page_title');
        $pageRobot = getPageRobot('login');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
        ];

        return view(getTemplate() . '.auth.login.login', $data);
    }


    public function login(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'numeric', 'regex:/^[0][9][0-9]{9}$/'],
        ]);
        $user =  user::where('mobile', $request->mobile)->first();
        if (isset($user)) {
            $otp =  otp::where('user_id',  $user->id)->first();
            $key = uuid_create();
            $code = rand(1000, 9999);
            if ($otp && $otp->created_at->diffInMinutes(now()) < 3) {
                $toastData = [
                    'title' => 'کد قبلا ارسال شده است',
                    'msg' => ($otp->created_at->diffInMinutes(now()) - 3) * -1 . ' دقیقه دیگر دوباره تلاش کنید ',
                    'status' => 'error'
                ];
                return redirect(url('login/otp'))->with(['toast' => $toastData]);
            } else {
                if (isset($otp)) {
                    $otp->delete();
                }
                otp::create([
                    'code' =>  $code,
                    'key' => $key,
                    'user_id' => $user->id,
                    'try' => 0
                ]);
                Http::get('http://api.kavenegar.com/v1/2F4E5079575663783031503968356E4E516851634C2F566C6B435A5A7254532B434E3676596443563068733D/verify/lookup.json', [
                    'receptor' => $user->mobile,
                    'token' => $code,
                    'template' => 'verify'
                ]);
                Session::put('otp_key', $key);
                $toastData = [
                    'title' => "موفق",
                    'msg' => 'کد تایید برای شما پیامک شد',
                    'status' => 'success'
                ];
                return redirect(url('login/otp'))->with(['toast' => $toastData]);
            }
        } else {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => 'کاربری یافت نشد',
                'status' => 'error'
            ];
            return redirect()->back()->with(['toast' => $toastData]);
        }
    }

    public function showOtpForm()
    {
        $key = Session::get('otp_key');
        $otp =  otp::where('key', $key)->first();
        $user = User::where('id', $otp->user_id)->first();
        $data = [
            'pageTitle' => 'بررسی کد تایید',
            'user' => $user
        ];
        return view(getTemplate() . '.auth.login.otp', $data);
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


    // public function login(Request $request)
    // {

    //     $type = $request->get('type');

    //     if ($type == 'mobile') {
    //         $rules = [
    //             'mobile' => 'required|numeric',
    //             'country_code' => 'required',
    //             'password' => 'required|min:6',
    //         ];
    //     } else {
    //         $rules = [
    //             'email' => 'required|email|exists:users,email',
    //             'password' => 'required|min:6',
    //         ];
    //     }

    //     if (!empty(getGeneralSecuritySettings('captcha_for_login'))) {
    //         $rules['captcha'] = 'required|captcha';
    //     }

    //     $this->validate($request, $rules);

    //     if ($type == 'mobile') {
    //         $value = $this->getUsernameValue($request);

    //         $checkIsValid = checkMobileNumber("+{$value}");

    //         if (!$checkIsValid) {
    //             $errors['mobile'] = [trans('update.mobile_number_is_not_valid')];
    //             return back()->withErrors($errors)->withInput($request->all());
    //         }
    //     }

    //     if ($this->attemptLogin($request)) {
    //         return $this->afterLogged($request);
    //     }

    //     return $this->sendFailedLoginResponse($request);
    // }

    // public function logout(Request $request)
    // {
    //     $user = auth()->user();

    //     $this->guard()->logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();

    //     if (!empty($user) and $user->logged_count > 0) {

    //         $user->update([
    //             'logged_count' => $user->logged_count - 1
    //         ]);
    //     }

    //     return redirect('/');
    // }

    // public function username()
    // {
    //     $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

    //     if (empty($this->username)) {
    //         $this->username = 'mobile';
    //         if (preg_match($email_regex, request('username', null))) {
    //             $this->username = 'email';
    //         }
    //     }
    //     return $this->username;
    // }

    // protected function getUsername(Request $request)
    // {
    //     $type = $request->get('type');

    //     if ($type == 'mobile') {
    //         return 'mobile';
    //     } else {
    //         return 'email';
    //     }
    // }

    // protected function getUsernameValue(Request $request)
    // {
    //     $type = $request->get('type');
    //     $data = $request->all();

    //     if ($type == 'mobile') {
    //         return ltrim($data['country_code'], '+') . ltrim($data['mobile'], '0');
    //     } else {
    //         return $request->get('email');
    //     }
    // }

    // protected function attemptLogin(Request $request)
    // {
    //     $credentials = [
    //         $this->getUsername($request) => $this->getUsernameValue($request),
    //         'password' => $request->get('password')
    //     ];
    //     $remember = true;

    //     /*if (!empty($request->get('remember')) and $request->get('remember') == true) {
    //         $remember = true;
    //     }*/

    //     return $this->guard()->attempt($credentials, $remember);
    // }

    // public function sendFailedLoginResponse(Request $request)
    // {
    //     throw ValidationException::withMessages([
    //         $this->getUsername($request) => [trans('validation.password_or_username')],
    //     ]);
    // }

    // protected function sendBanResponse(Request $request, $user)
    // {
    //     throw ValidationException::withMessages([
    //         $this->getUsername($request) => [trans('auth.ban_msg', ['date' => dateTimeFormat($user->ban_end_at, 'j M Y')])],
    //     ]);
    // }

    // protected function sendNotActiveResponse($user)
    // {
    //     $toastData = [
    //         'title' => trans('public.request_failed'),
    //         'msg' => trans('auth.login_failed_your_account_is_not_verified'),
    //         'status' => 'error'
    //     ];

    //     return redirect('/login')->with(['toast' => $toastData]);
    // }

    // protected function sendMaximumActiveSessionResponse()
    // {
    //     $toastData = [
    //         'title' => trans('update.login_failed'),
    //         'msg' => trans('update.device_limit_reached_please_try_again'),
    //         'status' => 'error'
    //     ];

    //     return redirect('/login')->with(['login_failed_active_session' => $toastData]);
    // }

    // public function afterLogged(Request $request, $verify = false)
    // {
    //     $user = auth()->user();

    //     if ($user->ban) {
    //         $time = time();
    //         $endBan = $user->ban_end_at;
    //         if (!empty($endBan) and $endBan > $time) {
    //             $this->guard()->logout();
    //             $request->session()->flush();
    //             $request->session()->regenerate();

    //             return $this->sendBanResponse($request, $user);
    //         } elseif (!empty($endBan) and $endBan < $time) {
    //             $user->update([
    //                 'ban' => false,
    //                 'ban_start_at' => null,
    //                 'ban_end_at' => null,
    //             ]);
    //         }
    //     }

    //     if ($user->status != User::$active and !$verify) {
    //         $this->guard()->logout();
    //         $request->session()->flush();
    //         $request->session()->regenerate();

    //         $verificationController = new VerificationController();
    //         $checkConfirmed = $verificationController->checkConfirmed($user, $this->username(), $request->get('username'));

    //         if ($checkConfirmed['status'] == 'send') {
    //             return redirect('/verification');
    //         }
    //     } elseif ($verify) {
    //         session()->forget('verificationId');

    //         $user->update([
    //             'status' => User::$active,
    //         ]);

    //         $registerReward = RewardAccounting::calculateScore(Reward::REGISTER);
    //         RewardAccounting::makeRewardAccounting($user->id, $registerReward, Reward::REGISTER, $user->id, true);
    //     }

    //     if ($user->status != User::$active) {
    //         $this->guard()->logout();
    //         $request->session()->flush();
    //         $request->session()->regenerate();

    //         return $this->sendNotActiveResponse($user);
    //     }

    //     $checkLoginDeviceLimit = $this->checkLoginDeviceLimit($user);
    //     if ($checkLoginDeviceLimit != "ok") {
    //         $this->guard()->logout();
    //         $request->session()->flush();
    //         $request->session()->regenerate();

    //         return $this->sendMaximumActiveSessionResponse();
    //     }

    //     $user->update([
    //         'logged_count' => (int)$user->logged_count + 1
    //     ]);

    //     $cartManagerController = new CartManagerController();
    //     $cartManagerController->storeCookieCartsToDB();

    //     if ($user->isAdmin()) {
    //         return redirect(getAdminPanelUrl() . '');
    //     } else {
    //         return redirect('/panel');
    //     }
    // }

    // private function checkLoginDeviceLimit($user)
    // {
    //     $securitySettings = getGeneralSecuritySettings();

    //     if (!empty($securitySettings) and !empty($securitySettings['login_device_limit'])) {
    //         $limitCount = !empty($securitySettings['number_of_allowed_devices']) ? $securitySettings['number_of_allowed_devices'] : 1;

    //         $count = $user->logged_count;

    //         if ($count >= $limitCount) {
    //             return "no";
    //         }
    //     }

    //     return 'ok';
    // }
}
