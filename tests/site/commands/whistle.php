<?php

use Bnomei\Janitor;
use Kirby\CLI\CLI;

return [
    'description' => 'Whistle',
    'args' => [] + Janitor::ARGS,
    'command' => static function (CLI $cli): void {
        defined('STDOUT') && $cli->success('whistle');

        janitor()->data($cli->arg('command'), [
            'status' => 200,
            'label' => '♫',
        ]);
    }
];
