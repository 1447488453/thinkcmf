<?php
namespace app\api\controller;
use think\Db;
class CommonController{

    /**
     * 生成数字和字母
     *
     * @param int $len 长度
     * @return string
     */
    public static function alnum($len = 6)
    {
        return self::build('alnum', $len);
    }
    /**
     * 获取全球唯一标识
     * @return string
     */
    public static function uuid()
    {
        return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }


    /**
     * 获取当前登录用户的id
     * @return int
     */
    public function getUserId($token){

        $user_id = Db::name('user_token')->where("token='$token'")->value('user_id');
     
        if (empty($user_id)) {
            return json(['error' => 10001, 'msg' => '用户未登录']);
        }
        return $user_id;
    }

    
    /**
     * 能用的随机数生成
     * @param string $type 类型 alpha/alnum/numeric/nozero/unique/md5/encrypt/sha1
     * @param int $len 长度
     * @return string
     */
    public static function build($type = 'alnum', $len = 8){
        switch ($type)
        {
            case 'alpha':
            case 'alnum':
            case 'numeric':
            case 'nozero':
                switch ($type)
                {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                }
                return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
            case 'unique':
            case 'md5':
                return md5(uniqid(mt_rand()));
            case 'encrypt':
            case 'sha1':
                return sha1(uniqid(mt_rand(), TRUE));
        }
    }

    /**
     * 处理用户等级
    * @user_level(
     *     'user_id'   => '用户id',
     *     'level' => '运动等级',
     *     'step_num' => '步数',
     *     'add_score' => '奖励积分',
     *     'jb_score' => '完成基本步数奖励积分',
     * )
     */
    public function user_level($user_id,$level=0,$step_num=0,$add_score=0,$jb_score=0){
        // $range_days    = get_parameter_settings('range_days');//时间天数范围;
        $range_days    = strtotime(date("Y-m-d"),time())-24*60*60*get_parameter_settings('range_days');//N天前零点的时间戳

        $wn_step       = get_parameter_settings('wn_stepgoal');//蜗牛基本要求步数;
        $tz_step       = get_parameter_settings('tz_stepgoal');//兔子基本要求步数;
        $lb_step       = get_parameter_settings('lb_stepgoal');//猎豹基本要求步数;

        $wn_upgrade_days   = get_parameter_settings('wn_upgrade_days');//升级天数
        $tz_upgrade_days   = get_parameter_settings('tz_upgrade_days');//升级天数
        $lb_upgrade_days   = get_parameter_settings('lb_upgrade_days');//升级天数

        $wn_downgrade_days = get_parameter_settings('wn_downgrade_days');//降级天数
        $tz_downgrade_days = get_parameter_settings('tz_downgrade_days');//降级天数
        $lb_downgrade_days = get_parameter_settings('lb_downgrade_days');//降级天数

        if($level==0){
            //增加积分
            if($step_num>=$wn_step){
                Db::name('user')->where('id',$user_id)->setInc('score',$add_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$add_score);
                $data['user_id']        = $user_id;
                $data['num']            = $add_score;
                $result = $this->reward_score( $data['user_id'],'步行',$data['num']);
            }
            //改变等级
            if($tz_step>$step_num&&$step_num>= $wn_step){
                $wn_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num>=$wn_step and is_valid=1" )->count('id');
                //判断是否达到了蜗牛达人的要求
                if($wn_count_days>=$wn_upgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level+1);//达到蜗牛达人
                }
            }elseif($lb_step>$step_num&&$step_num>=$tz_step){
                $tz_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num>=$tz_step and is_valid=1" )->count('id');
                //判断是否达到了兔子达人的要求
                if($tz_count_days>=$tz_upgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level+2);//达到兔子达人
                }
            }elseif($step_num>=$lb_step){
                $lb_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num>=$lb_step and is_valid=1" )->count('id');
                //判断是否达到了猎豹达人的要求
                if($lb_count_days>=$lb_upgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level+3);//达到猎豹达人
                }
            }
        }elseif($level==1){
            //增加积分
            $data['user_id']        = $user_id;
            if($tz_step>$step_num&&$step_num>=$wn_step){
                Db::name('user')->where('id',$user_id)->setInc('score',$jb_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$jb_score);
                $data['num']            = $jb_score;
            }elseif($step_num>=$tz_step){
                Db::name('user')->where('id',$user_id)->setInc('score',$add_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$add_score);
                $data['num']            = $add_score;
            }
            if($step_num>=$wn_step){
                $result = $this->reward_score($data['user_id'],'步行',$data['num']);
            }
            //改变等级
            if($lb_step>$step_num&&$step_num>=$tz_step){
               $tz_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num>=$tz_step and is_valid=1" )->count('id');
                //判断是否达到了兔子达人的要求
                if($tz_count_days>=$tz_upgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level+1);//达到兔子达人
                } 
            }elseif($step_num>=$lb_step){
                $lb_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num>=$lb_step and is_valid=1" )->count('id');
                //判断是否达到了猎豹达人的要求
                if($lb_count_days>=$lb_upgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level+2);//达到猎豹达人
                }
            }elseif($step_num<$wn_step){
                $step_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num<$wn_step and is_valid=1" )->count('id');
                if($step_count_days>=$wn_downgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level-1);//降为普通会员
                }
            }  
        }elseif($level==2){
            //增加积分
            $data['user_id']        = $user_id;
            if($tz_step>$step_num&&$step_num>=$wn_step){
                $wn_jb_score   = get_parameter_settings('wn_jb_score');//蜗牛奖励积分
                Db::name('user')->where('id',$user_id)->setInc('score',$wn_jb_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$wn_jb_score);
                $data['num']            = $wn_jb_score;
            }elseif($lb_step>$step_num&&$step_num>=$tz_step){
                Db::name('user')->where('id',$user_id)->setInc('score',$jb_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$jb_score);
                $data['num']            = $jb_score;
            }elseif($step_num>=$lb_step){
                Db::name('user')->where('id',$user_id)->setInc('score',$add_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$add_score);
                $data['num']            = $add_score; 
            }
            if($step_num>=$wn_step){
                $result = $this->reward_score($data['user_id'],'步行',$data['num']);
            }
            //改变等级
            if($step_num>=$lb_step){
                $lb_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num>=$lb_step and is_valid=1" )->count('id');
                //判断是否达到了猎豹达人的要求
                if($lb_count_days>=$lb_upgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level+1);//达到猎豹达人
                }
            }elseif ($step_num>=$wn_step&&$step_num<$tz_step) {
                $step_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num<$tz_step and is_valid=1" )->count('id');
                if($step_count_days>=$tz_downgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level-1);//降为蜗牛会员
                }
            }elseif($step_num<$wn_step){
                $step_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num<$wn_step and is_valid=1" )->count('id');
                if($step_count_days>=$wn_downgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level-2);//降为普通会员
                }
            }
        }elseif($level==3){
            //增加积分
            $data['user_id']        = $user_id;
            if($tz_step>$step_num&&$step_num>=$wn_step){
                $wn_jb_score   = get_parameter_settings('wn_jb_score');//蜗牛奖励积分
                Db::name('user')->where('id',$user_id)->setInc('score',$wn_jb_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$wn_jb_score);
                $data['num']            = $wn_jb_score;
            }elseif($lb_step>$step_num&&$step_num>=$tz_step){
                $tz_jb_score   = get_parameter_settings('tz_jb_score');//兔子奖励积分
                Db::name('user')->where('id',$user_id)->setInc('score',$tz_jb_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$tz_jb_score);
                $data['num']            = $tz_jb_score;
            }elseif($step_num>=$lb_step){//猎豹奖励积分
                Db::name('user')->where('id',$user_id)->setInc('score',$jb_score);
                Db::name('user')->where('id',$user_id)->setInc('total_score',$jb_score);
                $data['num']            = $jb_score;
            }
            if($step_num>=$wn_step){
                $result = $this->reward_score($data['user_id'],'步行',$data['num']);
            }
            //改变等级
            if($step_num>=$tz_step&&$step_num<$lb_step){
                $step_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num<$lb_step and is_valid=1" )->count('id');
                if($step_count_days>=$lb_downgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level-1);//降为兔子会员
                } 
            }elseif($step_num>=$wn_step&&$step_num<$tz_step){
                $step_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num<$tz_step and is_valid=1" )->count('id');
                if($step_count_days>=$tz_downgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level-2);//降为蜗牛会员
                }
            }elseif($step_num<$wn_step){
                $step_count_days = Db::name('user_run')->where("add_time >=$range_days and user_id= $user_id and step_num<$wn_step and is_valid=1" )->count('id');
                if($step_count_days>=$wn_downgrade_days){
                    $res = Db::name('user')->where('id',$user_id)->setfield('level',$level-3);//降为普通会员
                }
            }
        }
        
    }
    /**
     * 记录奖励积分
     */ 
    public function reward_score($user_id,$reason='',$num){
        $data_log['user_id']    = $user_id;
        $data_log['add_time']   = time();
        $data_log['status']     = 1;
        $data_log['reason']     = $reason;
        $res = Db::name('score_log')->insert($data_log);
        if($res){
            return 1;
        }else{
            return 0;
        }
    }


    /**
     * 上传单文件
     * @param string $file 数组
     * @param string $dir 保存目录名
     * @return string
     */
    public function upload($file,$dir=''){
        // 获取表单上传文件 例如上传了001.jpg
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['size'=>115678,'ext'=>'jpg,png'])->move(ROOT_PATH . 'public' . DS . 'upload'. DS . $dir);
            if($info){
                // 成功上传后 获取上传信息
                $data['error']=0;
                $data['url'] = $info->getSaveName();
                return $data;
            }else{
                // 上传失败获取错误信息
                $data['error']=-1;
                $data['msg'] = $file->getError();
                return $data;
                // return $file->getError();
            }
        }
    }

    /**
     * 上传单文件 base64
     * @param string $file 数组
     * @param string $dir 保存目录名
     * @return string
     */
    public function up_image($data,$dir=''){
        $base64_img = trim($data);
        $up_dir = './upload/'.$dir.'/';//存放在当前目录的upload文件夹下
        
        if(!file_exists($up_dir)){
          mkdir($up_dir,0777);
        }
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
        $type = $result[2];
        if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
        $new_file = $up_dir.date('YmdHis_').mt_rand(1000,9999).'.'.$type;
        if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
          $img_path = str_replace('../../..', '', $new_file);
          // echo '图片上传成功</br>![](' .$img_path. ')';
            $data_img['error']=0;
            $data_img['url'] = $img_path;
            return $data_img;
        }else{
            $data_img['error']=-1;
            $data_img['msg'] = "图片上传失败";
            return $data_img;
        }
          }else{
            //文件类型错误
            $data_img['error']=-1;
            $data_img['msg'] = "图片上传类型错误";
            return $data_img;
          }
        }else{
            $data_img['error']=-1;
            $data_img['msg'] ="文件错误";
            return $data_img;
        }
    }


}

?>