<?php

namespace KBox\Providers;

use Carbon\Carbon;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use KBox\DocumentsElaboration\DocumentElaborationManager;
use KBox\Services\Quota;
use Jenssegers\Date\Date as LocalizedDate;
use KBox\Changelog\ChangelogCommand;
use KBox\Changelog\ReleaseCommand;
use KBox\Pages\Page;
use Klink\DmsSearch\SearchRequest;
use Oneofftech\Identities\Facades\Identity;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Identity::useNamespace("KBox\\");
        Identity::useIdentityModel("KBox\\Identity");
        Identity::useUserModel("KBox\\User");

        /**
         * Check if a non empty input value is NOT an array.
         * No additional parameters are supported
         */
        Validator::extend('not_array', function ($attribute, $value, $parameters, $validator) {
            return ! is_array($value);
        });

        /**
         * Register a request macro that checks if the request comes from a K-Link that want to download the document
         */
        Request::macro('isKlinkRequest', function () {
            
            // This is a way of identifying that the request is coming from the K-Search, as, thanks to the proxy,
            // the real host and IP addresses are not available
            return $this->isMethod('get')
                   && network_enabled()
                   && Str::contains(strtolower($this->userAgent()), 'guzzlehttp');
        });

        /**
         * Tells if the current request is a search request
         */
        Request::macro('hasSearch', function () {
            return optional($this->search())->isSearchRequested() ?? false;
        });

        /**
         * Get the @see SeachRequest instance generated from the current request
         */
        Request::macro('search', function () {
            $req = SearchRequest::create($this);

            // Number of Items per page

            $items_per_page = (int)optional(auth()->user())->optionItemsPerPage() ?? 12;

            $requested_items_per_page = (int)$this->input('n', $items_per_page);

            try {
                if ($items_per_page !== $requested_items_per_page) {
                    optional(auth()->user())->setOptionItemsPerPage($requested_items_per_page);
                    $items_per_page = $requested_items_per_page;
                }
            } catch (\Exception $limit_ex) {
            }

            $req->limit($items_per_page);

            return $req;
        });

        /**
         * Register a response macro that respond as an head request
         */
        Response::macro('head', function ($headers) {
            return response('', 200, array_merge(['Content-Length' => 0], $headers));
        });

        /**
         * Register the flags helper as a blade if statement
         */
        Blade::if('flag', function ($flag) {
            return flags($flag);
        });

        /**
         * Define date related directive for blade
         */

        Blade::directive('date', function ($expression) {
            return "<?php echo optional($expression)->render(); ?>";
        });
        
        Blade::directive('datetime', function ($expression) {
            return "<?php echo optional($expression)->render(true); ?>";
        });

        /**
         * Register the haspage helper as a blade if statement.
         *
         * Check if a page exists, in any language
         */
        Blade::if('haspage', function ($page) {
            return ! is_null(Page::find($page));
        });

        /**
         * Register a macro on Carbon to convert a Carbon instance
         * in a fully localizable date time.
         *
         * This should only be required until the upgrade to
         * Carbon 2 can be performed. https://carbon.nesbot.com/docs/#api-localization
         *
         * @return \Jenssegers\Date\Date
         */
        Carbon::macro('asLocalizableDate', function () {
            return LocalizedDate::instance($this);
        });
        
        /**
         * Attempt to render a date/time in a common format across all K-Box UI
         *
         * @param bool $withTime set to true to include hours and minutes. Default false
         * @return string the formatted and localized datetime
         */
        Carbon::macro('render', function ($withTime = false) {
            $format = (trans($withTime ? 'units.date_format_full' : 'units.date_format'));

            return $this->asLocalizableDate()->format($format).($withTime ? ' (UTC)' : '');
        });

        $this->registerDevelopmentCommand();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // This service provider is a great spot to register your various container
        // bindings with the application. As you can see, we are registering our
        // "Registrar" implementation here. You can add your own bindings too!

        // $this->app->bind(
        //     'Illuminate\Contracts\Auth\Registrar',
        //     \KBox\Services\Registrar::class
        // );

        $this->app->singleton(DocumentElaborationManager::class, function ($app) {
            return new DocumentElaborationManager();
        });
        
        $this->app->singleton(Quota::class, function ($app) {
            return new Quota();
        });

        // Loading workbench service provider only if running in console (aka Artisan) and the environment is set to 'development'

        if ($this->app->runningInConsole() && $this->app->environment('development')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    protected function registerDevelopmentCommand()
    {
        if ($this->app->runningInConsole() && $this->app->environment('local', 'testing')) {
            $this->commands([
                ChangelogCommand::class,
                ReleaseCommand::class,
            ]);
        }
    }
}
