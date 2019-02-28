<?php
namespace app\api\model;
use think\Model;
use think\Cache;
use think\Db;
use think\Validate;
use app\api\controller\CommonController;
use think\Request;
class UserModel extends Model{
    /**
     * 会员登入
     * @user_register(
     *     'user_pass'   => '登入密码',
     *     'mobile' => '注册手机号',
     * )
     */
	public function user_login($params){
		$user_pass 	   =	!empty($params['user_pass'])? $params['user_pass']:'';
		$mobile 	     =	!empty($params['mobile'])	? $params['mobile']:'';
		$data['user_pass']	= $user_pass;
		$data['mobile']		  = $mobile;
		$validate = validate('User');
		if(!$validate->scene('user_login')->check($data)){
            return json(['error'=>-1,'msg'=>$validate->getError()]);
    }
		$is_exist_mobile = Db::name('user')->field('id,user_pass,mobile,salt')->where("mobile = $mobile and user_type =2 and user_status=1")->find();
		if(!$is_exist_mobile) return json(['error'=>-1,'msg'=>'该账号不存在']);
		if($is_exist_mobile['user_pass'] == md5(md5($user_pass.$is_exist_mobile['salt']))){
			  $common = new \app\api\controller\CommonController();
        $data_token['token'] = md5(uniqid()) . md5(uniqid());
        $data_token['create_time'] = time();
        $data_token['expire_time'] = $data_token['create_time']+(30*24*60*60);
        $data_token['user_id']     = $is_exist_mobile['id'];
        $res = Db::name('user_token')->insert($data_token);
        $login_data['last_login_ip']   = get_client_ip(0, true);
        $login_data['last_login_time'] = time();
        Db::name('user')->where('id',$is_exist_mobile['id'])->update($login_data);
	      return json(['error'=>0,'msg'=>'登入成功','token'=>$data_token['token'],'user_id'=>$data_token['user_id']]);
		}else{
			  return json(['error'=>-1,'msg'=>'密码错误']);
		}
	}

    /**
     * 会员注册
     * @user_register(
     *     'user_pass'   => '登入密码',
     *     'mobile' => '注册手机号',
     * )
     */
	public function user_register($params){
		$user_pass 	=	!empty($params['user_pass'])? $params['user_pass']:'';
		$mobile 	  =	!empty($params['mobile'])	? $params['mobile']:'';
		$parent_id  =	!empty($params['parent_id'])	? intval($params['parent_id']):0;//邀请人的id
    $data['parent_id']     = $parent_id;
		$data['user_pass']	   = $user_pass;
		$data['mobile']		     = $mobile;
		$validate = validate('User');
		if(!$validate->scene('user_register')->check($data)){
        	return json(['error'=>-1,'msg'=>$validate->getError()]);
    	}
		$is_exist_mobile = Db::name('user')->where("mobile = $mobile and user_type =2")->find();
		if($is_exist_mobile) return json(['error'=>-1,'msg'=>'该账号已注册']);
		$common = new \app\api\controller\CommonController();
		$salt = $common->alnum();
		$data['user_pass']	= 	md5(md5($user_pass.$salt));
		$data['salt']		=	$salt;
		$data['user_type'] 	=	2;
		$data['create_time'] = time();
		$res 	= Db::name('user')->insert($data);
		$userId = Db::name('user')->getLastInsID();
		if($res){
			if($parent_id){
				$a = strtotime(date("Y-m-d"),time());//今天零点的时间戳
				$b = $a + 24*60*60-1;//今天23:59:59的时间戳
				$times = Db::name('score_log')->where("user_id = $parent_id and reason='邀请' and add_time>=$a and add_time<=$b")->count('id');
				if($times<3){//一天只能获得3次邀请奖励
  					Db::name('user')->where('id',$parent_id)->setInc('score',10);//邀请人增加积分
            Db::name('user')->where('id',$parent_id)->setInc('total_score',10);//邀请人增加总积分
  					$common = new \app\api\controller\CommonController();
  					$result = $common->reward_score($parent_id,'邀请',10);
	      }
    				Db::name('user')->where('id',$userId)->setInc('score',10);//被邀请人增加积分
            Db::name('user')->where('id',$userId)->setInc('total_score',10);//邀请人增加总积分
    				$common = new \app\api\controller\CommonController();
    				$result = $common->reward_score($parent_id,'注册',10);
			}
			return json(['error'=>0,'msg'=>'注册成功']);
		}else{
			return json(['error'=>-1,'msg'=>'注册失败']);
		}
	}
   	/**
     * 设置个人信息
     */
   	public function set_user_info($params){
      $token   = $params['token'];
      $Common  = new CommonController();
      $user_id = $Common->getUserId($token);//获取登入用户的user_id
   		$year	   = isset($params['year'])	  ? intval($params['year'])   :'';
   		$month 	 = isset($params['month'])	? intval($params['month'])  :'';
   		$weight  = isset($params['weight'])	? intval($params['weight']) :'';
   		$height	 = isset($params['height'])	? intval($params['height']) :'';
   		$sex 	   = isset($params['sex'])		? intval($params['sex'])    :'';
   		$user_nickname 	= isset($params['user_nickname']) ? trim($params['user_nickname']):'';
      $avatar = isset($params['avatar']) ?$params['avatar']:'';
      // echo $avatar;exit;
      if($avatar){
        $info = $Common->up_image($avatar,'avatar');
        if($info['error']==0){
          $url = substr($info['url'],1);
          $avatar     = 'http://'.$_SERVER['HTTP_HOST'].$url;
         }else{
            return json(['error'=>-1,'msg'=>$info['msg']]);
         }
      }
   		$unit			  = isset($params['unit']) 			?intval($params['unit']):'';
   		$stepgoal		= isset($params['stepgoal']) 		?intval($params['stepgoal']):'';
   		if($year&&$month){//设置生日
   			$data['year'] = $year;
   			$data['month'] = $month;
        $data['age'] = date('Y')-$year;
   		}
   		if($user_nickname){//设置昵称
   			$data['user_nickname'] = $user_nickname;
   		}
   		if($weight){//设置体重
   			$data['weight'] = $weight;
   		}
   		if($height){//设置身高
   			$data['height'] = $height;
   		}
   		if($sex||$sex===0){//设置性别
   			$data['sex'] = $sex;
   		}
   		if($avatar){//设置头像
   			$data['avatar'] = $avatar;
   		}
   		if($unit||$unit===0){//设置单位0公制1英制
   			$data['unit'] = $unit;
   		}
   		if($stepgoal||$stepgoal===0){//步距
   			$data['stepgoal'] = $stepgoal;
   		}
   		$res = Db::name('user')->where('id',$user_id)->update($data);
   		if($res!==false){
   			return json(['error'=>0,'msg'=>'设置成功']);
   		}else{
   			return json(['error'=>-1,'msg'=>'设置失败']);
   		}
   	}

   	/**
     * 编辑提交实名认证
     */
   	public function real_name_audit($params){
      $token = $params['token'];
      $Common = new CommonController();
      $data['user_id'] = $Common->getUserId($token);//获取登入用户的user_id
   		$id = isset($params['id'])?intval($params['id']):0;
   		$data['real_name'] 			 = isset($params['real_name'])		? trim($params['real_name']):'';
   		$data['id_card'] 			   = isset($params['id_card'])			? trim($params['id_card']):'';
   		$data['sfz_front_img'] 	 = isset($params['sfz_front_img'])? trim($params['sfz_front_img']):'';
   		$data['sfz_back_img'] 	 = isset($params['sfz_back_img'])	? trim($params['sfz_back_img']):'';
      $data['sfz_sc_img']      = isset($params['sfz_sc_img'])   ? trim($params['sfz_sc_img']):'';

      // echo 123;
      //  echo "<br>";
      // echo $data['sfz_front_img'];
      // echo "<br>";
      //  echo $data['sfz_back_img'];
      //   echo "<br>";
      //   echo $data['sfz_sc_img'];exit;

      if($data['sfz_front_img']){
      
        $info = $Common->up_image($data['sfz_front_img'],'sfz_img');
        if($info['error']==0){
          $url = substr($info['url'],1);
          $data['sfz_front_img']     = 'http://'.$_SERVER['HTTP_HOST'].$url;
         }else{
            return json(['error'=>-1,'msg'=>$info['msg']]);
         }
      }

      if($data['sfz_back_img']){
       
        $info = $Common->up_image($data['sfz_back_img'],'sfz_img');
        if($info['error']==0){
          $url = substr($info['url'],1);
          $data['sfz_back_img']     = 'http://'.$_SERVER['HTTP_HOST'].$url;
         }else{
            return json(['error'=>-1,'msg'=>$info['msg']]);
         }
      }

      if($data['sfz_sc_img']){
      
        $info = $Common->up_image($data['sfz_sc_img'],'sfz_img');
        if($info['error']==0){
          $url = substr($info['url'],1);
          $data['sfz_sc_img']     = 'http://'.$_SERVER['HTTP_HOST'].$url;
         }else{
            return json(['error'=>-1,'msg'=>$info['msg']]);
         }
      }
   		$data['add_time'] 			  = time();
   		$data['status'] 			    = 0;
   		if($id){
   			$res = Db::name('name_audit')->where('id',$id)->update($data);
   		}else{
   			$res = Db::name('name_audit')->insert($data);
   		}
   		if($res!==false){
   			return json(['error'=>0,'msg'=>'提交成功']);
   		}else{
   			return json(['error'=>-1,'msg'=>'提交失败']);
   		}
   	}  	
   	/**
     * 设置支付密码
  	* @set_pay_pass(
     *     'user_id'   => '用户id',
     *     'verify' => '验证码',
     *     'pay_password' => '支付密码',
     *     're_pay_password' => '确认支付密码',
     * )
     */
   	public function set_pay_pass($params){
      $token = $params['token'];
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);//获取登入用户的user_id
   		$mobile 	= isset($params['mobile'])	?$params['mobile']:0;
   		$verify 	= isset($params['verify'])	?$params['verify']:0;
   		$pay_password 		= isset($params['pay_password'])	?trim($params['pay_password']):0;
   		$re_pay_password 	= isset($params['re_pay_password'])	?trim($params['re_pay_password']):0;
        if(!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]{8,64}$/',$pay_password)){
            return json(['error'=>-1,'msg'=>'支付密码要是8位以上数字+字母的组合']); 
        }
        if($pay_password!==$re_pay_password){
        	return json(['error'=>-1,'msg'=>'两次密码不一致']);
        }
        $errMsg = cmf_check_verification_code($mobile,$verify);
        if(!empty($errMsg)){
          return json(['error'=>-1,'msg'=>$errMsg]);
        }
        $salt = Db::name('user')->where('id',$user_id)->value('salt');
        $pay_password	= md5(md5($pay_password.$salt));
        $res = Db::name('user')->where('id',$user_id)->setfield('pay_password',$pay_password);
        if($res!==false){
        	return json(['error'=>0,'msg'=>'设置成功']);
        }else{
        	return json(['error'=>-1,'msg'=>'设置失败']);
    	}
    }

   	/**
 	 * 修改登入密码
   	* @update_user_pass(
     *     'user_id'   => '用户id',
     *     'user_pass' => '登入密码',
     *     're_user_pass' => '确认登入密码',
     * )
     */
   	public function update_user_pass($params){
   		$user_pass 		  = isset($params['user_pass']) 			? trim($params['user_pass']):0;
   		$old_user_pass 	= isset($params['old_user_pass']) 	? trim($params['old_user_pass']):0;
   		$re_user_pass 	= isset($params['re_user_pass']) 		? trim($params['re_user_pass']):0;
 		  $token = $params['token'];
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);//获取登入用户的user_id
      if($old_user_pass){
        $user_info = Db::name('user')->field('salt,user_pass')->where('id',$user_id)->find();  
     		if($user_info['user_pass']!== md5(md5($old_user_pass.$user_info['salt']))){
     			return json(['error'=>-1,'msg'=>'旧密码错误']);
     		}
      }
   		if($user_pass!==$re_user_pass){
        	return json(['error'=>-1,'msg'=>'两次密码不一致']);
        }
     	$salt = Db::name('user')->where('id',$user_id)->value('salt');
        $user_pass	= 	md5(md5($user_pass.$salt));
   		$res = Db::name('user')->where('id',$user_id)->setfield('user_pass',$user_pass);
        if($res!==false){
        	return json(['error'=>0,'msg'=>'修改成功']);
        }else{
        	return json(['error'=>-1,'msg'=>'修改失败']);
    	}
   	}
    /**
   * 忘记登入密码
    * @update_user_pass(
     *     'mobile'   => '用户注册手机号',
     *     'user_pass' => '登入密码',
     *     're_user_pass' => '确认登入密码',
     *     'verificode' => '验证码',
     * )
     */
    public function forget_user_pass($params){
      $user_pass      = isset($params['user_pass'])       ? trim($params['user_pass']):0;
      $re_user_pass   = isset($params['re_user_pass'])    ? trim($params['re_user_pass']):0;
      $mobile = isset($params['mobile'])  ? trim($params['mobile']):0;
      $verificode = isset($params['verificode'])    ? trim($params['verificode']):0;
      $errMsg = cmf_check_verification_code($mobile,$verificode);
      if(!empty($errMsg)){
          // $this->error($errMsg);
         return json(['error'=>-1,'msg'=>$errMsg]);
      }
      if(empty($user_pass)||empty($mobile)){
          return json(['error'=>-1,'msg'=>'密码或手机不能为空']);
      }
      if($user_pass!==$re_user_pass){
          return json(['error'=>-1,'msg'=>'两次密码不一致']);
        }
      $salt = Db::name('user')->where('mobile',$mobile)->value('salt');
      if(!$salt){
        return json(['error'=>-1,'msg'=>'该号码未注册']);
      }
      $user_pass  =  md5(md5($user_pass.$salt));
      $res = Db::name('user')->where('mobile',$mobile)->setfield('user_pass',$user_pass);
        if($res!==false){
          return json(['error'=>0,'msg'=>'修改成功']);
        }else{
          return json(['error'=>-1,'msg'=>'修改失败']);
      }
    }



    /**
     * 记录步数 心率 血压 睡眠 数据
     */
    public function ajax_add_info($params){

    	$token = $params['token'];
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);//获取登入用户的user_id
   		$level = Db::name('user')->where('id',$user_id)->value('level');
   		$type = isset($params['type'])?intval($params['type']):0;
   		$data['user_id'] 	   =	$user_id;
   		if($type==1){//type=1记录步数2记录心率3记录睡眠4记录血压
   		// echo strtotime(date("Y-m-d"),time());当天零点的时间戳
      $step_num   = isset($params['step_num']) ?$params['step_num']:'';
      $height     = Db::name('user')->where('id',$user_id)->value('height');
      $weight     = Db::name('user')->where('id',$user_id)->value('weight');
      $stepLength = $this->ByHeight($height);//获取步距
      $device_sn  = isset($params['device_id']) ? $params['device_id']:'';
      foreach ($step_num as $key => $value){
        $distance           = $value['step']*$stepLength/100;   //单位为米
        $data['consume']    = $distance*$weight*6/10/1000;  //单位为大卡
        $data['stride']     = $stepLength;
        $data['time_long']  = isset($value['time_long'])  ? $value['time_long']:'';
        $data['device_sn']  = $device_sn;
        $data['add_time']   = isset($value['time'])       ? strtotime($value['time']):'';
        $data['step_num']   = isset($value['step'])       ? $value['step']:0;
        $res = Db::name('user_run')->insert($data);
      }
      // 步数达到一定时处理一些逻辑---------------------------
        //每天一条总数据汇总
        $total_data['user_id'] = $user_id;
        $total_data['device_sn']  = $device_sn;
        $total_data['is_valid'] = 1;
        $total_data['stride']     = $stepLength;
        $total_data['add_time'] = isset($params['up_time'])?$params['up_time']:0;
        $total_data['step_num'] = isset($params['total_step_num'])?$params['total_step_num']:0;
        $distance        = $total_data['step_num']*$stepLength/100;   //单位为米
        $total_data['consume']  = $distance*$weight*6/10/1000;  //单位为大卡
        $res = Db::name('user_run')->insert($total_data);
          $common = new \app\api\controller\CommonController();
          $jb_score  = 0;
          $add_score = 0;
          if($level==0){
            $add_score  = get_parameter_settings('wn_add_score');
          }elseif($level==1){
            $add_score  = get_parameter_settings('tz_add_score');//达到目标要求奖励积分
            $jb_score   = get_parameter_settings('wn_jb_score'); //达到基本要求奖励积分
          }elseif($level==2){
            $add_score  = get_parameter_settings('lb_add_score');//达到目标要求奖励积分
            $jb_score   = get_parameter_settings('tz_jb_score'); //达到基本要求奖励积分
          }else{
            $jb_score   = get_parameter_settings('lb_jb_score'); //达到基本要求奖励积分
          }
          $common->user_level($user_id,$level,$total_data['step_num'],$add_score,$jb_score);
			
     
			//--------------------------------------------
		}elseif($type==2){
			$data['min_heart_rate']     = isset($params['min_heart_rate'])?trim($params['min_heart_rate']):'';
			$data['max_heart_rate']     = isset($params['max_heart_rate'])?trim($params['max_heart_rate']):'';
			$data['avg_heart_rate']     = isset($params['avg_heart_rate'])?trim($params['avg_heart_rate']):'';
			$data['resting_heart_rate'] = isset($params['resting_heart_rate'])?trim($params['resting_heart_rate']):'';

      $device_sn  = isset($params['device_id']) ? $params['device_id']:'';
      $data['device_sn']  = $device_sn;

      $data['add_time'] = isset($params['up_time'])?$params['up_time']:0;
			$res = Db::name('user_heart_rate')->insert($data);	
		}elseif($type==3){
			$data['deep_sleep'] 	  = isset($params['deep_sleep'])	?trim($params['deep_sleep']):'';
			$data['light_sleep'] 	  = isset($params['light_sleep'])	?trim($params['light_sleep']):'';
			$data['sleep_time'] 	  = isset($params['sleep_time'])	?trim($params['sleep_time']):'';
			$data['clear_headed'] 	= isset($params['clear_headed'])?trim($params['clear_headed']):'';

      $device_sn  = isset($params['device_id']) ? $params['device_id']:'';
      $data['device_sn']  = $device_sn;
 
      $data['add_time'] = isset($params['up_time'])?$params['up_time']:0;
      $data['array_data'] = isset($params['array_data'])?serialize($params['array_data']):'';
			$res = Db::name('user_sleep')->insert($data);
			//if(如果睡眠时间在22点到8点之间){
			//增加积分奖励
	   		  //$common = new \app\api\controller\CommonController();
			    //$result = $common->reward_score($user_id,'睡眠',5);
			//}	
		}elseif($type==4){
			$data['blood_pressure'] 	= isset($params['blood_pressure'])	?trim($params['blood_pressure']):'';
			$data['blood_oxygen'] 		= isset($params['blood_oxygen'])	?trim($params['blood_oxygen']):'';
			$data['fatigue'] 			    = isset($params['fatigue'])			?trim($params['fatigue']):'';
      $device_sn  = isset($params['device_id']) ? $params['device_id']:'';
      $data['device_sn']  = $device_sn;
      $data['add_time'] = isset($params['up_time'])?$params['up_time']:0;
			$res = Db::name('user_blood_pressure')->insert($data);
		}else{
			return json(['error'=>0,'msg'=>'参数错误,type未传']);
		}
		if($res){
			return json(['error'=>0,'msg'=>'记录成功']);
		}else{
			return json(['error'=>-1,'msg'=>'记录失败']);
		}
    }

    /**
     * 根据身高计算步长
     */
    public function ByHeight($height){
      $stepLength=62;
      if($height < 50) {
        $height = 50;
      }elseif($height>190){
        $height = 190;
      }else{
        if($height%10){
          $height=($height/10+1)*10;
        }else {
            $height = $height/10*10;
        }
      }
      switch ($height) {
        case 50:
        {
          $stepLength = 20;
        }
            break;
        case 60:
        {
            $stepLength = 22;
        }
            break;
        case 70:
        {
            $stepLength = 25;
        }
            break;
        case 80:
        {
            $stepLength = 29;
        }
            break;
        case 90:
        {
            $stepLength = 33;
        }
        break;
        case 100:
        {
            $stepLength = 37;
        }
            break;
        case 110:
        {
            $stepLength = 40;
        }
            break;
        case 120:
        {
            $stepLength = 44;
        }
            break;
        case 130:
        {
            $stepLength = 48;
        }
            break;
        case 140:
        {
            $stepLength = 51;
        }
            break;
        case 150:
        {
            $stepLength = 55;
        }
            break;
        case 160:
        {
            $stepLength = 59;
        }
            break;
        case 170:
        {
            $stepLength = 62;
        }
            break;
        case 180:
        {
            $stepLength = 66;
        }
            break;
        case 190:
        {
            $stepLength = 70;
        }
            break;
        default:
            break;
    }
      return $stepLength;
    }

   /**
     * 会员签到积分奖励
     */
    public function sign_score($user_id){
    	$da = date("w");//输出周几
    	$da = $da== 0?7:$da;
    	$start_time	= strtotime(date("Y-m-d"),time());//当天零点的时间戳
    	$end_time 	= strtotime(date("Y-m-d"),time())+24*60*60-1;//当天23:59:59的时间戳
    	$t = $start_time-24*60*60*($da-1);
    	$is_sign = Db::name('sign_score')->where("sign_time>='$start_time' and sign_time<='$end_time' and user_id=$user_id")->count('id');//判断今天是否签到
		  $is_sign = $is_sign?1:0;
		  $sign_times = Db::name('sign_score')->where("sign_time>='$t' and user_id=$user_id")->count('id');//当周签到的次数
		  return json(['error'=>0,'msg'=>'success','is_sign'=>$is_sign,'sign_times'=>$sign_times]);
    }
    /**
     * 记录会员签到积分奖励
     */	
    public function ajax_sign_score($params){
  		$data['sign_time'] 	= time();
      $token = $params['token'];
      $Common = new CommonController();
      $data['user_id'] = $Common->getUserId($token);//获取登入用户的user_id
  		$data['score']		= isset($params['score'])	?intval($params['score']):0;
  		if(!$data['user_id']||!$data['score']){
  			return json(['error'=>-1,'msg'=>'传参错误']);
  		}
  		$res = Db::name('sign_score')->insert($data);
  		$result1 = Db::name('user')->where('id',$data['user_id'])->setInc('score',$data['score']);
      $result2 = Db::name('user')->where('id',$data['user_id'])->setInc('total_score',$data['score']);
  		$common = new \app\api\controller\CommonController();
  		$common->reward_score($data['user_id'],'签到',$data['score']);
  		if($res){
  			return json(['error'=>0,'msg'=>'签到成功']);
  		}else{
  			return json(['error'=>-1,'msg'=>'签到失败']);
  		}
    }
	/**
     * 钱包管理
     */
    public function ajax_user_wallet($params){
      $token = $params['token'];
      $Common = new CommonController();
      $data['user_id'] = $Common->getUserId($token);//获取登入用户的user_id

    	$data['wallet_url'] 	= isset($params['wallet_url'])	?trim($params['wallet_url']):'';
    	$data['remark'] 		= isset($params['remark'])		?trim($params['remark']):'';
    	$data['third_party'] 	= isset($params['third_party'])	?trim($params['third_party']):'';

      $pay_password = isset($params['pay_password']) ? $params['pay_password']:'';
      $salt = Db::name('user')->where('id',$data['user_id'])->value('salt');
      $pay_password = md5(md5($pay_password.$salt));
      $user_pay_password = Db::name('user')->where('id',$data['user_id'])->value('pay_password');
      if($user_pay_password!==$pay_password){
        return json(['error'=>-1,'msg'=>'密码错误']);
      }
    	$id = isset($params['id'])?intval($params['id']):0;
    	if($id){
    		$res = Db::name('user_wallet')->where("user_id = $data[user_id]")->update($data);
    	}else{
    		$res = Db::name('user_wallet')->insert($data);
    	}
    	if($res!==false){
    		return json(['error'=>0,'msg'=>'操作成功']);
    	}else{
    		return json(['error'=>-1,'msg'=>'操作失败']);
    	}

    }

}

?>