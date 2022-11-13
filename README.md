# Kirby 3 Janitor

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby3-janitor?color=ae81ff)
![Downloads](https://flat.badgen.net/packagist/dt/bnomei/kirby3-janitor?color=272822)
[![Build Status](https://flat.badgen.net/travis/bnomei/kirby3-janitor)](https://travis-ci.com/bnomei/kirby3-janitor)
[![Coverage Status](https://flat.badgen.net/coveralls/c/github/bnomei/kirby3-janitor)](https://coveralls.io/github/bnomei/kirby3-janitor)
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/kirby3-janitor)](https://codeclimate.com/github/bnomei/kirby3-janitor)
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)

Kirby 3 Plugin for running commands.

- It is a Panel Button!
- It has commands built-in for cleaning the cache, sessions, create zip-backup, pre-generate thumbs, open URLs, refresh the current Panel page and more.
- You can define your own commands (call API hooks, play a game, hack a server, ...)
- It can be triggered in your frontend code, with the official kirby CLI and a CRON.

## Install

Using composer:

```bash
composer require getkirby/cli bnomei/kirby3-janitor
```

Using git submodules:

```bash
git submodule add https://github.com/getkirby/cli.git site/plugins/cli
git submodule add https://github.com/bnomei/kirby3-janitor.git site/plugins/kirby3-janitor
```

Using download & copy: download [the latest release of this plugin](https://github.com/bnomei/kirby3-janitor/releases) and [the latest release of the kirby cli](https://github.com/getkirby/cli/releases) then unzip and copy them to `site/plugins`

## Commercial Usage

> <br>
> <b>Support open source!</b><br><br>
> This plugin is free but if you use it in a commercial project please consider to sponsor me or make a donation.<br>
> If my work helped you to make some cash it seems fair to me that I might get a little reward as well, right?<br><br>
> Be kind. Share a little. Thanks.<br><br>
> &dash; Bruno<br>
> &nbsp;

| M | O | N | E | Y |
|---|----|---|---|---|
| [Github sponsor](https://github.com/sponsors/bnomei) | [Patreon](https://patreon.com/bnomei) | [Buy Me a Coffee](https://buymeacoff.ee/bnomei) | [Paypal dontation](https://www.paypal.me/bnomei/15) | [Hire me](mailto:b@bnomei.com?subject=Kirby) |

## Setup

### CLI Command

In any blueprint create a janitor field with your command, browse to that page in panel and press the button.

**site/blueprints/page/default.yml**
```yml
title: Default Page
fields:
  call_my_command:
    type: janitor
    command: 'example --data test'
    label: Call `Example` Command
```

Janitor will automatically fill in the current model.

- So for example if you press the panel button on a page you will have `--page` argument set.
- If you call it on a user then `--user` arg will be set.
- On a panel file view... `--file`.
- And lastly `--site` will be automatically set when you had the button in the `site/blueprints/site.yml` blueprint.

Create a Kirby CLI command [via a custom plugin](https://getkirby.com/docs/reference/plugins/extensions/commands) or put them into `site/commands`.

**site/commands/example.php**
```php
<?php

use Bnomei\Janitor;
use Kirby\CLI\CLI;

return [
    'description' => 'Example',
    'args' => [] + Janitor::ARGS, // page, file, user, site, data
    'command' => static function (CLI $cli): void {
        $page = page($cli->arg('page'));

        // output for the command line
        defined('STDOUT') && $cli->success(
            $model->title() . ' ' . $cli->arg('data')
        );

        // output for janitor
        janitor()->data($cli->arg('command'), [
            'status' => 200,
            'message' => $model->title() . ' ' . $cli->arg('data'),
        ]);
    }
];

```

### Callback

Instead of using a command you can also create a callback in a custom plugin options or any config file.

**site/config/config.php**
```php
<?php

return [
    'example' => function ($model, $data = null) {
        return [
            'status' => 200,
            'message' => $model->title() . ' ' . $data,
        ];
    },
    // ... other options
];
```

The Janitor plugin has a special command `janitor:job` that you can use to trigger your callback.

**site/blueprints/page/default.yml**
```yml
title: Default Page
fields:
  call_my_command:
    type: janitor
    command: 'janitor:job --key example --data test'
    label: Call `Example` Command
```

The `$model` will match the model of whatever page, file, user or site object you pressed the button at.

> Why just a single model variable instead of one each for page, file, user and site? For one reason to make it work directly with any existing version 2 callbacks you might have already created and secondly because this why it is very easy to get the model that triggered the callback.

### Built in commands and examples

This plugin comes with a [few commands](https://github.com/bnomei/kirby3-janitor/tree/master/commands) you might like to use yourself and some [example commands](https://github.com/bnomei/kirby3-janitor/tree/master/tests/site/commands) used to showcase the various options the button has (like how to change the icon or open a URL in a new tab). Some commands can be used in both panel and terminal. Others are limited in their use to either one of them. In the terminal you can use `--help` argument to view the help for each command.

- `janitor:backupzip`, creates a backup zip
- `janitor:cleancontent`, removes fields from content file that are not defined in your blueprints
- `janitor:clipboard`, copies a defined value to your clipboard
- `janitor:download`, triggers a download of an URL
- `janitor:flush`, flush a cache by providing its name
- `janitor:job`, run a callback
- `janitor:maintenance`, toggle maintenance mode
- `janitor:open`, triggers opening of an URL in panel
- `janitor:pipe`, map input argument to output argument
- `janitor:render`, render a certain page or all pages (to create thumb jobs)
- `janitor:thumbs`, process thumb jobs of a certain page or all pages
- `janitor:tinker`, run a REPL session in terminal

### Blueprint field options

The button you create with the `field: janitor` in your blueprint can be configured to do various things. Checkout the [example default.yml blueprint](https://github.com/bnomei/kirby3-janitor/blob/master/tests/site/blueprints/pages/default.yml) to familiarize yourself with how to use it.

- `autosave`, if `true` then save before pressing the button
- `command`, command like you would enter it in terminal, with [query language support](https://getkirby.com/docs/guide/blueprints/query-language) and page/file/user/site/data arguments
- `cooldown`, time in milliseconds the message is flashed on the button (default: 2000)
- `error`, set message to show on all **non-200**-status returns with query language support
- `icon`, set the [icon](https://getkirby.com/docs/reference/panel/icons) of the button
- `intab`, if `true` then use in combination with the `open`-option to open an URL in a new tab
- `label`, set label of the button
- `progress`, set message to show while the button waits for the response, with query language support
- `success`, set message to show on all **200**-status returns, with query language support
- `unsaved`, if `false` then disable the button if panel view has unsaved content

### Janitor API options

In either the command or the callback you will be setting/returning data to the Janitor button via its api. Depending on what you return you can trigger various things to happen in the panel.

- `clipboard`, string to copy to clipboard
- `download`, URL to start downloading
- `error`, see `error`-field option
- `icon`, see `icon`-field option
- `label`, see `label`-field option
- `message`, see `message`-field option
- `open`, URL to open, use with `intab`-field option to open in a new tab
- `reload`, if `true` will reload panel view once api call is received
- `success`, see `success`-field option
- `status`, return `200` for a **green** button flash, anything else for a **red** flash

### Run commands in your code

You can run any command in you own code as well like in a model, template, controller or hook. Since commands do not return data directly you need to retrieve data stored for janitor using a helper `janitor()->data($commandName)`.

#### Get data returned from a command
```php
Kirby\CLI\CLI::command('whistle'); // tests/site/commands/whistle.php
var_dump(janitor()->data('whistle'));
```

#### Create and download a backup
```php
Kirby\CLI\CLI::command('janitor:backupzip');
$backup = janitor()->data('janitor:backupzip')['path'];
if(F::exists($backup)) {
  Header::download([
    'mime' => F::mime($backup),
    'name' => F::filename($backup),
  ]);
  readfile($backup);
  die(); // needed to make content type work
}
```

### Examples

Again... check out the [built-in commands](https://github.com/bnomei/kirby3-janitor/tree/master/commands) and plugin [example commands](https://github.com/bnomei/kirby3-janitor/tree/master/tests/site/commands) to learn how to use the field and api options yourself.

```yml
  test_ping:
    type: janitor
    command: 'ping' # see tests/site/commands/ping.php
    label: Ping
    progress: ....
    success: Pong
    error: BAMM

  janitor_open:
    type: janitor
    command: 'janitor:open --data {{ user.panel.url }}'
    intab: true
    label: Open current user URL in new tab
    icon: open
    # the open command will forward the `data` arg to `open` and open that URL

  janitor_clipboarddata:
    type: janitor
    command: 'janitor:clipboard --data {{ page.title }}'
    label: 'Copy "{{ page.title }}" to Clipboard'
    progress: Copied!
    icon: copy
    # the clipboard command will forward the `data` arg to `clipboard` and copy that

  janitor_download:
    type: janitor
    command: 'janitor:download --data {{ site.index.files.first.url }}'
    label: Download File Example
    icon: download
    # the download command will forward the `data` arg to `download` and start downloading that

  janitor_backupzip:
    type: janitor
    command: 'janitor:backupzip'
    cooldown: 5000
    label: Generate Backup ZIP
    icon: archive

  janitor_render:
    type: janitor
    command: 'janitor:render'
    label: Render pages to create missing thumb jobs

  janitor_thumbssite:
    type: janitor
    command: 'janitor:thumbs --site'
    label: Generate thumbs from existing thumb jobs (full site)
```

If you want you can also call any of [the core shipping with the CLI](https://github.com/getkirby/cli#available-core-commands) like `clear:cache`.

## Dependencies

- [Kirby CLI](https://github.com/getkirby/cli)
- [CLImate](https://github.com/thephpleague/climate)
- [Symfony Finder](https://symfony.com/doc/current/components/finder.html)

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-janitor/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

