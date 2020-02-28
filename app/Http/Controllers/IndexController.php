<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Image;
class IndexController extends Controller
{	
	//构造方法，New app对象
	public $app = null;
	public function __construct(){
		$config = [
			'app_id' => 'wxffe577a74f2ed038',
			'secret' => '0d1fb7b7214b597ca1cb2b91718a3182',
			'token' => 'LINUX',
			'response_type' => 'array',

			'log'=>[
				'level' => 'debug',
				'file' => base_path().'/wechat.log',
 			],
		];

		//初始化实例
		$this->app = Factory::officialAccount($config);
	}

    public function index(){
		$this->app->server->push(function($message){
			if($message['MsgType'] == 'event'){
				return $this->sub($message);
			}else{
				return $this->other($message);
			}
		});

		$response = $this->app->server->serve();

		// 将响应输出
		return $response;
    }

    //订阅消息回复
    public function sub($message){
    	$openId = $message['FromUserName'];
    	$userinfo = new \App\User();
    	if($message['Event'] == 'subscribe'){
    		//查询数据库有没用户信息
    		$Query = $userinfo->where('openid',$openId)->first();
    		if($Query){
    			$Query->status = 1;
    			$Query->save();
    		}else{
	    		$user = $this->app->user->get($openId);
	    		//获取用户信息入库
	    		$userinfo->name = $user['nickname'];
	    		$userinfo->openid = $openId;
	    		$userinfo->pubtime = time();
	    		if($message['EventKey']){
	    			//获取代理信息
	    			$qopenid = str_replace('qrscene_','',$message['EventKey']);
	    			$pUser = $userinfo->where('openid',$qopenid)->first();
	    			//区分上下级关系，区分三级关系
	    			$userinfo->p1 = $pUser->id;
	    			$userinfo->p2 = $pUser->p1;
	    			$userinfo->p3 = $pUser->p2;
	    		}
	    		$userinfo->save();
    		}
    		$this->qrcode($openId);
    		return $this->code($openId);
    		//return '你好，欢迎关注我的公众号';
    	}elseif($message['Event'] == 'unsubscribe'){
    		$Query = $userinfo->where('openid',$openId)->first();
    		if($Query){
    			$Query->status = 0;
    			$Query->save();
    		}
    	}
    }

    //文本图片消息回复
    public function other($message){
    	switch($message['MsgType']){
    		case 'text':
    		//return $this->wenben($message);
    		return '你好';
    		break;

    		case 'image':
    		return $this->wenben($message);
    		break;

    		default:
    		return '有事？';
    		break;
    	}
    }

    //回复图片消息
    public function wenben($message){
    	if($message['Content'] == '图片'){
    		//上传本地图片到素材管理库
    		$res = $this->app->material->uploadImage(public_path()."/1.png");
    		//获取素材图片Id
    		$media_id = $res['media_id'];
    		//回复图片
    		$image = new Image($media_id);
    		return $image;
    	}else{
    		return '有事?';
    	}
    }

    //生成带参数的二维码
    public function qrcode($openId){
    	//创建ticket
    	$res = $this->app->qrcode->forever($openId);
    	$ticket = $res['ticket'];
    	//通过ticket生成二维码
    	$url = $this->app->qrcode->url($ticket);
    	$content = file_get_contents($url);
    	file_put_contents(public_path().'/'.$openId.'.png',$content);
    }

    //二维码推广，发送二维码图片 
    public function code($openId){
    	//上传本地图片到素材管理库
		$res = $this->app->material->uploadImage(public_path().'/'.$openId.'.png');
		//获取素材图片Id
		$media_id = $res['media_id'];
		//回复图片
		$image = new Image($media_id);
		return $image;
    }

    public function login(){
    	return view('login');
    }

    public function center(){
    	//code->access_token->userInfo
        //获取code
    	//获取access_token
		$code = $_GET['code'];
		$url = "https://api.weibo.com/oauth2/access_token";
		$data = [
			'client_id' => '3495392613',
			'client_secret' => 'e8c05b39fa0af824be575ac8ffa46221',
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => 'http://qingfeng.wicp.vip/weibo/center'
		];
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		$output = curl_exec($ch);
		curl_close($ch);
		$arr = json_decode($output,true);
		$access_token = $arr['access_token'];
		//根据用户ID获取用户信息
		$uid = $arr['uid']; 
		$user = file_get_contents('https://api.weibo.com/2/users/show.json?access_token='.$access_token.'&uid='.$uid);
		echo $user;
    }
}