# Artim Logger

## How to start

At first we need install package:

```bash
composer require artim/logger
```

Add LARAVEL_START const to artisan file in root directory:

```php
define('LARAVEL_START', microtime(true));
```

This is necessary to fix the start time of request processing.

Then we need append ArtimLoggerServiceProvider.php to app.php config

```php
class ArtimLoggerServiceProvider extends ServiceProvider  
{  
  public function boot(): void  
  {  
      $this->publishes([  
          __DIR__ . '/../config/artim-logger.php' => config_path('artim-logger.php'),  
     ], 'config');  
  }

  public function register(): void  
  {  
      $this->app->make(LogRegistrator::class)->set();  
      $this->app->make(HttpRegistrator::class)->set();  
      $this->app->make(DBLogRegistrator::class)->set();  
      $this->app->make(AppLogRegistrator::class)->set();  

      $this->mergeConfigFrom(__DIR__ . '/../config/artim-logger.php', 'artim-logger');
 }
}
```

It's register Logger

```php
$this->app->make(LogRegistrator::class)->set();
```

It's register Http with method Http::withLogToken to add LOG-TOKEN to request in your another projects

```php
$this->app->make(HttpRegistrator::class)->set();
```

It's register DB listener on every request to database and log it

```php
$this->app->make(DBLogRegistrator::class)->set();
```

It's register handler for app-terminating event for log time and request payload

```php
$this->app->make(AppLogRegistrator::class)->set();
```

Then we need add Http facade to list of aliases in app.php:

```php
'Http' => \Artim\Logger\Http\Http::class,
```

Now you can append configuration for logger in logging.php:

```php
'stack' => [  
  'driver' => 'custom',  
  'via' => \Artim\Logger\Logger\File\FileLogSetter::class,  
  
  'handler' => \Monolog\Handler\StreamHandler::class,  
  'handler_with' => [  
    'stream' => storage_path('logs/laravel-test.log'),  
   ],  
  'formatter' => \Artim\Logger\Logger\File\JsonFormatter::class,  
  'formatter_with' => [  
    'dateFormat' => 'Y-m-d H:i:s',  
    'includeStackTraces' => true,  
   ],
 ],
```

It will write logs to storage/logs/laravel-test.log file:

```json
{  
    "message": "App terminating",  
    "additional": {  
        "startedAt": 1666384186.589226,  
        "endedAt": 1666384186.745444,  
        "request": {  
            "method": "GET",  
            "uri": "/",  
            "body": [],  
            "headers": {/* headers */},  
            "files": []  
        }  
    },  
    "level": "INFO",  
    "datetime": "2022-10-21 20:29:46",  
    "type": "application",  
    "user": null,  
    "token": "c56cea9b6bdd595e6cb470a3d6b53417"  
}
```

Если вы хотите отследить действия вашего приложения даже внутри очередей, вы можете использовать класс Artim\Logger\Job

```php
namespace Artim\Logger\Job;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class Job implements ShouldQueue
{
    protected string $logToken;

    public function __construct()
    {
        $this->logToken = get_log_token();
    }

    public function handle(): void
    {
        set_log_token($this->logToken);
    }
}
```

