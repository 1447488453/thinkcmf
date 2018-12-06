<?php

namespace app\admin\model;

use think\Model;
use think\Db;
class ScoreModel extends Model
{

    protected $status = ['未通过','成功','待审核','确认中'];
    protected $score_status = ['未冻结','冻结中'];
    protected $type;
    /**
     * 所有总积分
     */
    public function getScoreTotal($where)
    {
        if (empty($where)) {
            $where = 0;
        }
        $scoreLogModel = Db::name('score_log');
        $score = $scoreLogModel->where($where)->sum('num');
        return $score;
    }

    /**
     * 设备使用记录列表
     */
    public function getDeviceUseScoreList($where,  $order = '')
    {
        if (empty($where)) {
            return [];
        }
        $useLogModel = Db::name('device_use_log');

        if(!empty($order)){
            $useLogModel->order($order);
        }
        $list = $useLogModel->alias('a')
        ->join('cmf_score_log b','a.score_log_id=b.id')
        ->join('cmf_device c','c.device_id=a.device_id')
        ->field('a.package_num,a.create_time as use_time,a.end_time,a.score_log_id,a.start_time,b.*,c.name')
        ->where($where)
        ->group('a.score_log_id')
        ->paginate(10);
        $list = $list->items();
        foreach($list as $key => $val){
            if(empty($val['score_log_id']) and (($val['end_time']-$val['start_time'])/60>=30)){
                $list[$key]['score'] = 490;
            }
            $list[$key]['type_name'] = $this->type[$val['type']]; 
            $list[$key]['use_time'] = date('Y.m.d H:i:s',$val['end_time']);
        }
        return $list;
    }

    /**
     * 积分记录列表
     */
    public function getScoreList($where,  $order = '')
    {
        if (empty($where)) {
            return [];
        }
        $scoreModel = Db::name('score_log');

        if(!empty($order)){
            $scoreModel->order($order);
        }
        $list = $scoreModel
                ->where($where)
                ->paginate(10)
                ->each(function($item, $key){
                    $item['status_name'] = $this->score_status[$item['status']];
                    $item['add_time'] = date('Y.m.d H:i:s',$item['add_time']);
                    $item['num'] = round($item['num'],2);
                    return $item;
                });
        return $list;
    }

    /**
     * 设备产生的积分统计
     */
    public function getDeviceUseScoreTotal($where)
    {
        if (empty($where)) {
            return [];
        }
        $useLogModel = Db::name('device_use_log');
        $score = $useLogModel->alias('a')->join('cmf_score_log b','a.score_log_id=b.id')->where($where)->sum('score');
        return $score;
    }
    
    /**
     * 获取积分图表数据
     */
    public function getDeviceScoreChart($where = array())
    {
        date_default_timezone_set('PRC'); //设置时区      

        $useLogModel = Db::name('device_use_log');
        //按月
        $maxTime = $useLogModel
                ->alias('a')
                ->join('cmf_score_log b','a.score_log_id=b.id')
                ->where($where)
                ->order('a.create_time desc')
                ->value('a.create_time');//时间最新一条
        $minTime = $useLogModel
                ->alias('a')
                ->join('cmf_score_log b','a.score_log_id=b.id')
                ->where($where)
                ->order('a.create_time desc')
                ->value('a.create_time');//时间最早一条 

        if(empty($maxTime)){
            return ['echart_data_type'=>'','data'=>[]];
        }
        $echart_data_type = '';
        $days = round(($maxTime-$minTime)/3600/24); 
        if($days>30){//$days>30
            $echart_data_type = '本年药包使用统计';
            $currYear = strtotime(date('Y-01-01 00:00:00'));//当年
            $where['a.create_time'] = array(['egt',$currYear],['elt',time()],'and');//获得当年内12个月的数据 
            $list = $useLogModel->alias('a')->join('cmf_score_log b','a.score_log_id=b.id')->field('a.start_time,a.end_time,a.create_time,a.score_log_id,b.score')->where($where)->select()->toArray();
            $resultData = [];
            for ($i=1; $i <= 12; $i++) { 
                if($i<10){
                    $i = '0'.$i;
                }
                $resultData[date("$i").'月'] = 0;
            }
            foreach($list as $key => $val){
                if(empty($val['score_log_id']) and (($val['end_time']-$val['start_time'])/60>=30)){
                    $val['score'] = 490;
                }
                foreach ($resultData as $k => $v) {
                    if((date('m',$val['create_time']).'月')==$k){
                        $resultData[$k] += $val['score'];
                    } 
                } 
            }    
        }elseif($days<=7) {//$days<=7
            $echart_data_type = '七日药包使用统计';
            $before7Day = strtotime("-7 day");
            $where['a.create_time'] = array(['egt',$before7Day],['elt',time()],'and');//获得当天起七天之前各天的数据
            $list = $useLogModel->alias('a')->join('cmf_score_log b','a.score_log_id=b.id')->field('a.start_time,a.end_time,a.create_time,a.score_log_id,b.score')->where($where)->select()->toArray();

            $resultData = [];
            for ($i=0; $i < 7; $i++) { 
                $resultData[date('d', strtotime('-'.$i.' day'))] = 0; 
            } 
            foreach($list as $key => $val){
                if(empty($val['score_log_id']) and (($val['end_time']-$val['start_time'])/60>=30)){
                    $val['score'] = 490;
                }
                foreach ($resultData as $k => $v) {
                    if(date('d',$val['create_time'])==$k){
                        $resultData[$k] += $val['score'];
                    } 
                }  
            } 
            $resultData = array_reverse($resultData,true);
        }else{
            $echart_data_type = '本月药包使用统计';
            $currMonthHasDays = date('t', strtotime(date('Y-m-01')));
            $where['a.create_time'] = array(['egt',strtotime(date('Y-m-01'))],['elt',strtotime(date('Y-m-'.$currMonthHasDays))],'and');//本月内各天的数据
            $list = $useLogModel->alias('a')->join('cmf_score_log b','a.score_log_id=b.id')->field('a.start_time,a.end_time,a.create_time,a.score_log_id,b.score')->where($where)->select()->toArray();
            $resultData = [];
            for ($i=1; $i <= $currMonthHasDays; $i++) { 
                if($i<10){
                    $resultData['0'.$i] = 0;
                }else{
                    $resultData[$i] = 0;
                }  
            }
            foreach($list as $key => $val){
                if(empty($val['score_log_id']) and (($val['end_time']-$val['start_time'])/60>=30)){
                    $val['score'] = 490;
                }
                foreach ($resultData as $k => $v) {
                    if(date('d',$val['create_time'])==$k){
                        $resultData[$k] += $val['score'];
                    } 
                } 
            }
        }
        return ['echart_data_type'=>$echart_data_type,'data'=>$resultData];
    }

    /**
     * 积分兑换
     */
    public function scoreExchange($data,$user){
        $data['score'] = -$data['score'];//积分兑换是扣除，则必须是负数
        $score_id = handle_score($data['score'],'积分兑换',$user);
        unset($data['score']);
        $data['score_id'] = $score_id;
        $result = Db::name('score_log')->update($data);
    }

    /**
     * 积分兑换记录列表
     */
    public function getScoreExchangeList($where, $order = 'id desc',$limit=10)
    {
        if(empty($where)){
            $where = [];
        }
        $scoreExchangeModel = Db::name('score_log');
        $list = $scoreExchangeModel->alias('a')
                ->join('cmf_user b','a.user_id=b.id')
                ->join('cmf_user c','a.audit_user_id=c.id','left')
                ->field('a.*,b.user_nickname,b.user_login,b.mobile,c.user_login as audit_user_login,c.user_nickname as audit_user_nickname,a.num,a.status as score_status')
                ->where($where)
                ->order($order)
                ->paginate($limit)
                ->each(function($item, $key){
                    if($item['update_time']>0){
                        $item['update_time'] = date('Y.m.d H:i:s',$item['update_time']);
                    }else{
                        $item['update_time'] = '';
                    }
                    $item['add_time'] = date('Y.m.d H:i:s',$item['add_time']);
                    $item['txid'] = $item['txid'];
                    return $item;
                });
        return $list;
    }

    /**
     * 积分转赠记录列表
     */
    public function getScoreTransferList($where,  $order = 'id desc')
    {
        if (empty($where)) {
            return [];
        }
        $list = [];
        return $list;
    }
}
