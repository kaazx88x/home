<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Repositories\ProductRepo;
use Validator;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\S3ClientRepo;
use App\Repositories\ProductImageRepo;
use App\Repositories\ProductPricingRepo;
use Helper;

class ProductImageController extends Controller
{
    public function __construct(ProductRepo $productrepo, ProductImageRepo $productimagerepo, ProductPricingRepo $productpricingrepo) {
        $this->product = $productrepo;
        $this->image = $productimagerepo;
        $this->pricing = $productpricingrepo;
    }

    public function image_script()
    {
        $products = Product::all();

        foreach ($products as $product)
        {
            if(!empty($product->pro_Img))
            {
                $images = array_filter(array_map('trim',explode('/**/', $product->pro_Img)));
                foreach($images as $key => $image)
                {
                    ProductImage::create([
                        'pro_id' => $product->pro_id,
                        'title' => '',
                        'image' => $image,
                        'status' => 1,
                        'order' => $key+1,
                        'main' => ($key == 0)? 1 : 0, //temp set main image to first image in loop
                    ]);
                }
                echo 'Successfully export ('. $product->pro_id.'-'.$product->pro_title_en.') image to nm_product_image<br>';
            }
        }
    }

    public function product_image_saveorder()
    {
        $input = \Request::only('newOrder');
        $save = $this->image->product_image_saveorder($input);
        return $save;
    }

    public function set_main_image($id, $mer_id)
    {
        $image = $this->image->get_product_image_by_id($id);
        $pro_id = $image->pro_id;

        if($image->status == 0)
            return back()->with('error', trans('localize.Failed_to_update_image_please_set_image_status_to_active_first'));

        $old_thumbnail_image = 'thumbnail_'.$this->image->get_product_main_image($pro_id);
        $new_thumbnail_image = 'thumbnail_'.$image->image;

        $update = $this->image->set_product_main_image($id, $pro_id, $mer_id);

        if($update == 'success')
            Helper::set_product_thumbnail_image($new_thumbnail_image, $old_thumbnail_image, $pro_id, $mer_id);

        return back()->with('success', trans('localize.Successfully_set_main_image'));
    }

    public function toggle_image_status($id)
    {
        $image = $this->image->get_product_image_by_id($id);
        if($image->main == 1)
            return back()->with('error', trans('localize.Failed_to_update_image_status'));

        $this->image->toggle_image_status($id);

        return back()->with('success', trans('localize.Successfully_update_image_status'));
    }

    public function view_product_image($mer_id,$pro_id)
    {
        $product = $this->product->get_merchant_product_details($mer_id, $pro_id);
        $images = $this->image->get_product_image_by_pro_id($pro_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'productmanageedit');

        return view('admin.product.image', compact('product','images','mer_id','pro_id','edit_permission'));
    }

    public function upload_product_image()
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $mer_id = $data['mer_id'];
            $pro_id = $data['pro_id'];

            $v = Validator::make($data, [
                'file' => 'mimes:jpeg,jpg,png|required|max:1000',

	        ],[
                'file.required' => trans('localize.Image_File_is_required'),
                'file.mimes' => trans('localize.Only_jpeg_bmp_png_gif_or_svg_file_is_allowed'),
                'file.max' => trans('localize.Image_size_is_too_big_maximum_size_is_1mb'),
            ]);
            if ($v->fails())
               return back()->withInput()->withErrors($v);

            if (!empty($data['file'])) {

                $file = $data['file'];
                $main_image = 0;

                $image = Helper::upload_image($file, $main_image, $mer_id);

                $data['new_file_name'] = $image;
                $save_image = $this->image->add_product_image($pro_id,$data);

                if($data['pro_status'] == 1)
                    return back()->with('success', trans('localize.Successfully_add_new_image'));

                $product_completed_status = $this->pricing->get_product_pricing_by_pro_id($pro_id);
                if(!$product_completed_status->isEmpty()) {
                try {
                    $this->product->update_product_status($pro_id, 0);
                    } catch (\Exception $e) {
                        return back()->with('error', trans('localize.Unable_to_set_product_completed_status'));
                    }
                }

                return back()->with('success', trans('localize.Successfully_add_new_image'));
            }

            return back()->with('error', trans('localize.Please_insert_image_file'));

        }
    }

    public function delete_product_image($id,$mer_id)
    {

        $image = $this->image->get_product_image_by_id($id);

        $path = 'product/'.$mer_id;
        S3ClientRepo::Delete($path, $image->image);

        $this->image->delete_product_image($id);

        return back()->with('success', trans('localize.Suuccessfully_delete_image'));

    }

    public function get_main_image_by_pro_id($pro_id)
    {
        return ProductImage::where('pro_id',$pro_id)->wehere('main','=',1)->first();
    }

}