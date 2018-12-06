<?php
namespace cmf\controller;
use think\Db;
use think\Request;
use think\Response;
use think\exception\HttpResponseException;
class ApiBaseController extends  BaseController{

    /**
     * @var Request Request 实例
     */
    protected $request;

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 验签
     * @var array
     */
    protected $noNeedSign = [];

        /**
     * 默认响应输出类型,支持json/xml
     * @var string 
     */
    protected $responseType = 'json';


    /**
     * 构造方法
     * @access public
     * @param Request $request Request 对象
     */
    public function __construct(Request $request = null)
    {
        $this->request = is_null($request) ? Request::instance() : $request;

        // 控制器初始化
        $this->_initialize();
    }


	    /**
     * 初始化操作
     * @access protected
     */
    protected function _initialize(){

// header('Content-type: application/json');

            if(!$this->match($this->noNeedLogin)){
                $params = $this->request->param();

                if(empty($params['token'])){
                    $this->error(('请先登入'), null, 403);
                    exit;
                }
                $res = Db::name('user_token')->field('token,user_id,expire_time')->where("token='$params[token]'")->find();
                if($res){
                    if($res['expire_time']<time()){
                        Db::name('user_token')->where("token='$params[token]'")->delete();
                        $this->error(('Token已过期,请重新登入'), null, 403);
                        exit;
                    }else{
                        $expiretime = time()+(30*24*60*60);
                        Db::name('user_token')->where("token='$params[token]'")->setField('expire_time',$expiretime);//更新token过期时间
                    }
                }else{
                    $this->error(('Token无效'), null, 403);
                    exit;
                }
            }
     
        // if(!$this->match($this->noNeedSign)){
        //     $secret = "baguznshGu5t9xGARNpq86cd98joQYCN3Cozk1qAbaguznsh";
        //     $params = $this->request->param();
        //     if(empty($params['sign'])){
        //         $this->error(('验签失败,没有签名值'), null, 403);
        //         exit;
        //     }
        //     if(!empty($params['tamp'])){
        //         if(time()-$params['tamp']>600){
        //             $this->error(('sign已经失效'), null, 403);
        //             exit;
        //         }
        //     }
              
        //         $val = $this->coverParamsToString($params);
              
        //     $sign = strtoupper(md5(md5($secret.$val)));//字符串转为大写
        //         // echo $sign; echo"<br>";

        //         // echo $params['sign'];exit;
          
        //     if($sign!==$params['sign']){
        //         $this->error(('验签失败'), null, 403);
        //         exit;
        //     }
        // }
    }



        /**
     * 操作失败返回的数据
     * @param string $msg   提示信息
     * @param mixed $data   要返回的数据
     * @param int   $code   错误码，默认为0
     * @param string $type  输出类型
     * @param array $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = null, $error = 0, $type = null, array $header = [])
    {
        $this->result($msg, $data, $error, $type, $header);
    }

        /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型，支持json/xml/jsonp
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {
        $result = [
            'error' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : $this->responseType);

        if (isset($header['statuscode']))
        {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        }
        else
        {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }


    /**
    * 数组 排序后转化为字体串
    *
    * @param array $params          
    * @return string
    */
    public function coverParamsToString($params) {
        $sign_str = '';
        // 排序
        ksort ( $params );
        foreach ( $params as $key => $val ) {
        if($key == 'sign') {
            continue;
        }
        else if($val!='')
        {
            $sign_str .= sprintf ( "%s=%s&", $key, $val );
        }
    }
        return substr ( $sign_str, 0, strlen ( $sign_str ) - 1 );
    }


        /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr)
        {
            return FALSE;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr))
        {
            return TRUE;
        }

        // 没找到匹配
        return FALSE;
    }

}
?>
