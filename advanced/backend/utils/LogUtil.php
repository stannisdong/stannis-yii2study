<?php
namespace backend\utils;

/**
 * This is util for log pure data.
 * The original log for yii contains too much information.
 * @author Vincent Hou
 **/

class LogUtil
{
    /**
     * Transform LogUtil methods to Yii log methods to use log component configurations
     * @param $name, LogUtil static method names.
     * @param $arguments, function arguments
     * @author Vincent Hou
     **/
    public static function __callStatic($name, $arguments)
    {
        // LogUtil provide warn method
        // But yii log use warning as method name
        if ('warn' === $name) {
            $name = 'warning';
        }

        // $arguments is an array which has three values: $shortMessage, $category, $context.
        // $shortMessage must be a string, $category is normally the module name, $context is optional.
        // $context is the detail message when error happen.
        if (is_string($arguments[0])) {
            $message = [];
            $category = $arguments[1];
            if (isset($arguments[2]) && is_array($arguments[2])) {
                $arguments[2]['shortMessage'] = $arguments[0];
                $message = $arguments[2];
            } else {
                $message['shortMessage'] = $arguments[0];
            }
            $newArguments = [$message, $category];
            call_user_func_array("Yii::$name", $newArguments);
        } elseif (is_array($arguments[0])) {
            call_user_func_array("Yii::$name", $arguments);
        }
    }

    /**
     * Record exception log, it will add the exception trace to log
     * @param  \Exception $exception the exception to be logged
     * @param  string $category type the exception belong to
     * @param  array $context the context when exception happen
     */
    public static function exception($exception, $category, $context = [])
    {
        if (is_array($context)) {
            $context['exceptionTrace'] = self::convertCommonExceptionToArray($exception);
        }

        self::error($exception->getMessage(), $category, $context);
    }

    /**
     * Converts a common exception into an array contains detailed information about error.
     * @param \Exception $exception the exception being converted
     * @return array the array representation of the exception.
     */
    public static function convertCommonExceptionToArray($exception)
    {
        $isYiiException = $exception instanceof \yii\base\Exception || $exception instanceof \yii\base\ErrorException;
        $array = [
            'name' => $isYiiException ? $exception->getName() : 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'type' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stack-trace' => explode("\n", $exception->getTraceAsString()),
        ];
        if ($exception instanceof \yii\db\Exception) {
            $array['error-info'] = $exception->errorInfo;
        }
        if ($exception instanceof \yii\web\HttpException) {
            $array['status'] = $exception->statusCode;
        }
        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = self::convertCommonExceptionToArray($prev);
        }

        return $array;
    }
}
