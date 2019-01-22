<?php
namespace app\api\controller;
use cmf\controller\ApiBaseController;
use think\Db;
use app\admin\model\ScoreModel;
use app\api\controller\CommonController;
use think\Validate;

use think\Cache\driver\Redis;

class UserController extends ApiBaseController{
	 // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['user_register','user_login','get_verifi_code','yz_code','forget_user_pass','GetTheMonth','test'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = [];
    // 无需验签的接口
    protected $noNeedSign  = ['user_register','user_login','login_out','get_verifi_code','yz_code','forget_user_pass'];

    /**
     * 用户注册
     */
    public function user_register(){
    	$params = $this->request->param();
    	$user = new \app\api\model\UserModel();
    	$result = $user->user_register($params);
    	return $result;
    }
    /**
     * 用户登入
     */
	public function user_login(){
		$params = $this->request->param();
		$user = new \app\api\model\UserModel();
		$result = $user->user_login($params);
		return $result;
	} 
    /**
     * 注销登入
     * @login_out(
     *     'token'   => 'token',
     * )
     */
    public function login_out(){
    	$token = $this->request->param('token');
    	if(empty($token)){
			   return json(['error'=>-1,'msg'=>'传参错误']);	
    	}
    	$res = Db::name('user_token')->where('token',$token)->delete();
    	if($res){
    		return json(['error'=>0,'msg'=>'退出成功']);	
    	}else{
    		return json(['error'=>-1,'msg'=>'退出失败']);	
    	}
    }

    /**
     * 个人信息页
     */
    public function user_info(){
      $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);//获取登入用户的user_id
    	$res = Db::name('user')->field('avatar,height,weight,sex,user_nickname,stepgoal,unit,mobile,year,month,pay_password,score,age')->where('id',$user_id)->find();
      $res['is_pay_pass'] = isset($res['pay_password']) ? 1:0;
      $is_name_audit = Db::name('name_audit')->where("user_id = $user_id and status=1")->find();
      $res['is_name_audit'] = $is_name_audit ? 1:0;
      $num =  Db::name('message_user_rel')->alias('a')->join('message m','a.message_id = m.id','left')->field('a.read_time,m.title,m.content,m.type,m.create_time')->where("a.user_id=$user_id and a.read_time=0 and a.is_del=0")->count('a.id');
      $res['is_read'] = $num>0?1:0;
      $tb_time = Db::name('user_run')->where("user_id=$user_id and is_valid=1")->order('add_time desc')->limit(1)->value('add_time');
      if($tb_time){
        $res['tb_time']=date('Y-m-d H:i:s',$tb_time);
      }else{
        $res['tb_time'] = '';
      }
    	return json(['error'=>0,'msg'=>'success','data'=>$res]);
    }
   	/**
     * 设置个人信息
     */
   	public function set_user_info(){
   		$params = $this->request->param();
   		$user = new \app\api\model\UserModel();
  		$result = $user->set_user_info($params);
  		return $result;
   	}

   	/**
     * 实名认证信息页
     */
   	public function name_audit_info(){	
 		  $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);//获取登入用户的user_id
   		$res = Db::name('name_audit')->field('id,user_id,real_name,id_card,sfz_front_img,sfz_back_img,sfz_sc_img,add_time,status,examine_time')->where('user_id',$user_id)->find();
   		return json(['error'=>0,'msg'=>'success','data'=>$res]);
   	}
   	/**
     * 实名认证
     */
   	public function real_name_audit(){ 
   		$params = $this->request->param();
   		$user = new \app\api\model\UserModel();
		  $result = $user->real_name_audit($params);
		  return $result;
   	}
   	/**
     * 设置支付密码
     */
   	public function set_pay_pass(){
   		$params = $this->request->param();
   		$user = new \app\api\model\UserModel();
		  $result = $user->set_pay_pass($params);
		  return $result;
   	}
   	/**
     * 修改登入密码
     */
   	public function update_user_pass(){
   		$params = $this->request->param();
   		$user = new \app\api\model\UserModel();
		  $result = $user->update_user_pass($params);
		  return $result;
   	}

    /**
     * 忘记登入密码
     */
    public function forget_user_pass(){
      $params = $this->request->param();
      $user = new \app\api\model\UserModel();
      $result = $user->forget_user_pass($params);
      return $result;
    }

   	
    /**
     * 消息中心
     */
    public function user_message(){
        $token = $this->request->param('token');
        $Common = new CommonController();
        $user_id = $Common->getUserId($token);
        // $res = Db::name('user_message')->field('title,add_time,category,content')->where('user_id='.$user_id)->paginate(10)->toarray();
        $res = Db::name('message_user_rel')->alias('a')->join('message m','a.message_id = m.id','left')->field('a.read_time,m.title,m.content,m.type,m.num,m.create_time')->where("a.user_id=$user_id and a.is_del=0")->paginate(10)->toarray();
        $type=[' ','系统消息','积分变化'];
        if($res){
          foreach ($res['data'] as $key => $value){
            if($value['read_time']){
              $res['data'][$key]['read_time'] = '已读';
            }else{
              $res['data'][$key]['read_time'] = '未读';
            }
            $res['data'][$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
            $res['data'][$key]['type'] =$type[$value['type']];
          }
        }
        return json(['error'=>0,'msg'=>'success','data'=>$res]);
    }
    /**
     * 消息详情
     */
    public function message_info(){
      	$id = $this->request->param('id',0,'intval');
      	if(!$id){
          return json(['error'=>-1,'msg'=>'传参错误']);
        }
      	// $res= Db::name('user_message')->field('id,title,add_time,category,content')->where('id',$id)->find();
        $res = Db::table('message_user_rel')->alias('a')->join('message m','a.message_id = m.id','left')->field('a.read_time,m.title,m.content,m.type,m.create_time')->where('id',$id)->find();
      	if($res){
      		Db::name('message_user_rel')->where('id',$id)->setField('read_time',time());
      		return json(['error'=>0,'msg'=>'success','data'=>$res]);
      	}else{
      		return json(['error'=>-1,'msg'=>'false','data'=>array()]);
      	}
    }
    /**
     * 一键阅读
     */
    public function read_message(){
        $token = $this->request->param('token');
        $Common = new CommonController();
        $user_id = $Common->getUserId($token);
        $res = Db::name('message_user_rel')->whereIn("user_id =$user_id and read_time=0")->setField('read_time',time());
        if($res){
            return json(['error'=>0,'msg'=>'已读']);
        }else{
            return json(['error'=>-1,'msg'=>'未读']);
        }
    }


    /**
     * 积分明细列表
     */
    public function score_log_list(){
      $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);
    	$res = Db::name('score_log')->where('user_id',$user_id)->paginate(10)->toarray();
      if($res){
        foreach($res['data'] as $key => $value) {
            $res['data'][$key]['add_time'] = date('Y-m-d H:i:s',$value['add_time']);
            if($value['num']>0){
              $res['data'][$key]['num'] = '+'.$value['num'];
            }
        }
      }
    	return json(['error'=>0,'msg'=>'success','data'=>$res]);
    }
    /**
     * 记录步数 心率 血压 睡眠 数据
     */
    public function ajax_add_info(){
    	$params = $this->request->param();
    	$user = new \app\api\model\UserModel();
      // $result = $user->ajax_add_info($params);
      // return $result;
      $redis = new Redis();
      $redis->connect('127.0.0.1', 6379);
      $redis->select(1);//选择数据库1
      try{
        $redis->LPUSH('step_data',json_encode($params));
      }catch(Exception $e){
        echo $e->getMessage();
        exit;
      }
      do{
          try{
          $value = json_decode($redis->LPOP('step_data'),true);
          if(!$value){
            break;
          }
          $result = $user->ajax_add_info($value);
          }catch(Exception $e){
            echo $e->getMessage();exit;
          }
      }while(true);
		  return $result;
    }
    /**
     * 获取步数记录
     */
    public function get_step_log(){
      $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);//获取登入用户的user_id
    	$res = Db::name('user_run')->field('id,user_id,step_num,stride,consume,time_long,add_time')->where("user_id=$user_id and is_valid=1")->paginate(10)->toarray();
    	foreach ($res['data'] as $key => $value){
    		$res['data'][$key]['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
    	}
    	return json(['error'=>0,'msg'=>'success','data'=>$res]);
    }

    /**
     * 会员签到积分奖励
     */
    public function sign_score(){
      $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);
		  $user = new \app\api\model\UserModel();
		  $result = $user->sign_score($user_id);
		  return $result;
    }
    /**
     * 记录会员签到积分奖励
     */	
    public function ajax_sign_score(){
    	$params = $this->request->param();
		  $user = new \app\api\model\UserModel();
		  $result = $user->ajax_sign_score($params);
		  return $result;
    }

    /**
     * 关于我们
     */	
    public function about_us(){
    	$res = Db::name('portal_post')->field('id,parent_id,post_type,post_title,post_content')->where('id=1')->find();
    	// print_r($res);exit;
    	// echo htmlentities($res['post_content']);exit;
		return json(['error'=>0,'msg'=>'success','data'=>$res]);
    }
    /**
     * 首页
     */	
    public function user_index(){
    
      $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);
    	$user_info = Db::name('user')->field('total_score,stepgoal,level')->where("id= $user_id and user_type=2")->select()->toarray();
    	foreach ($user_info as $key => $value) {
    		if($value['level']==0){
    			$user_info[$key]['level'] = '普通会员';
    		}elseif($value['level']==1){
    			$user_info[$key]['level'] = '蜗牛达人';
    		}elseif($value['level']==2){
    			$user_info[$key]['level'] = '兔子达人';
    		}elseif($value['level']==3){
    			$user_info[$key]['level'] = '猎豹达人';
    		}
    	}
    	//排行
    	$rank_user = Db::name('user')->field('mobile,total_score')->where('user_type=2')->order('total_score desc')->paginate(10)->toarray();
    	foreach ($rank_user['data'] as $key => $value) {
    		$rank_user['data'][$key]['mobile'] = substr_replace($value['mobile'],'****',3,4);
    	}
    	$data['user_info'] = $user_info;
    	$data['rank_user'] = $rank_user;
    	return json(['error'=>0,'msg'=>'success','data'=>$data]);
    }
	/**
     * 钱包管理页
     */
    public function user_wallet(){
      $token   = $this->request->param('token');
      $Common  = new CommonController();
      $user_id = $Common->getUserId($token);
    	$res = Db::name('user_wallet')->field('id,user_id,wallet_url,remark,third_party')->where("user_id=$user_id")->select();
    	return json(['error'=>0,'msg'=>'success','data'=>$res]);
    }
	/**
     * 钱包管理
     */
	public function ajax_user_wallet(){
		$params = $this->request->param();
		$user = new \app\api\model\UserModel();
		$result = $user->ajax_user_wallet($params);
		return $result;
	}


    // 获取兑换记录
    public function exchange_log(){
      $params = $this->request->param(); 
      $token = $params['token'];
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);
      $scoreModel = new ScoreModel();
      $map = [];
      $map['a.user_id'] = $user_id;
      $map['a.reason'] = '兑换';
      $startTime = empty($params['start_time']) ? 0 :$params['start_time'];
      $endTime   = empty($params['end_time']) ? 0   :$params['end_time'];
      if(!empty($startTime) && !empty($endTime)){
          $map['a.add_time'] = [['>= time', $startTime], ['<= time', $endTime]];
      }
      $result = $scoreModel->getScoreExchangeList($map);
      // $this->success("获取数据成功！",['list'=>$result->items()]);
      return json(['error'=>0,'msg'=>'获取数据成功','list'=>$result]);
    }

    // 获取钱包地址
    public function get_wallet_address_list(){
        // $walletModel = Db::name('third_party_wallet_address');
        // $list = $walletModel
        //         ->alias('a')
        //         ->field('a.*,b.name')
        //         ->join('__THIRD_PARTY__ b','a.thirdparty_id=b.id','left')
        //         ->where(['user_id'=>$this->getUserId()])
        //         ->order('id desc')
        //         ->select()->toArray();
      // $user_id = $this->request->param('user_id',0,'intval');
      $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);

      $walletModel = Db::name('user_wallet');
        $list = $walletModel
                ->field('id,user_id,wallet_url,remark,third_party')
                ->where(['user_id'=>$user_id])
                ->order('id desc')
                ->select()->toArray();
        // $this->success("获取信息成功!",['list'=>$list]);
        return json(['error'=>0,'msg'=>'获取信息成功','list'=>$list]);        
    }

    // 提交兑换
    public function exchange_add(){
      $scoreLogModel = Db::name('score_log');
      $data = $this->request->param();
      $token = $this->request->param('token');
      $Common = new CommonController();
      $user_id = $Common->getUserId($token);   
      $userid =$user_id;
      $user = get_user($userid);
      if($user['score']<$data['num'] || $user['score']==0){
        $this->error("你的积分不够!");
      }
      $validate = new Validate([
          'num'            => 'require',
          'transaction_address'  => 'require',
          'pay_password' => 'require',
          'verification_code'    => 'require'
      ]);

      $validate->message([
          'num.require'            => '请输入数量!',
          'transaction_address.require'  => '请输入地址!',
          'pay_password.require' => '请输入交易密码!',
          'verification_code.require'    => '请输入验证码!'
      ]);

      $res = $scoreLogModel->where(['user_id'=>$userid,'status'=>0])->find();
      if(!empty($res)){
        $this->error('你有已提交订单未审核');
      }
      
      if (!$validate->check($data)) {
          $this->error($validate->getError());
      }
      $exchange_lower_limit = get_parameter_settings('exchange_lower_limit');
      
      if($data['num']<$exchange_lower_limit){
        $this->error("积分兑换数量必须大于或等于".$exchange_lower_limit);
      }

      // $errMsg = cmf_check_verification_code($user['mobile'], $data['verification_code']);
      // if (!empty($errMsg)) {
      //     $this->error($errMsg);
      // }
   
      $frist_str = substr($data['transaction_address'], 0, 1 );
      if($frist_str !== '0'){
        $this->error("请绑定正确的第三方地址");
      }
      if(empty($user['pay_password'])){
        $this->error("请先在“安全设置”里设置交易密码!");
      }
      if ($user['pay_password'] !== md5(md5($data['pay_password'].$user['salt']))) {
          $this->error("交易密码不正确!");
      }
      $score_id = handle_score((-$data['num']),'兑换',$userid,0,$data['transaction_address']);
      if($score_id){
        // $this->success("提交成功!");
        return json(['error'=>0,'msg'=>'提交成功']);
      }else{
        // $this->error("提交失败!");
        return json(['error'=>-1,'msg'=>'提交失败']);
      }
    }

    /**
     * 提交绑定手环设备
     */
    public function set_device(){
      $params   = $this->request->param();
      $token    = $params['token'];
      $Common   = new CommonController();
      $user_id  = $Common->getUserId($token);
      $data['device_id'] = !empty($params['device_id'])? $params['device_id']:0;
      $data['user_id']   = $user_id;
      $data['status']    = 1;
      $data['create_time'] = time();
      if(!$data['device_id']){
         return json(['error'=>-1,'msg'=>'设备号不能为空']);
      }

      $id3 = Db::name('device_user_rel')->where("device_id = '$data[device_id]' and status=1 and user_id=$user_id")->find();
      if($id3){
        return 0;
      }

      $id = Db::name('device')->where("device_id = '$data[device_id]' and is_del=0 and is_bind=0")->find();
      if(!$id){
        return json(['error'=>-1,'msg'=>'设备不存在']);
      }
      $id2 = Db::name('device_user_rel')->where("device_id = '$data[device_id]' and status=2")->find();
      if($id2){
        return json(['error'=>-1,'msg'=>'该设备绑定正在审核中']);
      }

      
      $data['update_time'] = time();
      $res = Db::name('device_user_rel')->insert($data);
      // $result = Db::name('device_user_rel')->where(['device_id'=>$data['device_id'])->update(['status'=>1,'update_time'=>time(),'audit_user_id'=>cmf_get_current_admin_id()]);
      $result1 = Db::name('device')->where(['device_id'=>$data['device_id']])->update(['is_bind'=>1]);

      if($res&&$result1!==false){
        return json(['error'=>0,'msg'=>'提交成功']);
      }else{
        return json(['error'=>-1,'msg'=>'提交失败']);
      }
    }

    /**
     * 获取设备信息
     */
    public function get_device_info(){
      $params   = $this->request->param();
      $token    = $params['token'];
      $Common   = new CommonController();
      $user_id  = $Common->getUserId($token);
      $device_id = !empty($params['device_id'])? $params['device_id']:0;
      if(!$device_id){
         return json(['error'=>-1,'msg'=>'设备号不能为空']);
      }
      $device_info = Db::name('device')->where("device_id = '$device_id' and is_del=0")->find();
      if($device_info){
        return json(['error'=>0,'msg'=>'获取成功','device_info'=>$device_info]);
      }else{
        return json(['error'=>-1,'msg'=>'获取失败','device_info'=>'']);
      }
    }


      /*获取注册验证码*/
    public function get_verifi_code(){
     
        $params = $this->request->param();
        $phone = isset($params['mobile'])?$params['mobile']:0;
        if(empty($phone)){
            // $this->error("你的手机号码为空!");
            return json(['error'=>-1,'msg'=>'你的手机号码为空']);
        }
        // if(!isset($params['type'])){
        //     $this->error("请选择验证类型!");
        // }
        
        $code = cmf_random_number_string();
        $type = isset($param['type'])?$param['type']:1;
        switch ($type) {
            case 1:
                $params['code'] = $code;//注册验证码
                $params['text'] = "【秀牛手环】您的验证码是".$code;//短信模板
                $params['tpl_id'] = 1;
                break;
            case 2:
                $params['code'] = $code;//设置支付密码
                $params['text'] = "【秀牛手环】您的验证码是".$code;//短信模板
                $params['tpl_id'] = 1;
                break;
        }
        $params['mobile'] = $phone;
         //TODO 限制 每个ip 的发送次数

        $code_1 = cmf_get_verification_code($phone);
        if (empty($code_1)) {
            // $this->error("验证码发送过多,请明天再试!");
            return json(['error'=>-1,'msg'=>'验证码发送过多,请明天再试!']);
        }

        $result = sendSms($params);
        
        if(isset($result) && $result['code']==0){
            cmf_verification_code_log($phone,$code);
            return json(['error'=>0,'msg'=>'验证码已发至你的手机，请留意你的短信!']);  
        }else{
            return json(['error'=>-1,'msg'=>'验证码发送失败，请待会再试!']);  
        }
    }

    //验证验证码是否正确
    public function yz_code(){
      $params = $this->request->param();
      $phone = isset($params['mobile'])?$params['mobile']:0;
      $verificode = isset($params['verificode'])?$params['verificode']:0;
      $errMsg = cmf_check_verification_code($phone,$verificode);
      if(!empty($errMsg)){
        // $this->error($errMsg);
        return json(['error'=>-1,'msg'=>$errMsg]);

      }
      return json(['error'=>0,'msg'=>'验证成功']);
    }

    // 解绑device
    public function untie_device(){
      $param = $this->request->param();
      $token    = $param['token'];
      $Common   = new CommonController();
      $user_id  = $Common->getUserId($token);
      $deviceModel = Db::name('device');
      $device_user_rel = Db::name('device_user_rel');
      $where['device_id'] = !empty($param['device_id'])?$param['device_id']:'';
      if(!isset($param['device_id'])){
        $this->error('传参错误');
      }
      $device = $deviceModel->where($where)->find();   
      if(empty($device)){
        $this->error('设备有误，请稍后再尝试');
      }
      $res = true;
      $time = time();
      $result = $device_user_rel->where(['device_id'=>$param['device_id'],'status'=>1])->update(['status'=>3]);//解绑所有人的设备
      if($result){
        $rel_result = $deviceModel->where(['device_id'=>$param['device_id']])->update(['is_bind'=>0]);
        if($rel_result ==false){ $res = false; }
      }else{
        $res = false;
      }
      if($res){
          //插入设备操作记录
          Db::name('device_action_log')->insertGetId([
            'device_id'=>$param['device_id'],
            'action_user_id'=>$user_id,
            'type'=>2,
            'user_type'=>2,
            'create_time'=>$time
          ]);
        return json(['error'=>0,'msg'=>'设备解绑成功！']);
      }else{
        return json(['error'=>-1,'msg'=>'设备解绑失败！']);
      }
    }

    //返回当周步数的数据
    public  function getday_info(){
        $params = $this->request->param();
        $token    = $params['token'];
        $Common   = new CommonController();
        $user_id  = $Common->getUserId($token);
        // 返回当前所在周的第一天(周一)日期
        $now = time();    //当时的时间戳
        $number = date("w",$now);  //当时是周几
        $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
        $diff_day = $number - 1; //求到周一差几天
        $m= strtotime(date("Y-m-d",$now - ($diff_day * 60 * 60 * 24)));
        $n= strtotime(date("Y-m-d",$now - ($diff_day * 60 * 60 * 24)+60*60*24*7))-1;
        $res = Db::name('user_run')->field('id,user_id,step_num,stride,consume,time_long,add_time,device_sn,is_valid')->where(" user_id = $user_id and is_valid=0 and add_time>=$m and add_time<=$n")->order('add_time desc')->select()->toarray();//当周的数据


        $res_is_valid = Db::name('user_run')->field('id,user_id,step_num,stride,consume,time_long,add_time,device_sn,is_valid')->where(" user_id = $user_id and is_valid=1 and add_time>=$m and add_time<=$n")->order('add_time desc')->select()->toarray();//当周的数据
        foreach ($res_is_valid as $key => $value) {
            $res_is_valid[$key]['add_time'] = date('Y-m-d',$value['add_time']);
            $number = date("w",$value['add_time']);  //当时是周几
            $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
            $res_is_valid[$key]['day'] = $number;
        }
        foreach ($res as $key => $value) {
              $res[$key]['add_date'] = date('H',$value['add_time']);
              $number = date("w",$value['add_time']);  //当时是周几
              $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
              $res[$key]['day'] = $number;
        }

       
        $week_day = array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'');
         //时间数组
        $c = array('00'=>0,'01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,'07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0,'18'=>0,'19'=>0,'20'=>0,'21'=>0,'22'=>0,'23'=>0);
        $p ='';
        $q ='';
        foreach ($res as $k => $v){ 
            if(array_key_exists($v['day'],$week_day)){
              $p[$v['day']][]= $v;
            }
            if($v['is_valid']==1){
              $p['count_day'] = $v;
            }
        }

        if($p){
          foreach ($p as $key => $value) {
            $c = array('00'=>0,'01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,'07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0,'18'=>0,'19'=>0,'20'=>0,'21'=>0,'22'=>0,'23'=>0);
              foreach ($value as $k => $v) {
                 if(array_key_exists($v['add_date'],$c)){
                    $c[$v['add_date']] = $v['step_num'];
                    $q[$key]= $c;
                }
              }
          }
        }
        return json(['error'=>0,'msg'=>'success','data'=>$q,'res_is_valid'=>$res_is_valid]);
    }

    //获取指定周步数的数据
    public function getweek_info(){
        $params = $this->request->param();
        $token    = $params['token'];
        $Common   = new CommonController();
        $user_id  = $Common->getUserId($token);
        //获取用户第一条数据得出第一周数据
        $first_run = Db::name('user_run')->where("user_id = $user_id and is_valid=0")->limit(1)->order('add_time asc')->value('add_time');
        // 返回执行日期所在周的第一天(周一)日期
        $now = $first_run;    //当时的时间戳
        $number = date("w",$now);  //当时是周几
        $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
        $diff_day = $number - 1; //求到周一差几天
        $a= strtotime(date("Y-m-d",$now - ($diff_day * 60 * 60 * 24)));
        $b= strtotime(date("Y-m-d",$now - ($diff_day * 60 * 60 * 24)+60*60*24*7))-1;
        $data['0'] = Db::name('user_run')->field('id,user_id,step_num,stride,consume,time_long,add_time,device_sn,is_valid')->where(" user_id = $user_id  and is_valid=0 and add_time>=$a and add_time<=$b")->order("add_time desc")->select()->toarray();//第一周的数据

        $res_is_valid['0'] = Db::name('user_run')->field('id,user_id,step_num,stride,consume,time_long,add_time,device_sn,is_valid')->where(" user_id = $user_id and is_valid=1 and add_time>=$a and add_time<=$b")->order('add_time desc')->select()->toarray();//当周的数据
        foreach ($res_is_valid['0'] as $key => $value) {
         $res_is_valid['0'][$key]['add_time'] = date('Y-m-d',$value['add_time']);
          $number = date("w",$value['add_time']);  //当时是周几
          $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
          $res_is_valid['0'][$key]['day'] = $number;

        }


        if($data['0']){
          foreach ($data['0'] as $key => $value) {
            $data['0'][$key]['add_date'] = date('H',$value['add_time']);
            $number = date("w",$value['add_time']);  //当时是周几
            $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
            $data['0'][$key]['day'] = $number;
          }
        }

        $week_day = array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'');
        //时间数组
        $c = array('00'=>0,'01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,'07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0,'18'=>0,'19'=>0,'20'=>0,'21'=>0,'22'=>0,'23'=>0);
        $p ='';
        $q ='';
        if($data['0']){
          foreach ($data['0'] as $k => $v) { 
              if(array_key_exists($v['day'],$week_day)){
                $p[$v['day']][]= $v;
              }
          }
        }

        if($p){
          foreach ($p as $key => $value) {
            $c = array('00'=>0,'01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,'07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0,'18'=>0,'19'=>0,'20'=>0,'21'=>0,'22'=>0,'23'=>0);
              foreach ($value as $k => $v) {
                 if(array_key_exists($v['add_date'],$c)){
                    $c[$v['add_date']] = $v['step_num'];
                    $q[$key]= $c;
                  }
              }
          }
        }
       
        $z['0'] =$q;
        // print_r($z);exit;
        // 返回当前所在周的第一天(周一)日期
        $now1 = time();    //当时的时间戳
        $number1 = date("w",$now1);  //当时是周几
        $number1 = $number1 == 0 ? 7 : $number1; //如遇周末,将0换成7
        $diff_day1 = $number1 - 1; //求到周一差几天
        $m= strtotime(date("Y-m-d",$now1 - ($diff_day1 * 60 * 60 * 24)));
        $n= strtotime(date("Y-m-d",$now1 - ($diff_day1 * 60 * 60 * 24)+60*60*24*7));
        // //从开始到现在有几周
        $count_week=($n-$a)/(60*60*24)/7-1;
      
        $week_id = isset($params['week_id'])?intval($params['week_id']):$count_week;//默认为当前周

        for($i=1;$i<=$count_week;$i++){
            $monday_time = strtotime(date("Y-m-d",($now - ($diff_day * 60 * 60 * 24))+60*60*24*7*$i));
            $sunday_time = strtotime(date("Y-m-d",($now - ($diff_day * 60 * 60 * 24))+60*60*24*7*($i+1)))-1;
            $data[$i] = Db::name('user_run')->field('id,user_id,step_num,stride,consume,time_long,add_time,device_sn')->where(" user_id = $user_id and is_valid=0 and add_time>=$monday_time and add_time<=$sunday_time")->select()->toarray();//第N周的数据
           

            $res_is_valid[$i] = Db::name('user_run')->field('id,user_id,step_num,stride,consume,time_long,add_time,device_sn,is_valid')->where(" user_id = $user_id and is_valid=1 and add_time>=$monday_time and add_time<=$sunday_time")->order('add_time desc')->select()->toarray();//当周的数据
            foreach ($res_is_valid[$i] as $key => $value) {
             $res_is_valid[$i][$key]['add_time'] = date('Y-m-d',$value['add_time']);

            $number = date("w",$value['add_time']);  //当时是周几
            $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
            $res_is_valid[$i][$key]['day'] = $number;

            }

            $p1='';
            foreach ($data[$i] as $key => $value) {
              $data[$i][$key]['add_date'] = date('H',$value['add_time']);
              $number = date("w",$value['add_time']);  //当时是周几
              $number = $number == 0 ? 7 : $number; //如遇周末,将0换成7
              $data[$i][$key]['day'] = $number;
            } 
             $week_day1 = array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'');

             $d = array('00'=>0,'01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,'07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0,'18'=>0,'19'=>0,'20'=>0,'21'=>0,'22'=>0,'23'=>0);

             foreach ($data[$i] as $key => $value) {
               if(array_key_exists($value['day'],$week_day1)){
                  $p1[$value['day']][]= $value;
                }
             }

             if($p1){
               foreach ($p1 as $key => $value) {
                 $d = array('00'=>0,'01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,'07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0,'18'=>0,'19'=>0,'20'=>0,'21'=>0,'22'=>0,'23'=>0);
                  foreach ($value as $k => $v) {
                     if(array_key_exists($v['add_date'],$d)){
                        $d[$v['add_date']] = $v['step_num'];
                        $z[$i][$key]= $d;
                      }
                  }
               }
            }
        }

        if(isset($z[$week_id])){
          $res = $z[$week_id];
        }else{
          $res =array();
        }

        if(isset($res_is_valid[$week_id])){
          $res_is_valid = $res_is_valid[$week_id];
        }else{
          $res_is_valid =array();
        }
        return json(['error'=>0,'msg'=>'success','now_week_id'=>$count_week,'data'=>$res,'res_is_valid'=>$res_is_valid]); 
    }
    //获取指定月步数的数据
    public function getmonth_info(){
      $params = $this->request->param();
      $month_time = isset($params['month_time'])?$params['month_time']:'';
      if(!$month_time){
          return json(['error'=>-1,'msg'=>'传参错误']); 
      }
      $token    = $params['token'];
      $Common   = new CommonController();
      $user_id  = $Common->getUserId($token);
      $res = $this->GetTheMonth($month_time);
      $month_first_day  = $res[0];
      $month_last_day   = $res[1];
      //指定月的数据
      $res_month = Db::name('user_run')->where("user_id = $user_id")->where("add_time>=$month_first_day and add_time<=$month_last_day and is_valid=0")->select()->toarray();
      $all_month_step   =0;
      $all_month_consume=0;
      $all_month_time   = 0;
      $all_month_jl     = 0;
      foreach($res_month as $key => $value){
              $res_month[$key]['add_date']  = date('Y-m-d H:i:s',$value['add_time']);
              $all_month_step     +=$value['step_num'];
              $all_month_consume  +=$value['consume'];
              $all_month_time     +=$value['time_long'];
              $all_month_jl       +=$value['step_num']*$value['stride']/100;
      }
      $total['all_month_step']    =$all_month_step;
      $total['all_month_jl']      =$all_month_jl.'米';
      $total['all_month_consume'] =$all_month_consume;
      $total['all_month_time']    =$all_month_time;
      return json(['error'=>0,'msg'=>'success','data'=>$res_month,'total'=>$total]);
    }

    //获取指定日期当月的第一天和最后一天零点的时间戳
    public function GetTheMonth($date){
      $firstday = date("Y-m-01",$date);
      $c        = strtotime(date("Y-m-01",$date));
      $lastday  = date("Y-m-d",strtotime("$firstday +1 month -1 day"));
      $d        = strtotime($lastday)+60*60*24-1;
      return array($c,$d);
    }

    //设置设备各种属性状态
    public function set_sb_status(){
      $params   = $this->request->param();
      $token    = $params['token'];
      $Common   = new CommonController();
      $user_id  = $Common->getUserId($token);
      if(isset($params['ld_tx'])){
        $data['ld_tx'] = $params['ld_tx'];
      }
      if(isset($params['dx_tx'])){
        $data['dx_tx'] = $params['dx_tx'];
      }
      if(isset($params['wx_tx'])){
 
        $data['wx_tx'] = $params['wx_tx'];
      }
      if(isset($params['qq_tx'])){
        $data['qq_tx'] = $params['qq_tx'];
      }
      $data['device_id']  = isset($params['device_id'])?$params['device_id']:'';
      $data['user_id']    = $user_id;
      if(!$user_id || !$data['device_id']){
        return json(['error'=>-1,'msg'=>'参数错误']); 
      }
      $is_set = Db::name('sb_status')->where("device_id='$data[device_id]' and user_id = $user_id")->find();
      if($is_set){
        $res = Db::name('sb_status')->update($data);
      }else{
        $res = Db::name('sb_status')->insert($data);
      }
      if($res!==false){
        return json(['error'=>0,'msg'=>'设置成功']); 
      }else{
        return json(['error'=>-1,'msg'=>'设置失败']); 
      }

    }


}

?>