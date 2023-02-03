<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Banner;
use App\Repositories\S3ClientRepo;
class UploadImgToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BannerImg:s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload banner images to S3';

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
            $this->info('uploading now...  ' . date('d M Y h:i a'));
            $imglist = $this->BannerImg();
            $this->info('upload complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function BannerImg()
    {
        $bannerImgList = Banner::where('type', 1)->get();

        foreach ($bannerImgList as $key => $banner) {
            $image = $banner->bn_img;
            \URL::forceRootUrl( \Config::get('app.url') );
            $imageFile = \URL::to('images/banner/' . $image);
            $this->info($imageFile);
            $path = 'banner/';
            if(@file_get_contents($imageFile) && !S3ClientRepo::IsExisted($path, $image))
            {
                $upload = S3ClientRepo::Upload($path, $imageFile, $image);
            }
        }
    }
}
