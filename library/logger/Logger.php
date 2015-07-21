<?php
namespace library\logger;

use \Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * 日志工具类
 */
class Logger
{
    private static $DEBUG = false;

    private static $FILE_LOG_DIR;

    public static $LOG_INFO = 'info';

    public static $LOG_DEBUG = 'debug';

    public static $LOG_ERROR = 'error';

    public static $LOG_EXCEPTION = 'exception';

    /**
     * @var \PhpConsole\Handler
     */
    private static $logger;

    private static $infoFileLogger = false;

    private static $debugFileLogger = false;

    private static $errorFileLogger = false;

    private static $exceptionFileLogger = false;

    /**
     * 初始化
     * @param $logDir
     * @param $debug
     * @throws \Exception
     */
    static function init($logDir, $debug)
    {
        if ($debug) {
            Logger::$logger = \PhpConsole\Handler::getInstance()->getInstance();
            Logger::$logger->setHandleErrors(false);
            Logger::$logger->setHandleExceptions(false);
            Logger::$logger->setCallOldHandlers(false);
            Logger::$logger->start();

            Logger::$FILE_LOG_DIR = $logDir;

            self::$DEBUG = true;
        }
    }

    /**
     * @param $type
     *
     * @return FileAdapter
     */
    private static function getFileLogger($type)
    {
        $loggerFile = Logger::$FILE_LOG_DIR . '/' . $type . '_' . date('Y-m-d') . '.log';
        switch ($type) {
            case Logger::$LOG_INFO: {
                if (empty(Logger::$infoFileLogger)) {
                    Logger::$infoFileLogger = new FileAdapter($loggerFile);
                }

                return Logger::$infoFileLogger;
            }
            case Logger::$LOG_DEBUG: {
                if (empty(Logger::$debugFileLogger)) {
                    Logger::$debugFileLogger = new FileAdapter($loggerFile);
                }

                return Logger::$debugFileLogger;
            }
            case Logger::$LOG_ERROR: {
                if (empty(Logger::$errorFileLogger)) {
                    Logger::$errorFileLogger = new FileAdapter($loggerFile);
                }

                return Logger::$errorFileLogger;
            }
            case Logger::$LOG_EXCEPTION: {
                if (empty(Logger::$exceptionFileLogger)) {
                    Logger::$exceptionFileLogger = new FileAdapter($loggerFile);
                }

                return Logger::$exceptionFileLogger;
            }
        }
    }

    /**
     * @param $log
     * @param bool $logInFile 是否保存到文件
     */
    static function info($log, $logInFile = false)
    {
        if (!self::$DEBUG) {
            return;
        }

        Logger::$logger->debug($log, Logger::$LOG_INFO);
        if ($logInFile) {
            self::file(Logger::$LOG_INFO, $log);
        }
    }

    /**
     * @param $log
     * @param bool $logInFile 是否保存到文件
     */
    static function debug($log, $logInFile = false)
    {
        if (!self::$DEBUG) {
            return;
        }

        Logger::$logger->debug($log, Logger::$LOG_DEBUG);
        if ($logInFile) {
            self::file(Logger::$LOG_DEBUG, $log);
        }
    }

    /**
     * @param $log
     * @param bool $logInFile 是否保存到文件
     */
    static function error($log, $logInFile = false)
    {
        if (!self::$DEBUG) {
            return;
        }

        Logger::$logger->debug($log, Logger::$LOG_ERROR);
        if ($logInFile) {
            self::file(Logger::$LOG_ERROR, $log);
        }
    }

    /**
     * @param $exception Exception
     * @param bool $logInFile 是否保存到文件
     */
    static function exception($exception, $logInFile = false)
    {
        if (!self::$DEBUG) {
            return;
        }

        Logger::$logger->handleException($exception);
        if ($logInFile) {
            self::file(Logger::$LOG_EXCEPTION, "code:{$exception->getCode()} file:{$exception->getFile()} line:{$exception->getLine()} msg:{$exception->getMessage()}");
        }
    }

    /**
     * @param string $level 日志等级
     * @param string $log 日志内容
     */
    public static function file($level, $log)
    {
        if (!self::$DEBUG) {
            return;
        }

        switch ($level) {
            case self::$LOG_DEBUG: {
                $lv = \Phalcon\Logger::DEBUG;
                break;
            }
            case self::$LOG_ERROR: {
                $lv = \Phalcon\Logger::ERROR;
                break;
            }
            case self::$LOG_EXCEPTION: {
                $lv = \Phalcon\Logger::CRITICAL;
                break;
            }
            default: {
                $lv = \Phalcon\Logger::INFO;
            }
        }

        Logger::getFileLogger($level)->log($lv, $log);
    }
}