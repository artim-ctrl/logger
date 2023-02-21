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

Then we need append ArtimLoggerServiceProvider.php to app.php config.
Here you can see the effect of configuration settings on the information that will be logged.

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

        if (config('artim-logger.logs.application')) {
            $this->app->make(AppLogRegistrator::class)->set();
        }

        if (config('artim-logger.logs.db')) {
            $this->app->make(DBLogRegistrator::class)->set();
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/artim-logger.php', 'artim-logger');
    }
}
```

```php
class AppLogRegistrator extends AbstractRegistrator
{
    public function set(): void
    {
        $this->app->terminating(function () {
            $data = [
                'type' => 'application',
                'startedAt' => LARAVEL_START,
                'endedAt' => microtime(true),
            ];

            if (config('artim-logger.logs.request')) {
                $data['request'] = request();
            }

            \Log::info('App terminating', $data);
        });
    }
}
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

If you want to track the action of your application inside the queues, you can use the class Artim\Logger\Job

```php
namespace Artim\Logger\Job;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class Job implements ShouldQueue
{
    use LogToken;

    protected string $logToken;
}
```

