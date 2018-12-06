<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
use think\Config;
use think\Db;
use think\Url;
use dir\Dir;
use think\Route;
use think\Loader;
use think\Request;
use cmf\lib\Storage;
use GatewayClient\Gateway;
use SignatureHelper\SignatureHelper;

// 应用公共文件

//设置插件入口路由
Route::any('plugin/[:_plugin]/[:_controller]/[:_action]', "\\cmf\\controller\\PluginController@index");
Route::get('captcha/new', "\\cmf\\controller\\CaptchaController@index");
require EXTEND_PATH.'GatewayClient/Gateway.class.php';

/*获取短信模板数据*/
function getSmsTemplate($index=false){
    //药包购买成功,您在${name}的订单提交成功
    //申请代理成功通知,您的${mtname}申请已于${submittime}审核已经通过
    //用户解绑设备验证,设备解绑验证码：${code}，您正进行身份验证，请勿告知他人！
    //管理员更换,您的验证码：${code}，更换管理员，请保证短信安全请勿给予他人
    //短信登陆，${code}，您正在登陆平台会员，感谢您的支持！
    //兑换验证码,兑换动态码为：${code}，您正在进行密码重置操作，如非本人操作，请忽略本短信！
    //转赠验证码，转赠确认您的验证码${code}，该验证码5分钟内有效，请勿泄漏于他人
    //找回密码，您的动态码为：${code}，您正在进行密码重置操作，5分钟内有效，如非本人操作，请忽略本短信！
    //后台验证通用模板，管理员进行操作${action}，需要验证码:${code}
    //尊敬的用户，您于${submittime}兑换已经审核，提交至第三方。
    //短信找回密码，您好，您的交易密码已经重置为${password}，请及时修改密码。
    //您的验证码${code}，该验证码15分钟内有效，请勿泄漏于他人！(待审核)
    $config = array('','SMS_136386690','SMS_136386688','SMS_135797788','SMS_135802727','SMS_134319759','SMS_134314774','SMS_134314770','SMS_134324737','SMS_136855871','SMS_138060746','SMS_137425905','SMS_137665561');
    if($index){
        return $config[$index];
    }else{
        return $config;
    }

}
/*获取设备云平台参数*/
function getConfig(){
    //设备使用的时间范围
    $device_use_time = get_parameter_settings('use_time_range');
    if(empty($device_use_time)){
        $device_use_time = [40,70];
    }else{
        $device_use_time = explode('~', $device_use_time);
    }
    $config = array(
        'device_use_time' => $device_use_time,
        'access_token' => 'NDA3MjcyMTAyNjZCQTcwREQ1RDc0NTJDMzkwODM1QjRBMEFDRTVEQjkwODFDRTA2Mzk4NjRFMzIyNDZEN0Q2Mg=='
    );
    return $config;
}

/*
*保存设备提交数据记录缓存
*/
function receive_data_save_to_cache($device,$data){
    try{
        if(empty($device['device_id'])){
            return false;
        }
        Db::name('device_receive_log')->insertGetId(['device_id'=>$device['device_id'],'param'=>json_encode($data),'time'=>time()]);
    } catch (\Exception $e) {}
}

function _SITEID($id){
    $_SERVER['SITEID'] = $id;
}

/*获取字典*/
function getDictionary($key){
    Config::load(ROOT_PATH.'data/conf/dictionary.php');
    $config = Config::get($key);
    return $config;
}
//require EXTEND_PATH.'SignatureHelper/SignatureHelper.php';
/*
$type 推送类型
$to_users 需要推送的用户
$is_all 全部推送，为true时$to_users失效
*/
function pushTipMessage($sendData,$to_users=array(),$is_all = false){
    try{

        Gateway::$registerAddress = '127.0.0.1:8004';

        if(empty($to_users) && !$is_all){
            return false;
        }

        if(!$to_users[0] && !$is_all){
            return false;
        }

        if(!is_array($to_users) && !$is_all){
            return false;
        }

        $all_online_list = Gateway::getAllClientSessions();
        $Client_uid = array();
        $date = time();
        foreach($all_online_list as $key => $val){
            if(isset($val['id'])){
                if((in_array($val['id'], $to_users))){
                    $Client_uid[] = $key;
                }elseif($is_all){
                    $Client_uid[] = $key;
                }
            }
        }
        if(empty($Client_uid) && !$is_all){
            return false;
        }
        $data = ['time'=>$date,'Client_uid'=>$Client_uid];

        //file_put_contents('data/gateway.txt',json_encode(array('cid'=>$Client_uid,'to'=>$to_users)));
        Gateway::sendToAll(json_encode($sendData),$Client_uid);
        file_put_contents('device/receive_push_log_'.date('Ymd').'.txt','发送数据：'.json_encode($sendData).'。用户：'.json_encode($to_users).'。时间：'.date('Y-m-d H:i:s',$date).PHP_EOL,FILE_APPEND);

    } catch (\Exception $e) {}

}

function bindClientUID($id,$client_id){
    Gateway::$registerAddress = '127.0.0.1:8004';
    // 此session是在用户登录时设置的
    if (!$id) {
        exit(json_encode(array('code'=> '400','msg' => '请登录')));
    }
    if (!$client_id || $client_id=='') {
        exit(json_encode(array('code'=> '400','msg' => '缺少参数')));
    }
    $user = DB::name('User')->where(['id'=>$id])->find();
    Gateway::bindUid($client_id, $id);
    Gateway::updateSession($client_id, $user);
}


function getAllClient(){
    Gateway::$registerAddress = '127.0.0.1:8004';
    $all_online_list = Gateway::getAllClientSessions();
    return $all_online_list;

}
/**
 *
 * 替换url
 * @param string $$content 内容字符串
 * @param string $url 替换成的内容
 */
function changestring($content='',$url=''){
    preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU",$content,$matches);
    if(!empty($matches)){
        $imgurl = $matches[2];
        foreach($imgurl as $val){
            // preg_match("/^.*\//",$val,$res);   //先匹配出来图片存储的路径
            if((strpos($val,'https')===false) and (strpos($val,'http')===false)){
                $content = str_replace($val,$url.$val,$content);
            }
        }
    }else{
        return FALSE;
    }
    return $content;
}
//处理无限子分类
function make_tree($list,$pk='id',$pid='pid',$child='_child',$root=0){
    $tree=array();
    $packData=array();
    foreach ($list as  $data) {
        $packData[$data[$pk]] = $data; //$packData[1]=$data; $packData[2]=$data
    }
    foreach ($packData as $key =>$val){
        if($val[$pid]==$root){   //代表跟节点
            $tree[]=& $packData[$key];
        }else{
            //找到其父类
            $packData[$val[$pid]][$child][]=& $packData[$key];
        }
    }
    return $tree;
}
/*
*$type 1 查找普通三级分销用户，2查找代理多级分销用户
*/
function recursionQuery($uid,&$result=array(),$type=1){
    $res = Db::name('user_referrer')->where(['user_id'=>$uid,'type'=>$type])->find();
    if(!empty($res)){
        if ($res['parent_user_id']>0) {
            //防止死循环
            if(in_array($res['parent_user_id'],$result)){
                return $result;
            }
            $result[]=$res['parent_user_id'];
            recursionQuery($res['parent_user_id'],$result,$type);
        }
    }
    return $result;
}
/*递归获取该用户的所有上级推介人员。$type 1 查找普通三级分销用户，2查找代理多级分销用户*/
function get_referrer_user_up_path($user_id,$type){
    $arr = array();
    $user_id_arr = recursionQuery($user_id,$arr,$type);
    $user_id_arr = array_reverse($user_id_arr);//把顶级的排前面。先来后到
    array_push($user_id_arr,$user_id);//加入推介者
    return array_unique($user_id_arr);
}
/*递归*/
function recursionAgentQuery($agentid,&$result=array()){
    $res = Db::name('agent')->where(['id'=>$agentid])->find();
    if(!empty($res)){
        if ($res['parent_id']>0) {
            //防止死循环
            if(in_array($res['parent_id'],$result)){
                return $result;
            }
            $result[]=$res['parent_id'];
            recursionAgentQuery($res['parent_id'],$result);
        }
    }
    return $result;
}
/*递归获取该用户的所有上级代理*/
function get_agent_up_path($agent_id){
    $agent_id_arr = recursionAgentQuery($agent_id);
    $agent_id_arr = array_reverse($agent_id_arr);//把顶级的排前面
    array_push($agent_id_arr,$agent_id);//加入当前代理级
    return array_unique($agent_id_arr);
}
/*获取用户的所有设备*/
function get_user_all_device($user_id){
    $list = Db::name('device')->alias('a')->join('__DEVICE_USER_REL__ b','a.device_id=b.device_id')->where(['b.user_id'=>$user_id])->select()->toArray();
}
/*获取站点ID。$type:1返回id，2返回站点信息*/
function get_current_site($type=1){
    $site_id = session('SITE_ID');
    if(!$site_id){
        $site_id = 0;
    }
    if(!$site_id){
        $site_id = isset($_SERVER['SITEID'])?$_SERVER['SITEID']:0;//获取定义的变量
    }
    //如果是超级管理员且站点未选的情况下默认1站点
    if(!$site_id && cmf_get_current_admin_id()==1){
        $site_id = 1;
    }
    if(!$site_id){
        $site_id = 1;//测试阶段1
    }
    if($type==1){
        return $site_id;
    }else{
        $site = Db::name('site')->where(['id'=>$site_id])->find();
        return $site;
    }
}
/**
 * 处理插入推介表
 * $referrer_phone  推介者账户名
 * $curr_user_id  被推介的用户ID，也就是当前用户ID
 * $type 1普通会员三级分销关系，2代理各级的关系
 * @return int
 */
function handle_referrer_insert_db($referrer_phone, $curr_user_id, $type=1){
    $res = Db::name('user_referrer')->where(['user_id'=>$curr_user_id,'status'=>array('egt',1),'type'=>$type])->find();
    if(!empty($res)){
        //防止注入重复推介
        return false;
    }
    //查找一级推介人
    $user = Db::name('User')->where(['user_login'=>$referrer_phone])->find();
    //检测是否自己推介自己
    if($curr_user_id == $user['id']){
        return false;
    }
    if(empty($user)){
        return false;
    }
    $urser_referrer = Db::name('user_referrer')->where(['user_id'=>$user['id'],'status'=>1])->order('type desc')->find();
    if(!empty($urser_referrer)){
        $path = $urser_referrer['path'].','.$curr_user_id;
    }else{
        $path = '0,'.$user['id'].','.$curr_user_id;
    }
    //插入推介表
    $data = [
        'user_id'=>$curr_user_id,
        'parent_user_id'=>$user['id'],
        'type'=>$type,
        'path'=>$path,
        'create_time'=>time()
    ];
    if($type == 2){
        $data['status'] = 1;
    }
    $result = Db::name('user_referrer')->insertGetId($data);
    return $result;
}

/**
 * 处理分销奖励
 * $referrer_user_id  推介者账ID
 * $score  积分
 * $type  积分类型
 * @return int
 */

function handle_distribution_reward($referrer_user_id, $score, $type, $curr_user_id){
    $agentModel = Db::name('agent');//代理商
    $agentApplyModel = Db::name('agent_apply');//代理商申请
    $userReferrerModel = Db::name('user_referrer');//用户推介关系表

    $referrer = Db::name('User')->where(['id'=>$referrer_user_id])->find();//查找一级推介人
    //$referrerAgentApply = $agentApplyModel->where(['user_login'=>$referrer_user_id])->find();
    //检测是否自己推介自己
    if($curr_user_id == $referrer_user_id){
        return false;
    }
    if(empty($referrer)){
        return false;
    }

    $user_referrer = $userReferrerModel->where(['user_id'=>$referrer_user_id,'status'=>1])->order('type desc')->find();
    if(empty($user_referrer)){
        //没任何推荐关系就只能是单个人了
        $agent = Db::name('agent_apply')
            ->alias('a')
            ->field('b.*')
            ->join('__AGENT__ b','a.agent_id=b.id')
            ->where(['a.user_id'=>$referrer_user_id,'a.status'=>1])
            ->find();
        if(!empty($agent)){
            //走代理的抽成率
            $percentage_score = $score*($agent['percentage']/100);//按设置好的百分比抽成
            handle_score($percentage_score,$type,$referrer_user_id,$curr_user_id);
        }else{
            //走会员的抽成率
            $referrer_reward = get_parameter_settings('referrer_reward');//获取分成设置
            $percentage = $referrer_reward['referrer_reward_1']/100;//3% 
            $percentage_score = ($score*$percentage);//按设置好的百分比抽成
            handle_score($percentage_score,$type,$referrer_user_id,$curr_user_id);
        }
        return false;
    }
    //如果有推荐关系则继续走
    $pathArr = explode(',', $user_referrer['path']);
    if($user_referrer['type']==2){
        //奖励代理分销
        foreach ($pathArr as $key => $val) {
            //查找其所在的代理级别抽成比例。公司-片区经理-总代理-省代理-市代理；
            $agent = Db::name('agent_apply')
                ->alias('a')
                ->field('b.*')
                ->join('__AGENT__ b','a.agent_id=b.id')
                ->where(['a.user_id'=>$val,'a.status'=>1])
                ->find();
            if(!empty($agent)){
                $percentage_score = $score*($agent['percentage']/100);//按设置好的百分比抽成
                handle_score($percentage_score,$type,$val,$curr_user_id);
            }
            if($val==$referrer_user_id){
                break;
            }
        }
        //再次奖励普通会员三级分销
        three_level_distribution_reward($referrer_user_id,$score,$type,$curr_user_id);
    }else{
        three_level_distribution_reward($referrer_user_id,$score,$type,$curr_user_id);
        $pathArr = array_reverse($pathArr);//反转把直系推介人排在最前面
        foreach ($pathArr as $key => $val) {
            //查找其推荐关系链是否有代理关系存在。
            $agent = Db::name('agent_apply')
                ->alias('a')
                ->field('a.user_id,b.*')
                ->join('__AGENT__ b','a.agent_id=b.id')
                ->where(['a.user_id'=>$val,'a.status'=>1])
                ->find();
            if(!empty($agent)){
                //如果判断其有代理类型的用户，则重新以该用户ID为推荐ID，再次奖励代理关系链的抽成
                handle_distribution_reward($agent['user_id'], $score, $type, $curr_user_id);
                break;
            }
        }
    }
}
/**
 * 处理普通三级分销奖励
 * $referrer_user_id  推介者账户ID
 * $score  积分
 * $type  分销类型
 * @return int
 */
function three_level_distribution_reward($referrer_user_id,$score,$type,$curr_user_id){

    //查找一级推介人
    $referrer = Db::name('User')->where(['id'=>$referrer_user_id])->find();
    //检测是否自己推介自己
    if($curr_user_id == $referrer['id']){
        return false;
    }
    if(empty($referrer)){
        return false;
    }

    $user_referrer = Db::name('user_referrer')->where(['user_id'=>$referrer_user_id,'status'=>1])->order('type asc')->order('type asc')->find();
    if(empty($user_referrer)){
        return false;
    }

    $pathArr = explode(',', $user_referrer['path']);

    $referrer_reward = get_parameter_settings('referrer_reward');//获取分成设置
    $percentage = [];
    $percentage[] = $referrer_reward['referrer_reward_1']/100;//3% 
    $percentage[] = $referrer_reward['referrer_reward_2']/100;//2%
    $percentage[] = $referrer_reward['referrer_reward_3']/100;//1%
    $pathArr = array_reverse($pathArr);//反转把直系推介人排在最前面。入：0，1，2变成2，1，0。2是直系推介人
    foreach ($pathArr as $key => $val) {
        if($key>2){
            break;
        }
        $percentage_score = ($score*$percentage[$key]);//按设置好的百分比抽成
        handle_score($percentage_score,$type,$val,$curr_user_id);
    }
}


//直接获取平台设置参数
function get_parameter_settings($key){
    $parameterSettings  = cmf_get_option('parameter_settings');
    return $parameterSettings[$key];
}

//更新用户积分
function update_user_score($user_id,$score){
    $result = Db::name('user')->where('id', $user_id)->setInc('score',$score);
    return $result;
}

//更新用户药包数量
function update_user_package($user_id,$package){
    $order_code =  generate_order_code($user_id);
    $order_id = generate_order($package,$user_id,$order_code,3,2);

    $result = Db::name('user')->where('id', $user_id)->setInc('package',$package);
    return $result;
}
/*直推奖励,$user_id推介者ID*/
function handle_direct_referral_reward($user_id,$curr_user_id){
    $register_reward_score = get_parameter_settings('register_reward_score');
    handle_score($register_reward_score,6,$user_id,$curr_user_id);
}
/**
 * 处理积分插入积分表并更新用户的积分
 * $score 积分
 * $type 积分类型
 * $user_id 获得积分的人
 * $come_from_user_id 生产积分的人
 * @return boolean
 */
function handle_score($score,$type='',$user_id,$status = 0,$wallet_address=''){
    if(!$score){
        return false;
    }
    if(!$type){
        return false;
    }
    if(empty($user_id)){
        return false;
    }
    $user = get_user($user_id);
    $data_score['user_id'] = $user_id;
    $data_score['num'] = $score;
    $data_score['reason'] = $type;
    $data_score['status'] = $status;
    // $data_score['come_from_user_id'] = $come_from_user_id;
    $data_score['add_time'] = time();
    $data_score['wallet_address'] = $wallet_address;
    $result = Db::name('Score_log')->insertGetId($data_score);
    if($result){
        update_user_score($user_id,$score);
        // try{
        //       $score_type_arr = getDictionary('score_type');
        //       if($score>0){
        //           $score = '+'.$score;
        //       }
        //       $score_type = '';
        //       if(isset($score_type_arr[$type])){
        //           $score_type = '因“'.$score_type_arr[$type].'”';
        //       }
        //       send_message(array($user_id), '积分变化', "你的积分".$score_type."有新变化，变化值：".$score, 0, null, 2);
        //   }catch(Exception $e){}
    }
    return $result;
}
/**
 * 处理药包购买插入订单表
 */
function generate_order($package_num,$user_id,$order_code,$type=1,$status=1,$money=null,$remarks=null){
    if(!$package_num){
        return false;
    }
    if(!$user_id){
        return false;
    }
    if(!$order_code){
        return false;
    }
    if(!$status){
        return false;
    }
    if(!$money){
        $money = $package_num*5;
    }
    $address = Db::name('address')->where(['user_id'=>$user_id])->order('id desc')->find();
    $data['package_num'] = $package_num;
    $data['order_code'] = $order_code;
    $data['price'] = $money;
    $data['user_id'] = $user_id;
    $data['status'] = $status;
    $data['type'] = $type;
    $data['remarks'] = $remarks;
    $data['address'] = json_encode($address, JSON_UNESCAPED_UNICODE);
    $data['create_time'] = time();
    $result = Db::name("order")->insertGetId($data);
    return $result;
}
/*生成订单编码*/
function generate_order_code($userid){
    $order_code = 'R'.$userid.time().rand('100000','999999');//规则：字符+用户ID+时间戳+随机6位字符串
    return $order_code;
}
/*更新设备状态*/
function update_device_state($device,$state){
    $result = Db::name('device')->where(['device_id'=>$device['device_id']])->update(['online'=>$state]);
    $sendData = [];
    if($state==1){
        $sendData['message_type'] = 'device_open';
    }else{
        $sendData['message_type'] = 'device_close';
    }
    $sendData['device_id'] = $device['device_id'];
    $sendData['time'] = time();
    pushTipMessage($sendData,array($device['user_id']));
    return $result;
}
/*
*$device_id  设备ID
*$score_log_id 本次使用产生的积分ID
*$user_id 用户ID
*$start_time  设备传过来的开始时间
*$end_time  设备传过来的结束时间，有可能没有，诸如突然断电或断网之类的情况就没有 
*$over_time  完毕时间
*$type  1插入，2更新
*$type  机器使用方式。1插电后等待一分钟左右使用，2开机后立马使用
*/

function handle_device_use_log($device_id,$user_id,$score_log_id=0,$start_time=0,$end_time=0,$type=1,$insert_type = null){

    if(empty($user_id)){
        return false;
    }
    if(empty($device_id)){
        return false;
    }
    if($type==1){
        $data['user_id'] = $user_id;
        $data['device_id'] = $device_id;
        $data['package_num'] = 0;
        $data['score_log_id'] = $score_log_id;
        $data['start_time'] = $start_time;
        $data['create_time'] = time();
        $data['type'] = $insert_type;
        $result = Db::name('device_use_log')->insertGetId($data);
    }else{
        $data['end_time'] = $end_time;
        $data['package_num'] = 1;
        $result = Db::name('device_use_log')->where(['device_id'=>$device_id,'user_id'=>$user_id,'start_time'=>$start_time,'end_time'=>0])->order('id desc')->update($data);
    }
    return $result;
}



/**
 * 根据ID获取用户
 * @return array|boolean
 */
function get_user($id)
{
    $user = Db::name('User')->where('id', $id)->find();
    return $user;
}

/**
 * 根据长ID获取用户
 * @return array|boolean
 */
function get_user_long_id($long_id)
{
    $user = Db::name('User')->where('long_id', $long_id)->find();
    return $user;
}

/**
 * 生成唯一用户6位数长度起ID
 * @return int
 */
function create_only_long_id(){
    $number = cmf_random_number_string();
    $user = Db::name('User')->where(['long_id'=>$number])->find();
    if(empty($user)){
        return $number;
    }else{
        create_only_long_id();
    }
}

/**
 * 获取当前登录的管理员ID
 * @return int
 */
function cmf_get_current_admin_id()
{
    return session('ADMIN_ID');
}

/**
 * 判断前台用户是否登录
 * @return boolean
 */
function cmf_is_user_login()
{
    $sessionUser = session('user');
    return !empty($sessionUser);
}

/**
 * 获取当前登录的前台用户的信息，未登录时，返回false
 * @return array|boolean
 */
function cmf_get_current_user()
{
    $sessionUser = session('user');
    if (!empty($sessionUser)) {
        unset($sessionUser['user_pass']); // 销毁敏感数据
        return $sessionUser;
    } else {
        return false;
    }
}

/**
 * 更新当前登录前台用户的信息
 * @param array $user 前台用户的信息
 */
function cmf_update_current_user($user)
{
    session('user', $user);
}

/**
 * 获取当前登录前台用户id
 * @return int
 */
function cmf_get_current_user_id()
{
    $sessionUserId = session('user.id');
    if (empty($sessionUserId)) {
        return 0;
    }

    return $sessionUserId;
}

/**
 * 返回带协议的域名
 */
function cmf_get_domain()
{
    $request = Request::instance();
    return $request->domain();
}

/**
 * 获取网站根目录
 * @return string 网站根目录
 */
function cmf_get_root()
{
    $request = Request::instance();
    $root    = $request->root();
    $root    = str_replace('/index.php', '', $root);
    if (defined('APP_NAMESPACE') && APP_NAMESPACE == 'api') {
        $root = preg_replace('/\/api$/', '', $root);
        $root = rtrim($root, '/');
    }

    return $root;
}

/**
 * 获取当前主题名
 * @return string
 */
function cmf_get_current_theme()
{
    static $_currentTheme;

    if (!empty($_currentTheme)) {
        return $_currentTheme;
    }

    $t     = 't';
    $theme = config('cmf_default_theme');

    $cmfDetectTheme = config('cmf_detect_theme');
    if ($cmfDetectTheme) {
        if (isset($_GET[$t])) {
            $theme = $_GET[$t];
            cookie('cmf_template', $theme, 864000);
        } elseif (cookie('cmf_template')) {
            $theme = cookie('cmf_template');
        }
    }

    $hookTheme = hook_one('switch_theme');

    if ($hookTheme) {
        $theme = $hookTheme;
    }

    $_currentTheme = $theme;

    return $theme;
}


/**
 * 获取当前后台主题名
 * @return string
 */
function cmf_get_current_admin_theme()
{
    static $_currentAdminTheme;

    if (!empty($_currentAdminTheme)) {
        return $_currentAdminTheme;
    }

    $t     = '_at';
    $theme = config('cmf_admin_default_theme');

    $cmfDetectTheme = true;
    if ($cmfDetectTheme) {
        if (isset($_GET[$t])) {
            $theme = $_GET[$t];
            cookie('cmf_admin_theme', $theme, 864000);
        } elseif (cookie('cmf_admin_theme')) {
            $theme = cookie('cmf_admin_theme');
        }
    }

    $hookTheme = hook_one('switch_admin_theme');

    if ($hookTheme) {
        $theme = $hookTheme;
    }

    $_currentAdminTheme = $theme;

    return $theme;
}

/**
 * 获取前台模板根目录
 * @param string $theme
 * @return string 前台模板根目录
 */
function cmf_get_theme_path($theme = null)
{
    $themePath = config('cmf_theme_path');
    if ($theme === null) {
        // 获取当前主题名称
        $theme = cmf_get_current_theme();
    }

    return './' . $themePath . $theme;
}

/**
 * 获取用户头像地址
 * @param $avatar 用户头像文件路径,相对于 upload 目录
 * @return string
 */
function cmf_get_user_avatar_url($avatar)
{
    if (!empty($avatar)) {
        if (strpos($avatar, "http") === 0) {
            return $avatar;
        } else {
            if (strpos($avatar, 'avatar/') === false) {
                $avatar = 'avatar/' . $avatar;
            }

            return cmf_get_image_url($avatar, 'avatar');
        }

    } else {
        return $avatar;
    }

}

/**
 * CMF密码加密方法
 * @param string $pw 要加密的原始密码
 * @param string $authCode 加密字符串
 * @return string
 */
function cmf_password($pw, $authCode = '')
{
    if (empty($authCode)) {
        $authCode = Config::get('database.authcode');
    }
    $result = "###" . md5(md5($authCode . $pw));
    return $result;
}

/**
 * CMF密码加密方法 (X2.0.0以前的方法)
 * @param string $pw 要加密的原始密码
 * @return string
 */
function cmf_password_old($pw)
{
    $decor = md5(Config::get('database.prefix'));
    $mi    = md5($pw);
    return substr($decor, 0, 12) . $mi . substr($decor, -4, 4);
}

/**
 * CMF密码比较方法,所有涉及密码比较的地方都用这个方法
 * @param string $password 要比较的密码
 * @param string $passwordInDb 数据库保存的已经加密过的密码
 * @return boolean 密码相同，返回true
 */
function cmf_compare_password($password, $passwordInDb)
{
    if (strpos($passwordInDb, "###") === 0) {
        return cmf_password($password) == $passwordInDb;
    } else {
        return cmf_password_old($password) == $passwordInDb;
    }
}

/**
 * 文件日志
 * @param $content 要写入的内容
 * @param string $file 日志文件,在web 入口目录
 */
function cmf_log($content, $file = "log.txt")
{
    file_put_contents($file, $content, FILE_APPEND);
}
/**
 * 随机数字字符串生成
 * @param int $len 生成的字符串长度
 * @return string
 */
function cmf_random_number_string($len = 4)
{
    $chars    = [ "0", "1", "2","3", "4", "5", "6", "7", "8", "9"];
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}
/**
 * 随机字符串生成
 * @param int $len 生成的字符串长度
 * @return string
 */
function cmf_random_string($len = 4)
{
    $chars    = [
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    ];
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 清空系统缓存
 */
function cmf_clear_cache()
{
    $dirs     = [];
    $rootDirs = cmf_scan_dir(RUNTIME_PATH . "*");
    //$noNeedClear=array(".","..","Data");
    $noNeedClear = ['.', '..', 'log'];
    $rootDirs    = array_diff($rootDirs, $noNeedClear);
    foreach ($rootDirs as $dir) {

        if ($dir != "." && $dir != "..") {
            $dir = RUNTIME_PATH . $dir;
            if (is_dir($dir)) {
                //array_push ( $dirs, $dir );
                $tmpRootDirs = cmf_scan_dir($dir . "/*");
                foreach ($tmpRootDirs as $tDir) {
                    if ($tDir != "." && $tDir != "..") {
                        $tDir = $dir . '/' . $tDir;
                        if (is_dir($tDir)) {
                            array_push($dirs, $tDir);
                        } else {
                            @unlink($tDir);
                        }
                    }
                }
            } else {
                @unlink($dir);
            }
        }
    }
    $dirTool = new Dir("");
    foreach ($dirs as $dir) {
        $dirTool->delDir($dir);
    }
}

/**
 * 保存数组变量到php文件
 * @param string $path 保存路径
 * @param mixed $var 要保存的变量
 * @return boolean 保存成功返回true,否则false
 */
function cmf_save_var($path, $var)
{
    $result = file_put_contents($path, "<?php\treturn " . var_export($var, true) . ";?>");
    return $result;
}

/**
 * 设置动态配置
 * @param array $data <br>如：["cmf_default_theme"=>'simpleboot3'];
 * @return boolean
 */
function cmf_set_dynamic_config($data)
{

    if (!is_array($data)) {
        return false;
    }

    $configFile = CMF_ROOT . "data/conf/config.php";
    if (file_exists($configFile)) {
        $configs = include $configFile;
    } else {
        $configs = [];
    }

    $configs = array_merge($configs, $data);
    $result  = file_put_contents($configFile, "<?php\treturn " . var_export($configs, true) . ";");

    cmf_clear_cache();
    return $result;
}

/**
 * 转化格式化的字符串为数组
 * @param string $tag 要转化的字符串,格式如:"id:2;cid:1;order:post_date desc;"
 * @return array 转化后字符串<pre>
 * array(
 *  'id'=>'2',
 *  'cid'=>'1',
 *  'order'=>'post_date desc'
 * )
 */
function cmf_param_lable($tag = '')
{
    $param = [];
    $array = explode(';', $tag);
    foreach ($array as $v) {
        $v = trim($v);
        if (!empty($v)) {
            list($key, $val) = explode(':', $v);
            $param[trim($key)] = trim($val);
        }
    }
    return $param;
}

/**
 * 获取后台管理设置的网站信息，此类信息一般用于前台
 * @return array
 */
function cmf_get_site_info()
{
    $siteInfo = cmf_get_option('site_info');

    if (isset($siteInfo['site_analytics'])) {
        $siteInfo['site_analytics'] = htmlspecialchars_decode($siteInfo['site_analytics']);
    }

    return $siteInfo;
}

/**
 * 获取CMF系统的设置，此类设置用于全局
 * @return array
 */
function cmf_get_cmf_setting()
{
    return cmf_get_option('cmf_setting');
}

/**
 * 更新CMF系统的设置，此类设置用于全局
 * @param array $data
 * @return boolean
 */
function cmf_set_cmf_setting($data)
{
    if (!is_array($data) || empty($data)) {
        return false;
    }

    return cmf_set_option('cmf_setting', $data);
}

/**
 * 设置系统配置，通用
 * @param string $key 配置键值,都小写
 * @param array $data 配置值，数组
 * @param bool $replace 是否完全替换
 * @return bool 是否成功
 */
function cmf_set_option($key, $data, $replace = false)
{
    if (!is_array($data) || empty($data) || !is_string($key) || empty($key)) {
        return false;
    }
    $site_id    = get_current_site();
    $key        = strtolower($key);
    $option     = [];
    $findOption = Db::name('option')->where(['option_name' => $key,'site_id'=>$site_id])->find();
    if ($findOption) {
        if (!$replace) {
            $oldOptionValue = json_decode($findOption['option_value'], true);
            if (!empty($oldOptionValue)) {
                $data = array_merge($oldOptionValue, $data);
            }
        }

        $option['option_value'] = json_encode($data);
        Db::name('option')->where(['option_name'=>$key,'site_id'=>$site_id])->update($option);
        Db::name('option')->getLastSql();
    } else {
        $option['site_id'] = $site_id;
        $option['option_name']  = $key;
        $option['option_value'] = json_encode($data);
        Db::name('option')->insert($option);
    }

    cache('cmf_options_' . $key.($site_id), null);//删除缓存

    return true;
}

/**
 * 获取系统配置，通用
 * @param string $key 配置键值,都小写
 * @return array
 */
function cmf_get_option($key)
{
    if (!is_string($key) || empty($key)) {
        return [];
    }

    $site_id    = get_current_site();
    static $cmfGetOption;

    if (empty($cmfGetOption)) {
        $cmfGetOption = [];
    } else {
        if (!empty($cmfGetOption[$key])) {
            return $cmfGetOption[$key];
        }
    }

    $optionValue = cache('cmf_options_' . $key.($site_id));

    if (empty($optionValue)) {
        $optionValue = Db::name('option')->where(['option_name'=>$key,'site_id'=>$site_id])->value('option_value');

        if (!empty($optionValue)) {
            $optionValue = json_decode($optionValue, true);

            cache('cmf_options_' . $key.($site_id), $optionValue);
        }
    }

    $cmfGetOption[$key] = $optionValue;

    return $optionValue;
}

/**
 * 获取CMF上传配置
 */
function cmf_get_upload_setting()
{
    $uploadSetting = cmf_get_option('upload_setting');
    if (empty($uploadSetting) || empty($uploadSetting['file_types'])) {
        $uploadSetting = [
            'file_types' => [
                'image' => [
                    'upload_max_filesize' => '10240',//单位KB
                    'extensions'          => 'jpg,jpeg,png,gif,bmp4'
                ],
                'video' => [
                    'upload_max_filesize' => '10240',
                    'extensions'          => 'mp4,avi,wmv,rm,rmvb,mkv'
                ],
                'audio' => [
                    'upload_max_filesize' => '10240',
                    'extensions'          => 'mp3,wma,wav'
                ],
                'file'  => [
                    'upload_max_filesize' => '10240',
                    'extensions'          => 'txt,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar'
                ]
            ],
            'chunk_size' => 512,//单位KB
            'max_files'  => 20 //最大同时上传文件数
        ];
    }

    if (empty($uploadSetting['upload_max_filesize'])) {
        $uploadMaxFileSizeSetting = [];
        foreach ($uploadSetting['file_types'] as $setting) {
            $extensions = explode(',', trim($setting['extensions']));
            if (!empty($extensions)) {
                $uploadMaxFileSize = intval($setting['upload_max_filesize']) * 1024;//转化成B
                foreach ($extensions as $ext) {
                    if (!isset($uploadMaxFileSizeSetting[$ext]) || $uploadMaxFileSize > $uploadMaxFileSizeSetting[$ext]) {
                        $uploadMaxFileSizeSetting[$ext] = $uploadMaxFileSize;
                    }
                }
            }
        }

        $uploadSetting['upload_max_filesize'] = $uploadMaxFileSizeSetting;
    }

    return $uploadSetting;
}

/**
 * 获取html文本里的img
 * @param string $content html 内容
 * @return array 图片列表 数组item格式<pre>
 * [
 *  "src"=>'图片链接',
 *  "title"=>'图片标签的 title 属性',
 *  "alt"=>'图片标签的 alt 属性'
 * ]
 * </pre>
 */
function cmf_get_content_images($content)
{
    //import('phpQuery.phpQuery', EXTEND_PATH);
    \phpQuery::newDocumentHTML($content);
    $pq         = pq(null);
    $images     = $pq->find("img");
    $imagesData = [];
    if ($images->length) {
        foreach ($images as $img) {
            $img            = pq($img);
            $image          = [];
            $image['src']   = $img->attr("src");
            $image['title'] = $img->attr("title");
            $image['alt']   = $img->attr("alt");
            array_push($imagesData, $image);
        }
    }
    \phpQuery::$documents = null;
    return $imagesData;
}

/**
 * 去除字符串中的指定字符
 * @param string $str 待处理字符串
 * @param string $chars 需去掉的特殊字符
 * @return string
 */
function cmf_strip_chars($str, $chars = '?<*.>\'\"')
{
    return preg_replace('/[' . $chars . ']/is', '', $str);
}

/**
 * 发送邮件
 * @param string $address 收件人邮箱
 * @param string $subject 邮件标题
 * @param string $message 邮件内容
 * @return array<br>
 * 返回格式：<br>
 * array(<br>
 *    "error"=>0|1,//0代表出错<br>
 *    "message"=> "出错信息"<br>
 * );
 */
function cmf_send_email($address, $subject, $message)
{
    $smtpSetting = cmf_get_option('smtp_setting');
    $mail        = new \PHPMailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    $mail->IsHTML(true);
    //$mail->SMTPDebug = 3;
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet = 'UTF-8';
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);
    // 设置邮件正文
    $mail->Body = $message;
    // 设置邮件头的From字段。
    $mail->From = $smtpSetting['from'];
    // 设置发件人名字
    $mail->FromName = $smtpSetting['from_name'];
    // 设置邮件标题
    $mail->Subject = $subject;
    // 设置SMTP服务器。
    $mail->Host = $smtpSetting['host'];
    //by Rainfer
    // 设置SMTPSecure。
    $Secure           = $smtpSetting['smtp_secure'];
    $mail->SMTPSecure = empty($Secure) ? '' : $Secure;
    // 设置SMTP服务器端口。
    $port       = $smtpSetting['port'];
    $mail->Port = empty($port) ? "25" : $port;
    // 设置为"需要验证"
    $mail->SMTPAuth    = true;
    $mail->SMTPAutoTLS = false;
    $mail->Timeout     = 10;
    // 设置用户名和密码。
    $mail->Username = $smtpSetting['username'];
    $mail->Password = $smtpSetting['password'];
    // 发送邮件。
    if (!$mail->Send()) {
        $mailError = $mail->ErrorInfo;
        return ["error" => 1, "message" => $mailError];
    } else {
        return ["error" => 0, "message" => "success"];
    }
}

/**
 * 转化数据库保存的文件路径，为可以访问的url
 * @param string $file
 * @param mixed $style 图片样式,支持各大云存储
 * @return string
 */
function cmf_get_asset_url($file, $style = '')
{
    if (strpos($file, "http") === 0) {
        return $file;
    } else if (strpos($file, "/") === 0) {
        return $file;
    } else {
        $storage = cmf_get_option('storage');
        if (empty($storage['type'])) {
            $storage['type'] = 'Local';
        }
        if ($storage['type'] != 'Local') {
            $watermark = cmf_get_plugin_config($storage['type']);
            $style     = empty($style) ? $watermark['styles_watermark'] : $style;
        }
        $storage = Storage::instance();
        return $storage->getUrl($file, $style);
    }
}

/**
 * 转化数据库保存图片的文件路径，为可以访问的url
 * @param string $file 文件路径，数据存储的文件相对路径
 * @param string $style 图片样式,支持各大云存储
 * @return string 图片链接
 */
function cmf_get_image_url($file, $style = 'watermark')
{
    if (strpos($file, "http") === 0) {
        return $file;
    } else if (strpos($file, "/") === 0) {
        return cmf_get_domain() . $file;
    } else {
        $storage = cmf_get_option('storage');
        if (empty($storage['type'])) {
            $storage['type'] = 'Local';
        }
        if ($storage['type'] != 'Local') {
            $watermark = cmf_get_plugin_config($storage['type']);
            $style     = empty($style) ? $watermark['styles_watermark'] : $style;
        }
        $storage = Storage::instance();
        return $storage->getImageUrl($file, $style);
    }
}

/**
 * 获取图片预览链接
 * @param string $file 文件路径，相对于upload
 * @param string $style 图片样式,支持各大云存储
 * @return string
 */
function cmf_get_image_preview_url($file, $style = 'watermark')
{
    if(empty(!$file)){
        if (strpos($file, "http") === 0) {
            return $file;
        } else if (strpos($file, "/") === 0) {
            return $file;
        } else {
            $storage = cmf_get_option('storage');
            if (empty($storage['type'])) {
                $storage['type'] = 'Local';
            }
            if ($storage['type'] != 'Local') {
                $watermark = cmf_get_plugin_config($storage['type']);
                $style     = empty($style) ? $watermark['styles_watermark'] : $style;
            }
            $storage = Storage::instance();
            return $storage->getPreviewUrl($file, $style);
        }
    }
}

/**
 * 获取文件下载链接
 * @param string $file 文件路径，数据库里保存的相对路径
 * @param int $expires 过期时间，单位 s
 * @return string 文件链接
 */
function cmf_get_file_download_url($file, $expires = 3600)
{
    if (strpos($file, "http") === 0) {
        return $file;
    } else if (strpos($file, "/") === 0) {
        return $file;
    } else {
        $storage = Storage::instance();
        return $storage->getFileDownloadUrl($file, $expires);
    }
}

/**
 * 解密用cmf_str_encode加密的字符串
 * @param $string 要解密的字符串
 * @param string $key 加密时salt
 * @param int $expiry 多少秒后过期
 * @param string $operation 操作,默认为DECODE
 * @return bool|string
 */
function cmf_str_decode($string, $key = '', $expiry = 0, $operation = 'DECODE')
{
    $ckey_length = 4;

    $key  = md5($key ? $key : config("authcode"));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey   = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string        = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box    = range(0, 255);

    $rndkey = [];
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp     = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a       = ($a + 1) % 256;
        $j       = ($j + $box[$a]) % 256;
        $tmp     = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result  .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }

}

/**
 * 加密字符串
 * @param $string 要加密的字符串
 * @param string $key salt
 * @param int $expiry 多少秒后过期
 * @return bool|string
 */
function cmf_str_encode($string, $key = '', $expiry = 0)
{
    return cmf_str_decode($string, $key, $expiry, "ENCODE");
}

/**
 * 获取文件相对路径
 * @param string $assetUrl 文件的URL
 * @return string
 */
function cmf_asset_relative_url($assetUrl)
{
    if (strpos($assetUrl, "http") === 0) {
        return $assetUrl;
    } else {
        return str_replace('/upload/', '', $assetUrl);
    }
}

/**
 * 检查用户对某个url内容的可访问性，用于记录如是否赞过，是否访问过等等;开发者可以自由控制，对于没有必要做的检查可以不做，以减少服务器压力
 * @param string $object 访问对象的id,格式：不带前缀的表名+id;如post1表示xx_post表里id为1的记录;如果object为空，表示只检查对某个url访问的合法性
 * @param int $countLimit 访问次数限制,如1，表示只能访问一次
 * @param boolean $ipLimit ip限制,false为不限制，true为限制
 * @param int $expire 距离上次访问的最小时间单位s，0表示不限制，大于0表示最后访问$expire秒后才可以访问
 * @return true 可访问，false不可访问
 */
function cmf_check_user_action($object = "", $countLimit = 1, $ipLimit = false, $expire = 0)
{
    $request = request();
    $action  = $request->module() . "/" . $request->controller() . "/" . $request->action();

    if (is_array($object)) {
        $userId = $object['user_id'];
        $object = $object['object'];
    } else {
        $userId = cmf_get_current_user_id();
    }

    $ip = get_client_ip(0, true);//修复ip获取

    $where = ["user_id" => $userId, "action" => $action, "object" => $object];

    if ($ipLimit) {
        $where['ip'] = $ip;
    }

    $findLog = Db::name('user_action_log')->where($where)->find();

    $time = time();
    if ($findLog) {
        Db::name('user_action_log')->where($where)->update([
            "count"           => Db::raw("count+1"),
            "last_visit_time" => $time,
            "ip"              => $ip
        ]);

        if ($findLog['count'] >= $countLimit) {
            return false;
        }

        if ($expire > 0 && ($time - $findLog['last_visit_time']) < $expire) {
            return false;
        }
    } else {
        Db::name('user_action_log')->insert([
            "user_id"         => $userId,
            "action"          => $action,
            "object"          => $object,
            "count"           => Db::raw("count+1"),
            "last_visit_time" => $time, "ip" => $ip
        ]);
    }

    return true;
}

/**
 * 判断是否为手机访问
 * @return  boolean
 */
function cmf_is_mobile()
{
    static $cmf_is_mobile;

    if (isset($cmf_is_mobile))
        return $cmf_is_mobile;

    $cmf_is_mobile = Request::instance()->isMobile();

    return $cmf_is_mobile;
}

/**
 * 判断是否为微信访问
 * @return boolean
 */
function cmf_is_wechat()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

/**
 * 添加钩子
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @param mixed $extra 额外参数
 * @return void
 */
function hook($hook, &$params = null, $extra = null)
{
    return \think\Hook::listen($hook, $params, $extra);
}

/**
 * 添加钩子,只执行一个
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @param mixed $extra 额外参数
 * @return mixed
 */
function hook_one($hook, &$params = null, $extra = null)
{
    return \think\Hook::listen($hook, $params, $extra, true);
}


/**
 * 获取插件类的类名
 * @param string $name 插件名
 * @return string
 */
function cmf_get_plugin_class($name)
{
    $name      = ucwords($name);
    $pluginDir = cmf_parse_name($name);
    $class     = "plugins\\{$pluginDir}\\{$name}Plugin";
    return $class;
}

/**
 * 获取插件类的配置
 * @param string $name 插件名
 * @return array
 */
function cmf_get_plugin_config($name)
{
    $class = cmf_get_plugin_class($name);
    if (class_exists($class)) {
        $plugin = new $class();
        return $plugin->getConfig();
    } else {
        return [];
    }
}

/**
 * 替代scan_dir的方法
 * @param string $pattern 检索模式 搜索模式 *.txt,*.doc; (同glog方法)
 * @param int $flags
 * @param $pattern
 * @return array
 */
function cmf_scan_dir($pattern, $flags = null)
{
    $files = glob($pattern, $flags);
    if (empty($files)) {
        $files = [];
    } else {
        $files = array_map('basename', $files);
    }

    return $files;
}

/**
 * 获取某个目录下所有子目录
 * @param $dir
 * @return array
 */
function cmf_sub_dirs($dir)
{
    $dir     = ltrim($dir, "/");
    $dirs    = [];
    $subDirs = cmf_scan_dir("$dir/*", GLOB_ONLYDIR);
    if (!empty($subDirs)) {
        foreach ($subDirs as $subDir) {
            $subDir = "$dir/$subDir";
            array_push($dirs, $subDir);
            $subDirSubDirs = cmf_sub_dirs($subDir);
            if (!empty($subDirSubDirs)) {
                $dirs = array_merge($dirs, $subDirSubDirs);
            }
        }
    }

    return $dirs;
}

/**
 * 生成访问插件的url
 * @param string $url url格式：插件名://控制器名/方法
 * @param array $param 参数
 * @param bool $domain 是否显示域名 或者直接传入域名
 * @return string
 */
function cmf_plugin_url($url, $param = [], $domain = false)
{
    $url              = parse_url($url);
    $case_insensitive = true;
    $plugin           = $case_insensitive ? Loader::parseName($url['scheme']) : $url['scheme'];
    $controller       = $case_insensitive ? Loader::parseName($url['host']) : $url['host'];
    $action           = trim($case_insensitive ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = [
        '_plugin'     => $plugin,
        '_controller' => $controller,
        '_action'     => $action,
    ];
    $params = array_merge($params, $param); //添加额外参数

    return url('\\cmf\\controller\\PluginController@index', $params, true, $domain);
}

/**
 * 检查权限
 * @param $userId  int        要检查权限的用户 ID
 * @param $name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
 * @param $relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
 * @return boolean            通过验证返回true;失败返回false
 */
function cmf_auth_check($userId, $name = null, $relation = 'or')
{
    if (empty($userId)) {
        return false;
    }

    if ($userId == 1) {
        return true;
    }

    $authObj = new \cmf\lib\Auth();
    if (empty($name)) {
        $request    = request();
        $module     = $request->module();
        $controller = $request->controller();
        $action     = $request->action();
        $name       = strtolower($module . "/" . $controller . "/" . $action);
    }
    return $authObj->check($userId, $name, $relation);
}

function cmf_alpha_id($in, $to_num = false, $pad_up = 4, $passKey = null)
{
    $index = "aBcDeFgHiJkLmNoPqRsTuVwXyZAbCdEfGhIjKlMnOpQrStUvWxYz0123456789";
    if ($passKey !== null) {
        // Although this function's purpose is to just make the
        // ID short - and not so much secure,
        // with this patch by Simon Franz (http://blog.snaky.org/)
        // you can optionally supply a password to make it harder
        // to calculate the corresponding numeric ID

        for ($n = 0; $n < strlen($index); $n++) $i[] = substr($index, $n, 1);

        $passhash = hash('sha256', $passKey);
        $passhash = (strlen($passhash) < strlen($index)) ? hash('sha512', $passKey) : $passhash;

        for ($n = 0; $n < strlen($index); $n++) $p[] = substr($passhash, $n, 1);

        array_multisort($p, SORT_DESC, $i);
        $index = implode($i);
    }

    $base = strlen($index);

    if ($to_num) {
        // Digital number  <<--  alphabet letter code
        $in  = strrev($in);
        $out = 0;
        $len = strlen($in) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $bcpow = pow($base, $len - $t);
            $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
        }

        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) $out -= pow($base, $pad_up);
        }
        $out = sprintf('%F', $out);
        $out = substr($out, 0, strpos($out, '.'));
    } else {
        // Digital number  -->>  alphabet letter code
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) $in += pow($base, $pad_up);
        }

        $out = "";
        for ($t = floor(log($in, $base)); $t >= 0; $t--) {
            $bcp = pow($base, $t);
            $a   = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in  = $in - ($a * $bcp);
        }
        $out = strrev($out); // reverse
    }

    return $out;
}

/**
 * 验证码检查，验证完后销毁验证码
 * @param string $value
 * @param string $id
 * @param bool $reset
 * @return bool
 */
function cmf_captcha_check($value, $id = "", $reset = true)
{
    $captcha        = new \think\captcha\Captcha();
    $captcha->reset = $reset;
    return $captcha->check($value, $id);
}

/**
 * 切分SQL文件成多个可以单独执行的sql语句
 * @param $file sql文件路径
 * @param $tablePre 表前缀
 * @param string $charset 字符集
 * @param string $defaultTablePre 默认表前缀
 * @param string $defaultCharset 默认字符集
 * @return array
 */
function cmf_split_sql($file, $tablePre, $charset = 'utf8mb4', $defaultTablePre = 'cmf_', $defaultCharset = 'utf8mb4')
{
    if (file_exists($file)) {
        //读取SQL文件
        $sql = file_get_contents($file);
        $sql = str_replace("\r", "\n", $sql);
        $sql = str_replace("BEGIN;\n", '', $sql);//兼容 navicat 导出的 insert 语句
        $sql = str_replace("COMMIT;\n", '', $sql);//兼容 navicat 导出的 insert 语句
        $sql = str_replace($defaultCharset, $charset, $sql);
        $sql = trim($sql);
        //替换表前缀
        $sql  = str_replace(" `{$defaultTablePre}", " `{$tablePre}", $sql);
        $sqls = explode(";\n", $sql);
        return $sqls;
    }

    return [];
}

/**
 * 判断当前的语言包，并返回语言包名
 * @return string  语言包名
 */
function cmf_current_lang()
{
    return request()->langset();
}

/**
 * 获取惟一订单号
 * @return string
 */
function cmf_get_order_sn()
{
    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 获取文件扩展名
 * @param string $filename 文件名
 * @return string 文件扩展名
 */
function cmf_get_file_extension($filename)
{
    $pathinfo = pathinfo($filename);
    return strtolower($pathinfo['extension']);
}

/**
 * 检查手机或邮箱是否还可以发送验证码,并返回生成的验证码
 * @param string $account 手机或邮箱
 * @param integer $length 验证码位数,支持4,6,8
 * @return string 数字验证码
 */
function cmf_get_verification_code($account, $length = 6)
{
    if (empty($account)) return false;

    $verificationCodeQuery = Db::name('verification_code');
    $currentTime           = time();
    $maxCount              = 20;
    $findVerificationCode  = $verificationCodeQuery->where('account', $account)->find();

    $result                = false;
    if (empty($findVerificationCode)) {
        $result = true;
    } else {
        $sendTime       = $findVerificationCode['send_time'];
        $todayStartTime = strtotime(date('Y-m-d', $currentTime));
        if ($sendTime < $todayStartTime) {
            $result = true;
        } else if ($findVerificationCode['count'] < $maxCount) {
            $result = true;
        }
    }

    if ($result) {
        switch ($length) {
            case 4:
                $result = rand(1000, 9999);
                break;
            case 6:
                $result = rand(100000, 999999);
                break;
            case 8:
                $result = rand(10000000, 99999999);
                break;
            default:
                $result = rand(100000, 999999);
        }
    }

    return $result;
}

/**
 * 更新手机或邮箱验证码发送日志
 * @param string $account 手机或邮箱
 * @param string $code 验证码
 * @param int $expireTime 过期时间
 * @return boolean
 */
function cmf_verification_code_log($account, $code, $expireTime = 0)
{
    $currentTime           = time();
    $expireTime            = $expireTime > $currentTime ? $expireTime : $currentTime + 30 * 60;
    $verificationCodeQuery = Db::name('verification_code');
    $findVerificationCode  = $verificationCodeQuery->where('account', $account)->find();
    if ($findVerificationCode) {
        $todayStartTime = strtotime(date("Y-m-d"));//当天0点
        if ($findVerificationCode['send_time'] <= $todayStartTime) {
            $count = 1;
        } else {
            $count = Db::raw('count+1');
        }
        $result = $verificationCodeQuery
            ->where('account', $account)
            ->update([
                'send_time'   => $currentTime,
                'expire_time' => $expireTime,
                'code'        => $code,
                'count'       => $count
            ]);
    } else {
        $result = $verificationCodeQuery
            ->insert([
                'account'     => $account,
                'send_time'   => $currentTime,
                'code'        => $code,
                'count'       => 1,
                'expire_time' => $expireTime
            ]);
    }

    return $result;
}

/**
 * 手机或邮箱验证码检查，验证完后销毁验证码增加安全性,返回true验证码正确，false验证码错误
 * @param string $account 手机或邮箱
 * @param string $code 验证码
 * @param boolean $clear 是否验证后销毁验证码
 * @return string  错误消息,空字符串代码验证码正确
 */
function cmf_check_verification_code($account, $code, $clear = false)
{
    $verificationCodeQuery = Db::name('verification_code');
    $findVerificationCode  = $verificationCodeQuery->where('account',$account)->find();
   
    if ($findVerificationCode) {

        if ($findVerificationCode['expire_time'] > time()) {
            //if ($findVerificationCode['expire_time'] < time()) { //测试使用

            if ($code == $findVerificationCode['code']) {
                if ($clear) {
                    $verificationCodeQuery->where('account', $account)->update(['code' => '']);
                }
            } else {
                return "验证码不正确!";
            }
        } else {
            return "验证码已经过期,请先获取验证码!";
        }

    } else {
        return "请先获取验证码!";
    }

    return "";
}

/**
 * 清除某个手机或邮箱的数字验证码,一般在验证码验证正确完成后
 * @param string $account 手机或邮箱
 * @return boolean true：手机验证码正确，false：手机验证码错误
 */
function cmf_clear_verification_code($account)
{
    $verificationCodeQuery = Db::name('verification_code');
    $verificationCodeQuery->where('account', $account)->update(['code' => '']);
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename)
{
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
 * 生成用户 token
 * @param $userId
 * @param $deviceType
 * @return string 用户 token
 */
function cmf_generate_user_token($userId, $deviceType)
{
    $userTokenQuery = Db::name("user_token")
        ->where('user_id', $userId)
        ->where('device_type', $deviceType);
    $findUserToken  = $userTokenQuery->find();
    $currentTime    = time();
    $expireTime     = $currentTime + 24 * 3600 * 180;
    $token          = md5(uniqid()) . md5(uniqid());
    if (empty($findUserToken)) {
        Db::name("user_token")->insert([
            'token'       => $token,
            'user_id'     => $userId,
            'expire_time' => $expireTime,
            'create_time' => $currentTime,
            'device_type' => $deviceType
        ]);
    } else {
        if ($findUserToken['expire_time'] <= time()) {
            Db::name("user_token")
                ->where('user_id', $userId)
                ->where('device_type', $deviceType)
                ->update([
                    'token'       => $token,
                    'expire_time' => $expireTime,
                    'create_time' => $currentTime
                ]);
        } else {
            $token = $findUserToken['token'];
        }

    }

    return $token;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @param bool $ucfirst 首字母是否大写（驼峰规则）
 * @return string
 */
function cmf_parse_name($name, $type = 0, $ucfirst = true)
{
    return Loader::parseName($name, $type, $ucfirst);
}

/**
 * 判断字符串是否为已经序列化过
 * @param $str
 * @return bool
 */
function cmf_is_serialized($str)
{
    return ($str == serialize(false) || @unserialize($str) !== false);
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function cmf_is_ssl()
{
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }
    return false;
}

/**
 * 获取CMF系统的设置，此类设置用于全局
 * @param string $key 设置key，为空时返回所有配置信息
 * @return mixed
 */
function cmf_get_cmf_settings($key = "")
{
    $cmfSettings = cache("cmf_settings");
    if (empty($cmfSettings)) {
        $objOptions = new \app\admin\model\OptionModel();
        $objResult  = $objOptions->where("option_name", 'cmf_settings')->find();
        $arrOption  = $objResult ? $objResult->toArray() : [];
        if ($arrOption) {
            $cmfSettings = json_decode($arrOption['option_value'], true);
        } else {
            $cmfSettings = [];
        }
        cache("cmf_settings", $cmfSettings);
    }

    if (!empty($key)) {
        if (isset($cmfSettings[$key])) {
            return $cmfSettings[$key];
        } else {
            return false;
        }
    }
    return $cmfSettings;
}

/**
 * 判读是否sae环境
 * @return bool
 */
function cmf_is_sae()
{
    if (function_exists('saeAutoLoader')) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return string
 */
function get_client_ip($type = 0, $adv = false)
{
    return request()->ip($type, $adv);
}

/**
 * 生成base64的url,用于数据库存放 url
 * @param $url 路由地址，如 控制器/方法名，应用/控制器/方法名
 * @param $params url参数
 * @return string
 */
function cmf_url_encode($url, $params)
{
    // 解析参数
    if (is_string($params)) {
        // aaa=1&bbb=2 转换成数组
        parse_str($params, $params);
    }

    return base64_encode(json_encode(['action' => $url, 'param' => $params]));
}

/**
 * CMF Url生成
 * @param string $url 路由地址
 * @param string|array $vars 变量
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 * @return string
 */
function cmf_url($url = '', $vars = '', $suffix = true, $domain = false)
{
    static $routes;

    if (empty($routes)) {
        $routeModel = new \app\admin\model\RouteModel();
        $routes     = $routeModel->getRoutes();
    }

    if (false === strpos($url, '://') && 0 !== strpos($url, '/')) {
        $info = parse_url($url);
        $url  = !empty($info['path']) ? $info['path'] : '';
        if (isset($info['fragment'])) {
            // 解析锚点
            $anchor = $info['fragment'];
            if (false !== strpos($anchor, '?')) {
                // 解析参数
                list($anchor, $info['query']) = explode('?', $anchor, 2);
            }
            if (false !== strpos($anchor, '@')) {
                // 解析域名
                list($anchor, $domain) = explode('@', $anchor, 2);
            }
        } elseif (strpos($url, '@') && false === strpos($url, '\\')) {
            // 解析域名
            list($url, $domain) = explode('@', $url, 2);
        }
    }

    // 解析参数
    if (is_string($vars)) {
        // aaa=1&bbb=2 转换成数组
        parse_str($vars, $vars);
    }

    if (isset($info['query'])) {
        // 解析地址里面参数 合并到vars
        parse_str($info['query'], $params);
        $vars = array_merge($params, $vars);
    }

    if (!empty($vars) && !empty($routes[$url])) {

        foreach ($routes[$url] as $actionRoute) {
            $sameVars = array_intersect_assoc($vars, $actionRoute['vars']);

            if (count($sameVars) == count($actionRoute['vars'])) {
                ksort($sameVars);
                $url  = $url . '?' . http_build_query($sameVars);
                $vars = array_diff_assoc($vars, $sameVars);
                break;
            }
        }
    }

    if (!empty($anchor)) {
        $url = $url . '#' . $anchor;
    }

//    if (!empty($domain)) {
//        $url = $url . '@' . $domain;
//    }

    return Url::build($url, $vars, $suffix, $domain);
}

/**
 * 判断 cmf 核心是否安装
 * @return bool
 */
function cmf_is_installed()
{
    static $cmfIsInstalled;
    if (empty($cmfIsInstalled)) {
        $cmfIsInstalled = file_exists(CMF_ROOT . 'data/install.lock');
    }
    return $cmfIsInstalled;
}

/**
 * 替换编辑器内容中的文件地址
 * @param string $content 编辑器内容
 * @param boolean $isForDbSave true:表示把绝对地址换成相对地址,用于数据库保存,false:表示把相对地址换成绝对地址用于界面显示
 * @return string
 */
function cmf_replace_content_file_url($content, $isForDbSave = false)
{
    //import('phpQuery.phpQuery', EXTEND_PATH);
    \phpQuery::newDocumentHTML($content);
    $pq = pq(null);

    $storage       = Storage::instance();
    $localStorage  = new cmf\lib\storage\Local([]);
    $storageDomain = $storage->getDomain();
    $domain        = request()->host();

    $images = $pq->find("img");
    if ($images->length) {
        foreach ($images as $img) {
            $img    = pq($img);
            $imgSrc = $img->attr("src");

            if ($isForDbSave) {
                if (preg_match("/^\/upload\//", $imgSrc)) {
                    $img->attr("src", preg_replace("/^\/upload\//", '', $imgSrc));
                } elseif (preg_match("/^http(s)?:\/\/$domain\/upload\//", $imgSrc)) {
                    $img->attr("src", $localStorage->getFilePath($imgSrc));
                } elseif (preg_match("/^http(s)?:\/\/$storageDomain\//", $imgSrc)) {
                    $img->attr("src", $storage->getFilePath($imgSrc));
                }

            } else {
                $img->attr("src", cmf_get_image_url($imgSrc));
            }

        }
    }

    $links = $pq->find("a");
    if ($links->length) {
        foreach ($links as $link) {
            $link = pq($link);
            $href = $link->attr("href");

            if ($isForDbSave) {
                if (preg_match("/^\/upload\//", $href)) {
                    $link->attr("href", preg_replace("/^\/upload\//", '', $href));
                } elseif (preg_match("/^http(s)?:\/\/$domain\/upload\//", $href)) {
                    $link->attr("href", $localStorage->getFilePath($href));
                } elseif (preg_match("/^http(s)?:\/\/$storageDomain\//", $href)) {
                    $link->attr("href", $storage->getFilePath($href));
                }

            } else {
                if (!(preg_match("/^\//", $href) || preg_match("/^http/", $href))) {
                    $link->attr("href", cmf_get_file_download_url($href));
                }

            }

        }
    }

    $content = $pq->html();

    \phpQuery::$documents = null;


    return $content;

}

/**
 * 获取后台风格名称
 * @return string
 */
function cmf_get_admin_style()
{
    $adminSettings = cmf_get_option('admin_settings');
    return empty($adminSettings['admin_style']) ? 'flatadmin' : $adminSettings['admin_style'];
}

/**
 * curl get 请求
 * @param $url
 * @return mixed
 */
function cmf_curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    if ($SSL) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名
    }
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

/**
 * 用户操作记录
 * @param string $action 用户操作
 */
function cmf_user_action($action)
{
    $userId = cmf_get_current_user_id();

    if (empty($userId)) {
        return;
    }

    $findUserAction = Db::name('user_action')->where('action', $action)->find();

    if (empty($findUserAction)) {
        return;
    }

    $changeScore = false;

    if ($findUserAction['cycle_type'] == 0) {
        $changeScore = true;
    } elseif ($findUserAction['reward_number'] > 0) {
        $findUserScoreLog = Db::name('user_score_log')->order('create_time DESC')->find();
        if (!empty($findUserScoreLog)) {
            $cycleType = intval($findUserAction['cycle_type']);
            $cycleTime = intval($findUserAction['cycle_time']);
            switch ($cycleType) {//1:按天;2:按小时;3:永久
                case 1:
                    $firstDayStartTime = strtotime(date('Y-m-d', $findUserScoreLog['create_time']));
                    $endDayEndTime     = strtotime(date('Y-m-d', strtotime("+{$cycleTime} day", $firstDayStartTime)));
//                    $todayStartTime        = strtotime(date('Y-m-d'));
//                    $todayEndTime          = strtotime(date('Y-m-d', strtotime('+1 day')));
                    $findUserScoreLogCount = Db::name('user_score_log')->where([
                        'user_id'     => $userId,
                        'create_time' => [['gt', $firstDayStartTime], ['lt', $endDayEndTime]]
                    ])->count();
                    if ($findUserScoreLogCount < $findUserAction['reward_number']) {
                        $changeScore = true;
                    }
                    break;
                case 2:
                    if (($findUserScoreLog['create_time'] + $cycleTime * 3600) < time()) {
                        $changeScore = true;
                    }
                    break;
                case 3:

                    break;
            }
        } else {
            $changeScore = true;
        }
    }

    if ($changeScore) {
        Db::name('user_score_log')->insert([
            'user_id'     => $userId,
            'create_time' => time(),
            'action'      => $action,
            'score'       => $findUserAction['score'],
            'coin'        => $findUserAction['coin'],
        ]);

        $data = [];
        if ($findUserAction['score'] > 0) {
            $data['score'] = Db::raw('score+' . $findUserAction['score']);
        }

        if ($findUserAction['score'] < 0) {
            $data['score'] = Db::raw('score-' . abs($findUserAction['score']));
        }

        if ($findUserAction['coin'] > 0) {
            $data['coin'] = Db::raw('coin+' . $findUserAction['coin']);
        }

        if ($findUserAction['coin'] < 0) {
            $data['coin'] = Db::raw('coin-' . abs($findUserAction['coin']));
        }

        Db::name('user')->where('id', $userId)->update($data);

    }


}

function cmf_api_request($url, $params = [])
{
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:1314/api/' . $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);

    $token = session('token');

    curl_setopt($curl, CURLOPT_HTTPHEADER, ["XX-Token: $token"]);
    //设置post数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据

    return json_decode($data, true);
}

/**
 * 判断是否允许开放注册
 */
function cmf_is_open_registration()
{

    $cmfSettings = cmf_get_option('cmf_settings');

    return empty($cmfSettings['open_registration']) ? false : true;
}

/**
 * XML编码
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id 数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function cmf_xml_encode($data, $root = 'think', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
{
    if (is_array($attr)) {
        $_attr = [];
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr = trim($attr);
    $attr = empty($attr) ? '' : " {$attr}";
    $xml  = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml  .= "<{$root}{$attr}>";
    $xml  .= cmf_data_to_xml($data, $item, $id);
    $xml  .= "</{$root}>";
    return $xml;
}

/**
 * 数据XML编码
 * @param mixed $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id 数字索引key转换为的属性名
 * @return string
 */
function cmf_data_to_xml($data, $item = 'item', $id = 'id')
{
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if (is_numeric($key)) {
            $id && $attr = " {$id}=\"{$key}\"";
            $key = $item;
        }
        $xml .= "<{$key}{$attr}>";
        $xml .= (is_array($val) || is_object($val)) ? cmf_data_to_xml($val, $item, $id) : $val;
        $xml .= "</{$key}>";
    }
    return $xml;
}

/**
 * 发送短信
 */
// function sendSms($params) {
//     header('Content-Type: text/plain; charset=utf-8');
//     //$params = array ();

//     // *** 需用户填写部分 ***

//     // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
//     $accessKeyId = "LTAIYIXkKMOTbh4V";
//     $accessKeySecret = "QJVRkWGrfPNC1ZPMN8YiV72Lr619s5";

//     // fixme 必填: 短信接收号码
//     $params["PhoneNumbers"] = $params['mobile'];

//     // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
//     $params["SignName"] = "蒸妙熏蒸仪";



//     // fixme 可选: 设置发送短信流水号
//     $params['OutId'] = time();

//     // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//     $params['SmsUpExtendCode'] = "1234567";


//     // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
//     if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
//         $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
//     }

//     // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
//     $helper = new SignatureHelper();

//     // 此处可能会抛出异常，注意catch
//     $content = $helper->request(
//         $accessKeyId,
//         $accessKeySecret,
//         "dysmsapi.aliyuncs.com",
//         array_merge($params, array(
//             "RegionId" => "cn-hangzhou",
//             "Action" => "SendSms",
//             "Version" => "2017-05-25",
//         ))
//     );
//     return json_decode(json_encode($content),true);

// }


/**
 * 发送短信
 */
 function sendSms($params) {

header("Content-Type:text/html;charset=utf-8");
$apikey = "fbaacac5b8ba2954fe08c513216bed82"; //修改为您的apikey(https://www.yunpian.com)登录官网后获取
$mobile = isset($params['mobile'])?$params['mobile']:''; //请用自己的手机号代替
$code   = $params['code'];
// $text="【猩猩链】您的验证码是".$code."。如非本人操作，请忽略本短信。";
$text   = $params['text'];
$tpl_id = $params['tpl_id'];
$ch = curl_init();
/* 设置验证方式 */
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8',
    'Content-Type:application/x-www-form-urlencoded', 'charset=utf-8'));
/* 设置返回结果为流 */
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

/* 设置超时时间*/
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

/* 设置通信方式 */
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 发送短信
$data=array('text'=>$text,'apikey'=>$apikey,'mobile'=>$mobile,'tpl_id'=>$tpl_id);
$json_data = send($ch,$data);
$array = json_decode($json_data,true);
return $array;
   
}
//发送短信
 function send($ch,$data){
    curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $result = curl_exec($ch);
    $error = curl_error($ch);
    checkErr($result,$error);
    return $result;
}
//检查错误
 function checkErr($result,$error){
    if($result === false){
        echo 'Curl error: ' . $error;
    }else{
        //echo '操作完成没有任何错误';
    }
}





/*
* 发送消息方法
* $to_user  arr [1,2,3,4]
* $content  string 发送的内容 
*/
function send_message($to_user=array(), $title='', $content='', $send_user_id=0, $more=null, $type=1){
    if(empty($to_user)){
        return false;
    }
    $MessageModel = Db::name("Message");
    $data['send_user_id']  = $send_user_id;
    $data['create_time']  = time();
    $data['title']  = $title;
    $data['content'] = $content;
    $data['type'] = $type;
    $data['more'] = $more;
    $result = $MessageModel->insertGetId($data);
    if($result){
        $data = array();
        foreach ($to_user as $key => $val) {
            $data['user_id'] = $val;
            $data['message_id'] = $result;
            Db::name('message_user_rel')->insertGetId($data);
        }

        $sendData = [];
        $sendData['message_type'] = 'has_new_message';
        $sendData['msg_id'] = $result;
        $sendData['time'] = time();
        pushTipMessage($sendData,$to_user);

        return true;
    }else{
        return false;
    }
}

function generateRequestUrl($param){
    $arr  = array('product'=>'160fa2b3062403e9160fa2b306248601');
    $arr = array_merge($arr,$param);
    $params  = '';
    foreach ($arr as $key => $val) {
        $params .= '/'.$key.'/'.$val;
    }
    $url = 'http://api2.xlink.cn/v2'.rtrim($params,'/');
    return $url;
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param string $param
 */
function zmxz_request_post($param) {
    if (empty($param)) {
        return false;
    }
    if(is_array($param['url'])){
        $url = generateRequestUrl($param['url']);
    }else{
        $url = $param['url'];
    }
    if(!isset($param['data'])){
        $param['data'] = [];
    }
    $config = getConfig();
    $access_token = $config['access_token'];
    $header = array();
    $header[] = 'Accept:application/json';
    $header[] = 'Content-Type:application/json;charset=utf-8';
    $header[] = 'Access-Token:'.$access_token;
    $header[] = 'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36';
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param['data']));

    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    //file_put_contents('device/request_post_log_'.date('Ymd').'.txt','URL：'.$url.'。RESULT：'.json_encode($data).PHP_EOL,FILE_APPEND);
    return $data;
}

/**
 * 模拟get进行url请求
 * @param string $url
 */
function zmxz_request_get($param){
    if (empty($param)) {
        return false;
    }
    if(is_array($param['url'])){
        $url = generateRequestUrl($param['url']);
    }else{
        $url = $param['url'];
    }
    $config = getConfig();
    $access_token = $config['access_token'];
    //初始化
    $ch = curl_init();
    $header = array();
    $header[] = 'Accept:application/json';
    $header[] = 'Content-Type:application/json;charset=utf-8';
    $header[] = 'Access-Token:'.$access_token;
    curl_setopt($ch, CURLOPT_URL,$url);
    // 执行后不直接打印出来
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // 不从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //执行并获取HTML文档内容
    $data = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    file_put_contents('device/request_get_log_'.date('Ymd').'.txt','URL：'.$url.'。RESULT：'.json_encode($data).PHP_EOL,FILE_APPEND);
    return $data;
}

/*获取指定设备在平台的信息*/
function getDeviceInfoForCloudPlatform($device_id){
    $arr = array('device'=>$device_id);
    $res = zmxz_request_get(array('url'=>$arr));
    if(!is_array($res)){
        $res = json_decode($res,true);
    }
    return $res;
}

/*获取指定设备在线状态*/
function device_online_state($device_id){
    $config = getConfig();
    $device_use_time = $config['device_use_time'];

    $online = [];
    $find_online = Db::name('device')->where(['device_id'=>$device_id])->find();
    $online['online'] = $find_online['online'];
    $online['time'] = null;
    if($find_online['online']){

        $where = '(((UNIX_TIMESTAMP() - start_time) < '.($device_use_time[0]*60).') or (end_time=0 and start_time>0)) and device_id = "'.$device_id.'"';
        $find = Db::name('device_use_log')->where($where)->order('id desc')->find();//查找40分钟之内或者未结束的数据
        if(!empty($find)){
            if($find['end_time']==0 || empty($find['end_time'])){

                $res_minute = (time()-$find['start_time'])/60;
                if($res_minute>=41 && $find['end_time']==0){
                    $online['time'] = 0;
                }elseif($res_minute<=40){
                    $online['time'] = 40-round($res_minute);
                }elseif($online['time']>40 and $online['time']<70){
                    $online['time'] = $res_minute;
                }else{
                    $online['time'] = null;
                }
                if($find['end_time']>0){
                    $online['time'] = null;
                }

            }else{
                //如果有结束时间直接处于不使用中
                $online['time'] = null;
            }
        }else{
            $online['time'] = null;
        }

    }
    return $online;
}

function get_device_state($device_id)
{
    $url = 'http://api2.xlink.cn/v2/device/command/'.$device_id.'/datapoint/status';
    $param = [];
    $param['url'] = $url;
    $param['data'] = ['filter'=> [
        "device_id",
        "online",
        "online_count",
        "last_login",
        "last_logout"
    ]
    ];
    $data = zmxz_request_post($param);
    return json_decode($data,true);
}

function remove_date_tz($date){
    $date = str_replace('T',' ',$date);
    $date = substr($date, 0,19);
    return $date;
}

/**
 * 模拟get进行url请求
 * @param string $url
 */
function curl_request_get($url){

    //初始化
    $ch = curl_init();
    $header = array();
    $header[] = 'Accept:application/json';
    $header[] = 'Content-Type:application/json;charset=utf-8';
    curl_setopt($ch, CURLOPT_URL,$url);
    // 执行后不直接打印出来
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // 不从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //执行并获取HTML文档内容
    $data = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    return $data;
}
/**
 * 模拟post进行url请求
 * @param string $url
 * @param string $param
 */
function curl_request_post($param) {
    if (empty($param)) {
        return false;
    }
    $url = $param['url'];
    if(!isset($param['data'])){
        $param['data'] = [];
    }
    $coin_type = '';
    if(isset($param['coin_type'])){
        $coin_type = $param['coin_type'];
    }
    $header = array();
    $header[] = 'Accept:application/json';
    $header[] = 'Content-Type:application/json;charset=utf-8';
    $header[] = 'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36';
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('datas'=>$param['data'],'access_token'=>$param['access_token'],'coin_type'=>$coin_type)));
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    return $data;
}