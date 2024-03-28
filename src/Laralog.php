<?php

namespace Mirmuxsin\Laralog;

use Composer\InstalledVersions;
use Milly\Laragram\Laragram;

/**
 * Class Laralog
 * @package Mirmuxsin\Laralog
 * @method static void debug(int|string|array|object $message, $auto_discover = true)
 * @method static void info(int|string|array|object $message, $auto_discover = true)
 * @method static void warning(int|string|array|object $message, $auto_discover = true)
 * @method static void error(int|string|array|object $message, $auto_discover = true)
 * @method void debug(int|string|array|object $message, $auto_discover = true)
 * @method void info(int|string|array|object $message, $auto_discover = true)
 * @method void warning(int|string|array|object $message, $auto_discover = true)
 * @method void error(int|string|array|object $message, $auto_discover = true)
 */
class Laralog
{
    // call debug function statically
    public static function __callStatic($method, $arguments)
    {
        if ($method === 'debug') {
            (new self)->sendLog("🟦 Debug", ...$arguments);
        }
        if ($method === 'info') {
            (new self)->sendLog("🟩 Info", ...$arguments);
        }
        if ($method === 'warning') {
            (new self)->sendLog("🟨 Warning", ...$arguments);
        }
        if ($method === 'error') {
            (new self)->sendLog("🟥 Error", ...$arguments);
        }
    }

    // call debug function dynamically
    public function __call($method, $arguments)
    {
        if ($method === 'debug') {
            $this->sendLog("🟦 Debug", ...$arguments);
        }
        if ($method === 'info') {
            $this->sendLog("🟩 Info", ...$arguments);
        }
        if ($method === 'warning') {
            $this->sendLog("🟨 Warning", ...$arguments);
        }
        if ($method === 'error') {
            $this->sendLog("🟥 Error", ...$arguments);
        }
    }

    private function sendLog($type, int|string|array|object $message, $auto_discover = true): void
    {
        $is_enabled = config('laralog.is_enabled');
        if ($is_enabled != 'local' or $is_enabled !== true) {
            return;
        }

        if ($auto_discover) {
            $message = htmlspecialchars($message);
        }

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        }

        $messages = str_split($message, 4000);

        foreach ($messages as $message) {
            $text = "<b>".$type."</b>";

            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            if (isset($trace[1]['file']) && isset($trace[1]['line'])) {
                $text .= "\n\nFile: <i>" . $trace[1]['file'] . "</i>\nLine: <i>". $trace[1]['line'] . "</i>\n\n";
            }

            $text .= "<pre>" . $message . "</pre>";

            if (InstalledVersions::getVersion('milly/laragram') < '3.0.0') {

                /**
                 * @param array $data
                 */
                Laragram::sendMessage([
                    'chat_id' => config('laralog.chat_id'),
                    'text' => $text,
                    'parse_mode' => 'HTML'
                ]);
            } elseif (InstalledVersions::getVersion('milly/laragram') >= '3.0.0') {
                Laragram::sendMessage(chat_id: config('laralog.chat_id'), text: $message, parse_mode: 'HTML');
            }
        }
    }

}