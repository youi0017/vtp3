<?php namespace Kernel;

/**
 * 日志类
 * v0 首发 err记录错误 20190816120032
 * 注：后续考虑 全局配置 和 加入channel
 */

class Logger
{
	/**
	 * 将内容写入错误日志
	 * 20190815115942
	 */
	public static function err($txt)
	{
		// log头与主体
		$txt =PHP_EOL.PHP_EOL.'Note Error '.date('Y-m-d H:i:s').'-'.$_SERVER['REMOTE_ADDR'].PHP_EOL.$txt.PHP_EOL;

		// 从backtrace中取出文件与行
		$backbrace = self::backbrace();	
		// var_dump($backbrace);exit;
		$txt .= "Uri is {$_SERVER['REQUEST_URI']} In {$backbrace['file']} At Line {$backbrace['line']}".PHP_EOL;

		// 生成日志
		$fp = fopen(\ERROR_LOG_FILE, 'a');
		fwrite($fp, $txt);
		fclose($fp);
	}


	// 返回调用栈信息 20190816083107
	public static function backbrace()
	{
        if (version_compare(PCRE_VERSION, '7.0.0', '>=')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $backtrace = debug_backtrace(false);
        }

        return $backtrace[1];
	}
		
}