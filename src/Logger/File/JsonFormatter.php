<?php

declare(strict_types = 1);

namespace Artim\Logger\Logger\File;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use JsonSerializable;
use Monolog\Formatter\NormalizerFormatter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

class JsonFormatter extends NormalizerFormatter
{
    public const BATCH_MODE_JSON = 1;
    public const BATCH_MODE_NEWLINES = 2;

    /** @var self::BATCH_MODE_* */
    protected int $batchMode;

    protected bool $appendNewline;
    protected bool $ignoreEmptyContextAndExtra;
    protected bool $includeStackTraces = false;

    /**
     * @param self::BATCH_MODE_* $batchMode
     */
    public function __construct(
        int $batchMode = self::BATCH_MODE_JSON,
        bool $appendNewline = true,
        bool $ignoreEmptyContextAndExtra = false,
        bool $includeStackTraces = false,
        ?string $dateFormat = null
    ) {
        $this->batchMode = $batchMode;
        $this->appendNewline = $appendNewline;
        $this->ignoreEmptyContextAndExtra = $ignoreEmptyContextAndExtra;
        $this->includeStackTraces = $includeStackTraces;

        parent::__construct($dateFormat);
    }

    /**
     * The batch mode option configures the formatting style for
     * multiple records. By default, multiple records will be
     * formatted as a JSON-encoded array. However, for
     * compatibility with some API endpoints, alternative styles
     * are available.
     */
    public function getBatchMode(): int
    {
        return $this->batchMode;
    }

    /**
     * True if newlines are appended to every formatted record
     */
    public function isAppendingNewlines(): bool
    {
        return $this->appendNewline;
    }

    /**
     * @param array $record
     * @return string
     */
    public function format(array $record): string
    {
        $normalized = $this->normalize($record);

        if (isset($normalized['additional']) && $normalized['additional'] === []) {
            if ($this->ignoreEmptyContextAndExtra) {
                unset($normalized['additional']);
            } else {
                $normalized['additional'] = new \stdClass();
            }
        }

        return $this->toJson($normalized, true) . ($this->appendNewline ? "\n" : '');
    }

    /**
     * {@inheritDoc}
     */
    public function formatBatch(array $records): string
    {
        return match ($this->batchMode) {
            static::BATCH_MODE_NEWLINES => $this->formatBatchNewlines($records),
            default => $this->formatBatchJson($records),
        };
    }

    /**
     * @param bool $include
     * @return self
     */
    public function includeStackTraces(bool $include = true): self
    {
        $this->includeStackTraces = $include;

        return $this;
    }

    /**
     * Return a JSON-encoded array of records.
     */
    protected function formatBatchJson(array $records): string
    {
        return $this->toJson($this->normalize($records), true);
    }

    /**
     * Use new lines to separate records instead of a
     * JSON-encoded array.
     */
    protected function formatBatchNewlines(array $records): string
    {
        return implode('', array_map(
            fn ($value) => $this->format($value),
            $records,
        ));
    }

    /**
     * Normalizes given $data.
     *
     * @param mixed $data
     * @param int $depth
     * @return mixed
     */
    protected function normalize($data, int $depth = 0): mixed
    {
        if ($depth > $this->maxNormalizeDepth) {
            return 'Over '.$this->maxNormalizeDepth.' levels deep, aborting normalization';
        }

        if (is_array($data)) {
            $normalized = [];

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ > $this->maxNormalizeItemCount) {
                    $normalized['...'] = 'Over '.$this->maxNormalizeItemCount.' items ('.count($data).' total), aborting normalization';

                    break;
                }

                $normalized[$key] = $this->normalize($value, $depth + 1);
            }

            return $normalized;
        }

        if (is_object($data)) {
            if ($data instanceof \DateTimeInterface) {
                return $this->formatDate($data);
            }

            if ($data instanceof Throwable) {
                return $this->normalizeException($data, $depth);
            }

            if ($data instanceof Request) {
                return $this->formatRequest($data);
            }

            // if the object has specific json serializability we want to make sure we skip the __toString treatment below
            if ($data instanceof JsonSerializable) {
                return $data;
            }

            if (method_exists($data, '__toString')) {
                return $data->__toString();
            }

            return $data;
        }

        if (is_resource($data)) {
            return parent::normalize($data);
        }

        return $data;
    }

    protected function formatRequest(Request $data): array
    {
        if (! app()->runningInConsole()) {
            return [
                'method' => $data->method(),
                'uri' => $data->getPathInfo(),
                'body' => $data->all(),
                'headers' => $data->headers->all(),
                'files' => $this->formatFiles($data->allFiles()),
            ];
        }

        return [
            'method' => 'CLI',
            'command' => $data->server('SCRIPT_FILENAME'),
            'arguments' => $this->formatArguments($data),
            'hostname' => gethostname(),
        ];
    }

    protected function formatArguments(Request $data): array
    {
        $arguments = $data->server('argv');
        reset($arguments);
        unset($arguments[0]);

        return array_values($arguments);
    }

    protected function formatFiles(array $files): array
    {
        return Arr::flatten(array_map([$this, 'formatFile'], $files));
    }

    protected function formatFile(UploadedFile|array|string $file): array|string
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }

        if (is_array($file)) {
            return array_map([$this, 'formatFile'], $file);
        }

        return (string)$file;
    }

    /**
     * Normalizes given exception with or without its own stack trace based on
     * `includeStacktraces` property.
     *
     * {@inheritDoc}
     */
    protected function normalizeException(Throwable $e, int $depth = 0): array
    {
        $data = parent::normalizeException($e, $depth);
        if (! $this->includeStackTraces) {
            unset($data['trace']);
        }

        return $data;
    }
}
