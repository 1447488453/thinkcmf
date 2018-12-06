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

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;

use think\Db;

/**
 * Class SettingController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   =>'设置',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 0,
 *     'icon'   =>'cogs',
 *     'remark' =>'系统设置入口'
 * )
 */
class SettingController extends AdminBaseController
{

    /**
     * 网站信息
     * @adminMenu(
     *     'name'   => '网站信息',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 0,
     *     'icon'   => '',
     *     'remark' => '网站信息',
     *     'param'  => ''
     * )
     */
    public function site()
    {
        $content = hook_one('admin_setting_site_view');

        if (!empty($content)) {
            return $content;
        }

        $noNeedDirs     = [".", "..", ".svn", 'fonts'];
        $adminThemesDir = config('cmf_admin_theme_path') . config('cmf_admin_default_theme') . '/public/assets/themes/';
        $adminStyles    = cmf_scan_dir($adminThemesDir . '*', GLOB_ONLYDIR);
        $adminStyles    = array_diff($adminStyles, $noNeedDirs);
        $cdnSettings    = cmf_get_option('cdn_settings');
        $cmfSettings    = cmf_get_option('cmf_settings');
        $adminSettings  = cmf_get_option('admin_settings');

        $this->assign('site_info', cmf_get_option('site_info'));
        $this->assign("admin_styles", $adminStyles);
        $this->assign("templates", []);
        $this->assign("cdn_settings", $cdnSettings);
        $this->assign("admin_settings", $adminSettings);
        $this->assign("cmf_settings", $cmfSettings);

        return $this->fetch();
    }

    /**
     * 网站信息设置提交
     * @adminMenu(
     *     'name'   => '网站信息设置提交',
     *     'parent' => 'site',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '网站信息设置提交',
     *     'param'  => ''
     * )
     */
    public function sitePost()
    {
        if ($this->request->isPost()) {
            $result = $this->validate($this->request->param(), 'SettingSite');
            if ($result !== true) {
                $this->error($result);
            }

            $options = $this->request->param('options/a');
            cmf_set_option('site_info', $options);

            $cmfSettings = $this->request->param('cmf_settings/a');

            $bannedUsernames                 = preg_replace("/[^0-9A-Za-z_\\x{4e00}-\\x{9fa5}-]/u", ",", $cmfSettings['banned_usernames']);
            $cmfSettings['banned_usernames'] = $bannedUsernames;
            cmf_set_option('cmf_settings', $cmfSettings);

            $cdnSettings = $this->request->param('cdn_settings/a');
            cmf_set_option('cdn_settings', $cdnSettings);

            $adminSettings = $this->request->param('admin_settings/a');

            $routeModel = new RouteModel();
            if (!empty($adminSettings['admin_password'])) {
                $routeModel->setRoute($adminSettings['admin_password'] . '$', 'admin/Index/index', [], 2, 5000);
            } else {
                $routeModel->deleteRoute('admin/Index/index', []);
            }

            $routeModel->getRoutes(true);

            cmf_set_option('admin_settings', $adminSettings);

            $this->success("保存成功！", '');

        }
    }

    /**
     * 密码修改
     * @adminMenu(
     *     'name'   => '密码修改',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '密码修改',
     *     'param'  => ''
     * )
     */
    public function password()
    {
        return $this->fetch();
    }

    /**
     * 密码修改提交
     * @adminMenu(
     *     'name'   => '密码修改提交',
     *     'parent' => 'password',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '密码修改提交',
     *     'param'  => ''
     * )
     */
    public function passwordPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();
            if (empty($data['old_password'])) {
                $this->error("原始密码不能为空！");
            }
            if (empty($data['password'])) {
                $this->error("新密码不能为空！");
            }

            $userId = cmf_get_current_admin_id();

            $admin = Db::name('user')->where(["id" => $userId])->find();

            $oldPassword = $data['old_password'];
            $password    = $data['password'];
            $rePassword  = $data['re_password'];

            if (cmf_compare_password($oldPassword, $admin['user_pass'])) {
                if ($password == $rePassword) {

                    if (cmf_compare_password($password, $admin['user_pass'])) {
                        $this->error("新密码不能和原始密码相同！");
                    } else {
                        Db::name('user')->where('id', $userId)->update(['user_pass' => cmf_password($password)]);
                        $this->success("密码修改成功！");
                    }
                } else {
                    $this->error("密码输入不一致！");
                }

            } else {
                $this->error("原始密码不正确！");
            }
        }
    }

    /**
     * 上传限制设置界面
     * @adminMenu(
     *     'name'   => '上传设置',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '上传设置',
     *     'param'  => ''
     * )
     */
    public function upload()
    {
        $uploadSetting = cmf_get_upload_setting();
        $this->assign('upload_setting', $uploadSetting);
        return $this->fetch();
    }

    /**
     * 上传限制设置界面提交
     * @adminMenu(
     *     'name'   => '上传设置提交',
     *     'parent' => 'upload',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '上传设置提交',
     *     'param'  => ''
     * )
     */
    public function uploadPost()
    {
        if ($this->request->isPost()) {
            //TODO 非空验证
            $uploadSetting = $this->request->post();

            cmf_set_option('upload_setting', $uploadSetting);
            $this->success('保存成功！');
        }

    }

    /**
     * 清除缓存
     * @adminMenu(
     *     'name'   => '清除缓存',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '清除缓存',
     *     'param'  => ''
     * )
     */
    public function clearCache(){
        $content = hook_one('admin_setting_clear_cache_view');

        if (!empty($content)) {
            return $content;
        }

        cmf_clear_cache();
        return $this->fetch();
    }


    public function admin_verificode_page(){
        $param = $this->request->param();       
        if(!isset($param['type'])){
            $this->error("请选择验证类型!");
        }
        $action = '';
        $type = $param['type'];


        switch ($type) {
            case 1:
                //药包充值
                $action = url('user/adminIndex/update_package');
                break;
            case 2:
                //代理审核
                $action = url('agent/AdminAgentApply/update_apply_status');
                break;
            case 3:
                //代理审核
                $action = url('agent/AdminAgentApply/one_key_adopt');
                break;
            case 5:
                //兑换一键审核
                $action = url('admin/score/one_key_adopt');
                break;
            case 6:
                //兑换审核
                $action = url('admin/score/audit');
                break;
            default:
                break;
        }        
        $more = '';
        if(isset($param['more'])){
            $more = $param['more'];
        }
        $value = '';
        if(isset($param['value'])){
            $value = $param['value'];
        }
        $this->assign("action", $action);//提交地址
        $this->assign("id", $param['id']);//ID操作的对象
        $this->assign("value", $value);//提交的值
        $this->assign("type", $type);//验证类型
        $this->assign("more", $more);//验证类型
        // $phone = get_parameter_settings('admin_phone');//用于展示
        // $this->assign("phone", $phone);

        return $this->fetch();
    }


    public function get_admin_verificode(){
        
        $param = $this->request->param();

        if(!isset($param['type'])){
            $this->error("请选择验证类型!");
        }
        $type = intval($param['type']);

        $code = cmf_random_number_string();
        $admin_phone = get_parameter_settings('admin_phone');
        // $admin_phone = 15757851183;
        $params = [];
          switch ($type){
            case 1:
                $params['code'] =$code;//管理员认证验证码
                $params['text'] = "【智能手环】您的验证码是".$code;//短信模板
                 $params['tpl_id'] = 1;
                break;
            case 2:
                $params['code'] =$code;//兑换审核验证码
                $params['text'] = "【智能手环】您的验证码是".$code;//短信模板
                $params['tpl_id'] = 1;
                break;
            case 3:
                $params['code'] =$code;//验证码
                $params['text'] = "【智能手环】您的验证码是".$code;//短信模板
                $params['tpl_id'] = 1;
                break;
            case 4:
                $params['code'] =$code;//参数设置验证码
                $params['text'] = "【智能手环】您的验证码是".$code;//短信模板
                $params['tpl_id'] = 1;
                break;            
        }
        $params['mobile'] = $admin_phone;

         //TODO 限制 每个ip 的发送次数
        $code_1 = cmf_get_verification_code($admin_phone);
        if (empty($code_1)) {
            $this->error("验证码发送过多,请明天再试!");
        }

        $result = sendSms($params);
        if(isset($result) && $result['code']==0){
            cmf_verification_code_log($admin_phone,$code);
            $this->success("验证码已发至管理员的手机，请等待管理员回复!");  
        } else{
            $this->error("验证码发送失败，请稍后再试!");
        }
    }

    public function parameter(){
        $parameterSettings  = cmf_get_option('parameter_settings'); 
        // echo "<pre>";
        // print_r($parameterSettings);exit; 
        $this->assign("templates", []);
        $this->assign("parameter_settings", $parameterSettings);
       
        session('old_admin_phone',$parameterSettings['admin_phone']);
        return $this->fetch();
    }

    public function parameterPost(){
        if ($this->request->isPost()) {
            $options = $this->request->param('options/a');
            // echo "<pre>";
            // print_r($options);exit;
            if(session('old_admin_phone')!=$options['admin_phone'] && !empty(session('old_admin_phone'))){
                $errMsg = cmf_check_verification_code(session('old_admin_phone'), $options['admin_phone_code']);
                if (!empty($errMsg)) {
                    $this->error($errMsg);
                }
            }
            // $use_time_range = explode(';', $options['use_time_range']);
            // $options['use_time_range'] = implode('~', $use_time_range);
            unset($options['old_admin_phone']);
            cmf_set_option('parameter_settings', $options);
            cmf_clear_cache();//更新缓存
            //cmf_verification_code_log($admin_phone,cmf_random_number_string());//成功之后立即更新验证码，防止再利用同一验证码进行二次操作
            $this->success("保存成功！", '');

        }
    }


}