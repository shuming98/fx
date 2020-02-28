<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Cart;
class GoodsController extends Controller
{
    public function index(){
    	$goods = DB::table('goods')->get();
    	return view('index',compact('goods'));
    }

    public function add(){
    	$data = [
    		['goods_name'=>'月季','goods_price'=>'23.8','goods_img'=>'/images/goods_1.jpg'],
    		['goods_name'=>'玫瑰','goods_price'=>'45.6','goods_img'=>'/images/goods_2.jpg'],
    		['goods_name'=>'桃花','goods_price'=>'30.8','goods_img'=>'/images/goods_3.jpg'],
    		['goods_name'=>'妖姬','goods_price'=>'55.6','goods_img'=>'/images/goods_4.jpg']
    	];
    	DB::table('goods')->insert($data);
    }

    public function goods($gid){
    	$goods = DB::table('goods')->where('gid',$gid)->first();
    	return view('goods',compact('goods'));
    }

    /**
     * 使用了购物车类Cart
     */
    //添加商品至购物车
    public function cartadd($gid){
    	$goods = DB::table('goods')->where('gid',$gid)->first();
    	Cart::add($gid,$goods->goods_name,$goods->goods_price,1,array());
    	return redirect('cartshow');
    }

    //购物车页面
    public function cartshow(){
    	$cart = Cart::getContent();
    	$total = Cart::getTotal();
    	return view('cart',compact('cart','total'));
    }

    //清空购物车
    public function cartclear(){
    	return Cart::clear()?redirect('/'):back();
    }

    //处理订单
    public function done(){
    	//Order表
    	$order = new \App\Order();
    	$user = \App\User::where('openid',session('wechat_user'))->first();
    	/**
    	 * 需微信公众号打开页面
    	 */
    	//$order->uid = $user->id;
    	//$order->openid = session('wechat_user');
    	$order->ordersn = date('YmdHis').mt_rand(1000,9999);
    	$order->address = request('address');
    	$order->xm = request('xm');
    	$order->mobile = request('mobile');
    	$order->money = Cart::getTotal();
    	$order->ordertime = time();
    	$res = $order->save();

    	//Item表
    	if($res){
    		$oid = $order->where('ordersn',$order->ordersn)->first()->oid;
			$carts = Cart::getContent();
			foreach($carts as $cart){
				$item = new \App\Item();
				$item->oid = $oid;
				$item->gid = $cart->id;
				$item->name = $cart->name;
				$item->price = $cart->price;
				$item->quantity = $cart->quantity;
				$item->save();
    		}
    		$this->cartclear();
    		return view('lijizhifu',['oid'=>$oid]);
    	}
    }

    //支付页面
    public function pay(){
    	$order = \App\Order::find(request('oid'));
    	$order->ispay = 1;
    	$order->save();

    	$oid = request('oid');
    	$byid = $order->uid;
    	$user = \App\User::where('id',$byid)->first();
    	//佣金三级收益人
    	$yj = [$user->p1,$user->p2,$user->p3];
    	//收益
    	$sy = [0.4,0.2,0.1];
    	foreach($yj as $k=>$v){
    		$fee = new \App\Fee;
    		$fee->oid = request('oid');
    		$fee->byid = $byid;
    		$fee->uid = $v;
    		$fee->money = $order->money * $sy[$k];
    		$fee->save();
    	}
    	return "购买成功";
    }

}
// 26_NmD0Z4y-SBwaoVOvXEDvIESB_3shQbR0qhh1ET_6wvxCWLWWhh3SOh2dE3R-YKqeU4H37bZKgziXv23_2jlRl70kvhWs8iA1taWfMhvOic2nVVrONYeyqNQwf5vnyJc3U82vn2DD46Si_VOWILFbAGADGA