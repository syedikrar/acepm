<?php

namespace App\Http\Controllers\Auth;

use App\Dealer;
use App\Models\Shop;
use App\Notifications\ApproveRequest;
use App\Models\User;
use function Aws\clear_compiled_json;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyInvitationMail;


class RegisterController extends Controller
{
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        if($request->input('email') && $request->input('invite') === 'true'){
           
            $user = User::where('email', '=', $request->input('email'))->first();
            if($user !== null){
                return response()->json([
                    'status' => 'user found',
                    'data'   => $request->input('email'),
                    'message' => 'User email already exists'
                ]);
            }
            if($user ===  null){
                Mail::to($request->email)->send(new VerifyInvitationMail($request->email));
                // check for failures
                if (Mail::failures()) {
                    return response()->json([
                        'status'  => 'failure',
                        'message' => 'Oops error occur while sending mail'
                    ]);
                }
                else{
                     //merging invite with request
                    $requestm =$request->merge(['invite' => 'staff']);
                    event(new Registered($user = $this->create($requestm)));
                    return $this->registered($request, $user);
                }
            }
        }
        if($request->input('email') && $request->input('name') && $request->input('password') && $request->input('invite') == ''){
            $user = User::where('email', '=', $request->input('email'))->first();
            
            if($user !== null && $user->role === 'staff'){
                $requestData = $request->merge(['invite' => $user->role]);
                $data = $request->all();
                $this->updateStaffUser($data);
            }
            if($user == null)
            {
                $this->validator($request->all())->validate();
                event(new Registered($user = $this->create($request)));
                return $this->registered($request, $user);
            }
        }
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request = null, $user)
    {
        $token = auth()->login($user);
        return response()->json(compact('token', 'user'));
    }

    public function socialLogin($provider){
        // Socialite will pick response data automatic
        $user = Socialite::driver($provider)->stateless()->user();
        $user->platform = $provider;
        $user = $this->updateOrCreate((array)$user);

        return $this->registered(null, $user->load(['education', 'skills']));
    }

    /**
     * @return string
     */
    private function generatePassword(){
        return hash('crc32',Str::random(8));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }


    /**
     * @param Request $request
     * @return mixed
     */
    protected function create(Request $request)
    {
        $data = $request->all();
        $role = isset($data['invite']) ? 'staff' : 'seller';
        
        if($role === 'seller'){
            
            $newData = [
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => bcrypt($data['password']),
                'role'      => $role,
            ];
            $user = User::create($newData);
             if(!isset($user->approved_at)) $user->notify(new ApproveRequest());

            return $user;
            
        }
        if($role == 'staff'){
            
            $newData = [
                'name'      => isset($data['name']) ? $data['name'] : '',
                'email'     => isset($data['email']) ? $data['email'] : '',
                'password'  => isset($data['password']) ? bcrypt($data['password']) : '',
                'role'      => $role,
            ];
            $user = User::create($newData);
            $shop = Shop::where('domain', $request->cookie('ace-shop'))->first();
            $user->shops()->attach($shop);

            // if(!isset($user->approved_at)) $user->notify(new ApproveRequest());

            return $user;
        }
        
    }

    protected function updateOrCreate(array $data)
    {
        $exist = User::Where('email', '=', $data['email'])->first();
        $record = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => 'seller',
            'approved_at' => Carbon::now(),
            'profile_picture' => $data['avatar_original'],
            'login_platform'  => $data['platform'],
            'login_platform_token'  => $data['token'],
        ];

        if(!$exist) $record['password'] = bcrypt($this->generatePassword());

        return User::updateOrCreate(['email' => $data['email']], $record);
    }

    /**
     * @param array $request
     * 
     */
    protected function updateStaffUser(array $data)
    {   
        $exist = User::Where('email', '=', $data['email'])->first();
        $record = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            // 'approved_at' => Carbon::now(),
            'password' => bcrypt($data['password'])
        ];

        return User::updateOrCreate(['email' => $data['email']], $record);
    }

    public function socialLoginCallBack(Request $request){}
}
