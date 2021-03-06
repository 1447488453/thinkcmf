<?php

namespace api\device\controller;

use think\Db;
use think\Validate;
use cmf\controller\RestBaseController;
use app\admin\model\ScoreModel;

class ApiController extends RestBaseController
{

    /**
     * 验证设备云平台服务器
     */
    public function verification()
    {
        $param = $this->request->param();
        $signature = $param['signature'];
        $echostr = $param['echostr'];

        $token = 'NDA3MjcyMTwREQ1RdddDc0NTJDMzkwODM1QjRBMEFDRTVEQjkwODFDRTA2Mzk4NjRFMzIyNDZEN0Q2Mg==';
        $timestamp = $param['timestamp'];
        $nonce = $param['nonce'];
        $_POST["perdata"] = "$token $timestamp $nonce";
        $data = chop (trim ($_POST["perdata"]));
        $a = explode (" ", $data);
        sort ($a);
        $str = implode ("", $a);

        if(sha1($str)==strtolower($signature)){
            echo $echostr;
        }
        die;
    }


    function curl_newzmxz_device($device_id,$param){
        //转发错误产品目录的设备 足熏仪
        try{
            $device = Db::name('device')->where(['device_id'=>$device_id])->find();
            if(empty($device)){
                //file_put_contents('device/newzmxzdevice_log_'.date('Ymd').'.txt','接收参数：'.json_encode($param).'。时间：'.$param['time'].PHP_EOL,FILE_APPEND);
                $datas = $param;
                $url = 'http://newxunzheng.zmiaosh.com/api/device/sitedevice/receive_distribute_data';
                $option = [];
                $option['url'] = $url;
                $option['access_token'] = 'newzmxz2018';
                $option['data'] = $datas;
                curl_request_post($option);
                die;
            }
        }catch(Exception $e){}

    }
    /**
     * 接收分发数据
     */
    public function receive_distribute_data()
    {
        date_default_timezone_set('Asia/Shanghai');
        $access_token = 'NDA3MjcyMTAyNjZCQTcwREQ1RDc0NTJDMzkwODM1QjRBMEFDRTVEQjkwODFDRTA2Mzk4NjRFMzIyNDZEN0Q2Mg==';//本机的token
        //$product_id = '160fa2b3062403e9160fa2b306248601';//当前的产品  暂定死
        $AccessToken = $this->request->header('access-token');//传递的token
        if($AccessToken!=$access_token){
            $this->error('非法访问！');
        }
        $param = $this->request->param();

        $device_id = isset($param['from_id'])?$param['from_id']:$param['id'];

        $this->curl_newzmxz_device($device_id,$param);

        switch (strtolower($param['type'])) {
            case 'state':

                // $testDevice = ['962786563','601319401','763325462','763326884','763326136'];
                // if(in_array($device_id, $testDevice)){
                //   try{

                //     $datas = $param;
                //     $url = 'http://47.75.40.69/api/bpp/api/handle_withdraw';
                //     $option = [];
                //     $option['url'] = $url;
                //     $option['data'] = $datas;
                //     $res = curl_request_post($option);

                //   }catch(Exception $e){}
                // }

                $device = Db::name('device_user_rel')->alias('a')
                    ->join('cmf_device b','a.device_id=b.device_id')
                    ->field('a.*,b.site_id')
                    ->where(['a.device_id'=>$device_id,'a.status'=>1,'b.is_bind'=>1,'is_del'=>0])
                    ->order('a.id desc')
                    ->find();
                if(empty($device)){
                    die('未绑定设备');
                }
                receive_data_save_to_cache($device,$param);
                //接收所有数据
                //file_put_contents('device/receive_data_log_'.date('Ymd').'.txt','接收参数：'.json_encode($param).'。时间：'.$param['time'].PHP_EOL,FILE_APPEND);

                if($param['state']=='online'){
                    update_device_state($device,1);
                }
                if($param['state']=='offline'){
                    update_device_state($device,0);
                }

                break;
            case 'sync':
                //获取设备最新数据



                // $testDevice = ['962786563','601319401','763325462','763326884','763326136'];
                // if(in_array($device_id, $testDevice)){
                //   try{
                //     $datas = $param;
                //     $url = 'http://47.75.40.69/api/bpp/api/handle_withdraw';
                //     $option = [];
                //     $option['url'] = $url;
                //     $option['data'] = $datas;
                //     $res = curl_request_post($option);
                //   }catch(Exception $e){}
                // }
                $datapoint = $param['datapoint'][0];
                $param['device_id'] = $device_id;
                $device = Db::name('device_user_rel')->alias('a')
                    ->join('cmf_device b','a.device_id=b.device_id')
                    ->field('a.*,b.site_id')
                    ->where(['a.device_id'=>$device_id,'a.status'=>1,'b.is_bind'=>1,'is_del'=>0])
                    ->order('a.id desc')
                    ->find();

                if(empty($device)){
                    die('未绑定设备');
                }
                receive_data_save_to_cache($device,$param);
                //$user = get_user($device['user_id']);
                //接收所有数据
                file_put_contents('device/receive_data_log_'.date('Ymd').'.txt','接收参数：'.json_encode($param).'。时间：'.$param['time'].PHP_EOL,FILE_APPEND);
                _SITEID($device['site_id']);//根据用户设置站点ID

                $config = getConfig();
                $device_use_time = $config['device_use_time'];

                //本查询条件凡是小于规定最小时间和结束时间未填的都视为在用记录，至于是否符合规定时间以下逻辑再判断
                $where = '(((UNIX_TIMESTAMP() - start_time) < '.($device_use_time[0]*60).') or (end_time=0 and start_time>0)) and device_id = "'.$device_id.'"';
                $find = Db::name('device_use_log')->where($where)->order('id desc')->find();////查找规定最小时间之内的数据

                if(!empty($find)){
                    $res_minute = (time()-$find['start_time'])/60;//最近的一条使用了多久时间
                    //如果是按了on/off都要给设备的最近一条使用不超过30分钟的记录设置为使用完毕
                    if($datapoint['index']=="0" and $datapoint['value']=="false" and $datapoint['type']=='bool'){
                        if($res_minute<30){
                            Db::name('device_use_log')->where(['device_id'=>$device_id,'start_time'=>array('gt',0),'end_time'=>0])->order('id desc')->limit(1)->update(['end_time'=>time()]);

                            $sendData = [];
                            $sendData['message_type'] = 'device_close';
                            $sendData['device_id'] = $device_id;
                            $sendData['time'] = time();
                            pushTipMessage($sendData,array($find['user_id']));

                            $this->success("设备使用结束！");

                        }
                    }

                    //如果最近的一条使用记录结束时间不为0可以认为是新开始
                    if($find['end_time']>0){
                        if($datapoint['index']=="0" and $datapoint['value']=='true' and $datapoint['type']=='bool'){
                            //按开机后只要按足熏或者座熏都会有个开始使用状态的类型数据，index为0
                            $this->device_start_use($param);
                            return false;
                        }
                    }else{

                        if($res_minute>=70){
                            if($datapoint['index']=="0" and $datapoint['value']=='true' and $datapoint['type']=='bool'){
                                $this->device_start_use($param);
                                return false;
                            }
                        }elseif($res_minute>=30 && $find['end_time']==0){

                            /*注意点*/
                            //要考虑发过来的数据是否是结束类型的数据才能走结束逻辑
                            //发过来的结束数据使用时间是否符合规定时间

                            /*先判断是否有本次使用熏蒸的结束类型数据记录走进来*/
                            if($datapoint['type']!='ushort'){
                                return false;
                            }
                            $use_arr = array(6,7);
                            if(!in_array(intval($datapoint['index']),$use_arr)){//如果不在本次座足熏结束类型数据里
                                return false;
                            }
                            if(intval($datapoint['value'])>=30){
                                //使用的时间大于规定的最小时间并且该条使用记录结束时间未填则算是完成结束
                                $param['start_time'] = $find['start_time'];//此处作为指定记录更新条件，可理解为该条的ID
                                $this->device_end_use($param);//结束使用
                            }
                            return false;

                        }elseif($res_minute<$device_use_time[0]){//使用的时间小于规定的最小时间认为是正在使用
                            //这里有可能是由于插电立即使用的记录type为2，那么就要更新type改为1
                            $this->success("设备正在使用中:".round($res_minute).'分');
                        }else{
                            $this->success("设备已经使用完毕！");
                        }
                    }
                }else{
                    //如果没有符合规定最小时间的使用记录则认为是新开始
                    if($datapoint['index']=='0' and $datapoint['value']=='true' and $datapoint['type']=='bool'){
                        $this->device_start_use($param);
                    }
                }
                break;
            case 'activation':

                break;
            default:
                break;
        }
    }
    //设备开始使用
    private function device_start_use($param)
    {
        $device_id = $param['device_id'];//模拟接收过来的设备ID
        $time = strtotime($param['time']);//接收开始时间
        //乐观锁
        $use_log = Db::name('device_use_log')->where(['start_time'=>$time,'device_id'=>$device_id])->find();
        if(!empty($use_log)){
            return false;
        }

        $device = Db::name('device_user_rel')->alias('a')
            ->join('cmf_device b','a.device_id=b.device_id')
            ->field('a.*,b.name,b.site_id,b.is_test')
            ->where(['a.device_id'=>$device_id,'a.status'=>1,'b.is_bind'=>1,'b.is_del'=>0])
            ->order('a.id desc')
            ->find();
        if(!empty($device)){
            update_device_state($device,1);
            $config = getConfig();
            $device_use_time = $config['device_use_time'];
            //检查设备当日的使用次数
            $use_number = get_parameter_settings('device_use_number');
            $where['create_time'] = [['egt',strtotime(date('Y-m-d 00:00:00'))],['elt',strtotime(date('Y-m-d 23:59:59'))],'and'];//限制一天的时间
            $where['device_id'] = $device_id;
            $where['score_log_id'] = array('gt',0);
            $useDayNum = Db::name('device_use_log')->where($where)->group('score_log_id')->count();
            $is_test = 0;

            if(isset($device['is_test'])){
                $is_test = $device['is_test'];
            }

            if($is_test==0){
                if($useDayNum>=$use_number){
                    //file_put_contents('device/receive_err_log_'.date('Ymd').'.txt','设备ID：'.$device_id.'。用户ID：'.$device['user_id'].'。错误信息：设备使用次数已达到限制！时间：'.$param['time'].PHP_EOL,FILE_APPEND);

                    $sendData = [];
                    $sendData['message_type'] = 'device_use_number_tips';
                    $sendData['device_id'] = $device_id;
                    $sendData['device_name'] = $device['name'];
                    $sendData['time'] = time();
                    pushTipMessage($sendData,array($device['user_id']));

                    $this->success("设备使用次数已达到限制！");
                    return false;
                }
            }

            handle_device_use_log($device_id,$device['user_id'],0,$time,0,1,1);//插入使用记录
            $sendData = [];
            $sendData['message_type'] = 'device_start_use';
            $sendData['device_id'] = $device_id;
            $sendData['time'] = time();
            pushTipMessage($sendData,array($device['user_id']));//长连接推送告知该用户已经设备启动使用
            $this->success("启动使用成功！");
        }else{
            file_put_contents('device/receive_err_log_'.date('Ymd').'.txt','设备ID：'.$device_id.'。错误信息：设备并未绑定！时间：'.$param['time'].PHP_EOL,FILE_APPEND);
            $this->error('设备并未绑定!');
        }
    }

    //设备结束使用
    private function device_end_use($param)
    {
        $device_id = $param['device_id'];//模拟接收过来的设备ID
        $time = strtotime($param['time']);

        $start_time = $param['start_time'];//接收开始时间
        $device = Db::name('device_user_rel')->alias('a')
            ->join('cmf_device b','a.device_id=b.device_id')
            ->field('a.*,b.site_id')
            ->where(['a.device_id'=>$device_id,'a.status'=>1,'b.is_bind'=>1,'is_del'=>0])
            ->order('a.id desc')
            ->find();
        if($device){
            $res = handle_device_use_log($device_id,$device['user_id'],0,$start_time,$time,2);//更新结束时间
            if($res){
                $user = get_user($device['user_id']);
                if($user['package']>0){
                    $score = get_parameter_settings('package_use_reward_score');//使用药包送积分
                    $score_id = handle_score($score,2,$device['user_id'],$device['user_id']);//处理积分
                    if($score_id){
                        $deduction_package = Db::name("user")->where(["id" => $device['user_id']])->setInc('package', -1);//扣减一次药包
                        //处理分销
                        $referrer_user = Db::name('user_referrer')->where(['user_id'=>$device['user_id'],'status'=>1])->order('type desc')->find();//定位到上级推荐人
                        handle_distribution_reward($referrer_user['parent_user_id'], $score, 7, $device['user_id']);
                        //有积分完成一次给一次动画
                        Db::name('user')->where(['id'=>$device['user_id']])->setInc('coin_animation',1);
                    }
                }else{
                    file_put_contents('device/receive_err_log_'.date('Ymd').'.txt','设备ID：'.$device_id.'。用户ID：'.$device['user_id'].'。错误信息：没有足够的药包，不赠送积分！时间：'.$param['time'].PHP_EOL,FILE_APPEND);
                }

                if(isset($score_id)){
                    Db::name('device_use_log')
                        ->where(['device_id'=>$device_id,'user_id'=>$device['user_id'],'start_time'=>$start_time])
                        ->order('id desc')
                        ->update(['score_log_id'=>$score_id]);
                }

                $sendData = [];
                $sendData['message_type'] = 'device_end_use';
                $sendData['device_id'] = $device_id;
                $sendData['time'] = time();
                pushTipMessage($sendData,array($device['user_id']));//长连接推送告知该用户已经设备完成一次使用
                $this->success("关闭使用成功！");
            }

        }else{
            file_put_contents('device/receive_err_log_'.date('Ymd').'.txt','设备ID：'.$device_id.'。错误信息：设备并未绑定！时间：'.$param['time'].PHP_EOL,FILE_APPEND);
            $this->error('设备有误!');
        }
    }
}


