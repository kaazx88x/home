<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Admin\Controller;
use App\Repositories\BlogRepo;
use App\Repositories\InquiriesRepo;
use App\Models\Customer;
use App\Models\Inquiries;
use App\Models\City;
use App\Models\Country;
use App\Models\Blog;
use Validator;

class BlogController extends Controller
{
    public function __construct(BlogRepo $BlogRepo) {
        $this->blog = $BlogRepo;

    }

    public function manage()
    {
        return view('admin.blog.manage', ['blogs'=>$this->blog->all()]);
    }




}
