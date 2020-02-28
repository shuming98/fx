<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EasyWeChat\Factory;

class UserController extends Controller
{
	//构造方法，New app对象
	public $app = null;
	public function __construct(){
		$config = [
			'app_id' => 'wxffe577a74f2ed038',
			'secret' => '0d1fb7b7214b597ca1cb2b91718a3182',

			'response_type' => 'array',

			'log'=>[
				'level' => 'debug',
				'file' => base_path().'/wechat.log',
 			],
 			'oauth' => [
 				'scopes' => ['snsapi_userinfo'],
 				'callback' => '/wechat/login',
 			],
		];

		//初始化实例
		$this->app = Factory::officialAccount($config);
	}

    public function center(){
    	//页面
    	//未登录跳转到授权登录
    	if(!request()->session()->has('wechat_user')){
    		$oauth = $this->app->oauth;
    		return $oauth->redirect();
    	}
    	return 'Welcome to wechat';
    }

    //获取用户信息的过程，授权登录过程
    public function login(){
    	//获取用户信息
    	$user = $this->app->oauth->user();//第三方登录
    	//把用户信息存储到SESSION
    	session(['wechat_user'=>$user->getId()]);
    	return redirect('wechat/center');
    	//调试：公众号：xxx/wechat/login
    }

    public function logout(){
    	session()->flush();
    }
}
