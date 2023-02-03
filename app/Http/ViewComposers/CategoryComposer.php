<?php

namespace App\Http\ViewComposers;

use Cache;
use Illuminate\View\View;
use App\Repositories\CategoryRepo;
use App\Repositories\CacheRepo;

class CategoryComposer
{
    public $categories = [];
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct(CategoryRepo $categoryrepo)
    {
        $this->category = $categoryrepo;

        // $this->nav_parent = $this->category->mega_menus();
        // $this->nav_parent = Cache::rememberForever('cat_parent', function() {
        //     return CacheRepo::parent_category();
        // });

        // $this->nav_featured = $this->category->navigation('featured');
        // $this->nav_featured = Cache::rememberForever('cat_nav', function() {
        //     return CacheRepo::nav_category();
        // });

        // $this->nav_footer = $this->category->navigation('footer');
        // $this->nav_footer = Cache::rememberForever('cat_footer', function() {
        //     return CacheRepo::footer_category();
        // });

        $this->nav_parent_all = Cache::rememberForever('cat_nav_parent', function() {
            return CacheRepo::nav_parent_category();
        });
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view
        // ->with('parents', $this->nav_parent)
        // ->with('nav_featured', $this->nav_featured)
        ->with('nav_parent_all', $this->nav_parent_all);
        // ->with('nav_footer', $this->nav_footer);
        // ->with('categories', $this->categories);
    }
}
