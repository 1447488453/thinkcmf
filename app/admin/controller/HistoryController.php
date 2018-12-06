<?php

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\ScoreModel;

class HistoryController extends AdminBaseController
{
    public function index(){
        $param = $this->request->param();

        $scoreExchangeModel = Db::name('score_log');
        
        $scoreModel = new ScoreModel();
        $where = [];

        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['update_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        }
        $keyword = isset($param['keyword'])?$param['keyword']:'';

        if (!empty($keyword) && !empty($keyword)) {
            $where['b.user_login|a.wallet_address|a.txid'] = ['like',"%$keyword%"];
        }
        $where['reason'] = '兑换';
        $where['status'] = ['eq', 1];
        $result = $scoreModel->getScoreExchangeList($where,'update_time desc, id desc',20);
        
        $list = $result->items();
        $total_page = $result->total();
        $this->assign('list', $list);
        $this->assign('total_page', $total_page);

        //今日的积分兑换总量
        $map = [];
        $map['reason'] = '兑换';
        $map['status'] = 1;
        $today_start = strtotime(date("Y-m-d 00:00:00"));
        $today_end = strtotime(date("Y-m-d 23:59:59"));
        $map['update_time'] = [['>= time', $today_start], ['<= time', $today_end]];
        $today_exchange_score = $scoreExchangeModel
                ->where($map)
                ->sum('num');

        if(!isset($_GET['page'])){
            //总积分 总的积分等于多少币        
            $map = [];
            $total_score = $scoreModel->getScoreTotal($map);   
            $url = 'http://ethbtcweb.maye.io/api/coin/eth/get_info/coin_type/bpp/access_token/zmxzbpp2018';
            $res = curl_request_get($url);
            $no_exchange_total_bpp = $res;//剩余多少币
        }else{
            $total_score = 0;
            $no_exchange_total_bpp = 0;
        }        
        
        //积分兑换总量
        $map = [];
        $map['reason'] = ['in',array('兑换')];
        $map['status'] = 1;
      
        if (!empty($startTime) && !empty($endTime)) {
            $map['update_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        }
        $exchange_total_score = $scoreModel->getScoreTotal($map);
        
        //处理格式
        $today_exchange_score = number_format(abs($today_exchange_score),2);
        $total_score = number_format($total_score,2);
        // $no_exchange_total_bpp = number_format($no_exchange_total_bpp,2);
        $exchange_total_score = number_format(abs($exchange_total_score),2);

        $this->assign('today_exchange_score', $today_exchange_score);
        $this->assign('total_score', $total_score);
        $this->assign('no_exchange_total_bpp', $no_exchange_total_bpp);
        $this->assign('exchange_total_score', $exchange_total_score);
        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');

        return $this->fetch();
    }

    public function export(){
        $param = $this->request->param();

        $scoreModel = new ScoreModel();
        $where = [];
        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.update_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        }
        $keyword = isset($param['keyword'])?$param['keyword']:'';

        if (!empty($keyword) && !empty($keyword)) {
            $where['b.user_login|a.wallet_url|a.result'] = ['like',"%$keyword%"];
        }
        $where['a.status'] = ['eq', 1];

        $result = $scoreModel->getScoreExchangeList($where,'id desc',1000000);
        $list = $result->items();
        $this->execl($list,date('Y-m-d',$startTime).'——'.date('Y-m-d',$endTime).'审核记录');
    }

    public function execl($data='',$name='历史审核记录')
    {
        //header('Content-Type: text/plain; charset=utf-8');
        error_reporting(E_ALL);
        import('phpexcel.PHPExcel', EXTEND_PATH);
        $objPHPExcel = new \PHPExcel();
        /*以下是一些设置 ，什么作者  标题啊之类的的*/
        $objPHPExcel->getProperties()
                    ->setCreator($name)
                    ->setLastModifiedBy("admin")
                    ->setTitle($name)
                    ->setSubject($name)
                    ->setDescription($name)
                    ->setKeywords($name)
                    ->setCategory($name);
        $objPHPExcel->setActiveSheetIndex(0)
                    //Excel的第A列，uid是你查出数组的键值，下面以此类推
                    ->setCellValue('A1', '兑换数量')    
                    ->setCellValue('B1', '兑换用户')
                    ->setCellValue('C1', '钱包地址')
                    ->setCellValue('D1', 'TXID')
                    ->setCellValue('E1', '通过时间')
                    ->setCellValue('F1', '申请时间')
                    ->setCellValue('G1', '审核人');
        //设置单元格宽度                
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        //设置表头行高
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(35);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(22);
        $objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
        
        //设置字体样式
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setName('黑体');
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(12);
         /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        foreach($data as $k => $v){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)
                        //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, round(abs($v['num']),2))    
                        ->setCellValue('B'.$num, $v['user_login'])
                        ->setCellValue('C'.$num, $v['wallet_address'])
                        ->setCellValue('D'.$num, $v['txid'])
                        ->setCellValue('E'.$num, $v['update_time'])
                        ->setCellValue('F'.$num, $v['add_time'])
                        ->setCellValue('G'.$num, $v['audit_user_nickname']);
        }
            
        $objPHPExcel->getActiveSheet()->setTitle('log');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        ob_start();
        $objWriter->save('php://output');
        exit;

    }
}
