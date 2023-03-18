<?php

namespace Artim\Logger\Logger\File;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;
use Stringable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

class JsonFormatter extends BaseJsonFormatter
{
    protected function normalize(mixed $data, int $depth = 0): mixed
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

            // if the object has specific json serializability we want to make sure we skip the __toString treatment below
            if ($data instanceof \JsonSerializable) {
                return $data;
            }

            if ($data instanceof Request) {
                return $this->formatRequest($data);
            }

            if ($data instanceof Stringable) {
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
}
