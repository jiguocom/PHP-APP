<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/26
 * Time: 14:42
 */

namespace App;


class Error
{
    const LOG_FILE_PREFIX = 'handler';
    const LOG_TYPE = 'CRITICAL';
    /**
     * 函数注册
     * @return none
     */
    static public function initSystemHandlers() {

        //异常处理
        set_exception_handler(array(__CLASS__, 'handleException'));

        //错误处理函数
        set_error_handler(array(__CLASS__, 'handleError'));

        //注册致命错误处理方法
        register_shutdown_function(array(__CLASS__, 'fatalError'));
    }
    /**
     * 自定义异常处理
     * @param mixed $e 异常对象
     * @return void
     */
    static public function handleException($e) {
        $error = array();
        $error['message']  = $e->getMessage();
        $error['file'] = $e->getFile();
        $error['line'] = $e->getLine();
        $trace = $e->getTrace();
        if(empty($trace[0]['function']) && $trace[0]['function'] == 'exception') {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        }
        $error['trace']  = $e->getTraceAsString();
        $error['function'] = $error['class'] = '';
        Loger::write($error['message'], self::LOG_TYPE, self::LOG_FILE_PREFIX, $error);
        self::halt($error);
    }

    /**
     * 自定义错误处理
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function handleError($errno, $errstr, $errfile, $errline) {
        $error['message'] = "[$errno] $errstr";
        $error['file'] = $errfile;
        $error['line'] = $errline;
        $error['class'] = $error['function'] = '';
        if (!in_array($errno, array(E_STRICT, E_DEPRECATED))) {
            Loger::write($error['message'], self::LOG_TYPE, self::LOG_FILE_PREFIX, $error);
            if (in_array($errno, array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR))) {
                self::halt($error);
            }
        }
    }

    /**
     * 致命错误捕获，PHP错误级别预定义常量参考：
     * http://php.net/manual/zh/errorfunc.constants.php
     * @return none
     */
    static public function fatalError() {
        $error = error_get_last() ?: null;
        if (!is_null($error) && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR))) {
            $error['class'] = $error['function'] = '';
            Loger::write($error['message'], self::LOG_TYPE, self::LOG_FILE_PREFIX, $error);
            self::halt($error);
        }
    }

    /**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
    static public function halt($error) {
        ob_get_contents() && ob_end_clean();
        $e = array();
        if (IS_DEBUG || IS_CLI) {
            //调试模式下输出错误信息
            $e = $error;
            if(IS_CLI){
                $e_message  = $e['message'].' in '.$e['file'].' on line '.$e['line'].PHP_EOL;
                if (isset($e['treace']) ) {
                    $e_message .= $e['trace'];
                }
                exit($e_message);
            }
        } else {
            //线网不显示错误信息，显示固定字符串，保护系统安全
            //TODO：比较友好的做法是重定向到一个漂亮的错误页面
            exit('Sorry, the system error');
        }
        // 包含异常页面模板
        //$exceptionFile = __DIR__ . '/exception.tpl';
        //include $exceptionFile;
        //exit(0);
    }

}