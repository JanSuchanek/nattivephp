# NativePHP for Nette

Build native desktop apps with **Nette Framework** and **Electron**.

A bridge between Nette and the [NativePHP](https://nativephp.com) Electron runtime — the same concept as `nativephp/laravel`, but for Nette.

## Installation

```bash
composer require jansuchanek/nattivephp
```

## Quick Start

### 1. Register Extension

Add to your `config/common.neon`:

```neon
extensions:
    nativephp: NativePHP\Nette\NativePhpExtension

nativephp:
    app_id: com.myapp.desktop
    app_name: My Nette App
    version: 1.0.0
    provider: App\NativePHP\MyAppProvider
```

### 2. Add API Routes

In your `RouterFactory`:

```php
// NativePHP API endpoints (Electron ↔ PHP communication)
$router->addRoute('_native/api/<action>', [
    'presenter' => 'NativeApi',
    'action' => 'default',
]);
```

### 3. Create NativeApiPresenter

```php
namespace App\Presentation\NativeApi;

use NativePHP\Nette\NativeAppProvider;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;

class NativeApiPresenter extends Presenter
{
    public function __construct(
        private readonly NativeAppProvider $provider,
    ) {
        parent::__construct();
    }

    public function startup(): void
    {
        parent::startup();
        $secret = $this->getHttpRequest()->getHeader('X-NativePHP-Secret');
        $expected = (string) getenv('NATIVEPHP_SECRET');
        $action = $this->getAction();
        if ($expected !== '' && $action !== 'cookie' && $secret !== $expected) {
            $this->getHttpResponse()->setCode(IResponse::S403_Forbidden);
            $this->sendJson(['error' => 'Invalid secret']);
        }
    }

    public function actionBooted(): void
    {
        $this->provider->boot();
        $this->sendJson(['success' => true]);
    }

    public function actionEvents(): void
    {
        $this->sendJson(['success' => true]);
    }

    public function actionCookie(): void
    {
        $secret = (string) getenv('NATIVEPHP_SECRET');
        $this->getHttpResponse()->setCookie('_php_native', $secret, '365 days');
        $this->sendJson(['success' => true]);
    }
}
```

### 4. Override NativeAppProvider (optional)

```php
namespace App\NativePHP;

use NativePHP\Nette\NativeAppProvider;

class MyAppProvider extends NativeAppProvider
{
    public function boot(): void
    {
        $phpPort = (string)
            ($_SERVER['SERVER_PORT'] ??
            getenv('NATIVEPHP_PHP_PORT') ?: '8000');

        $this->window->open(
            id: 'main',
            url: "http://127.0.0.1:{$phpPort}/",
            width: 1024,
            height: 768,
            title: 'My Nette App',
        );
    }
}
```

### 5. Configure Bootstrap

Your `Bootstrap.php` needs to handle NativePHP environment:

```php
public function __construct()
{
    $this->rootDir = dirname(__DIR__);
    $this->configurator = new Configurator;

    // Writable temp dir in NativePHP bundle
    $tempPath = getenv('NATIVEPHP_TEMP_PATH') ?: ($this->rootDir . '/temp');
    if (!is_dir($tempPath)) {
        @mkdir($tempPath, 0777, true);
    }
    $this->configurator->setTempDirectory($tempPath);
}

private function setupContainer(): void
{
    // Dynamic storage path (writable in DMG context)
    $storagePath = getenv('NATIVEPHP_STORAGE_PATH')
        ? (string) getenv('NATIVEPHP_STORAGE_PATH')
        : ($this->rootDir . '/storage');

    if (!is_dir($storagePath)) {
        @mkdir($storagePath, 0777, true);
    }

    $this->configurator->addDynamicParameters([
        'storagePath' => $storagePath,
    ]);

    $this->configurator->addConfig($configDir . '/common.neon');
}
```

## Building a DMG

### Prerequisites

The `electron/` directory from [nette-bridge](https://github.com/JanSuchanek/nattivephp) contains the Electron shell.

### Build Steps

```bash
# 1. Copy your Nette app into electron/resources/app/
cp -R app/ electron/resources/app/app/
cp -R config/ electron/resources/app/config/
cp -R vendor/ electron/resources/app/vendor/

# 2. Install Electron dependencies
cd electron && npm install

# 3. Build DMG (macOS ARM64)
npm run build:mac-arm64

# Output: electron/dist/MyApp-1.0.0-arm64.dmg
```

### Other Platforms

```bash
npm run build:mac-x86     # macOS Intel
npm run build:win-x64     # Windows
npm run build:linux-x64   # Linux AppImage + deb
```

## Using SQLite (Doctrine ORM)

Perfect for desktop apps — no external database server needed.

### Install Nettrine

```bash
composer require nettrine/orm nettrine/dbal nettrine/cache
```

### Configure

```neon
extensions:
    dbal: Nettrine\DBAL\DI\DbalExtension
    orm: Nettrine\ORM\DI\OrmExtension
    orm.cache: Nettrine\Cache\DI\CacheExtension

dbal:
    connections:
        default:
            driver: pdo_sqlite
            path: %storagePath%/app.db   # writable in both dev and DMG

orm:
    managers:
        default:
            connection: default
            proxyDir: %tempDir%/proxies
            autoGenerateProxyClasses: true
            mapping:
                App:
                    type: attributes
                    directories: [%appDir%/Entity]
                    namespace: App\Entity
```

> **Important:** Use `%storagePath%` (not `%appDir%`) for the SQLite path. Inside a DMG, the app bundle is read-only. The `%storagePath%` parameter resolves to `~/Library/Application Support/<app-name>/storage/` on macOS.

### Create an Entity

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'contacts')]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    // ... getters, constructor
}
```

### Auto-create Schema

Add to your presenter's `startup()`:

```php
public function startup(): void
{
    parent::startup();

    // Creates tables on first access (idempotent, fast no-op when schema exists)
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
    $metadata = $this->em->getMetadataFactory()->getAllMetadata();
    if (count($metadata) > 0) {
        $schemaTool->updateSchema($metadata);
    }
}
```

## Available API Classes

All classes follow the same pattern and can be injected via DI. Each PHP class includes `@see` annotations referencing the Electron TypeScript source and Laravel equivalent for easy upstream sync.

| PHP Class | Electron TS Source | Laravel Facade | Key Methods |
|---|---|---|---|
| `Window` | `api/window.ts` | `Window` | open, close, resize, title, maximize, minimize, hide, show, reload |
| `Menu` | `api/menu.ts` | `Menu` | set |
| `MenuBar` | `api/menuBar.ts` | `MenuBar` | create, label, tooltip, icon, contextMenu, show, hide, resize |
| `ContextMenu` | `api/contextMenu.ts` | `ContextMenu` | set, remove |
| `Clipboard` | `api/clipboard.ts` | `Clipboard` | read, write, clear |
| `Notification` | `api/notification.ts` | `Notification` | send |
| `Dialog` | `api/dialog.ts` | `Dialog` | open, save |
| `Alert` | `api/alert.ts` | `Alert` | message, error |
| `Shell` | `api/shell.ts` | `Shell` | openExternal, showItemInFolder, trashItem |
| `Screen` | `api/screen.ts` | `Screen` | primaryDisplay, allDisplays, cursorPosition |
| `App` | `api/app.ts` | `App` | quit, relaunch, hide, show |
| `Dock` | `api/dock.ts` | `Dock` | menu, show, hide, icon, bounce, cancelBounce, getBadge, setBadge |
| `GlobalShortcut` | `api/globalShortcut.ts` | `GlobalShortcut` | register, unregister, isRegistered |
| `ProgressBar` | `api/progressBar.ts` | `ProgressBar` | update |
| `PowerMonitor` | `api/powerMonitor.ts` | `PowerMonitor` | getSystemIdleState, getSystemIdleTime, getCurrentThermalState, isOnBatteryPower |
| `ChildProcess` | `api/childProcess.ts` | `ChildProcess` | start, startPhp, stop, restart, get, all, message |
| `AutoUpdater` | `api/autoUpdater.ts` | `AutoUpdater` | checkForUpdates, downloadUpdate, quitAndInstall |
| `System` | `api/system.ts` | `System` | canPromptTouchID, promptTouchID, encrypt, decrypt, getPrinters, print, printToPdf, getTheme, setTheme |
| `Settings` | `api/settings.ts` | `Settings` | get, set, delete, clear |
| `Broadcasting` | `api/broadcasting.ts` | `Broadcasting` | send |
| `Debug` | `api/debug.ts` | `Debug` | log, info, warning, error |

### Syncing with NativePHP/Laravel Updates

Each PHP class has `@see` annotations linking to:
1. **Electron source**: `electron-plugin/src/server/api/*.ts` — the Express API routes
2. **Laravel facade**: corresponding NativePHP/Laravel class

When upstream NativePHP adds new endpoints:
1. Check `electron-plugin/src/server/api.ts` for new `httpServer.use()` mounts
2. Read the corresponding `.ts` file for endpoint signatures
3. Add matching methods to the PHP wrapper class
4. Register in `NativePhpExtension::loadConfiguration()`

## Usage in Presenters

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

    public function handleCopy(): void
    {
        $this->clipboard->write('Copied from desktop app');
    }
}
```

## Architecture

```
Electron (Express API :4000)  ←→  HTTP  ←→  PHP (Nette :8100)
         ↑                                         ↑
   NativePHP runtime                          Your Nette app
   (window, menu, tray)                    (presenters, forms, DB)
```

- **PHP → Electron**: `Client` sends HTTP POST/GET to Express API (window, clipboard, etc.)
- **Electron → PHP**: HTTP POST to `/_native/api/*` (booted, events, cookie)
- **Environment**: Electron sets `NATIVEPHP_API_URL`, `NATIVEPHP_SECRET`, `NATIVEPHP_STORAGE_PATH`

## Code Quality

```bash
vendor/bin/phpstan analyse    # PHPStan (max level)
vendor/bin/rector --dry-run   # Rector
vendor/bin/tester tests/      # Tests
```

## License

MIT
