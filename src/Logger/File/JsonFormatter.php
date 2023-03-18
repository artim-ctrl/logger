<?php

namespace Artim\Logger\Logger\File;

use Illuminate\Http\Request;
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

    /**
     * @param Request $data
     * @return array<string, mixed>
     */
    protected function formatRequest(Request $data): array
    {
        if (app()->runningInConsole()) {
            return [
                'method' => 'CLI',
                'command' => $data->server('SCRIPT_FILENAME'),
                'arguments' => $this->formatArguments($data),
                'hostname' => gethostname(),
            ];
        }

        $files = array_values(array_filter($data->allFiles(), fn (UploadedFile|array|null $file) => $file instanceof UploadedFile));

        return [
            'method' => $data->method(),
            'uri' => $data->getPathInfo(),
            'body' => $data->all(),
            'headers' => $data->headers->all(),
            'files' => $this->formatFiles($files),
        ];
    }

    /**
     * @param Request $data
     * @return array<string|int, string>
     */
    protected function formatArguments(Request $data): array
    {
        $arguments = $data->server('argv');
        if (null === $arguments) {
            return [];
        } elseif (is_string($arguments)) {
            return [$arguments];
        }

        reset($arguments);
        unset($arguments[0]);

        return array_values($arguments);
    }

    /**
     * @param array<int, UploadedFile> $files
     * @return array<int, string>
     */
    protected function formatFiles(array $files): array
    {
        return array_map(fn (UploadedFile $file) => $file->getClientOriginalName(), $files);
    }
}
