<?php

/** this is bootstrap file for phpstan */

define('LARAVEL_START', microtime(true));

/**
 * @method static \Illuminate\Log\Logger withContext(array $context = [])
 * @method static \Illuminate\Log\Logger withoutContext()
 * @method static void write(string $level, string $message, array $context = [])
 * @method static void listen(\Closure $callback)
 * @see \Illuminate\Log\Logger
 */
class Log extends \Illuminate\Support\Facades\Facade
{
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @static
     */
    public static function emergency($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->emergency($message, $context);
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
     * @static
     */
    public static function alert($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @static
     */
    public static function critical($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @static
     */
    public static function error($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->error($message, $context);
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
     * @static
     */
    public static function warning($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @static
     */
    public static function notice($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @static
     */
    public static function info($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @static
     */
    public static function debug($message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     * @static
     */
    public static function log($level, $message, $context = [])
    {
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->log($level, $message, $context);
    }

    /**
     * Build an on-demand log channel.
     *
     * @param array $config
     * @return \Psr\Log\LoggerInterface
     * @static
     */
    public static function build($config)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->build($config);
    }

    /**
     * Create a new, on-demand aggregate logger instance.
     *
     * @param array $channels
     * @param string|null $channel
     * @return \Psr\Log\LoggerInterface
     * @static
     */
    public static function stack($channels, $channel = null)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->stack($channels, $channel);
    }

    /**
     * Get a log channel instance.
     *
     * @param string|null $channel
     * @return \Psr\Log\LoggerInterface
     * @static
     */
    public static function channel($channel = null)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->channel($channel);
    }

    /**
     * Get a log driver instance.
     *
     * @param string|null $driver
     * @return \Psr\Log\LoggerInterface
     * @static
     */
    public static function driver($driver = null)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->driver($driver);
    }

    /**
     * Share context across channels and stacks.
     *
     * @param array $context
     * @return \Artim\Logger\Logger\LogManager
     * @static
     */
    public static function shareContext($context)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->shareContext($context);
    }

    /**
     * The context shared across channels and stacks.
     *
     * @return array
     * @static
     */
    public static function sharedContext()
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->sharedContext();
    }

    /**
     * Flush the shared context.
     *
     * @return \Artim\Logger\Logger\LogManager
     * @static
     */
    public static function flushSharedContext()
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->flushSharedContext();
    }

    /**
     * Get the default log driver name.
     *
     * @return string|null
     * @static
     */
    public static function getDefaultDriver()
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->getDefaultDriver();
    }

    /**
     * Set the default log driver name.
     *
     * @param string $name
     * @return void
     * @static
     */
    public static function setDefaultDriver($name)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        $instance->setDefaultDriver($name);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string $driver
     * @param \Closure $callback
     * @return \Artim\Logger\Logger\LogManager
     * @static
     */
    public static function extend($driver, $callback)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->extend($driver, $callback);
    }

    /**
     * Unset the given channel instance.
     *
     * @param string|null $driver
     * @return \Artim\Logger\Logger\LogManager
     * @static
     */
    public static function forgetChannel($driver = null)
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->forgetChannel($driver);
    }

    /**
     * Get all of the resolved log channels.
     *
     * @return array
     * @static
     */
    public static function getChannels()
    {            //Method inherited from \Illuminate\Log\LogManager
        /** @var \Artim\Logger\Logger\LogManager $instance */
        return $instance->getChannels();
    }
}

/**
 */
class DB extends \Illuminate\Support\Facades\DB
{
}
