<?php
namespace Exinfinite\Helpers;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

final class Log {
    public function __construct($log_path) {
        $logger = new Logger('Log');
        $logger->pushHandler(new StreamHandler($log_path, Logger::DEBUG));
        $this->logger = $logger;
    }
    public function error($msg, array $context = []) {
        $this->logger->error($msg, $context);
    }

    public function info($msg, array $context = []) {
        $this->logger->info($msg, $context);
    }

    public function warning($msg, array $context = []) {
        $this->logger->warning($msg, $context);
    }
}
?>