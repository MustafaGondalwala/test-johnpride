<?php

namespace App\Providers;

use App\Setting;
use App\Category;
use App\Helpers\CategoryHelper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load all Settings from model
        /*$settings = Setting::all();

        // Set or retrieve $categories from cache
        $categories = cache()->remember('categories', 60, function () {
            $categories['all'] = Category::all();

            if ($categories['all']->count() > 0) {
                $categoriesGroupedByParent = Category::orderBy('parent_id')->orderBy('sort_order')->get()->groupBy('parent_id');

                $categories['list'] = CategoryHelper::categoriesAsList($categoriesGroupedByParent);

                $categories['tree'] = CategoryHelper::buildTree($categories['all']->toArray());
            } else {
                $categories['list'] = [];

                $categories['tree'] = [];
            }

            return $categories;
        });

        // Set $categories to 'global' app variables
        app()->global = [
            'settings' => $settings->keyBy('name')->toArray(),
            'categories' => $categories
        ];

        // Also share $categories to all views
        view()->share(compact('categories'));*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);
    }
}
