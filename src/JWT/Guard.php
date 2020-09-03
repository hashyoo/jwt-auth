<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2020/6/30 0030
 * Time: 18:00
 */

namespace HashyooJWTAuth\JWT;

use Illuminate\Support\Facades\Hash;

class Guard extends Base
{
//
    private $defaults_guard;//

    private $guards;//

    private $providers;//
////
////    private $guard;//当前使用的guard
//
////    private $provider;//当前使用的provider
////
////    private $user;//用户信息
////
////    private $token;//用户token
////
////    private $new_jwt_model;//JwtModel实例化
////
////    private $new_token;//Token实例化
////
////    private $redis_key_user;//用户redis前缀
////
////    private $redis_key_token;//token redis前缀
//
//    private $password_key;//密码字段名称
    private $guard;//
    private $provider;//
//    private $new_jwt_model;//
//    private $new_token;//
//    private $user;//
//    private $token;//
//
    public function __construct($module = '')
    {
        parent::__construct();
//
        $config = $this->config;
        $this->defaults_guard       = $config['defaults']['guard'];
        $this->guards       = $config['guards'];
        $this->providers       = $config['providers'];

     /*   if (is_null($this->defaults_guard)) {
            throw new \Exception('没有设置默认的guard');
        }*/

        /*设置guard和provider*/
        $module   =  empty($module) ? $this->defaults_guard : $module;
        $guard    = $this->guards[$module];
        $provider = $this->providers[$guard['provider']];
     /*   if (is_null($guard)) {
            throw new \Exception('没有找到对应的guard');
        }
        if (is_null($provider)) {
            throw new \Exception('没有找到对应的provider');
        }*/

        $this->guard    = $guard;
        $this->provider = $provider;
        $this->provider['pass_key'] = !empty($this->provider['pass_key']) ? $this->provider['pass_key'] : 'password';

//
//        /* Model */
//        $this->new_jwt_model = new Model($this->provider);
//        $this->new_token     = new Token($guard,$provider);
////
////        /* redis */
////        $this->redis_key_user  = $this->redis_user_prefix . $this->guard['provider'] . '_';
////        $this->redis_key_token = $this->redis_token_prefix . $this->guard['provider'] . '_';
    }

    public function get_password_key(){
//        $this->password_key = $password_key;
        return $this->provider['pass_key'];
    }

    public function get_provider(){
        return $this->provider;
    }

    public function get_guard(){
        return $this->guard;

    }
//
//    /**
//     *
//     *
//     * @return $this
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    public function jwt_guard($module = ''){
//
//        /*设置guard和provider*/
//        $module   =  empty($module) ? $this->defaults_guard : $module;
//        $guard    = $this->guards[$module];
//        $provider = $this->providers[$guard['provider']];
//
////        if (is_null($guard)) {
////            throw new \Exception('没有找到对应的guard');
////        }
////        if (is_null($provider)) {
////            throw new \Exception('没有找到对应的provider');
////        }
//
//        $this->guard    = $guard;
//        $this->provider = $provider;
//        $this->provider['pass_key'] = !empty($this->provider['pass_key']) ? $this->provider['pass_key'] : 'password';
//        $this->password_key = $this->provider['pass_key'];
////        dd($this->provider);
//
//
//
//
//        /* Model */
//        $this->new_jwt_model = new Model($this->provider);
////        return $this;
////        dd($this);
//    }
//
//
//    /**
//     * 设置token
//     *
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    private function set_token($n_uid = 0)
//    {
//        //创建token
//        $this->token = $this->new_token->create_token($n_uid);
//        $this->redis_set_user($n_uid);
//    }
//
//
//    /**
//     * redis存储用户
//     *
//     * @param $n_user_id
//     *
//     * @return mixed
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    private function redis_set_user($n_user_id)
//    {
//        $redis_key    = $this->redis_key_user . $n_user_id;
//        $n_redis_db   = $this->redis_db;
//        $arr_user     = $this->new_jwt_model->find($n_user_id);
//        $n_expiretime = $this->get_user_expire();
//        predis_str_set($redis_key, $arr_user, $n_expiretime, $n_redis_db);
//
//        return $arr_user;
//    }
//
//    /**
//     * 获取用户信息
//     *
//     * @return mixed|null
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    private function get_user()
//    {
//        $n_user_id = $this->user_id();
//        if ($n_user_id === null) {
//            return null;
//        }
//
//        $redis_key  = $this->redis_key_user . $n_user_id;
//        $n_redis_db = $this->redis_db;
//        $arr_user   = predis_str_get($redis_key, $n_redis_db);
//        if (is_null($arr_user)) {
//            $arr_user     = $this->new_jwt_model->find($n_user_id);
//            $n_expiretime = $this->get_user_expire();
//            predis_str_set($redis_key, $arr_user, $n_expiretime, $n_redis_db);
//        }
//
//        return $arr_user;
//    }
//
    /**
     * 尝试登录
     *
     * @param array $login_data
     *
     * @return bool
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    protected function jwt_attempt($login_data = [])
    {
        /* 判断登录数据有没有密码字段 */
        $password_key = $this->password_key;
        if (!array_key_exists($password_key, $login_data)) {
            return false;
        }

        /* 查询用户并验证 */
        $s_pass      = $login_data[$password_key];
        $arr_wherein = yoo_array_remove($login_data, [$password_key]);
        $user        = $this->new_jwt_model->get_one($arr_wherein);
        if (is_null($user)) {
            return false;
        }
        $n_uid = intval($user['id']);
        if($n_uid <= 0){
            return false;
        }

        //验证密码
        if (Hash::check($s_pass, $user[$password_key]) !== true) {
            return false;
        }

        //设置token
        $this->user = $user;
        $this->set_token($n_uid);
        return $this->token;
    }
//
//    /**
//     * 刷新token
//     *
//     * @return mixed
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    public function refresh_token()
//    {
//        $n_user_id        = $this->user_id();
//        $this->set_token($n_user_id);
//        $this->redis_set_user($n_user_id);
//        return $this->token;
//
//    }
//
//    /**
//     * 检测登录状态
//     *
//     * @return bool
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    public function check()
//    {
//        $result = $this->new_token->check_token();
//        return $result;
//    }
//
//    /**
//     * 获取用户id
//     *
//     * @return int|null
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    public function user_id()
//    {
//        if ($this->check() !== true) {
//            return null;
//        }
//        $n_userid = $this->new_token->get_user_id();
//        return $n_userid;
//    }
//
//    /**
//     * 获取用户信息
//     *
//     * @return mixed|null
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    public function user()
//    {
//        $arr_user = $this->get_user();
//        return $arr_user;
//    }
//
//    /**
//     * 用户退出登录
//     *
//     * @return bool
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    public function loginout()
//    {
//        $n_userid = $this->user_id();
//        if ($n_userid === null) {
//            return true;
//        }
//
//        $redis_key = $this->redis_key_token . $n_userid;
//        $n_db      = $this->redis_db;
//        $result    = predis_str_del($redis_key, $n_db);
//        if ($result) {
//            return true;
//        }
//        else {
//            return false;
//        }
//    }

}