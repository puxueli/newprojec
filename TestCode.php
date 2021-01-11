<?php


namespace app\api\controller;


use think\Db;

class TestCode
{
    function initialize()
    {
        /**
         * php常用代码封装
         */
    }

    //**************************************PHP数组处理*********************************************//

    //PHP中的shuffle函数只能打乱一维数组

    /**
     * @param $list
     * @return array
     * 随机打乱二维数组
     */
    function shuffle_assoc($list)
    {
        if(!is_array($list)){
            return $list;
        }else{
            $keys = array_keys($list);
            shuffle($keys);
            $random = array();
            foreach ($keys as $key){
                $random[$key] = $list[$key];
            }
            return $random;
        }

    }

    /**
     * @param $list
     * @return array
     * 随机打乱多维数组
     */
    function shuffle_assocmore($list)
    {
        if(!is_array($list)){
            return $list;
        }else{
            $keys = array_keys($list);
            shuffle($keys);
            $random = array();
            foreach ($keys as $key){
                $random[$key] = shuffle_assoc($list[$key]);
            }
        }
        return $random;
    }

    /**
     * @param $arr
     * @return array
     * 二维转一维
     * 函数可以直接转一维
     * 如果第二维是数字键名:array_reduce($list,'array_merge', array())
     * array_column($user_ids,'user_id')
     */

    function array_reduceone($arr){
        $result = array_reduce($arr, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, array());
        return $result;
    }


    /**
     * @param $arr
     * @return array
     * 多维数组转一维
     */
    function disposeArray($arr){
        $result = [];
        array_map(function ($value) use (&$result) {
            $result = array_merge($result, array_values($value));
        }, $arr);
        return $result;
    }

    /**
     * @return bool
     * 判断两个数组是否相等
     */
    function arr_arra_diff(){
        $a1=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");
//        $a1=array("a"=>"red","b"=>"green","c"=>"blue");//正确答案
        $a2=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");//提交的答案
        if(!array_diff($a1,$a2) && !array_diff($a2,$a1)){
            // 即相互都不存在差集，那么这两个数组就是相同的了，多数组也一样的道理
            return true;
        }else{
            return false;
        }
    }

    /**
     * 多个数组合并到一个数组，保留重复键值
     * array_merge_recursive($arr,$arr,$arr,$arr);
     */


    //**************************************PHP数组处理*********************************************//




    //**************************************PHPEXCEL处理*******************************************//

    /**
     * @return string
     * 获取上传的excel文件
     */
    public function load_excel()
    {
        if(!$_FILES['file_stu']['name']){
            return "<script>alert('老铁请选择文件');window.history.go(-1);</script>";
        }

        $path=ROOT_PATH.'data'.DS.'upload'.DS.'excel_load'.DS;
        $types=explode('.',$_FILES['file_stu']['name']);
        $type=$types[1];//扩展名
        $name=$types[0];//文件名
        if(input('filename')!=$name){
            return '文件名错误，正确文件名应为：'.input('filename');
        }
        $uploadfile = $path .$_FILES['file_stu']['name'];//上传到的路径
        //分辨excel是2007还是2003
        if($type=='xls'){
            $st='Excel5';
        }elseif ($type=='xlsx'){
            $st='Excel2007';
        }else{
            return '上传文件类型错误';
        }
        if(!file_exists($path))
        {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($path,0700,true);
        }
        //读取上传的excel文件
        $count=0;
        if (move_uploaded_file($_FILES['file_stu']['tmp_name'], $uploadfile)) {

            $res = $this->read_excel($uploadfile,$st);
            $titles=array();
            $func=$name.'_insert';
            $counts=$this->$func($res,$name,$titles,$count);
        } else {
            return '<h1>获取文件失败</h1>';
        }
    }

    /**
     * @param $filename
     * @param $type
     * @return array
     * 将读取的excel文件转换为数组
     */
    function read_excel($filename,$type)
    {
        $objReader = \PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

    //根据值分组
    function array_group($arr){

        $result =   [];  //初始化一个数组
        foreach($arr as $k=>$v){
        $result[$v['initial']][]    =   $v;  //根据initial 进行数组重新赋值
        }
        return $result;
    }



//**************************************PHPEXCEL处理*******************************************//




//**************************************分页处理***********************************************//


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

        $total_page = ceil($total_num/$re['num']);

        return ['num'=>$re['num'], 'cur_page'=>$re['page'], 'total_num'=>$total_num, 'total_page'=>$total_page];
    }



//**************************************分页处理***********************************************//



//**************************************获取系统浏览器***********************************************//

    /**
     * 获取浏览器
     * @return string
     */
    function getBrowser()
    {
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
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
//**************************************获取系统浏览器***********************************************//


//**************************************后端端返回数据，统一输出格式***********************************//

    /**
     * 统一输出格式
     * @param $data 单个详情或多个数据列表
     * @param array $other_data      其他单独数据，如分页等信息 ['num'=>20, 'cur_page'=>1, 'total_num'=>0, 'total_page'=>0]
     * @param int $code        状态码
     * @param string $msg      成功或失败消息
     * @return \think\response\Json|\think\response\Jsonp
     */
    function result_data($data, $other_data = array(), $code = 200, $msg = 'succ')
    {
        $re = array(
            'code' => $code,
            'msg' => $msg,
            'data' => empty($data)?'':$data,
        );

        if (!empty($other_data)) {
            $re = array_merge($re, $other_data);
        }
        $callback = isset ( $_GET ['callback'] ) ? $_GET ['callback'] : '';

        if ($callback) {
            return jsonp($re);
        } else {
            return json($re);
        }
    }

//**************************************后端端返回数据，统一输出格式***********************************//

}