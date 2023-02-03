<?php
namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Repositories\ProductRepo;
use App\Repositories\ProductImageRepo;
use App\Repositories\S3ClientRepo;
use App\Repositories\ProductPricingRepo;
use Validator;
use Helper;

class ProductImageController extends Controller
{
    private $mer_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(\Auth::guard('merchants')->check()) {
                $this->mer_id = \Auth::guard('merchants')->user()->mer_id;
            }

            if(\Auth::guard('storeusers')->check()) {
                $this->mer_id = \Auth::guard('storeusers')->user()->mer_id;
            }

            return $next($request);
        });
    }

    public function product_image_saveorder()
    {
        $input = \Request::only('newOrder');
        $save = ProductImageRepo::product_image_saveorder($input);

        return $save;
    }

    public function set_main_image($id)
    {
        $mer_id = $this->mer_id;

        $image = ProductImageRepo::get_product_image_by_id($id);
        $pro_id = $image->pro_id;
        if($image->status == 0)
            return back()->with('error','Failed to update image, please set image status to active first');

        $old_thumbnail_image = 'thumbnail_'.ProductImageRepo::get_product_main_image($pro_id);
        $new_thumbnail_image = 'thumbnail_'.$image->image;

        $update = ProductImageRepo::set_product_main_image($id, $pro_id, $mer_id);

        if($update == 'success')
            Helper::set_product_thumbnail_image($new_thumbnail_image, $old_thumbnail_image, $pro_id, $mer_id);

        return back()->with('success','Successfully set main image');
    }

    public function toggle_image_status($id)
    {
        $image = ProductImageRepo::get_product_image_by_id($id);
        if($image->main == 1)
            return back()->with('error','Failed to update image status, please set main image to other first');

        ProductImageRepo::toggle_image_status($id);

        return back()->with('success','Successfully update image status');
    }

    public function view_product_image($pro_id)
    {
        $mer_id = $this->mer_id;

        $product = ProductRepo::get_merchant_product_details($mer_id, $pro_id);
        $images = ProductImageRepo::get_product_image_by_pro_id($pro_id);

        return view('merchant.product.image', compact('product','images','mer_id','pro_id'));
    }

    public function upload_product_image()
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $mer_id = $this->mer_id;
            $pro_id = $data['pro_id'];

            $v = Validator::make($data, [
                'file' => 'required|mimes:jpeg,jpg,png|max:1000',

	        ],[
                'file.required' => 'Image File is required',
                'file.mimes' => 'Only jpeg,jpg and png file is allowed',
                'file.max' => 'Image size is too big, maximum size is 1mb',
            ]);
            if ($v->fails())
               return back()->withInput()->withErrors($v);

            if (!empty($data['file'])) {

                $file = $data['file'];
                $main_image = 0;

                $image = Helper::upload_image($file, $main_image, $mer_id);

                $data['new_file_name'] = $image;
                $save_image = ProductImageRepo::add_product_image($pro_id,$data);

                if($data['pro_status'] == 1)
                    return back()->with('success','Successfully add new image');

                $product_completed_status = ProductPricingRepo::get_product_pricing_by_pro_id($pro_id);
                if(!$product_completed_status->isEmpty()) {
                try {
                    $this->product->update_product_status($pro_id, 0);
                    } catch (\Exception $e) {
                        return back()->with('error', 'Unable to set product completed status');
                    }
                }

                return back()->with('success','Successfully add new image');
            }

            return back()->with('error','Please insert image file');
        }
    }

    public function delete_product_image($id)
    {
        $image = ProductImageRepo::get_product_image_by_id($id);
        $mer_id = $this->mer_id;

        $path = 'product/'.$mer_id;
        S3ClientRepo::Delete($path, $image->image);

        ProductImageRepo::delete_product_image($id);

        return back()->with('success','Suuccessfully delete image');

    }

    public function get_main_image_by_pro_id($pro_id)
    {
        return ProductImage::where('pro_id',$pro_id)->wehere('main','=',1)->first();
    }
}
