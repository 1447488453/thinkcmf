<?php

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\ScoreModel;
/**
 *用户各项操作控制器
 */
class AdminUserController extends AdminBaseController
{
    public function detail(){
        $params     = $this->request->param();
        $user_id    =  $this->request->param('user_id');
        $user_info  = Db::name('user')->where("id=$user_id")->find();
        $this->assign('user',$user_info);
        $tabs = array(
            ['name'=>'积分记录','url'=>url('User/AdminUser/detail', ['user_id' => $user_info['id'],'type'=>1]),'type'=>1],
            ['name'=>'兑换记录','url'=>url('User/AdminUser/detail', ['user_id' => $user_info['id'],'type'=>2]),'type'=>2]
        );

        //按需获取数据
        if(!isset($params['type'])){
           $type = 1;
        }else{
           $type = $params['type']; 
        }
        $this->assign('type', $type);
           switch ($type) {
            case 1:
                //积分记录
                $map = [];
                $map['user_id']=$user_info['id'];
                $scoreModel = new ScoreModel();
                $score_list = $scoreModel->getScoreList($map,'id desc');
                $page = $score_list->render();
                $list = $score_list->items();
                break;
            case 2:
                //兑换记录
                $scoreModel = new ScoreModel();
                $where = [];
                $where['a.reason'] = '兑换';
                $where['b.id'] = array('eq', $user_info['id']);
                $result = $scoreModel->getScoreExchangeList($where,'id desc',10);
                $list = $result->items();
                $page = $result->render();
                break;
            default:
                # code...
                break;
        }
        $this->assign('page',$page);        
        $this->assign('list',$list);
        // 渲染模板输出
        $tabs[$type-1]['class'] = 'active';
        $this->assign('tabs',$tabs);
        return $this->fetch('detail');
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
    public function ban()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $result = Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 0);
            if ($result) {
                $this->success("会员拉黑成功！", "adminIndex/index");
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    public function update_package()
    {
        
        $param = $this->request->param();
        $package_num = $param['value'];
        $id = $param['id'];
        $verificode = $param['verificode'];
        $admin_phone = get_parameter_settings('admin_phone');
        $errMsg = cmf_check_verification_code($admin_phone, $verificode);
        if (!empty($errMsg)) {
            $this->error($errMsg);
        }
        if ($id) {
            $user = get_user($id);
            $order_code =  generate_order_code($id);
            $order_id = generate_order($package_num,$id,$order_code,2,2);
            if($order_id){
                $result = Db::name("user")->where(["id" => $id])->setInc('package', $package_num);
                if ($result && $order_id) {
                    cmf_verification_code_log($admin_phone,cmf_random_number_string());//成功之后立即更新验证码，防止再利用同一验证码进行二次操作
                    $this->success("操作成功！", "adminIndex/index");
                } else {
                    $this->error('操作失败');
                }
            }else{
                $this->error('修改失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 1);
            $this->success("会员启用成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
}
