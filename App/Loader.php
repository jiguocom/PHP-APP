<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/26
 * Time: 19:12
 */

namespace App;


class Loader{
    static function autoload($class){
        $filePath = BASE_PATH.'/'.str_replace('\\','/',$class).'.php';
        if(is_file($filePath)){
            require_once $filePath;
        }else{
            throw new \Exception('文件不存在');
        }
    }
}