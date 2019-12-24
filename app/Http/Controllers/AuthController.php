<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    public function __construct()
    {
       # $this->middleware('auth:api')->only('logout');
    }

    public function register(Request $request)
    {
        // 校验注册字段
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|email|max:255|unique:users',
            'password' => "required|string|min:6",
            'src' => 'bail|active_url|max:255'
        ],[],[
            'name' => '账号',
            'password' => '密码',
            'src' => '头像',
        ]);


        if ($validator->fails()) {
            return [
                'errno' => 1,
                'message' => str_replace(' ','' ,$validator->errors()->first())
            ];
        }
        $email = $request->get('name');
        try
        {
            $user = User::create([
                'name' => substr($email,0,strpos($email,'@')),
                'email' => $email,
                'avatar' => $request->input('src'),
                'password' => Hash::make($request->input('password')),
                'api_token' => Str::random(60)
            ]);

            if($user)
            {
                return [
                    'errno' => 0,
                    'user' => $user,
                    'message' => '用户注册成功'
                ];
            }
            else
            {
                return [
                    'errno' => 1,
                    'message' => '保存用户到数据库失败'
                ];
            }
        }
        catch (QueryException $e)
        {
            return [
                'errno' => 1,
                'message' => '保存用户到数据库异常：' . $e->getMessage()
            ];
        }
    }

    /**
     * 登录接口
     * @param Request $request
     * @author Red-Bo
     * @return array
     * @date 2019-12-11 20:01
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'     => 'required|string',
            'password'  => 'required|string',
        ],[],[
            'name' => '用户账号', 'password' => '密码',
        ]);

        if ($validator->fails()) {
            return [ 'errno' => 1, 'message' =>str_replace(' ','' ,$validator->errors()->first()) ];
        }
        $email    = $request->input('name');
        $password = $request->input('password');
        $user = User::where('email', $email)->first();
        // 用户校验成功则返回 Token 信息
        if ($user && Hash::check($password, $user->password)) {
            $user->api_token = Str::random(60);
            $user->save();
            return [ 'errno' => 0, 'user' => $user,'message' => '用户登录成功' ];
        }
        return [ 'errno' => 1, 'message' => '用户名和密码不匹配，请重新输入'];
    }

    public function logout(Request $request)
    {



        $user = Auth::guard("auth:api")->user();
        $userModel = User::find($user->id);
        $userModel->api_token = null;
        $userModel->save();
        return [ 'errno' => 1, 'message' => '用户退出成功!'];
    }
}
