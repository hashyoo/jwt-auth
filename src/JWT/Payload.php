<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2020/6/30 0030
 * Time: 18:00
 */

namespace HashyooJWTAuth\JWT;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class Payload extends Base
{

    private $guard;//guard
    private $jwt_ttl_time;//token有效期

    public function __construct($guard, $provider)
    {
        parent::__construct();
        $this->guard = $guard;
        $jwt_ttl            = !isset($provider['ttl']) ? $this->config['ttl'] : $provider['ttl'];
        $this->jwt_ttl_time = $jwt_ttl * (60 * 60);
    }

    /**
     * base64 json转码
     *
     * @param $data
     *
     * @return mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    private function base64_json_encode($data)
    {
        $result = str_replace('=', '', base64_encode(json_encode($data)));
        return $result;
    }

    /**
     * payload
     *
     * @param int $n_user_id
     *
     * @return string
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function get_payload($n_user_id = 0)
    {
        /* playload-header */
        $arr_data       = [
          'typ' => 'hashyoo-jwt-auth',
          'alg' => $this->config['algo'],
        ];
        $payload_header = $this->base64_json_encode($arr_data);

        /* playload-claim */
        $now_time      = time();
        $ttl_time      = $this->jwt_ttl_time;
        $arr_data      = [
          'iat' => $now_time,
          'exp' => $now_time + $ttl_time,
          'lft' => $ttl_time,
          'mdl' => $this->guard['provider'],
          'sub' => $n_user_id,
        ];
        $payload_claim = $this->base64_json_encode($arr_data);

        /* playload */
        $payload = $payload_header . '.' . $payload_claim;
        return $payload;
    }


}