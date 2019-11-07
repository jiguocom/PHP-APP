<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/26
 * Time: 15:07
 */

namespace App;


/**
 * 日志接口
 * @filename Loger.php
 * @since 2016-12-08 12:13:50
 * @author 979137.com
 * @version $Id$
 */
class Loger {

    // 日志目录，建议独立于Web目录，这个目录将作用于 Logstash 日志收集
    protected static $log_path = BASE_PATH.'./log/';

    // 日志类型
    protected static $type = array('ERROR', 'INFO', 'WARN', 'CRITICAL');

    /**
     * 写入日志信息
     * @param mixed  $_msg  调试信息
     * @param string $_type 信息类型
     * @param string $file_prefix 日志文件名默认取当前日期，可以通过文件名前缀区分不同的业务
     * @param array  $trace TRACE信息，如果为空，则会从debug_backtrace里获取
     * @return bool
     */
    public static function write($_msg, $_type = 'info', $file_prefix = '', $trace = array()) {
        $_type = strtoupper($_type);
        $_msg  = is_string($_msg) ? $_msg : var_export($_msg, true);
        if (!in_array($_type, self::$type)) {
            return false;
        }
        $server = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
        $remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI';
        if (!is_array($trace) || empty($trace)) {
            $dtrace = debug_backtrace();
            $trace  = $dtrace[0];
            if (count($dtrace) == 1) {
                //不是在类或函数内调用
                $trace['function'] = '';
            } else {
                if ($dtrace[1]['function'] == '__callStatic') {
                    $trace['file'] = $dtrace[2]['file'];
                    $trace['line'] = $dtrace[2]['line'];
                    $trace['function'] = empty($dtrace[3]['function']) ? '' : $dtrace[3]['function'];
                } else {
                    $trace['function'] = $dtrace[1]['function'];
                }
            }
        }
        $ace = $trace;
        $now = date('Y-m-d H:i:s');
        $pre = "[{$now}][%s][{$ace['file']}][{$ace['line']}][{$ace['function']}][{$remote}][{$method}][{$server}]%s";
        $msg = sprintf($pre, $_type, $_msg);

        $filename = 'phplog_' . ($file_prefix ?: 'netbar') . '_' . date('Ymd') . '.log';
        $destination = self::$log_path . $filename;
        is_dir(self::$log_path) || mkdir(self::$log_path, 0777, true);
        //文件不存在，则创建文件并加入可写权限
        if (!file_exists($destination)) {
            touch($destination);
            chmod($destination, 0777);
        }
        return error_log($msg.PHP_EOL, 3, $destination) ?: false;
    }

    /**
     * 静态魔术调用
     * @param $method
     * @param $args
     * @return mixed
     *
     * @method void error($msg) static
     * @method void info($msg) static
     * @method void warn($msg) static
     */
    public static function __callStatic($method, $args) {
        $method = strtoupper($method);
        if (in_array($method, self::$type)) {
            $_msg = array_shift($args);
            return self::write($_msg, $method);
        }
        return false;
    }
}