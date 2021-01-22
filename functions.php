<?php
/**
 * Here is your custom functions.
 */

if (!function_exists('result_data')) {
    /**
     * 统一输出格式
     * @param $data //单个详情或多个数据列表
     * @param array $other_data 其他单独数据，如分页等信息 ['num'=>20, 'cur_page'=>1, 'total_num'=>0, 'total_page'=>0]
     * @param int $code 状态码
     * @param string $msg 成功或失败消息
     * @return \support\Response
     */
    function result_data($data, $other_data = array(), $code = 200, $msg = 'succ')
    {
        $re = array(
            'code' => $code,
            'msg' => $msg,
            'data' => empty($data) ? '' : $data,
        );

        if (!empty($other_data)) {
            $re = array_merge($re, $other_data);
        }

        $callback = isset ($_GET ['callback']) ? $_GET ['callback'] : '';

        if ($callback) {
            return jsonp($re, $callback);
        } else {
            return json($re);
        }
    }
}

if (!function_exists('crmResSuc')) {
    /**
     * crm成功返回（兼容旧版本）
     * @param string $data
     * @param string $msg
     * @param string $count
     * @return \support\Response
     */
    function crmResSuc($data = "", $msg = "", $count = "")
    {
        $res = array(
            'code' => 200,
            'msg' => $msg,
            'data' => $data,
            'count' => $count
        );

        return json($res);
    }
}

if (!function_exists('objToArray')) {
    /**
     * 对象转数组
     * @param $object
     * @return mixed
     */
    function objToArray($object)
    {
        return json_decode( json_encode( $object),true);
    }
}

if (!function_exists('encrypt_password')) {
    /**
     * crm加密方式（兼容旧版本）
     * @param $password
     * @param $password_salt
     * @return string
     */
    function encrypt_password($password, $password_salt)
    {
        return md5(md5($password) . md5($password_salt));
    }
}

if (!function_exists('my_random')) {

    /**
     * 生成随机数
     * @param $type 1:全数字；2：全字母；3：数字+字母
     * @param $max      //位数
     * @return string
     */
    function my_random($type, $max)
    {
        if (!is_numeric($max) || $max < 1 || $max > 16) {
            return '';
        }

        switch ($type) {
            case 1:
                $s = '';
                for ($i = 0; $i <= 5; $i++) {
                    $s .= '0123456789';
                }
                break;

            case 2:
                $s = '';
                for ($i = 0; $i <= 2; $i++) {
                    $s .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
                }
                break;

            case 3:
                $s = '';
                for ($i = 0; $i <= 2; $i++) {
                    $s .= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
                }

                break;
            default:
                return '';
        }

        $str = str_shuffle($s);
        return substr($str, 0, $max);
    }
}

if (!function_exists('get_api_token')) {
    /**
     * 根据指定参数获取token
     * @param array $p  //接口参数
     * @param $secret   //app_key对应秘钥
     * @return array|string
     */
    function get_api_token(array $p, $secret)
    {
        if (!is_array($p)) {
            return '';
        }

        //验证token
        foreach ($p as $key => $val) {
            if ($key == "app_key" || $key == "__flush_cache" || $key == "token" || is_array($val)) {
                unset($p[$key]);
            }
        }

        //排序
        ksort($p);
        reset($p);

        $arg = "";
        foreach ($p as $key => $val) {
            $arg .= $key . "=" . $val . "&";
        }

        //去掉最后一个&字符
        $arg = $arg . $secret;

        return ['token' => md5($arg), 'arg' => $arg];
    }
}

if (!function_exists('getBrowser')) {
    /**
     * 获取浏览器
     * @return string
     */
    function getBrowser()
    {
        $sys = request()->header('user-agent');

        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif (stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        } elseif (stripos($sys, 'Safari') > 0) {
            preg_match("/safari\/([^\s]+)/i", $sys, $safari);
            $exp[0] = "Safari";
            $exp[1] = $safari[1];
        } else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0] . '(' . $exp[1] . ')';
    }
}

if (!function_exists('getOs')) {
    /**
     * 获取操作系统信息
     * @return string
     */
    function getOs()
    {
        $agent = request()->header('user-agent');

        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';#添加win10判断
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        } else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        } else if (preg_match('/Mac/i', $agent)) {
            $os = 'Mac';
        } else if (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        } else if (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        } else if (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        } else if (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        } else if (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        } else if (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        } else if (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        } else if (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        } else if (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        } else if (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        } else if (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        } else if (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        } elseif (preg_match('/ucweb|MQQBrowser|J2ME|IUC|3GW100|LG-MMS|i60|Motorola|MAUI|m9|ME860|maui|C8500|gt|k-touch|X8|htc|GT-S5660|UNTRUSTED|SCH|tianyu|lenovo|SAMSUNG/i', $agent)) {
            $os = 'mobile';
        } else {
            $os = '未知操作系统';
        }
        return $os;
    }
}

if (!function_exists('pageParam')) {
    /**
     * 分页参数处理
     * @param $page
     * @param $num
     * @return array
     */
    function pageParam($page, $num)
    {
        $page = max(intval($page), 1);
        $num = min(max(intval($num), 1), 2000);
        $offset = ($page - 1) * $num;

        return ['page' => $page, 'num' => $num, 'offset' => $offset];
    }
}

if (!function_exists('pageOtherData')) {
    /**
     * 分页返回统一参数
     * @param $total_num
     * @param $page
     * @param $num
     * @return array
     */
    function pageOtherData($total_num, $page, $num)
    {
        $re = pageParam($page, $num);

        $total_page = ceil($total_num / $re['num']);

        return ['num' => $re['num'], 'cur_page' => $re['page'], 'total_num' => $total_num, 'total_page' => $total_page];
    }
}

if (!function_exists('changeArrayGroup')) {
    /**
     * 将数组分组
     * @param $lists
     * @param $key
     * @return array
     */
    function changeArrayGroup($lists, $key)
    {
        if (empty($lists)) {
            return [];
        }

        $li = [];
        foreach ($lists as $k => $v) {
            $li[$v[$key]][] = $v;
        }

        return $li;
    }
}

if (!function_exists('getArrayColumn')) {
    /**
     * 返回二维数组指定列
     * @param $li
     * @param $field_key
     * @return array|array[]
     */
    function getArrayColumn($li, $field_key)
    {
        if (empty($li)) {
            return [];
        }

        if (empty($field_key)) {
            return [];
        }

        //字段改为数组
        $af = explode(',', str_replace(" ", '', $field_key));

        $li = array_map(function ($v) use ($af) {
            $d = [];
            foreach ($af as $f) {
                if (isset($v[$f])) {
                    $d[$f] = $v[$f];
                }
            }
            return $d;
        }, $li);

        return $li;
    }
}

if (!function_exists('getArrayColumnAs')) {
    /**
     * 返回二维数组指定列,并返回别名
     * @param $li
     * @param array $field_arr  ['field'=>'field_as']
     * @return array|array[]
     */
    function getArrayColumnAs($li, array $field_arr)
    {
        if (empty($li)) {
            return [];
        }

        if (empty($field_arr)) {
            return [];
        }

        $li = array_map(function ($v) use ($field_arr) {
            $d = [];
            foreach ($field_arr as $field=>$field_as) {
                if (isset($v[$field])) {
                    $d[$field_as] = $v[$field];
                }
            }
            return $d;
        }, $li);

        return $li;
    }
}

if (!function_exists('changeArrayKey')) {
    /**
     * 更换数组的键信息
     * @param $lists
     * @param $key
     * @return array
     */
    function changeArrayKey($lists, $key)
    {
        if (empty($lists)) {
            return [];
        }

        $li = [];
        foreach ($lists as $k => $v) {
            $li[$v[$key]] = $v;
        }

        return $li;
    }
}

if (!function_exists('listsDataLoad')) {

    /**
     * 将列表数据的详细信息装载到列表
     * @param $lists
     * @return array
     */
    function listsDataLoad($lists)
    {
        if (empty($lists)) {
            return [];
        }

        $args = func_get_args();
        array_shift($args);

        if (empty($args)) {
            return $lists;
        }

        $li = [];
        foreach ($lists as $k => $v) {
            foreach ($args as $ak => $av) {
                $key = $av['key'];
                $info_key = $key . '_link_info';
                $default = isset($av['default']) ? $av['default'] : [];

                if (isset($av['li'][$v[$key]])) {
                    $v[$info_key] = $av['li'][$v[$key]];
                } else {
                    $v[$info_key] = $default;
                }
            }
            $li[] = $v;
        }

        return $li;
    }
}

if (!function_exists('getAgeByBirthday')) {
    /**
     * 年龄计算
     * @param $birthday
     * @return false|int|mixed|string
     */
    function getAgeByBirthday($birthday)
    {
        if (empty($birthday)) {
            return 0;
        }

        list($year, $month, $day) = explode('-', $birthday);
        if (date('m') > $month) {
            $age = date('Y') - $year - 1;
        } else {
            $age = date('Y') - $year;
        }
        return $age;
    }
}

if (!function_exists('imageCode')) {
    /**
     * 验证码图片
     * @param $code
     * @param int $width
     * @param int $height
     * @return false|string
     */
    function imageCode($code, $width = 60, $height = 26)
    {
        //1.创建黑色画布
        $image = imagecreatetruecolor($width, $height);

        //2.为画布定义(背景)颜色
        $bgColor = imagecolorallocate($image, 255, 255, 255);

        //3.填充颜色
        imagefill($image, 0, 0, $bgColor);

        $code .= "";

        //4.设置验证码内容
        $c = strlen($code);
        for ($i = 0; $i <= $c - 1; $i++) {
            // 字体大小
            $fontsize = 21;

            // 字体颜色
            $fontcolor = imagecolorallocate($image, mt_rand(0, 110), mt_rand(0, 110), mt_rand(0, 110));

            // 显示的坐标
            $x = ($i * 10) + mt_rand(5, 10);
            $y = mt_rand(1, 12);

            // 填充内容到画布中
            imagestring($image, $fontsize, $x, $y, $code[$i], $fontcolor);
        }

        //4.1设置背景干扰元素
        for ($$i = 0; $i < 200; $i++) {
            $pointColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imagesetpixel($image, mt_rand(1, 99), mt_rand(1, 29), $pointColor);
        }

        //4.2设置干扰线
        for ($i = 0; $i < 6; $i++) {
            $lineColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imageline($image, mt_rand(1, 99), mt_rand(1, 29), mt_rand(1, 99), mt_rand(1, 29), $lineColor);
        }

        ob_start();
        imagepng($image);
        $string = ob_get_contents();
        ob_flush();

        imagedestroy($image);

        return $string;
    }
}

if (!function_exists('httpData')) {

    /**
     * http请求
     * @param $request_url
     * @param string $method
     * @param array $post_data
     * @param int $timeout      //2超时秒
     * @return array
     */
    function httpData($request_url, $method = 'get', $post_data = array(), $timeout = 2)
    {
        if (is_array($post_data)) {
            $post_data = http_build_query($post_data);
        }

        $curl = curl_init();

        if (strtolower($method) == 'post') {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }

        curl_setopt($curl, CURLOPT_URL, $request_url);
        if (strpos($request_url, 'https:') !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);//严格校验
        }

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        $data = curl_exec($curl);
        $info = curl_getinfo($curl);
        $http_code = $info ['http_code'];

        return array('http_code' => $http_code, 'http_body' => $data);
    }
}

if (!function_exists('xmlToArray')) {

    /**
     * xml转数组
     * @param $xml
     * @return mixed
     */
    function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}

if (!function_exists('getMonthDateTime')) {

    /**
     * 根据某月返回当月的天区间
     * @param $month
     * @return mixed
     */
    function getMonthDateTime($month)
    {
        if ($month < 1 || $month > 12) {
            return [];
        }

        $t = strtotime(date("Y-{$month}-01"));

        $re['st'] = date('Y-m-01 00:00:00', $t);
        $re['et'] = date('Y-m-t 23:59:59', $t);

        return $re;
    }
}

if (!function_exists('getDayDateTime')) {
    /**
     * @param $time
     * @return mixed
     */
    function getDayDateTime($time)
    {
        $t = strtotime($time);

        $re['st'] = date('Y-m-d 00:00:00', $t);
        $re['et'] = date('Y-m-d 23:59:59', $t);

        return $re;
    }
}

if (!function_exists('checkDateTimeFormat')) {

    /**
     * 时间格式检查
     * @param $dateTime
     * @return bool
     */
    function checkDateTimeFormat($dateTime)
    {
        return $dateTime == date('Y-m-d H:i:s', strtotime($dateTime));
    }
}

if (!function_exists('checkPhone')) {

    /**
     * 检查手机格式
     * @param $phone
     * @return bool
     */
    function checkPhone($phone)
    {
        if (preg_match("/^1\d{10}$/", $phone)){
            return true;
        }
        return false;
    }
}



if (!function_exists('hideTel')) {

    /**
     * 隐藏联系方式
     * @param $tel
     * @return string|string[]|null
     */
    function hideTel($tel)
    {
        if (empty($tel)) {
            return '';
        }

        return substr($tel, 0,3).'****'.substr($tel, -3);
    }
}

if (!function_exists('getDayNum')) {

    /**
     * 判断时间过去多少天
     * @param $dateTime
     * @return float|int
     */
    function getDayNum($dateTime)
    {
        if (empty($dateTime)) {
            return 0;
        }

        return round((time() - strtotime($dateTime)) / 86400);
    }
}

if (!function_exists('mySqlBinds')) {

    /**
     * 绑定多个数据
     * @param array $w
     * @param int $num
     * @return array
     */
    function mySqlBinds(array $w, $num = 1)
    {
        //防止报错
        if (empty($w)) {
            $w = [-99999];
        }

        $c = ltrim(str_pad('', count($w)*2, ',?'), ',');

        $binds = [];
        for ($i=1; $i<=$num; $i++) {
            $binds = array_merge($binds, $w);
        }

        return ['char'=>$c, 'binds' => $binds];
    }
}

if (!function_exists('getAction')) {

    /**
     * 当是代理的时候，无法获取action问题处理
     * @return mixed|string
     */
    function getAction()
    {
        $action = \request()->action;

        if ($action) {
            return strtolower($action);
        }

        $path = \request()->path();

        $tmp = explode('/', $path);

        return isset($tmp[3])?strtolower($tmp[3]):'';
    }
}

if (!function_exists('toSqlString')) {

    /**
     * 返回执行sql
     * @param $sql_obj
     * @return string|string[]
     */
    function toSqlString($sql_obj)
    {
        $sql = $sql_obj->toSql();
        $param = $sql_obj->getBindings();

        foreach ($param as $k=>$v) {
            $pos = strpos($sql, '?');
            $sql = substr_replace($sql,"'{$v}'", $pos, 1);
        }

        return $sql.";\n";
    }
}

if (!function_exists('reJson')) {
    /**
     * 成功统一返回结构
     * @param array $data
     * @param array $other_data
     * @param string $msg
     * @param int $code
     * @return \support\Response
     */
    function reJson($data = [], $other_data = [], $msg = "ok", $code = 200)
    {
        $re = array(
            'code' => 200,
            'msg' => $msg,
            'data' => empty($data)?'':$data,
        );

        if (!empty($other_data)) {
            $re = array_merge($re, $other_data);
        }

        return json($re);
    }
}

if (!function_exists('clearAllCache')) {
    /**
     * 清除静态变量缓存，目前想到的就是重启服务
     */
    function clearAllCache()
    {
        posix_kill(posix_getppid(), SIGUSR1);
    }
}

if (!function_exists('addLogToFile')) {
    /**
     * 写日志
     * @param $file_name
     * @param $data
     */
    function addLogToFile($file_name, $data)
    {
        $base_dir = '/data/wwwroot/test/';
        $log_file = $base_dir.'log/'.date('Ymd').'/'.$file_name;
        $dir = dirname($log_file);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $t = date('Y-m-d H:i:s');
        $conte = "------------------------ {$t} --------------------------\n";
        $conte .= print_r($data, true)."\n";
        $conte .= "--------------------------------------------------\n";

        file_put_contents($log_file, $conte, FILE_APPEND);
    }

}