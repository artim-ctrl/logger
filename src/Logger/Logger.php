<?php

namespace Artim\Logger\Logger;

use Monolog\DateTimeImmutable;
use Monolog\Logger as BaseLogger;
use Throwable;

class Logger extends BaseLogger
{
    protected const RFC_5424_LEVELS = [
        7 => self::DEBUG,
        6 => self::INFO,
        5 => self::NOTICE,
        4 => self::WARNING,
        3 => self::ERROR,
        2 => self::CRITICAL,
        1 => self::ALERT,
        0 => self::EMERGENCY,
    ];
    protected const TURN_OUT_PROPERTIES = [
        'user',
        'token',
        'type',
    ];

    private bool $detectCycles = true;
    private int $logDepth = 0;

    public function addRecord(int $level, string $message, array $context = [], ?DateTimeImmutable $datetime = null): bool
    {
        if (array_key_exists($level, self::RFC_5424_LEVELS)) {
            $level = self::RFC_5424_LEVELS[$level];
        }

        if ($this->detectCycles) {
            $this->logDepth += 1;
        }

        if ($this->logDepth === 3) {
            $this->warning('A possible infinite logging loop was detected and aborted. It appears some of your handler code is triggering logging, see the previous log record for a hint as to what may be the cause.');

            return false;
        } elseif ($this->logDepth >= 5) { // log depth 4 is let through so we can log the warning above
            return false;
        }

        try {
            $record = null;

            foreach ($this->handlers as $handler) {
                if (null === $record) {
                    // skip creating the record as long as no handler is going to handle it
                    if (! $handler->isHandling(['level' => $level])) {
                        continue;
                    }

                    $levelName = static::getLevelName($level);

                    [$turnedOut, $context] = static::turnOut($context);

                    if (! isset($turnedOut['type'])) {
                        $turnedOut['type'] = 'custom';
                    }

                    $record = [
                        'message' => $message,
                        'additional' => $context,
                        'level' => $levelName,
                        'datetime' => $datetime ?? new DateTimeImmutable($this->microsecondTimestamps, $this->timezone),
                    ];

                    $record = array_merge($record, $turnedOut);

                    try {
                        foreach ($this->processors as $processor) {
                            $record = $processor($record);
                        }
                    } catch (Throwable $e) {
                        $this->handleException($e, $record);

                        return true;
                    }
                }

                // once the record exists, send it to all handlers as long as the bubbling chain is not interrupted
                try {
                    if (true === $handler->handle($record)) {
                        break;
                    }
                } catch (Throwable $e) {
                    $this->handleException($e, $record);

                    return true;
                }
            }
        } finally {
            if ($this->detectCycles) {
                $this->logDepth--;
            }
        }

        return null !== $record;
    }

    protected function turnOut(array $context): array
    {
        $keys = array_flip(static::TURN_OUT_PROPERTIES);

        return [
            array_intersect_key($context, $keys),
            array_diff_key($context, $keys),
        ];
    }
}
