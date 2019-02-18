<?php
/**
 * Created by PhpStorm.
 * User: lucky.li
 * Date: 2019/2/18
 * Time: 11:23
 */
namespace Opensite;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;
/**
 * http处理类
 * Class Http
 * @package Opensite
 */
class Http
{
    /**
     * @var array 对于开启IPV6的 按照IPV4 解析
     */
    protected static $globals = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ]
    ];
    /**
     * @var http client对象
     */
    protected static $client;
    /**
     * @var array
     */
    protected static $default = [];

    /**
     * @var $access_token
     */
    protected static $access_token;
    /**
     * @var array $headers
     */
    protected static $headers = [];
    /**
     * @var bool 是否需要http基础认证
     */
    protected static $auth = false;

    /**
     * 设置access_token
     * @param string $access_token
     */
    public static function setAccessToken($access_token = '')
    {
        self::$access_token = $access_token;
    }

    /**
     * 设置请求头
     * @param array $headers
     */
    public static function setHeaders($headers = [])
    {
        self::$headers = $headers;
    }

    /**
     * 获取请求header头
     * @return array
     */
    public static function getHeaders()
    {
        return self::$headers;
    }

    /**
     * 设置默认请求参数
     * @param array $default
     */
    public static function setDefaultOptions($default = [])
    {
        self::$default = array_merge(self::$globals,$default);
    }

    /**
     * 获取默认请求参数
     * @param array $default
     * @return array
     */
    public static function getDefaultOptions()
    {
        return self::$default;
    }
    /**
     * GET 请求
     * @param $url string 请求url地址
     * @param array $options 请求参数
     * @return mixed
     */
    protected static function get($url,$options = [])
    {
        return self::request($url,'GET',['body' => $options]);
    }

    /**
     * POST请求
     * @param $url string 请求url地址
     * @param array $options 请求参数
     * @return mixed
     */
    protected static function post($url,$options = [])
    {
        return self::request($url,'POST',['form_params' => $options]);
    }

    /**
     * json格式请求
     * @param $url string 请求的url地址
     * @param string $options json请求数据
     * @param array $queries get 请求参数
     * @param int $encodeOption
     * @return mixed
     */
    protected static function json($url,$options = '{}',$queries = [],$encodeOption = JSON_UNESCAPED_UNICODE)
    {
        is_array($options) && $options = json_encode($options,$encodeOption);
        return self::request($url,'POST',['query' => $queries,'json'=>$options]);
    }

    /**
     * 上传文件
     * @param $url string 请求的url地址
     * @param array $files 要上传的文件
     * @param array $forms form表单提交的数据
     * @param array $queries url携带的参数
     * @return mixed
     */
    protected static function upload($url,$files = [],$forms = [],$queries = [])
    {
        $multipart = [];
        //遍历上传文件
        foreach ($files as $name => $path){
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path,'r')
            ];
        }
        //处理form表单处理数据
        foreach ($forms as $name => $content){
            $multipart[] = compact($name,$content);
        }
        return self::request($url,'POST',['query' => $queries,'multipart' => $multipart]);
    }

    /**
     * @param $url string 请求url地址
     * @param string $method 请求方法
     * @param array $options 请求参数
     * @return mixed
     */
    protected static function request($url,$method = 'GET',$options = [])
    {
        $method = strtoupper($method);
        $options = array_merge(self::$default,$options);
        //是否设置请求头
        if (self::$headers){
            $options = array_merge(['headers' => self::$headers],$options);
        }
        $response = self::getClient()->request($method,$url,$options);
        return $response;
    }

    /**
     * @param $method
     * @param array $args
     * @return bool|mixed
     */
    public static function HTTPRequest($method,$args = [])
    {
        $response = call_user_func_array([__CLASS__,$method],$args);
        if (!($response instanceof ResponseInterface)){
            return false;
        }
        $body = $response->getBody();
        if (!$body){
            return false;
        }
        $contents = $body->getContents();

        $result = json_decode($contents);

        if (json_last_error() != JSON_ERROR_NONE){
            return false;
        }
        return $result;
    }

    /**
     * 获取 httpClient对象
     * @return HttpClient|Http
     */
    public static function getClient()
    {
        if (!(self::$client instanceof HttpClient)){
            self::$client = new HttpClient();
        }
        return self::$client;
    }

    /**
     * 设置http对象
     * @param HttpClient $client
     * @return HttpClient|Http
     */
    public function setClient(HttpClient $client)
    {
        self::$client = $client;
        return self::$client;
    }

}
