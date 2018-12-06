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
namespace app\api\validate;
use think\Validate;
class UserValidate extends Validate
{   

    protected $rule = [
        'mobile'    => 'require',
        'user_pass' => 'require|min:6|max:32',
        
    ];
    protected $message = [
        'mobile'            => '账号不能为空',
        'user_pass.require' => '密码不能为空',
        'user_pass.max'     => '密码不能超过32个字符',
        'user_pass.min'     => '密码不能小于6个字符',

    ];
    protected $scene = [
        'user_login'    =>['mobile','user_pass'],
        'user_register' =>['mobile','user_pass'],
    ];
}