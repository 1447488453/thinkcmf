<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < 479468212@qq.com>
// +----------------------------------------------------------------------

namespace app\device\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\device\model\DeviceModel;
use api\device\controller\ApiController;
class AdminIndexController extends AdminBaseController{
    /**
     * 设备列表
     */
    public function index(){
        $param = $this->request->param();
        $where   = [];
        $request = input('request.');
        $where['is_del'] = ['eq',0];
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['name|device_id|device_sn']    = ['like', "%$keyword%"];
        }
        $deviceQuery = Db::name('device');
        $list = $deviceQuery->whereOr($keywordComplex)->where($where)->order("id DESC")->paginate(20);
        // 获取分页显示
        $page = $list->render();
        $list = $list->items();
        foreach ($list as $key => $val) {
            $find = Db::name('device_action_log')
                ->where(['device_id'=>$val['device_id'],'type'=>2])
                ->find();
            if(!empty($find)){
                $list[$key]['is_untie'] = true;
            }
        }
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        // 渲染模板输出
        return $this->fetch();
    }

    public function receive_data_log(){
        $param = $this->request->param();
        $list = [];
        $type_arr = ['STATE'=>'在线状态','SYNC'=>'操作交互'];
        if(!empty($param['device_id'])){
            $type = isset($param['type'])?$param['type']:'';
            $url = 'http://xunzheng.zmiaosh.com/api/device/api/testScore/device_id/'.$param['device_id'];
            //$res = curl_request_get($url);

            $receive_data = Db::name('device_receive_log')->where(['device_id'=>$param['device_id']])->order('id desc')->select()->toArray();
            if(!empty($receive_data)){
                foreach ($receive_data as $key => $val) {
                    $time = date('Y-m-d H:i:s',$val['time']);
                    $val = json_decode($val['param'],true);
                    if(empty($val)){
                      continue;
                    }
                    $val['type_name']   = $type_arr[$val['type']];
                    $val['create_time'] = $time;
                    if(isset($val['datapoint']) and strtolower($val['type'])=='sync'){
                        $datapoint = $val['datapoint'][0];
                        switch ($datapoint['index']) {
                            case 6:
                                $use_name = '本次足熏使用时间：'.$datapoint['value'];
                                break;
                            case 7:
                                $use_name = '本次座熏使用时间：'.$datapoint['value'];
                                break;
                            case 8:
                                $use_name = '足熏累计使用时间：'.$datapoint['value'];
                                break;
                            case 9:
                                $use_name = '座熏累计使用时间：'.$datapoint['value'];
                                break; 
                            case 0:
                                if($datapoint['value']=='true'){
                                    $use_name = '设备按了开机键';
                                }else{
                                    $use_name = '设备按了关机键';
                                }
                                break;   
                        }
                    }elseif(strtolower($val['type'])=='state'){
                        if($val['state']=='online'){
                            $use_name = '设备在线';
                        }else{
                            $use_name = '设备离线';
                        }
                    }
                    $val['use_name'] = $use_name;
                    if(!empty($type)){
                        if($type==strtolower($val['type'])){
                            $list[] = $val;
                        } 
                    }else{
                        $list[] = $val;
                    }
                }
            }
        }
        $this->assign('type', isset($param['type']) ? $param['type'] : -1);
        $this->assign('device_id', isset($param['device_id']) ? $param['device_id'] : -1);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function action_log(){
        $param = $this->request->param();
        $where   = [];
        $where['a.device_id'] = ['eq',$param['device_id']];
        $list = Db::name('device_action_log')
                ->alias('a')
                ->field('a.*,b.device_sn,c.user_login,c.mobile')
                ->join('cmf_device b','a.device_id=b.device_id')
                ->join('cmf_user c','a.action_user_id=c.id')
                ->where($where)
                ->order("id DESC")
                ->paginate(20);
        $page = $list->render();
        $this->assign('type', getDictionary('device_action_type'));
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function use_log(){
        $param = $this->request->param();
        $where   = [];
        $where['a.device_sn'] = ['eq',$param['device_sn']];
        $where['b.reason'] = '步行';
        // $where['b.reason'] =  ['in',['步行','睡眠']];
        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.add_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        }

         //步数使用记录
        $deviceUseModel = Db::name('user_run');
        $count = $deviceUseModel->alias('a')->field('b.num')->join('cmf_score_log b','a.user_id=b.user_id')->where($where)->group('a.user_id')->select()->toArray();
        $count_score = 0;
        foreach ($count as $key => $val) {
            $count_score += $val['num'];
        }
        $uselist = $deviceUseModel->alias('a')->field('a.*,b.num,c.user_login')->join('cmf_score_log b','a.user_id=b.user_id','left')->join('cmf_user c','b.user_id=c.id','left')->where($where)->order('a.id desc')->paginate(10)->each(function($item, $key){
            $item['add_time'] = date('Y.m.d H:i:s',$item['add_time']);
              return $item;
            });;
        $page = $uselist->render();
        $list = $uselist->items();
        $this->assign('count_score', $count_score);
        $this->assign('count', $count);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('device_sn', $param['device_sn']);
        return $this->fetch();
    }

    public function replace_device(){
        die;//需求有变暂时屏蔽
        $param = $this->request->param();
        if($this->request->isPost()){

            $device = Db::name('device')->where(['device_id'=>$param['device_id']])->find();
            
            $result = Db::name('device')->where(['device_id'=>$param['device_id']])->update(['device_sn'=>$param['device_sn']]);
            if($result){
                //插入设备操作记录
                Db::name('device_action_log')->insertGetId([
                    'device_id'=>$param['device_id'],
                    'action_user_id'=>cmf_get_current_admin_id(),
                    'type'=>4,
                    'create_time'=>time()
                ]);
                $this->success('添修改成功!');
            }else{
                $this->error('修改失败');
            }
        }else{
           $this->assign('device_id', $param['device_id']);
           return $this->fetch(); 
        }
    }

    /**
     * 设备添加
     */
    public function add(){
        if($this->request->isPost()){
            $data   = $this->request->param();
            $deviceModel = new DeviceModel();
            $data['create_time'] =time();
            $data['device_sn'] = $this->myTrim($data['device_sn']);
            $find = $deviceModel->where(['device_sn'=>$data['device_sn']])->whereOr(['device_sn'=>$data['device_sn']])->find();
            if(!empty($find)){
                $this->error('已存在该设备！'); 
            }
            if(strlen($data['device_id'])<4){
              $data['device_id'] = null;  
            }
            $deviceModel->add($data);
            $this->success('添加成功!', url('AdminIndex/edit', ['id' => $deviceModel->id]));
        }else{
           return $this->fetch(); 
        }
    }
    /**
     * 设备编辑
     */
    public function edit(){

        if($this->request->isPost()){
            $data   = $this->request->param();
            $deviceModel = new DeviceModel();
            $data['create_time'] =time();
            $data['device_sn'] = $this->myTrim($data['device_sn']);
           
            if(strlen($data['device_id'])<4){
              $data['device_id'] = null;  
            }
            $deviceModel->edit($data);
            $this->success('保存成功!');
        }else{
           $id = $this->request->param('id', 0, 'intval');
           $deviceModel = new DeviceModel();
           $device      = Db::name('device')->where('id', $id)->find();
           $user = [];
           if($device['is_bind']){
                $user    =  Db::name('device_user_rel')
                            ->alias('a')
                            ->field('b.user_login')->join('cmf_user b','a.user_id=b.id')
                            ->where(['a.device_id'=>$device['device_id'],'a.status'=>1])
                            ->order('a.id desc')->find();
           }
            $this->assign('user', $user);
           $this->assign('device', $device);
           return $this->fetch(); 
        }
    }
    /**
     * 设备待审核列表
     */
    public function audit(){
        $where   = [];
        $param   = $this->request->param();
        $deviceModel = Db::name('device');
        
        $count1 = $deviceModel
                ->alias('a')
                ->join('cmf_device_user_rel b','a.device_id=b.device_id')
                ->join('cmf_user c','b.user_id=c.id')
                ->where(['b.status'=>1,'a.is_bind'=>1,'a.is_del'=>0])->count();//已通过

        $count2 = $deviceModel
                ->alias('a')
                ->join('cmf_device_user_rel b','a.device_id=b.device_id')
                ->join('cmf_user c','b.user_id=c.id')
                ->where(['b.status'=>2,'a.is_bind'=>0,'a.is_del'=>0])->count();//待审核
        
        $keyword = empty($param['keyword']) ? '' : $param['keyword'];
        if (!empty($keyword)) {
            $where['a.name|a.device_sn|c.user_login'] = ['like', "%$keyword%"];
        }

        $status = !isset($param['status']) ? '' : $param['status'];
        if ($status!='') {
            $where['b.status'] = ['eq', $status];
        }
        $where['a.is_del']  = 0;
        $list = $deviceModel
                ->alias('a')
                ->field('a.*,b.id as rel_id,b.status,b.create_time,c.user_login,c.user_nickname,b.update_time,c.mobile')
                ->join('cmf_device_user_rel b','a.device_id=b.device_id')
                ->join('cmf_user c','b.user_id=c.id')
                ->where($where)
                ->order('b.id desc')->paginate(20);
        // echo $deviceModel->getLastSql();
        // die;
        // 获取分页显示
        $page = $list->render();
        $this->assign('device_rel_user_status', getDictionary('device_rel_user_status'));
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : -1);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('count1', $count1);
        $this->assign('count2', $count2);
        // 渲染模板输出
        return $this->fetch();
    }
    /**
     * 设备审核提交
     */
    public function audit_post(){
       
        $id = $this->request->param('id');
        $find =  Db::name('device_user_rel')->where(['status' => 2,'id'=>$id])->find();
        $device = Db::name('device')->where(['device_id'=>$find['device_id'],'is_del'=>0])->find();

        if(empty($device)){
           $this->error('设备状态异常！'); 
        }
        if(empty($find)){
           $this->error('没有需要审核的记录！'); 
        }
        $is_new_bind = false;//判断人是否是新第一次绑定
        // $man_frist_bind = false;
        // $log = Db::name('device_user_rel')->where(['user_id'=>$find['user_id']])->select()->toArray();
        // if(count($log)==1){//大于1多次绑定
        //     if(empty($find['audit_user_id'])){//有可能之前绑定后解绑，所以要判断是否有审核过的人
        //         $man_frist_bind = true;
        //     } 
        // }
        //判断设备是否第一次
        // $log = Db::name('device_user_rel')->where(['device_id'=>$find['device_id']])->select()->toArray();
        // if(count($log)<=1){
        //     if(empty($find['audit_user_id'])){
        //        $is_new_bind = true; 
        //     } 
        // }
        $result = Db::name('device_user_rel')->where(['id'=>$id])->update(['status'=>1,'update_time'=>time(),'audit_user_id'=>cmf_get_current_admin_id()]);
        $result1 = Db::name('device')->where(['device_id'=>$find['device_id']])->update(['is_bind'=>1]);
        if($result!==false&&$result1!==false){
            //处理推介关系
            // if(intval($find['user_referrer_id'])){
            //     $user_referrer = Db::name('user_referrer')->where(['id'=>$find['user_referrer_id']])->find();//查找推介表记录
            //     $res = Db::name('user_referrer')->where(['id'=>$find['user_referrer_id']])->update(['status'=>1]);//普通会员推介关系状态通过
            //     if($res && !empty($user_referrer)){
            //         /*新绑定赠送直推人积分*/
            //         if($is_new_bind){
            //            handle_direct_referral_reward($user_referrer['parent_user_id'],$find['user_id']);
            //         } 
            //     }
            // }else{
            //     $user_referrer = Db::name('user_referrer')->where(['user_id'=>$find['user_id']])->find();//查找推介表记录
            //     if(!empty($user_referrer) && $is_new_bind){
            //         /*新绑定赠送直推人积分*/
            //         handle_direct_referral_reward($user_referrer['parent_user_id'],$find['user_id']);
            //     }
            // }

            // $sendData = [];
            // $sendData['message_type'] = 'device_open';
            // $sendData['device_id'] = $find['device_id'];
            // $sendData['time'] = time();
            // pushTipMessage($sendData,array($find['user_id']));
            
            // send_message(array($find['user_id']), '设备绑定', "你的新设备‘".$device['name']."’绑定审核成功", 0, null, 3);
            $this->success("操作成功！");

        }else{
            $this->error('操作失败！'); 
        }
        
    }

    // 导入excel
    public function recharge_kit_excel()
    {
        header('Content-Type: text/plain; charset=utf-8');
        if(request()->isPost()){
            $excel = request()->file('excel')->getInfo();
            $objPHPExcel = \PHPExcel_IOFactory::load($excel['tmp_name']);//读取上传的文件
            $arrExcel = $objPHPExcel->getSheet(0)->toArray();//获取其中的数据
            Db::startTrans();
            try{


                foreach($arrExcel as $key => $val){
                    if($key==0){
                        continue;
                    }
                    $user = Db::name('user')->where(['user_login'=>$val[1]])->find();
                    $order_code = generate_order_code($user['id']);
                    $device = Db::name('device')->where(['device_sn'=>$val[0]])->find();
                    if(empty($device)){
                        continue;
                    }
                    $order_id = generate_order(10,$user['id'],$order_code,3,2,null,'赠送的设备：'.$device['device_id']);
                    $res = Db::name('user')->where(['user_login'=>$val[1]])->setInc('package',10);
                    if($res===false){
                       $this->error('操作失败！'); 
                    }

                }
                Db::commit();

            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error($e->getMessage());
            }    
            $this->success("充值成功！");

            // 启动事务
            // Db::startTrans();
            // try{

                
                
            //     Db::commit(); 
            //     $this->success("充值成功！");
            // } catch (\Exception $e) {
            //     // 回滚事务
            //     Db::rollback();
            //     die($e->getMessage());
            //     $this->error($e->getMessage());
            // }
        }else{
            return $this->fetch();
        }
    }

    public function one_key_adopt()
    {
        $param = $this->request->param();
        // print_r($param);exit;
        if(empty($param['value'])){
            $this->error('传参有误！');
        }
        // print_r($param['value']);
        // die;
        // $verificode = $param['verificode'];
        // $admin_phone = get_parameter_settings('admin_phone');
        // $errMsg = cmf_check_verification_code($admin_phone, $verificode);
        // if (!empty($errMsg)) {
        //     $this->error($errMsg);
        // }

        $lists = Db::name('device_user_rel')
                ->alias('a')
                ->field('a.*')
                ->join('cmf_device b','a.device_id=b.device_id')
                ->where(['a.status' => 2,'a.id'=>array('in',explode(',', $param['value']))])
                ->select()
                ->toArray();
        // 启动事务
        Db::startTrans();
        try {
            foreach ($lists as $key => $val) { 
                $device = Db::name('device')->where(['device_id'=>$val['device_id'],'is_del'=>0])->find();
                if(empty($device)){
                   $this->error('设备状态异常，序列号：'.$device['device_sn']); 
                }
                $is_new_bind = false;//判断人是否是新第一次绑定
                //判断设备是否第一次
                $log = Db::name('device_user_rel')->where(['device_id'=>$val['device_id']])->select()->toArray();
                if(count($log)<=1){
                    if(empty($val['audit_user_id'])){
                       $is_new_bind = true; 
                    }                   
                }
                $result = Db::name('device_user_rel')->where(['id'=>$val['id']])->update(['status'=>1,'update_time'=>time(),'audit_user_id'=>cmf_get_current_admin_id()]);
                if($result!==false){
                    //处理推介关系
                    // if(intval($val['user_referrer_id'])){
                    //     //如果他的上级关系有推介关系
                    //     $user_referrer = Db::name('user_referrer')->where(['id'=>$val['user_referrer_id']])->find();//查找推介表记录
                    //     $res = Db::name('user_referrer')->where(['id'=>$val['user_referrer_id']])->update(['status'=>1]);//普通会员推介关系状态通过
                    //     if($res && !empty($user_referrer)){
                    //         /*新绑定赠送直推人积分*/
                    //         if($is_new_bind){
                    //            handle_direct_referral_reward($user_referrer['parent_user_id'],$val['user_id']);
                    //         } 
                    //     }
                    // }else{
                    //     $user_referrer = Db::name('user_referrer')->where(['user_id'=>$val['user_id']])->find();//查找推介表记录
                    //     if(!empty($user_referrer) && $is_new_bind){
                    //         /*新绑定赠送直推人积分*/
                    //         handle_direct_referral_reward($user_referrer['parent_user_id'],$val['user_id']);
                    //     }
                    // }
                    $sendData = [];
                    $sendData['message_type']   = 'device_open';
                    $sendData['device_id']      = $val['device_id'];
                    $sendData['time']           = time();
                    pushTipMessage($sendData,array($val['user_id']));
                    //send_message(array($val['user_id']), '设备绑定', "你的新设备‘".$device['name']."’绑定审核成功", 0, null, 3);
                }  
            }
            // 提交事务
            Db::commit();   
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
         $this->success("操作成功！");
    }

    /*删除审核*/
    public function audit_delete()
    {
       
        $id = input('param.id', 0, 'intval');
        $find =  Db::name('device_user_rel')->where(['id'=>$id])->find();
  
        if(empty($find)){
           $this->error('没有该记录！'); 
        }
        
        $result = Db::name('device_user_rel')->where(['id'=>$id])->update(['status'=>0]);
        if($result!==false){
            Db::name('device')->where(['device_id'=>$find['device_id']])->update(['is_bind'=>0]);
            $this->success("操作成功！");
        }else{
            $this->error('操作失败！'); 
        }
        
    }

    // 解绑device
    public function untie_device(){
      $param = $this->request->param();
      $deviceModel = Db::name('device');
      $device_user_rel = Db::name('device_user_rel');

      $where['device_id'] = $param['device_id'];
      $device = $deviceModel->where($where)->find();   

      if(empty($device)){
        $this->error('设备有误，请稍后再尝试');
      }

      $res = true;
      $time = time();
      $result = $device_user_rel->where(['device_id'=>$param['device_id']])->update(['status'=>3]);//解绑所有人的设备
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
            'action_user_id'=>cmf_get_current_admin_id(),
            'type'=>2,
            'create_time'=>$time
          ]);

        $this->success("设备解绑成功！");
      }else{
        $this->error('设备解绑失败！');
      }
    }

    function myTrim($str){
        $search = array(" ","　","\n","\r","\t");
        $replace = array("","","","","");
        return str_replace($search, $replace, $str);
    }
    // 导入excel
    public function import_excel()
    {
        
         header('Content-Type: text/plain; charset=utf-8');
        if(request()->isPost()){
            $excel = request()->file('excel')->getInfo();
            $objPHPExcel = \PHPExcel_IOFactory::load($excel['tmp_name']);//读取上传的文件
            $arrExcel = $objPHPExcel->getSheet(0)->toArray();//获取其中的数据
            $devices = [];
            $device_sn_arr = array();
            $time = time();

            // print_r($arrExcel);exit;
            foreach($arrExcel as $key => $val){
                if($key==0){
                    continue;
                }
                if(!empty($val[2])){
                    $device_sn = $this->myTrim($val[2]); 
                    $devices[$key]['name'] = $val[0];
                    $devices[$key]['device_id'] = $val[1];
                    $devices[$key]['device_sn'] = $device_sn;
                    $devices[$key]['product_id'] = '160fa2b3062403e9160fa2b306248601';  
                    $devices[$key]['create_time'] = $time; 
                    $device_sn_arr[] = $device_sn; 
                }               
            }
            $deviceQuery = Db::name('device');
            $device_sn_sql_arr = $deviceQuery->column('device_sn');
            $intersect = array_intersect($device_sn_sql_arr,$device_sn_arr);
            if(!empty($intersect)){
                $sn_str = '';
                $intersect = array_values($intersect);
                foreach ($intersect as $key => $val) {
                    $sn_str .= $val.'、';
                    if($key>5){
                        break;
                    }
                }
                $this->error('含有重复的序列号：'.trim($sn_str,'、'));
            }   

            $result = true;
            if(!empty($devices)){
                $deviceModel = Db::name('device');
                $result = $deviceModel->insertAll($devices);
            }
            if(!$result){
                $this->error('导入失败！');
            }
            $this->synchrodata();
            $this->success("导入成功！");
        }
        return $this->fetch();
        
    }
    public  function go_synchrodata(){
        $res = $this->synchrodata();
        if($res){
            $this->success("同步成功！");
        }else{
            $this->error('同步失败！');
        }
    }
    function synchrodata(){
        
        /*获取本平台数据*/
        $deviceQuery = Db::name('device');
        $devices = $deviceQuery->where('device_id','exp','is null')->where(['is_bind'=>0,'is_del'=>0])->select()->toArray();
        $query_sn = [];
        foreach ($devices as $key => $val) {
            if(!empty($val['device_sn'])){
                $query_sn[] = $val['device_sn'];
            }          
        }

        /*获取第三方平台数据*/
        $param = [];
        $param['url'] = ['devices'=>''];
        $filter = ['id','mac','sn'];
        $query  = ['mac'=>array('$in'=>$query_sn)];    
        $param['data'] = ['limit'=>10000,'order'=>['create_time'=>'desc'], 'filter'=>$filter,'query'=>$query];
        $list = zmxz_request_post($param);
        $list = json_decode($list,true);

        // print_r($list);
        // print_r(json_encode($param['data']));
        // die;
        if(empty($list['list'])){
            return true;
        }
        foreach ($devices as $key => $value) {
            foreach ($list['list'] as $k => $val) {
                if($this->myTrim($value['device_sn'])==$this->myTrim($val['mac'])){
                    $data = [];
                    $data['device_id'] = $val['id'];
                    Db::name('device')->where(['device_sn'=>$val['mac']])->update($data);
                }
            }
        }
        return true;
    }
    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $find = Db::name("device")->where(["id" => $id])->find(); //更新该机器绑定状态
            $result = Db::name("device")->where(["id" => $id])->setField('is_del', 1);
            if ($result) {
                Db::name("device")->where(["id" => $id])->update(['is_bind'=>0]); //更新该机器绑定状态
                Db::name("device_user_rel")->where(["device_id" => $find['device_id']])->update(['status'=>0]);//该机器曾经绑定的所有人状态失效0

                //插入设备操作记录
                Db::name('device_action_log')->insertGetId([
                    'device_id'=>$find['device_id'],
                    'action_user_id'=>cmf_get_current_admin_id(),
                    'type'=>3,
                    'create_time'=>time()
                ]);
                $this->success("操作成功！", "adminIndex/index");
            } else {
                $this->error('操作失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    
}

