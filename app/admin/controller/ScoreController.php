<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\ScoreModel;
class ScoreController extends AdminBaseController{
    public function score_change(){
        $param = $this->request->param();
        $scoreModel = new ScoreModel();
        $where = [];
        $where['a.reason'] = '兑换';
        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.add_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        }
        $keyword = empty($param['keyword']) ? '' : $param['keyword'];
        if (!empty($keyword)) {
            $where['b.user_login|b.mobile|a.wallet_address'] = ['like', "%$keyword%"];
        }
        $status = !isset($param['status']) ? '' : $param['status'];
        if ($status!='') {
            $where['a.status'] = ['eq', $status];
        }
        $result = $scoreModel->getScoreExchangeList($where,'id desc',10);
        $list = $result->items();
        $this->assign('list', $list);
        $this->assign('page', $result->render());
        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : -1);
        return $this->fetch('score_change');
    }

    public function audit(){
        $id     = input('param.id', 0, 'intval');
        $result = Db::name("score_log")->where(["id" => $id])->find();
        $score  = $result['num'];
        $user   = get_user($result['user_id']);
        $param  = $this->request->param();
        $verificode  = isset($param['verificode'])?$param['verificode']:'';
        $admin_phone = get_parameter_settings('admin_phone');
        //$admin_phone = 15757851183;
        $errMsg = cmf_check_verification_code($admin_phone, $verificode);
        if(!empty($errMsg)){
            $this->error($errMsg);
        }
        if(isset($param["more"]) and $param["more"]=='yes') {
            //die;
            $datas = [];
            $exchange_fee = get_parameter_settings('exchange_fee');
            //先计算积分
            $score_exchange_fee = abs($score)*($exchange_fee/100);//要扣除的积分
            $money = (abs($score)-$score_exchange_fee)*0.1;
            $datas['coin_value']        = $money;
            // $datas['address']           = '0xdbddc7cb49fcbd62a6f1141bb475ad8c4826dfc8';
            $datas['address']           = $result['wallet_address'];
            $datas['object_id']         = $id;
            $datas['saas_id']           = "zhinengshouhuan";
            $datas['notify_url']        = "http://xwbn38.natappfree.cc/admin/Receipteth/wait_for_receipt";
            $url    = 'http://ethbtcweb.maye.io/api/coin/eth/handle_transfer_accounts';
            $option = [];
            $option['coin_type']    = "bhe";
            $option['url']          = $url;
            $option['data']         = $datas;
            $option['access_token'] = 'zmxzbpp2018';
            // print_r($option);
            $res = curl_request_post($option);
            $res = json_decode($res,true);
            // print_r($res);
            if(!empty($res['code'])){
                $resExchangeArr = $res['data'][0];
                $res2 = Db::name("score_log")->where(["id" => $id])->update(['status'=>1,'third_party_platform'=>$option['coin_type'],'audit_user_id'=>cmf_get_current_admin_id(),'update_time'=>time(),'txid'=>$resExchangeArr['tx']]);
                // if($exchange_fee>0 and $score_exchange_fee>0){
                //     handle_score($score_exchange_fee,'手续费',$result['user_id'],2);//把扣除的手续费计入到用户
                // }
                // $params = [];
                // $submittime         = date('Y-m-d H:i:s',$result['add_time']);
                // $params['mobile']   = $user['mobile'];
                // $params['text']     = "【秀牛手环】尊敬的用户，您于".$submittime."兑换已经审核，提交至第三方。";//短信模板
                // $params['tpl_id']   = 1;
                // sendSms($params);
                $this->success("操作成功！");
            }else{
                $this->error($res['msg']);
            }
        }
        if (isset($param["more"]) and $param["more"]=='no'){
            $res1 = Db::name("score_log")->where(["id" => $result['id']])->update(['status'=>2,'audit_user_id'=>cmf_get_current_admin_id(),'update_time'=>time(),'rejected'=>$param['value']]);
            if($res1){
               $this->success("操作成功！");
            }
        }
    }

    public function one_key_adopt(){
        $param = $this->request->param();
        if (empty($param['value'])) {
            $this->error('传参有误！');
        }
        $verificode = $param['verificode'];
        $admin_phone = get_parameter_settings('admin_phone');
        $errMsg = cmf_check_verification_code($admin_phone, $verificode);
        if (!empty($errMsg)) {
            $this->error($errMsg);
        }
        $lists = Db::name('score_log')->where(['status' => 0,'id'=>array('in',explode(',', $param['value']))])->select()->toArray();
        $exchangeArr = [];//所有需要兑换的记录
        $exchange_fee = get_parameter_settings('exchange_fee');
        foreach ($lists as $key => $val) {
            $score = Db::name("score_log")->where(["id" => $val['id']])->find();
            $datas = [];
            //先计算积分
            $score_exchange_fee = abs($score['num'])*($exchange_fee/100);//要扣除的积分
            $money = (abs($score['score'])-$score_exchange_fee)*0.1;
            $datas['coin_value'] = $money;
            //$datas['address'] = '0xC4AB04f8adF514AA463E20C2E2f8DFfe98B5A283';
            $datas['address']    = $val['wallet_url'];
            $datas['object_id']  = $val['id'];
            $datas['notify_url'] = 'http://xunzheng.zmiaosh.com/withdraw/Receipteth/wait_for_receipt';
            $datas['saas_id'] = "zhinengshouhuan";
            $exchangeArr[] = $datas;
            // Db::name("score_exchange_log")->where(["id" => $val['id']])->update([
            //     'is_action'=>1,
            //     'audit_user_id'=>cmf_get_current_admin_id(),
            //     'exchange_rate'=>10,
            //     'exchange_coin'=>$datas['coin_value'],
            //     'fee_rate'=>$exchange_fee,
            //     'fee'=>$score_exchange_fee,//要扣除的积分,
            // ]);
          
        }

        $url = 'http://ethbtcweb.maye.io/api/coin/eth/handle_transfer_accounts';
        $option = [];
        $option['url'] = $url;
        $option['data'] = $exchangeArr;
        $option['coin_type'] = 'bhe';
        $option['access_token'] = 'zmxzbpp2018';
        $result = curl_request_post($option);
        $res = json_decode($result,true);
        foreach ($res['data'] as $key => $val) {
            Db::name("score_log")->where(["id" => $val['object_id']])->update(['status'=>1,'update_time'=>time(),'txid'=>$val['tx'],'audit_user_id'=>cmf_get_current_admin_id()]);
        }
        echo $result;
    }

}