<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2020/6/30 0030
 * Time: 18:00
 */

namespace HashyooJWTAuth\JWT;

use Illuminate\Support\Facades\Hash;

class JWT extends Base
{
//
//    private $defaults_guard;//
//
//    private $guards;//
//
//    private $providers;//
////
////    private $guard;//当前使用的guard
//
////    private $provider;//当前使用的provider
////
////    private $user;//用户信息
////
////    private $token;//用户token
////
////    private $model_query;//JwtModel实例化
////
////    private $model_token;//Token实例化
////
////    private $redis_key_user;//用户redis前缀
////
////    private $redis_key_token;//token redis前缀
//
//    private $password_key;//密码字段名称
//    private $guard;//
//    private $provider;//
    private $model_query;//
//    private $model_token;//
//    private $user;//
    //    private $token;//
    private $model_token;//
    private $model_guard;//

    public function __construct($module = '')
    {
        parent::__construct();
        $this->model_guard = new Guard($module);

        $provider = $this->model_guard->get_provider();
        $this->model_query = new Model($provider);

        $guard = $this->model_guard->get_guard();
        $this->model_token     = new Token($guard,$provider);


        //        $config = $this->config;
//        $this->defaults_guard       = $config['defaults']['guard'];
//        $this->guards       = $config['guards'];
//        $this->providers       = $config['providers'];
//
//        if (is_null($this->defaults_guard)) {
//            throw new \Exception('没有设置默认的guard');
//        }
//
//        /*设置guard和provider*/
//        $module   =  empty($module) ? $this->defaults_guard : $module;
//        $guard    = $this->guards[$module];
//        $provider = $this->providers[$guard['provider']];
//        if (is_null($guard)) {
//            throw new \Exception('没有找到对应的guard');
//        }
//        if (is_null($provider)) {
//            throw new \Exception('没有找到对应的provider');
//        }
//
//        $this->guard    = $guard;
//        $this->provider = $provider;
//        $this->provider['pass_key'] = !empty($this->provider['pass_key']) ? $this->provider['pass_key'] : 'password';
//        $this->password_key = $this->provider['pass_key'];
//
//        /* Model */
//        $this->model_query = new Model($this->provider);
//        $this->model_token     = new Token($guard,$provider);
////
////        /* redis */
////        $this->redis_key_user  = $this->redis_user_prefix . $this->guard['provider'] . '_';
////        $this->redis_key_token = $this->redis_token_prefix . $this->guard['provider'] . '_';
    }
//
//    /**
//     *
//     *
//     * @author wumengmeng <wu_mengmeng@foxmail.com>
//     */
//    public function guard($module = ''){
//        return $this;
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
//        $this->token = $this->model_token->create_token($n_uid);
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
//        $arr_user     = $this->model_query->find($n_user_id);
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
//            $arr_user     = $this->model_query->find($n_user_id);
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
    public function attempt($login_data = [])
    {
        /* 判断登录数据有没有密码字段 */
        $password_key = $this->model_guard->get_password_key();
        if (!array_key_exists($password_key, $login_data)) {
            return false;
        }

        /* 查询用户并验证 */
        $s_pass      = $login_data[$password_key];
        $arr_wherein = yoo_array_remove($login_data, [$password_key]);
        $user        = $this->model_query->get_one($arr_wherein);
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
        $this->model_token->create_token($n_uid);
        $token = $this->model_token->get_token();
        dd($token);
        //        $this->redis_set_user($n_uid);
        return $token;
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
//        $result = $this->model_token->check_token();
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
//        $n_userid = $this->model_token->get_user_id();
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