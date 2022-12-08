<?php

namespace App\Providers;

use Roots\Acorn\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booted(
            function ($app) {
                set_error_handler(function ($level, $message, $file = '', $line = 0, $context = []) {
                    // Check if this error level is handled by error reporting
                    if (error_reporting() & $level) {
                        // Return false for any error levels that should
                        // be handled by the built in PHP error handler.
                        if ($level & (E_WARNING | E_NOTICE | E_DEPRECATED)) {

                            if (defined('WP_ENV') && WP_ENV !== 'production') {
                                echo "<br />\n";
                                echo "<b>ERROR</b> [$level] $message<br />\n";
                                echo "  Error on line $line in file $file";
                                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                            }

                            return false;
                        }

                        // Throw an exception to be handled by Laravel for all other errors.
                        throw new \ErrorException($message, 0, $level, $file, $line);
                    }
                });
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
