<?php

declare(strict_types=1);

namespace Bnomei;

use Kirby\CLI\CLI;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Cms\User;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

final class Janitor
{
    public const ARGS = [
        'page' => [
            'prefix' => 'p',
            'longPrefix' => 'page',
            'description' => 'Janitor Page URI',
            'required' => true,
            'castTo' => 'string',
        ],
        'data' => [
            'prefix' => 'd',
            'longPrefix' => 'data',
            'description' => 'Janitor Data',
            'required' => false,
        ]
    ];

    /** @var $data array */
    private static array $data;

    public function data(string $command, ?array $data = null): ?array
    {
        if ($data) {
            Janitor::$data[$command] = $data;
        }
        return A::get(Janitor::$data, $command);
    }

    /**
     * @var array
     */
    private array $options;

    /**
     * Janitor constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $defaults = [
            'debug' => option('debug'),
            'secret' => option('bnomei.janitor.secret'),
        ];
        $this->options = array_merge($defaults, $options);

        foreach ($this->options as $key => $call) {
            if (is_callable($call) && $key == 'secret') {
                $this->options[$key] = $call();
            }
        }
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function option(?string $key = null): mixed
    {
        if ($key) {
            return A::get($this->options, $key);
        }

        return $this->options;
    }

    /**
     * @param string $secret
     * @param string $name
     * @param array $args
     * @return array
     */
    public function jobWithSecret(string $secret, string $name, array $args = []): array
    {
        if ($secret == $this->option('secret')) {
            return $this->job($name, $args);
        }

        return [
            'status' => 401,
        ];
    }

    /**
     * @param string $name
     * @param array $args
     * @return array
     */
    public function job(string $name, array $args = []): array
    {
        CLI::command($name, ...$args);

        return $this->data($name) ?? [
            'status' => 200,
            'message' => 'Janitor has no data from command "' . $name . '".',
        ];
    }

    /*
     * @var Janitor
     */
    private static $singleton;

    /**
     * @param array $options
     * @return Janitor
     */
    public static function singleton(array $options = []): Janitor
    {
        if (self::$singleton) {
            return self::$singleton;
        }

        self::$singleton = new Janitor($options);

        return self::$singleton;
    }

    /**
     * @param string|null $template
     * @param mixed|null $model
     * @return string
     */
    public static function query(string $template = null, mixed $model = null): string
    {
        $page = null;
        $file = null;
        $user = kirby()->user();
        if ($model instanceof Page) {
            $page = $model;
        } elseif ($model instanceof File) {
            $file = $model;
        } elseif ($model instanceof User) {
            $user = $model;
        }

        return Str::template($template, [
            'kirby' => kirby(),
            'site' => kirby()->site(),
            'page' => $page,
            'file' => $file,
            'user' => $user,
            'model' => $model ? get_class($model) : null,
        ]);
    }

    /**
     * @param $val
     * @param bool $return_null
     * @return bool
     */
    public static function isTrue($val, bool $return_null = false): bool
    {
        $boolval = (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val);

        return ($boolval === null && !$return_null ? false : $boolval);
    }
}
