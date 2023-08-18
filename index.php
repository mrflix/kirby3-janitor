<?php

use Bnomei\Janitor;
use Kirby\Cms\Field;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Cms\Site;
use Kirby\Cms\User;
use Kirby\Toolkit\I18n;

@include_once __DIR__ . '/vendor/autoload.php';

/*
janitor [noun]
one who keeps the premises of a building (such as an apartment or
office) clean, tends the heating system, and makes minor repairs
*/

Kirby::plugin('bnomei/janitor', [
	'options' => [
		'label.cooldown' => 2000, // ms
		'secret' => null,
	],
	'commands' => [ // https://github.com/getkirby/cli
		'janitor:backupzip' => require __DIR__ . '/commands/backupzip.php',
		'janitor:call' => require __DIR__ . '/commands/call.php',
		'janitor:cleancontent' => require __DIR__ . '/commands/cleancontent.php',
		'janitor:clipboard' => require __DIR__ . '/commands/clipboard.php',
		'janitor:download' => require __DIR__ . '/commands/download.php',
		'janitor:flush' => require __DIR__ . '/commands/flush.php',
		'janitor:job' => require __DIR__ . '/commands/job.php',
		'janitor:maintenance' => require __DIR__ . '/commands/maintenance.php',
		'janitor:open' => require __DIR__ . '/commands/open.php',
		'janitor:pipe' => require __DIR__ . '/commands/pipe.php',
		'janitor:render' => require __DIR__ . '/commands/render.php',
		'janitor:thumbs' => require __DIR__ . '/commands/thumbs.php',
		'janitor:tinker' => require __DIR__ . '/commands/tinker.php',
		'janitor:undertaker' => require __DIR__ . '/commands/undertaker.php',
	],
	'fields' => [
		'janitor' => [
			'props' => [
				'autosave' => function ($doAutosave = false) {
					return Janitor::isTrue($doAutosave);
				},
				'backgroundColor' => function ($style = 'var(--color-text)') {
					return Janitor::query($style, $this->model());
				},
				'clipboard' => function ($clipboard = null) {
					return Janitor::isTrue($clipboard);
				},
				'command' => function ($command = null) {
					// make lazy by default
					$command = str_replace(['{{', '}}'], ['{(', ')}'], $command);

					// allow non-lazy
					$command = str_replace(['{<', '>}'], ['{{', '}}'], $command);

					// resolve queries
					$command = Janitor::query($command, $this->model());

					// Temporary fix for https://github.com/getkirby/kirby/issues/4955
					$uuid = $this->model()->content()->uuid()?->toString();

					// append model
					if ($this->model() instanceof Page) {
						$uuid = !empty($uuid) ? 'page://' . $uuid : null;
						$command .= ' --page ' . ($uuid ?? $this->model()->id());
					} elseif ($this->model() instanceof File) {
						$uuid = !empty($uuid) ? 'file://' . $uuid : null;
						$command .= ' --file ' . ($uuid ?? $this->model()->id());
					} elseif ($this->model() instanceof User) {
						$uuid = !empty($uuid) ? 'user://' . $uuid : null;
						$command .= ' --user ' . ($uuid ?? $this->model()->id());
					} elseif ($this->model() instanceof Site) {
						$command .= ' --site'; // boolean argument
					}
					$command .= ' --model ' . (
						$uuid ?? ($this->model() instanceof Site ? 'site://' : $this->model()->id())
					);

					$command .= ' --quiet'; // no STDOUT on frontend PHP

					return $command;
				},
				'confirm' => function ($confirm = '') {
					return $confirm;
				},
				'color' => function ($style = 'white') {
					return Janitor::query($style, $this->model());
				},
				'cooldown' => function ($cooldownMilliseconds = null) {
					return (int)($cooldownMilliseconds ?? option('bnomei.janitor.label.cooldown'));
				},
				'error' => function ($label = null) {
					if (kirby()->multilang()) {
						$label = I18n::translate($label, $label, kirby()->language()->code());
					}
					return Janitor::query($label, $this->model());
				},
				'icon' => function ($icon = null) {
					return Janitor::query($icon, $this->model());
				},
				'intab' => function ($intab = false) {
					return Janitor::isTrue($intab);
				},
				'help' => function ($label = null) {
					if (kirby()->multilang()) {
						$label = I18n::translate($label, $label, kirby()->language()->code());
					}
					return Janitor::query($label, $this->model());
				},
				'label' => function ($label = null) {
					if (kirby()->multilang()) {
						$label = I18n::translate($label, $label, kirby()->language()->code());
					}
					return Janitor::query($label, $this->model());
				},
				'progress' => function ($label = null) {
					if (kirby()->multilang()) {
						$label = I18n::translate($label, $label, kirby()->language()->code());
					}
					return Janitor::query($label, $this->model());
				},
				'success' => function ($label = null) {
					if (kirby()->multilang()) {
						$label = I18n::translate($label, $label, kirby()->language()->code());
					}
					return Janitor::query($label, $this->model());
				},
				'unsaved' => function ($allowUnsaved = true) {
					return Janitor::isTrue($allowUnsaved);
				},
			],
		],
	],
	'hooks' => [
		// maintenance
		'route:before' => function () {
			if (Janitor::requestBlockedByMaintenance() &&
				F::exists(kirby()->roots()->index() . '/.maintenance')) {
				snippet('maintenance');
				die;
			}
		},
	],
	'api' => [
		'routes' => [
			[
				'pattern' => 'plugin-janitor/(:all)', // using (:all) fixes issues with kirbys routing for : and /
				'action' => function (string $command) {
					return Janitor::singleton()->command(urldecode($command));
				},
			],
			[
				'pattern' => 'plugin-janitor',
				'method' => 'POST',
				'action' => function () {
					return Janitor::singleton()->command(get('command'));
				},
			]
		],
	],
	'routes' => [
		[
			'pattern' => 'plugin-janitor/(:any)/(:all)', // using (:all) fixes issues with kirbys routing for : and /
			'action' => function (string $secret, string $command) {
				$janitor = Janitor::singleton();
				if ($secret == $janitor->option('secret')) {
					$command = urldecode($command);
					if (!Str::contains($command, ' --quiet')) {
						$command .= ' --quiet';
					}
					return $janitor->command($command);
				}
				return [
					'status' => 401,
				];
			},
		],
		[
			'pattern' => 'plugin-janitor/(:any)',
			'method' => 'POST',
			'action' => function (string $secret) {
				$janitor = Janitor::singleton();
				if ($secret == $janitor->option('secret')) {
					$command = get('command');
					if (!Str::contains($command, ' --quiet')) {
						$command .= ' --quiet';
					}
					return $janitor->command($command);
				}
				return [
					'status' => 401,
				];
			},
		],
	],
	'fieldMethods' => [
		'ecco' => function ($field, string $a, string $b = ''): string {
			return $field->bool() ? $a : $b;
		},
	],
	'siteMethods' => [
		'isUnderMaintenance' => function (): Field {
			return new Field(null, 'isUnderMaintenance', F::exists(kirby()->roots()->index() . '/.maintenance'));
		},
	],
	'snippets' => [
		'maintenance' => __DIR__ . '/snippets/maintenance.php',
	],
	'translations' => [
		'en' => [
			// defined inline as fallbacks
		],
		'de' => [
			'janitor.cleancontent.message' => '{{ updated }} / {{ count }} bereinigt',
			'janitor.maintenance.label' => 'Website ist {{ status }}',
			'janitor.maintenance.down' => 'im Wartungs-Modus',
			'janitor.maintenance.up' => 'online',
			'janitor.maintenance.notice' => 'Diese Webseite ist bald wieder online.', // in snippet
			'janitor.render.message' => '{{ count }} Seiten gerendert',
		],
	]
]);

if (!class_exists('Bnomei\Janitor')) {
	require_once __DIR__ . '/classes/Janitor.php';
}

if (!function_exists('janitor')) {
	function janitor(): Janitor
	{
		return Janitor::singleton();
	}
}

if (!function_exists('undertaker')) {
	function undertaker(\Kirby\Cms\Page $page): array
	{
		return janitor()->command(implode(' ', [
			'janitor:undertaker',
			'--page ' . ($page->uuid()?->toString() ?? $page->id()),
			'--user ' . kirby()->user()?->id(),
			'--quiet'
		]));
	}
}
