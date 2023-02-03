<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;

use App\Repositories\MerchantRepo;
use App\Repositories\MainCategoryRepo;
use Validator;

class DealController extends Controller
{
    public function __construct(MainCategoryRepo $categoryrepo, MerchantRepo $merchantrepo) {

        $this->category = $categoryrepo;
        $this->merchant = $merchantrepo;
    }

    public function add_deal()
    {
        $merchants = $this->merchant->all();
        $categories = $this->category->get_category();

        return view('admin.deal.add', compact('merchants','categories'));
    }
}