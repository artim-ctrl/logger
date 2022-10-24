<?php

declare(strict_types = 1);

namespace Artim\Logger\Logger;

use Illuminate\Log\LogManager as BaseLogManager;

class LogManager extends BaseLogManager
{
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $context = $this->addUser($context);
        $context = $this->addToken($context);

        parent::log($level, $message, $context);
    }

    /**
     * Add user info to log
     *
     * @param array $context
     * @return array
     */
    protected function addUser(array $context): array
    {
        if (! auth()->hasUser()) {
            $context['user'] = null;

            return $context;
        }

        $context['user'] = [];
        foreach (config('artim-logger.user.properties') ?? ['id'] as $property) {
            $context['user'][$property] = auth()->user()->$property;
        }

        return $context;
    }

    /**
     * Add request token to log
     *
     * @param array $context
     * @return array
     */
    protected function addToken(array $context): array
    {
        $context['token'] = get_log_token();

        return $context;
    }
}
