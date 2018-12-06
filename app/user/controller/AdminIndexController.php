<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;

/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class AdminIndexController extends AdminBaseController
{

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
    public function index(){
        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }

        $where   = [];
        $where['user_type'] =2;
        $request = input('request.');
        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['user_login|user_nickname|user_email|mobile']    = ['like', "%$keyword%"];
        }
        $usersQuery = Db::name('user');
        $list = $usersQuery->whereOr($keywordComplex)->where($where)->order("create_time DESC")->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
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
    public function ban(){
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
    public function cancelBan(){
        $id = input('param.id', 0, 'intval');
        if ($id) {
            Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 1);
            $this->success("会员启用成功！", '');
        }else{
            $this->error('数据传入失败！');
        }
    }
    /**
     * 实名审核列表
     */
    public function audit_list(){
        $status = $this->request->param('status', '', 'intval');
        $where   = [];
        if($status ==='0' ||$status&&$status!=-1){
            $where['status'] = intval($status);
        }
        $request = input('request.');
        if (!empty($request['uid'])) {
            $where['id'] = intval($request['uid']);
        }
        $keywordComplex = [];
        if(!empty($request['keyword'])){
            $keyword = $request['keyword'];
            $keywordComplex['real_name|id_card']    = ['like', "%$keyword%"];
        }
        $audit_list = Db::name('name_audit')->field('id,user_id,real_name,status,id_card,sfz_front_img,sfz_back_img,sfz_sc_img,add_time')->whereOr($keywordComplex)->where($where)->paginate(10);
        // 获取分页显示
        $page = $audit_list->render();
        $this->assign('status', $status);
        $this->assign('list', $audit_list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch('audit_list');
    }
    /**
     * 实名审核详情
     */
    public function audit_detial(){
        $id = input('param.id', 0, 'intval');
        $data = Db::name('name_audit')->where('id',$id)->find();
        $this->assign('data', $data);
        return $this->fetch('audit_detial');
    }
    /**
     * 实名审核改变状态
     */
    public function change_status(){
        $id = input('param.id', 0, 'intval');
        $status = input('param.status', 0, 'intval');
        $remark = input('param.remark');
        $data['remark'] = $remark;
        $data['status'] = $status;
        $data['examine_time'] = time();
        $res= Db::name('name_audit')->where('id',$id)->update($data);
        if($res!==false){
            $this->success("操作成功！", url('adminIndex/audit_list'));
        }else{
            $this->error('操作失败', url('adminIndex/audit_list'));
        }
    }

}
