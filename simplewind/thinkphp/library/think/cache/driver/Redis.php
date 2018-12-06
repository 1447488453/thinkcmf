<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\cache\driver;

use think\cache\Driver;

/**
 * Redis缓存驱动，适合单机部署、有前端代理实现高可用的场景，性能最好
 * 有需要在业务层实现读写分离、或者使用RedisCluster的需求，请使用Redisd驱动
 *
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 * @author    尘缘 <130775@qq.com>
 */
class Redis extends Driver
{
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Redis;
        if ($this->options['persistent']) {
            $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
        } else {
            $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
        }

        if ('' != $this->options['password']) {
            $this->handler->auth($this->options['password']);
        }

        if (0 != $this->options['select']) {
            $this->handler->select($this->options['select']);
        }
    }

    /**
     * 判断缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return $this->handler->exists($this->getCacheKey($name));
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $value = $this->handler->get($this->getCacheKey($name));
        if (is_null($value) || false === $value) {
            return $default;
        }

        try {
            $result = 0 === strpos($value, 'think_serialize:') ? unserialize(substr($value, 16)) : $value;
        } catch (\Exception $e) {
            $result = $default;
        }

        return $result;
    }

    /**
     * 写入缓存
     * @access public
     * @param string            $name 缓存变量名
     * @param mixed             $value  存储数据
     * @param integer|\DateTime $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        if ($this->tag && !$this->has($name)) {
            $first = true;
        }
        $key   = $this->getCacheKey($name);
        $value = is_scalar($value) ? $value : 'think_serialize:' . serialize($value);
        if ($expire) {
            $result = $this->handler->setex($key, $expire, $value);
        } else {
            $result = $this->handler->set($key, $value);
        }
        isset($first) && $this->setTagItem($key);
        return $result;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        $key = $this->getCacheKey($name);

        return $this->handler->incrby($key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        $key = $this->getCacheKey($name);

        return $this->handler->decrby($key, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->handler->delete($this->getCacheKey($name));
    }

    /**
     * 清除缓存
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                $this->handler->delete($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        }
        return $this->handler->flushDB();
    }

     /*****************hash表操作函数*******************/
     
    /**
     * 得到hash表中一个字段的值
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return string|false
     */
    public function hGet($key,$field)
    {
        return $this->handler->hGet($key,$field);
    }
     
    /**
     * 为hash表设定一个字段的值
     * @param string $key 缓存key
     * @param string  $field 字段
     * @param string $value 值。
     * @return bool 
     */
    public function hSet($key,$field,$value)
    {
        return $this->handler->hSet($key,$field,$value);
    }
     
    /**
     * 判断hash表中，指定field是不是存在
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return bool
     */
    public function hExists($key,$field)
    {
        return $this->handler->hExists($key,$field);
    }
     
    /**
     * 删除hash表中指定字段 ,支持批量删除
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return int
     */
    public function hdel($key,$field)
    {
        $fieldArr=explode(',',$field);
        $delNum=0;
 
        foreach($fieldArr as $row)
        {
            $row=trim($row);
            $delNum+=$this->handler->hDel($key,$row);
        }
 
        return $delNum;
    }
     
    /**
     * 返回hash表元素个数
     * @param string $key 缓存key
     * @return int|bool
     */
    public function hLen($key)
    {
        return $this->handler->hLen($key);
    }
     
    /**
     * 为hash表设定一个字段的值,如果字段存在，返回false
     * @param string $key 缓存key
     * @param string  $field 字段
     * @param string $value 值。
     * @return bool
     */
    public function hSetNx($key,$field,$value)
    {
        return $this->handler->hSetNx($key,$field,$value);
    }
     
    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array $value
     * @return array|bool
     */
    public function hMset($key,$value)
    {
        if(!is_array($value))
            return false;
        return $this->handler->hMset($key,$value); 
    }
     
    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array|string $value string以','号分隔字段
     * @return array|bool
     */
    public function hMget($key,$field)
    {
        if(!is_array($field))
            $field=explode(',', $field);
        return $this->handler->hMget($key,$field);
    }
     
    /**
     * 为hash表设这累加，可以负数
     * @param string $key
     * @param int $field
     * @param string $value
     * @return bool
     */
    public function hIncrBy($key,$field,$value)
    {
        $value=intval($value);
        return $this->handler->hIncrBy($key,$field,$value);
    }
     
    /**
     * 返回所有hash表的所有字段
     * @param string $key
     * @return array|bool
     */
    public function hKeys($key)
    {
        return $this->handler->hKeys($key);
    }
     
    /**
     * 返回所有hash表的字段值，为一个索引数组
     * @param string $key
     * @return array|bool
     */
    public function hVals($key)
    {
        return $this->handler->hVals($key);
    }
     
    /**
     * 返回所有hash表的字段值，为一个关联数组
     * @param string $key
     * @return array|bool
     */
    public function hGetAll($key)
    {
        return $this->handler->hGetAll($key);
    }
     
    /*********************有序集合操作*********************/
     
    /**
     * 给当前集合添加一个元素
     * 如果value已经存在，会更新order的值。
     * @param string $key
     * @param string $order 序号
     * @param string $value 值
     * @return bool
     */
    public function zAdd($key,$order,$value)
    {
        return $this->handler->zAdd($key,$order,$value);   
    }
     
    /**
     * 给$value成员的order值，增加$num,可以为负数
     * @param string $key
     * @param string $num 序号
     * @param string $value 值
     * @return 返回新的order
     */
    public function zinCry($key,$num,$value)
    {
        return $this->handler->zinCry($key,$num,$value);
    }
     
    /**
     * 删除值为value的元素
     * @param string $key
     * @param stirng $value
     * @return bool
     */
    public function zRem($key,$value)
    {
        return $this->handler->zRem($key,$value);
    }
     
    /**
     * 集合以order递增排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function zRange($key,$start,$end)
    {
        return $this->handler->zRange($key,$start,$end);
    }
     
    /**
     * 集合以order递减排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function zRevRange($key,$start,$end)
    {
        return $this->handler->zRevRange($key,$start,$end);
    }
     
    /**
     * 集合以order递增排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function zRangeByScore($key,$start='-inf',$end="+inf",$option=array())
    {
        return $this->handler->zRangeByScore($key,$start,$end,$option);
    }
     
    /**
     * 集合以order递减排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function zRevRangeByScore($key,$start='-inf',$end="+inf",$option=array())
    {
        return $this->handler->zRevRangeByScore($key,$start,$end,$option);
    }
     
    /**
     * 返回order值在start end之间的数量
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public function zCount($key,$start,$end)
    {
        return $this->handler->zCount($key,$start,$end);
    }
     
    /**
     * 返回值为value的order值
     * @param unknown $key
     * @param unknown $value
     */
    public function zScore($key,$value)
    {
        return $this->handler->zScore($key,$value);
    }
     
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param unknown $key
     * @param unknown $value
     */
    public function zRank($key,$value)
    {
        return $this->handler->zRank($key,$value);
    }
     
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param unknown $key
     * @param unknown $value
     */
    public function zRevRank($key,$value)
    {
        return $this->handler->zRevRank($key,$value);
    }
     
    /**
     * 删除集合中，score值在start end之间的元素　包括start end
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     * @return 删除成员的数量。
     */
    public function zRemRangeByScore($key,$start,$end)
    {
        return $this->handler->zRemRangeByScore($key,$start,$end);
    }
     
    /**
     * 返回集合元素个数。
     * @param unknown $key
     */
    public function zCard($key)
    {
        return $this->handler->zCard($key);
    }
    /*********************队列操作命令************************/
     
    /**
     * 在队列尾部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function rPush($key,$value)
    {
        return $this->handler->rPush($key,$value); 
    }
     
    /**
     * 在队列尾部插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function rPushx($key,$value)
    {
        return $this->handler->rPushx($key,$value);
    }
     
    /**
     * 在队列头部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function lPush($key,$value)
    {
        return $this->handler->lPush($key,$value);
    }
     
    /**
     * 在队列头插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function lPushx($key,$value)
    {
        return $this->handler->lPushx($key,$value);
    }
     
    /**
     * 返回队列长度
     * @param unknown $key
     */
    public function lLen($key)
    {
        return $this->handler->lLen($key); 
    }
     
    /**
     * 返回队列指定区间的元素
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public function lRange($key,$start,$end)
    {
        return $this->handler->lrange($key,$start,$end);
    }
     
    /**
     * 返回队列中指定索引的元素
     * @param unknown $key
     * @param unknown $index
     */
    public function lIndex($key,$index)
    {
        return $this->handler->lIndex($key,$index);
    }
     
    /**
     * 设定队列中指定index的值。
     * @param unknown $key
     * @param unknown $index
     * @param unknown $value
     */
    public function lSet($key,$index,$value)
    {
        return $this->handler->lSet($key,$index,$value);
    }
     
    /**
     * 删除值为vaule的count个元素
     * PHP-REDIS扩展的数据顺序与命令的顺序不太一样，不知道是不是bug
     * count<0 从尾部开始
     *  >0　从头部开始
     *  =0　删除全部
     * @param unknown $key
     * @param unknown $count
     * @param unknown $value
     */
    public function lRem($key,$value,$count)
    {
        return $this->handler->lRem($key,$value,$count);
    }
     
    /**
     * 删除并返回队列中的头元素。
     * @param unknown $key
     */
    public function lPop($key)
    {
        return $this->handler->lPop($key);
    }
     
    /**
     * 删除并返回队列中的尾元素
     * @param unknown $key
     */
    public function rPop($key)
    {
        return $this->handler->rPop($key);
    }
     
    /*************redis字符串操作命令*****************/

 
     
    /**
     * 设置一个key,如果key存在,不做任何操作.
     * @param unknown $key
     * @param unknown $value
     */
    public function setnx($key,$value)
    {
        return $this->handler->setnx($key,$value);
    }
     
    /**
     * 批量设置key
     * @param unknown $arr
     */
    public function mset($arr)
    {
        return $this->handler->mset($arr);
    }
     
    /*************redis　无序集合操作命令*****************/
     
    /**
     * 返回集合中所有元素
     * @param unknown $key
     */
    public function sMembers($key)
    {
        return $this->handler->sMembers($key);
    }
     
    /**
     * 求2个集合的差集
     * @param unknown $key1
     * @param unknown $key2
     */
    public function sDiff($key1,$key2)
    {
        return $this->handler->sDiff($key1,$key2);
    }
     
    /**
     * 添加集合。由于版本问题，扩展不支持批量添加。这里做了封装
     * @param unknown $key
     * @param string|array $value
     */
    public function sAdd($key,$value)
    {
        if(!is_array($value))
            $arr=array($value);
        else
            $arr=$value;
        foreach($arr as $row)
            $this->handler->sAdd($key,$row);
    }
     
    /**
     * 返回无序集合的元素个数
     * @param unknown $key
     */
    public function scard($key)
    {
        return $this->handler->scard($key);
    }
     
    /**
     * 从集合中删除一个元素
     * @param unknown $key
     * @param unknown $value
     */
    public function srem($key,$value)
    {
        return $this->handler->srem($key,$value);
    }
     
    /*************redis管理操作命令*****************/
     
    /**
     * 选择数据库
     * @param int $dbId 数据库ID号
     * @return bool
     */
    public function select($dbId)
    {
        $this->dbId=$dbId;
        return $this->handler->select($dbId);
    }
     
    /**
     * 清空当前数据库
     * @return bool
     */
    public function flushDB()
    {
        return $this->handler->flushDB();
    }
     
    /**
     * 返回当前库状态
     * @return array
     */
    public function info()
    {
        return $this->handler->info();
    }
     
    /**
     * 同步保存数据到磁盘
     */
    public function save()
    {
        return $this->handler->save();
    }
     
    /**
     * 异步保存数据到磁盘
     */
    public function bgSave()
    {
        return $this->handler->bgSave();
    }
     
    /**
     * 返回最后保存到磁盘的时间
     */
    public function lastSave()
    {
        return $this->handler->lastSave();
    }
     
    /**
     * 返回key,支持*多个字符，?一个字符
     * 只有*　表示全部
     * @param string $key
     * @return array
     */
    public function keys($key)
    {
        return $this->handler->keys($key);
    }
     
    /**
     * 删除指定key
     * @param unknown $key
     */
    public function del($key)
    {
        return $this->handler->del($key);
    }
     
    /**
     * 判断一个key值是不是存在
     * @param unknown $key
     */
    public function exists($key)
    {
        return $this->handler->exists($key);
    }
     
    /**
     * 为一个key设定过期时间 单位为秒
     * @param unknown $key
     * @param unknown $expire
     */
    public function expire($key,$expire)
    {
        return $this->handler->expire($key,$expire);
    }
     
    /**
     * 返回一个key还有多久过期，单位秒
     * @param unknown $key
     */
    public function ttl($key)
    {
        return $this->handler->ttl($key);
    }
     
    /**
     * 设定一个key什么时候过期，time为一个时间戳
     * @param unknown $key
     * @param unknown $time
     */
    public function exprieAt($key,$time)
    {
        return $this->handler->expireAt($key,$time);
    }
     
    /**
     * 关闭服务器链接
     */
    public function close()
    {
        return $this->handler->close();
    }
     
    /**
     * 关闭所有连接
     */
    public static function closeAll()
    {
        foreach(static::$_instance as $o)
        {
            if($o instanceof self)
                $o->close();
        }
    }
     
    /** 这里不关闭连接，因为session写入会在所有对象销毁之后。
    public function __destruct()
    {
        return $this->handler->close();
    }
    **/
    /**
     * 返回当前数据库key数量
     */
    public function dbSize()
    {
        return $this->handler->dbSize();
    }
     
    /**
     * 返回一个随机key
     */
    public function randomKey()
    {
        return $this->handler->randomKey();
    }
     
    /**
     * 得到当前数据库ID
     * @return int
     */
    public function getDbId()
    {
        return $this->dbId;
    }
     
    /**
     * 返回当前密码
     */
    public function getAuth()
    {
        return $this->auth;
    }
     
    public function getHost()
    {
        return $this->host;
    }
     
    public function getPort()
    {
        return $this->port;
    }
     
    public function getConnInfo()
    {
        return array(
            'host'=>$this->host,
            'port'=>$this->port,
            'auth'=>$this->auth
        );
    }
    /*********************事务的相关方法************************/
     
    /**
     * 监控key,就是一个或多个key添加一个乐观锁
     * 在此期间如果key的值如果发生的改变，刚不能为key设定值
     * 可以重新取得Key的值。
     * @param unknown $key
     */
    public function watch($key)
    {
        return $this->handler->watch($key);
    }
     
    /**
     * 取消当前链接对所有key的watch
     *  EXEC 命令或 DISCARD 命令先被执行了的话，那么就不需要再执行 UNWATCH 了
     */
    public function unwatch()
    {
        return $this->handler->unwatch();
    }
     
    /**
     * 开启一个事务
     * 事务的调用有两种模式Redis::MULTI和Redis::PIPELINE，
     * 默认是Redis::MULTI模式，
     * Redis::PIPELINE管道模式速度更快，但没有任何保证原子性有可能造成数据的丢失
     */
    public function multi($type=\Redis::MULTI)
    {
        return $this->handler->multi($type);
    }
     
    /**
     * 执行一个事务
     * 收到 EXEC 命令后进入事务执行，事务中任意命令执行失败，其余的命令依然被执行
     */
    public function exec()
    {
        return $this->handler->exec();
    }
     
    /**
     * 回滚一个事务
     */
    public function discard()
    {
        return $this->handler->discard();
    }
     
    /**
     * 测试当前链接是不是已经失效
     * 没有失效返回+PONG
     * 失效返回false
     */
    public function ping()
    {
        return $this->handler->ping();
    }
     
    public function auth($auth)
    {
        return $this->handler->auth($auth);
    }


    public function publish($redisChat,$message){
        return $this->handler->publish($redisChat,$message);
    }
    public function subscribe($patterns = array()){
        //return $this->handler->subscribe($patterns,$callback);
        // return $this->handler->subscribe($patterns,function($message,$channelName){
        //                 //    echo $message;
        //                 // unset($message, $channelName);
        //     $data['message'] = $message;
        //     Db::name('message')->insert($data);
        //             });

       return  $this->handler->subscribe($patterns,function($message, $channelName){echo 1111;});  

}


        /*********************自定义的方法,用于简化操作************************/
     
    /**
     * 得到一组的ID号
     * @param unknown $prefix
     * @param unknown $ids
     */
    public function hashAll($prefix,$ids)
    {
        if($ids==false)
            return false;
        if(is_string($ids))
            $ids=explode(',', $ids);
        $arr=array();
        foreach($ids as $id)
        {
            $key=$prefix.'.'.$id;
            $res=$this->handler->hGetAll($key);
            if($res!=false)
                $arr[]=$res;
        }
         
        return $arr;
    }
     
    /**
     * 生成一条消息，放在redis数据库中。使用0号库。
     * @param string|array $msg
     */
    public function pushMessage($lkey,$msg)
    {
        if(is_array($msg)){
            $msg    =    json_encode($msg);
        }
        $key    =    md5($msg);
         
        //如果消息已经存在，删除旧消息，已当前消息为准
        //echo $n=$this->lRem($lkey, 0, $key)."\n";
        //重新设置新消息
        $this->handler->lPush($lkey, $key);
        $this->handler->setex($key, 3600, $msg);
        return $key;
    }
     
     
    /**
     * 得到条批量删除key的命令
     * @param unknown $keys
     * @param unknown $dbId
     */
    public function delKeys($keys,$dbId)
    {
        $redisInfo=$this->handler->getConnInfo();
        $cmdArr=array(
            'redis-cli',
            '-a',
            $redisInfo['auth'],
            '-h',
            $redisInfo['host'],
            '-p',
            $redisInfo['port'],
            '-n',
            $dbId,
        );
        $redisStr=implode(' ', $cmdArr);
        $cmd="{$redisStr} KEYS \"{$keys}\" | xargs {$redisStr} del";
        return $cmd;
    }


}
