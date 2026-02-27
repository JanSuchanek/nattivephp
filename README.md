# NativePHP for Nette

Build native desktop apps with **Nette Framework** and **Electron**.

A bridge between Nette and the [NativePHP](https://nativephp.com) Electron runtime — the same concept as `nativephp/laravel`, but for Nette.

## Installation

```bash
composer require jansuchanek/nattivephp
```

## Configuration

Add to your `config/common.neon`:

```neon
extensions:
    nativephp: NativePHP\Nette\NativePhpExtension

nativephp:
    app_id: com.myapp.desktop
    app_name: My Nette App
    version: 1.0.0
```

Add NativePHP API routes in your `RouterFactory`:

```php
$router->addRoute('_native/api/<action>', [
    'presenter' => 'NativeApi',
    'action' => 'default',
]);
```

## Usage

```php
use NativePHP\Nette\Window;
use NativePHP\Nette\Notification;
use NativePHP\Nette\Clipboard;

class MyPresenter extends Presenter
{
    public function __construct(
        private readonly Window $window,
        private readonly Notification $notification,
        private readonly Clipboard $clipboard,
    ) {}

    public function handleNotify(): void
    {
        $this->notification->send('Hello', 'From Nette!');
    }
}
```

## Available API Classes

| Class | Methods |
|---|---|
| `Window` | open, close, resize, title, position, maximize, minimize, hide, show, reload |
| `Clipboard` | read, write, clear |
| `Notification` | send |
| `Dialog` | open, save |
| `Shell` | openExternal, showItemInFolder, trashItem |
| `Screen` | primaryDisplay, allDisplays, cursorPosition |
| `App` | quit, relaunch, hide, show |

## Code Quality

```bash
# PHPStan (max level)
vendor/bin/phpstan analyse

# Rector
vendor/bin/rector --dry-run

# Tests
vendor/bin/tester tests/
```

## How It Works

```
PHP (Nette)  ←→  HTTP  ←→  Electron (Express API)
     ↑                          ↑
  Your app               NativePHP runtime
```

- **PHP → Electron**: HTTP POST/GET to Express API (window, clipboard, menu, etc.)
- **Electron → PHP**: HTTP POST to `/_native/api/*` (booted, events, cookie)
- **CLI**: `bin/console native:config` + `native:php-ini` for Electron startup

## License

MIT
