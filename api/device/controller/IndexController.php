<?php

namespace api\device\controller;

use think\Db;
use think\Validate;
use cmf\controller\RestUserBaseController;
use api\user\model\ScoreModel;

class IndexController extends RestUserBaseController
{
    /*获取设备在线信息，以便获取精确的使用时间*/
    public function get_device_online_detail(){
      $config = getConfig();
      $device_use_time = $config['device_use_time'];
      $param = $this->request->param(); 
      $userid = $this->getUserId();
      $device_id = $param['device_id'];
      $device = Db::name('device_user_rel')->alias('a')
                ->join('cmf_device b','a.device_id=b.device_id')
                ->field('a.*')
                ->where(['a.device_id'=>$device_id,'user_id'=>$userid,'a.status'=>1,'b.is_bind'=>1,'is_del'=>0])
                ->order('a.id desc')
                ->find();
      $online = device_online_state($device_id);
      $score = get_parameter_settings('package_use_reward_score');//使用药包送积分
      $msg = '';
      if($online['online']==2){
        $msg = '设备处于离线状态！';
      }else{
        if($online['time']==null){
          $msg = '设备并未处于使用状态！'.$online['time'];
        }else{
          if($online['time']>0 and $online['time']<41){
            $msg = '数据获取成功！';
          }elseif($online['time']>40 and $online['time']<70){
            $list[$key]['online_state'] = 2;
            $list[$key]['class_name'] = ' s-b';
            $list[$key]['online_state_name'] = '确认完成中';
          }
        }
      }
      $this->success($msg,['device'=>$device,'online'=>$online,'score'=>$score]); 
    }
    // 设备列表
    //use app\design\model\DesignSpaceModel;
    public function device_list()
    {
      $userid = $this->getUserId();
      $config = getConfig();
      $device_use_time = $config['device_use_time'];
      $param = $this->request->param(); 
      $deviceModel = Db::name('device');
      $list = $deviceModel
                ->alias('a')
                ->field('a.*,b.status')
                ->join('cmf_device_user_rel b','a.device_id=b.device_id')
                ->where(['a.is_del'=>0,'a.is_bind'=>1,'b.user_id'=>$userid,'b.status'=>[array('gt',0),array('lt',3),'and']])
                ->order('b.id desc')
                ->select()->toArray();
      foreach ($list as $key => $val) {
        $online = device_online_state($val['device_id']);
        $list[$key]['time'] = $online['time'];

        if($online['online']==0){
          $list[$key]['online_state'] = 0;
          $list[$key]['online_state_name'] = '离线';
          $list[$key]['class_name'] = ' s-c';

        }else{

          $list[$key]['class_name'] = ' s-a';
          $list[$key]['online_state_name'] = '在线';
          $list[$key]['online_state'] = 1;
          if($online['time']>0 and $online['time']<41){
            $list[$key]['online_state'] = 2;
            $list[$key]['class_name'] = ' s-b';
            $list[$key]['online_state_name'] = '使用中';
          }elseif($online['time']>40 and $online['time']<70){
            $list[$key]['online_state'] = 2;
            $list[$key]['class_name'] = ' s-b';
            $list[$key]['online_state_name'] = '确认完成中';
          }

        }
      }          
      $this->success("获取数据成功！",['list'=>$list]); 
    }
    // 设备详情
    public function device_detail()
    {
      $param = $this->request->param(); 
      if(empty($param['device_id'])){
        $this->error('接收参数有误！');
      }
      $userid = $this->getUserId();
      //组装条件          
      $scoreModel = new ScoreModel();
      $map = [];
      $map['a.device_id']=$param['device_id'];
      $map['a.user_id']=$userid;
      $map['b.type']=2;
      //图表积分
      $echart_score_data = $scoreModel->getDeviceScoreChart($map);
      
      $echart_data_key_x = [];
      $echart_data_value_y = [];
      foreach ($echart_score_data['data'] as $key => $value) {
        $echart_data_key_x[] = $key;
        $echart_data_value_y[] = $value;
      }
      $echart = [];
      $echart['echart_data_type']  = $echart_score_data['echart_data_type'];
      $echart['x'] = $echart_data_key_x;
      $echart['y'] = $echart_data_value_y;
      //概览积分
      $total_score = $scoreModel->getDeviceUseScoreTotal($map);//总的
      $yesterday_start = strtotime(date("Y-m-d 00:00:00",strtotime("-1 day")));
      $yesterday_end = strtotime(date("Y-m-d 23:59:59",strtotime("-1 day")));
      $map['a.create_time']=array(array('egt',$yesterday_start),array('elt',$yesterday_end),'and');
      $yesterday_score = $scoreModel->getDeviceUseScoreTotal($map);//昨日的
      $this->success("获取数据成功！",['yesterday_score'=>$yesterday_score,'total_score'=>$total_score,'echart'=>$echart]); 
    }
    //使用记录
    public function device_use_log()
    {
      $userid = $this->getUserId();
      $param = $this->request->param();
      $map = []; 
      if(!empty($param['device_id'])){
        $map['a.device_id']=$param['device_id'];
      }
      $map['a.user_id']=$userid;
      $map['a.score_log_id']=array('gt',0);
      $scoreModel = new ScoreModel();
      $list = $scoreModel->getDeviceUseScoreList($map,'a.id desc');//总的
      $this->success("获取数据成功！",['list'=>$list]); 
    }
    // 绑定device
    public function bind_device()
    {
      $userid = $this->getUserId();
      $param = $this->request->param();
      $code_sn = '0'.preg_replace('# #','',$param['code']);
      $deviceModel = Db::name('device');
      $where['device_id|device_sn'] = array('eq',$code_sn);
      $device = $deviceModel->where($where)->find(); 
      if(empty($device)){
        $this->error('编号有误，请重新录入');
      }  
      if($device['is_bind']==1){
        $this->error('绑定失败，该设备已被绑定！');
      }
      if($device['is_del']==1){
        $this->error('绑定失败，该设备已被删除！');
      }
      if(empty($device['device_id'])){
        $this->error('该设备暂未跟云平台同步，请等些时间再试');
      }
      
      /*是否是第一次绑定*/
      $userDevice = $deviceModel
                ->alias('a')
                ->field('a.*')
                ->join('cmf_device_user_rel b','a.device_id=b.device_id')
                ->where(['b.user_id'=>$userid])
                ->find();
      $frist_bind = true;
      if(!empty($userDevice)){
        $frist_bind = false;
      }
      //是否有绑定的代理关系
      $user_agent =  Db::name('agent_apply')
                ->alias('a')
                ->join('__AGENT__ b','a.agent_id=b.id')//当前级别
                ->join('__AGENT__ c','b.id=c.parent_id','left')//下一级
                ->where(['a.user_id'=>$userid,'a.status'=>1])
                ->field('a.user_id,b.*,c.id as has_sub')
                ->order('a.id desc')
                ->find();
      if(!empty($user_agent)){
         $frist_bind = false; 
      } 

      //查看当前本人是否有已经提交过有效的（待审核、审核通过）推荐记录
      $curr_urser_referrer = Db::name('user_referrer')->where(['user_id'=>$userid,'status'=>array('egt',1)])->find();
      if(!empty($curr_urser_referrer)){
        if($curr_urser_referrer['type']==1){//如果不是代理，因为代理的话一般都是先申请代理产生了推荐关系
          $frist_bind = false; 
        }
      }

      //如果不是初次绑定
      if($frist_bind==false){
        $deviceModel->where($where)->update(['is_bind'=>1]); //更新该机器绑定状态
        $device_user_rel = Db::name('device_user_rel');
        $device_user = $device_user_rel->where(['device_id'=>$device['device_id'],'user_id'=>$userid])->find();//是否绑定过该机器 
        $time = time();
        if(!empty($device_user)){
          $device_user_rel->where(['device_id'=>$device['device_id'],'user_id'=>$userid])->order('id desc')->update(['status'=>2,'create_time'=>$time]);
        }else{
          $device_user_rel->where(['device_id'=>$device['device_id']])->update(['status'=>0]);//取消所有人的有效绑定
          $bindResultID = $device_user_rel->insertGetId([
                              'user_id'=>$userid,
                              'device_id'=>$device['device_id'],
                              'status'=>2,
                              'create_time'=>$time
                            ]);
        } 
        //插入设备操作记录
        Db::name('device_action_log')->insertGetId([
          'device_id'=>$device['device_id'],
          'action_user_id'=>$userid,
          'type'=>1,
          'create_time'=>$time
        ]);
      }
      $this->success("绑定成功！",['device'=>$device,'id'=>0,'status'=>2,'frist_bind'=>$frist_bind]);
    }

    public function untie_device_code(){
      $userid = $this->getUserId();
      $user = get_user($userid);
      if(empty($user)){
        $this->error("你的账户异常，验证码获取失败!");
      }
      $code = cmf_get_verification_code($user['mobile']);
      $params['mobile'] = $user['mobile'];
      $params['TemplateParam'] = ['code' => $code];
      $params['TemplateCode'] = 'SMS_134319759';
      $result = sendSms($params);
      if(isset($result['Code']) && $result['Code']=='OK'){
          cmf_verification_code_log($user['mobile'],$code);
          $this->success("验证码已发至你的手机，请留意你的短信!");  
      } else{
          $this->error("验证码发送失败，请待会再试!");
      }
    }
    public function change_device_name()
    {
      $userid = $this->getUserId();
      $param = $this->request->param();
      $name = $param['name'];
      if(empty($name)){
        $this->error('请输入备注名称');
      }
      $deviceModel = Db::name('device');
      $device_user_rel = Db::name('device_user_rel');

      $result = $device_user_rel->where(['device_id'=>$param['device_id'],'user_id'=>$userid,'status'=>1])->find();
      if(empty($result)){
        $this->error('你暂时不能操作该设备');
      }

      $update = $deviceModel->where(['device_id'=>$param['device_id']])->update(['name'=>$name]);
      if($update){
        $this->success("操作成功");
      }else{
        $this->error('操作失败');
      }


    }
    // 解绑device
    public function untie_device()
    {
      $userid = $this->getUserId();
      $param = $this->request->param();

      $user = get_user($userid);

      $errMsg = cmf_check_verification_code($user['mobile'], $param['code']);
      if (!empty($errMsg)) {
          $this->error($errMsg);
      }

      $deviceModel = Db::name('device');
      $device_user_rel = Db::name('device_user_rel');

      $where['device_id'] = $param['device_id'];
      $device = $deviceModel->where($where)->find();   

      if(empty($device)){
        $this->error('设备有误，请稍后再尝试');
      }
      $res = true;
      $time = time();
      $result = $device_user_rel->where(['device_id'=>$param['device_id'],'user_id'=>$userid])->update(['status'=>3]);
      if($result){
        $rel_result = $deviceModel->where(['device_id'=>$param['device_id']])->update(['is_bind'=>0]);
        if(!$rel_result){ $res = false; }
      }else{
        $res = false;
      }
      if($res){
          //插入设备操作记录
          Db::name('device_action_log')->insertGetId([
            'device_id'=>$param['device_id'],
            'action_user_id'=>$userid,
            'type'=>2,
            'create_time'=>$time
          ]);

        $this->success("设备解绑成功！");
      }else{
        $this->error('设备解绑失败！');
      }
    }
}
