<?php

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\ScoreModel;

class ReceiptethController extends AdminBaseController{
    
	protected $allow_host_arr = ['119.23.61.129'];
    public function wait_for_receipt(){
		if(!in_array(get_client_ip(0, true),$this->allow_host_arr)){
          $this->error('非法访问！');
        }
        $res = $this->request->param();
        $time = time();
        $date = date('Y-m-d H:i:s',$time);
        $resExchangeArr = $res['datas']; 
        $exchange_fee = get_parameter_settings('exchange_fee');
        $val = $resExchangeArr;
        // $find = Db::name("score_exchange_log")->where(['id'=>$val['object_id']])->find();
        $score = Db::name("score_log")->where(["id" => $val['object_id']])->find(); 
        $res1 = Db::name("score_log")->where(["id" => $val['object_id']])->update(['status'=>0]);
        $val['fee'] = abs($score['num'])*($exchange_fee/100);
        $res2 = Db::name("score_log")->where(["id" => $score['id']])->update([
            'status'=>1
        ]);                    
    
        $user = get_user($score['user_id']);
        $params = [];
        $submittime         = $date;
        $params['mobile']   = $user['mobile'];
        $params['text']     = "【秀牛手环】尊敬的用户，您于".$submittime."兑换已经审核，提交至第三方。";//短信模板
        $params['tpl_id']   = 1;
        sendSms($params);
        if($res1!==false and $res2!==false){
            if($exchange_fee>0 and $val['fee']>0){
                // handle_score($val['fee'],8,39,$score['user_id']);
                handle_score($val['fee'],'手续费',$score['user_id'],1);//把扣除的手续费计入到用户
            } 
        }

        echo "success";
        
    }
}
