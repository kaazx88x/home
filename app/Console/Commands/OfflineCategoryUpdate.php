<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\OfflineCategory;

class OfflineCategoryUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:offline-category-childs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate un-hashed and not empty customer payment securecode';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       try {
            $this->info('starting update now...  ' . date('d M Y h:i a'));
            $imglist = $this->migrate();
            $this->info('update complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function migrate()
    {
        // Update All child_list
        $categories = OfflineCategory::all();
        foreach ($categories as $key => $category) {
            if ($category->parent_list) {
                $parents = explode(',', $category->parent_list);

                foreach ($parents as $key => $parent) {
                    $parent_cat = OfflineCategory::where('id', $parent)->first();

                    if ($parent_cat) {
                        if (!in_array($category->id, explode(',', $parent_cat->child_list))) {
                            // Update parent category child_list
                            \DB::statement("UPDATE offline_categories SET child_list = IF(child_list IS NULL," . $category->id . ", CONCAT(child_list, ',' , " . $category->id . ")) where id = " . $parent_cat->id . ";");
                        }
                    }
                }
            }
        }
    }
}
