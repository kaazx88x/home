<?php
namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Admin\Controller;
use App\Models\Country;
use App\Models\City;
use App\Models\Banner;
use App\Models\AdminSetting;
use App\Models\ProductCategory;
use App\Models\StoreCategory;
use App\Repositories\CourierRepo;
use App\Repositories\CountryRepo;
use App\Repositories\CityRepo;
use App\Repositories\CmsRepo;
use App\Repositories\StateRepo;
use App\Repositories\BannerRepo;
use App\Repositories\CategoryRepo;
use App\Repositories\OfflineCategoryRepo;
use App\Repositories\FilterRepo;
use App\Repositories\S3ClientRepo;
use App\Repositories\WalletRepo;

class SettingController extends Controller
{
    public function __construct(CourierRepo $courierrepo, CountryRepo $countryrepo, CityRepo $cityrepo, CmsRepo $cmsrepo, StateRepo $staterepo, BannerRepo $bannerrepo)
    {
        $this->courier = $courierrepo;
        $this->country = $countryrepo;
        $this->city = $cityrepo;
        $this->cms = $cmsrepo;
        $this->state = $staterepo;
        $this->banner = $bannerrepo;
    }

    public function courier()
    {
        $couriers = $this->courier->all();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
		$admin_permission = Controller::adminPermissionList($adm_id);
        if(in_array('settingcourierlist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}
        $add_permission = Controller::checkAdminPermission($adm_id, 'settingcouriercreate');
        $delete_permission = Controller::checkAdminPermission($adm_id, 'settingcourierdelete');

        return view('admin.setting.courier', compact('couriers','delete_permission','add_permission'));
    }

    public function courier_add()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => trans('localize.Name'),
                'link' => trans('localize.Link'),
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'link' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $courier = $this->courier->add($data);

            return \redirect('admin/setting/courier')->with('success', trans('localize.Courier_is_created'));
        }

        return view('admin.setting.courier_add');
    }

    public function courier_edit($id)
    {
        $courier = $this->courier->find($id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingcourieredit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => trans('localize.Name'),
                'link' => trans('localize.Link'),
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'link' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
               return back()->withInput()->withErrors($v);

            $courier = $this->courier->update($id,$data);

            return view('admin.setting.courier_edit', compact('edit_permission'))->withCourier($courier)->withSuccess('Courier is updated.');
        }
        return view('admin.setting.courier_edit' , compact('edit_permission'))->withCourier($courier);
    }

    public function courier_delete($id)
    {
        $courier = $this->courier->delete($id);
        return \redirect('admin/setting/courier')->with('success', trans('localize.Courier_is_deleted'));
    }

    public function country()
    {
        $countries = $this->country->all();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);

		if(in_array('settingcountrieslist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        $add_permission = in_array('settingcountriescreate', $admin_permission);
        $delete_permission = in_array('settingcountriesdelete', $admin_permission);

        foreach($countries as $index => $country)
        {
            $count = DB::table('nm_state')
            ->where('nm_state.country_id', '=', $country->co_id)
            ->count();

            $country->state_count = $count;
        }

        return view('admin.setting.country', compact('add_permission', 'delete_permission'), ['countries'=>$countries]);
    }

    public function country_add()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => trans('localize.Name'),
                'code`' => trans('localize.code'),
                'cursymbol' => trans('localize.Currency_Symbol'),
                'curcode`' => trans('localize.Currency_Code'),
                'rate`' => trans('localize.Online_Exchange_Rate'),
                'offline_rate`' => trans('localize.Offline_Exchange_Rate'),
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'code' => 'required',
                'cursymbol' => 'required',
                'curcode' => 'required',
                'rate' => 'required',
                'offline_rate' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $country = $this->country->add($data);
            $this->buildCountryStateJs();

            return \redirect('admin/setting/country')->with('success',trans('localize.Country_is_created'));
        }

        return view('admin.setting.country_add');
    }

    public function country_edit($id)
    {
        $country = $this->country->find($id);
        $old_status = $country->co_status;

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingcountriesedit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => trans('localize.Name'),
                'code`' => trans('localize.code'),
                'cursymbol' => trans('localize.Currency_Symbol'),
                'curcode`' => trans('localize.Currency_Code'),
                'rate`' => trans('localize.Online_Exchange_Rate'),
                'offline_rate`' => trans('localize.Offline_Exchange_Rate'),
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'code' => 'required',
                'cursymbol' => 'required',
                'curcode' => 'required',
                'rate' => 'required',
                'offline_rate' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
               return back()->withInput()->withErrors($v);

            $country = $this->country->update($id,$data);
            $this->buildCountryStateJs();

            return view('admin.setting.country_edit', compact('edit_permission'))->withCountry($country)->withSuccess(trans('localize.Country_is_updated'));
        }
        return view('admin.setting.country_edit', compact('edit_permission'))->withCountry($country);
    }

    public function country_delete($id)
    {
        $this->country->delete($id);
        $this->buildCountryStateJs();

        return \redirect('admin/setting/country')->with('success', trans('localize.Country_is_deleted'));
    }

    public function city()
    {
        $cities = DB::table('nm_city')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_city.ci_con_id')
        ->get();
        return view('admin.setting.city', ['cities'=>$cities]);
    }

    public function city_add()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => 'Name',
                'country`' => 'Country',
                'lat' => 'Latitude',
                'long`' => 'Longitude',
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'country' => 'required',
                'lat' => 'required',
                'long' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $city = City::create([
    			'ci_name' => $data['name'],
    			'ci_con_id' => $data['country'],
    			'ci_lati' => $data['lat'],
    			'ci_long' => $data['long'],
    			'ci_status' => $data['status'],
            ]);

            return \redirect('admin/setting/city')->with('success','City is created');
        }

        return view('admin.setting.city_add', ['countries'=>Country::all()]);
    }

    public function city_edit($id)
    {
        $citydetails = DB::table('nm_city')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_city.ci_con_id')
        ->where('nm_city.ci_id', '=', $id)
        ->first();

        $countries = Country::all();

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => trans('localize.Name'),
                'country`' => trans('localize.country'),
                'lat' => trans('localize.latitude'),
                'long`' => trans('localize.longitude'),
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'country' => 'required',
                'lat' => 'required',
                'long' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);
            $city = City::findOrFail($id);
            $city->ci_name = $data['name'];
            $city->ci_con_id = $data['country'];
            $city->ci_lati = $data['lat'];
            $city->ci_long = $data['long'];
            $city->ci_status = $data['status'];
            $city->save();

            return redirect('admin/setting/city/edit/'.$id)->withSuccess('City is updated');
        }
        return view('admin.setting.city_edit')->withCity($citydetails)->withCountries($countries);
    }

    public function city_delete($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return \redirect('admin/setting/city')->with('success','City is deleted');
    }

    public function cms_type()
    {
        $cms_types = CmsRepo::cms_type();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		if(in_array('settingcmslist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}
        $add_permission = in_array('settingcmscreate', $admin_permission);

        return view('admin.setting.cms.type', compact('cms_types', 'add_permission'));
    }

    public function cms($type)
    {
        $cms_type = CmsRepo::cms_type($type);
        $cms_pages = CmsRepo::cms_by_type($type);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		if(in_array('settingcmslist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}
        $add_permission = in_array('settingcmscreate', $admin_permission);
        $delete_permission = in_array('settingcmsdelete', $admin_permission);

        return view('admin.setting.cms.listing' , compact('add_permission', 'delete_permission', 'cms_pages', 'cms_type'));
    }

    public function cms_add($type)
    {
        $cms_type = CmsRepo::cms_type($type);

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            \Validator::extend('alpha_underscore', function($attribute, $value) {
                return preg_match("/^[a-z_]*$/", $value);
            });

            $niceNames = [
                'title_en' => trans('localize.Title') . ' (' . trans('localize.English') . ')',
                'title_cn`' => trans('localize.Title') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Simplified') . ')',
                'title_cnt' => trans('localize.Title') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Traditional') . ')',
                'desc_en`' => trans('localize.Description') . ' (' . trans('localize.English') . ')',
                'desc_cn`' => trans('localize.Description') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Simplified') . ')',
                'desc_cnt`' => trans('localize.Description') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Traditional') . ')',
                'url' => trans('localize.Url_Slug'),
            ];

            $validate = [
                'title_en' => 'required|max:255',
                'desc_en' => 'required',
            ];

            if ($cms_type->type == 'web') {
                $validate['url'] = 'required|max:45|alpha_underscore|unique:nm_cms_pages,cp_url';
            }

            $v = \Validator::make($data, $validate, [
                'url.alpha_underscore' => ':attribute '. trans('localize.only_accept_lowercase_and_underscore_characters'),
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $cms = $this->cms->add($data, $cms_type);

            return \redirect('admin/setting/cms/manage/'.$type)->with('success', trans('localize.CMS_page_is_created'));
        }

        return view('admin.setting.cms.add', compact('cms_type'));
    }

    public function cms_edit($id)
    {
        $cms = $this->cms->find($id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingcmsedit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            \Validator::extend('alpha_underscore', function($attribute, $value) {
                return preg_match("/^[a-z_]*$/", $value);
            });

            $niceNames = [
                'title_en' => trans('localize.Title') . ' (' . trans('localize.English') . ')',
                'title_cn`' => trans('localize.Title') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Simplified') . ')',
                'title_cnt' => trans('localize.Title') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Traditional') . ')',
                'desc_en`' => trans('localize.Description') . ' (' . trans('localize.English') . ')',
                'desc_cn`' => trans('localize.Description') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Simplified') . ')',
                'desc_cnt`' => trans('localize.Description') . ' (' . trans('localize.Chinese') . ' ' . trans('localize.Traditional') . ')',
                'url' => trans('localize.Url_Slug'),
            ];

            $v = \Validator::make($data, [
                'title_en' => 'required|max:255',
                'desc_en' => 'required',
            ],[
                'url.alpha_underscore' => ':attribute '. trans('localize.only_accept_lowercase_and_underscore_characters'),
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $cms = $this->cms->update($id,$data);

            return redirect('admin/setting/cms/edit/'.$id)->withSuccess(trans('localize.CMS_Page_is_updated'));
        }
        return view('admin.setting.cms.edit', compact('edit_permission'))->withCms($cms);
    }

    public function cms_delete($id)
    {
        $this->cms->delete($id);

        return \redirect('admin/setting/cms')->with('success', trans('localize.CMS_Page_is_deleted'));
    }

    public function bannertype()
    {
        $bannertypes = $this->banner->all_bannertypes();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $add_permission = in_array('settingbannercreate', $admin_permission);

        return view('admin.setting.banner_type', compact('bannertypes', 'add_permission'));
    }

    public function bannertype_add()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'name' => 'required|max:40',
                'description' => 'max:255',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $banner = $this->banner->add_bannertype($data);

            return \redirect('admin/setting/banner')->with('success', trans('localize.Banner_Type_is_created'));
        }

        return view('admin.setting.banner_type_add', compact('type'));
    }

    public function bannertype_edit($id)
    {
        $bannertype = $this->banner->find_bannertype($id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id,'settingbanneredit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'name' => 'required|max:40',
                'description' => 'max:255',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $bannertype = $this->banner->update_bannertype($id,$data);

            return \redirect('admin/setting/banner')->with('success', trans('localize.Successfully_edit_Banner_Type'));
        }

        return view('admin.setting.banner_type_edit', compact('bannertype', 'edit_permission'));
    }

    // public function banner_manage()
    // {
    //     $banners = Banner::select('type', \DB::raw('count(*) as total'))->groupBy('type')->get();
    //     return view('admin.setting.banner_manage', compact('banners'));
    // }

    public function banner($type_id)
    {
        $banners = $this->banner->get_banner_by_type_id($type_id)->load('countries');
        $type = $this->banner->find_bannertype($type_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		if(in_array('settingbannerlist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}
        $add_permission = in_array('settingbannercreate', $admin_permission);
        $delete_permission = in_array('settingbannerdelete', $admin_permission);

        return view('admin.setting.banner', compact('add_permission','delete_permission'), ['banners'=>$banners,'type'=>$type]);
    }

    public function banner_saveorder()
    {
        $input = \Request::only('newOrder');

        $result = explode(',',$input['newOrder']);
        foreach ($result as $key => $i)
        {
            $banner = Banner::find($i);
            $banner->order = ($key+1);
            $banner->save();
        }

        return 'success';
    }

    public function banner_add($id)
    {
        $countries = $this->country->get_all_countries();
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'title' => trans('localize.Title'),
                'file`' => trans('localize.Banner_Image'),
                'url' => trans('localize.Redirect_URL'),
                );
            $v = \Validator::make($data, [
                'title' => 'required|max:255',
                'file' => 'required|image|mimes:jpeg,jpg,png|max:1000',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $file = $data['file'];
            $file_name = $file->getClientOriginalName();
            $file_details = explode('.', $file_name);
            $new_file_name = date('Ymd').'_'.str_random(4).'.'.$file_details[1];
            // $move_file = $file->move(public_path('images/banner'), $new_file_name);
            $path = 'banner/';
            if(@file_get_contents($data['file']) && !S3ClientRepo::IsExisted($path, $new_file_name))
                $upload = S3ClientRepo::Upload($path, $data['file'], $new_file_name);

            $data['img'] = $new_file_name;
            $banner = $this->banner->add_banner($data);

            if(\Request::has('banner_country'))
                $this->banner->update_banner_country($banner->bn_id, $data['banner_country']);

            return \redirect('admin/setting/banner/manage/'.$data['type'])->with('success', trans('localize.Banner_is_created'));
        }

        $type = $this->banner->find_bannertype($id);

        return view('admin.setting.banner_add', compact('type', 'countries'));
    }

    public function banner_edit($id)
    {
        $banner = $this->banner->find_banner($id);
        $bannertypes = $this->banner->all_bannertypes();
        $countries = $this->country->get_all_countries();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id,'settingbanneredit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'title' => trans('localize.Title'),
                'file`' => trans('localize.Banner_Image'),
                'url' => trans('localize.Redirect_URL'),
                );
            $v = \Validator::make($data, [
                'title' => 'required|max:255',
                'file' => 'image|mimes:jpeg,jpg,png|max:1000',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            if(\Request::hasFile('file')) {
                $old_img = $banner->bn_img;

                $file = $data['file'];
                $file_name = $file->getClientOriginalName();
                $file_details = explode('.', $file_name);
                $new_file_name = date('Ymd').'_'.str_random(4).'.'.$file_details[1];
                // $move_file = $file->move(public_path('images/banner'), $new_file_name);
                $path = 'banner/';
                if(@file_get_contents($data['file']) && !S3ClientRepo::IsExisted($path, $new_file_name))
                    $upload = S3ClientRepo::Upload($path, $data['file'], $new_file_name);

                //delete old Image
                if(S3ClientRepo::IsExisted($path, $old_img)){
                    $delete = S3ClientRepo::delete($path, $old_img);
                }

                $upload_file = $this->banner->upload_banner_image($id,$new_file_name);
            }

            $banner = $this->banner->update_banner($id,$data);

            if(\Request::has('banner_country')) {
                $this->banner->update_banner_country($id, $data['banner_country']);
            } else {
                $this->banner->remove_all_banner_country($id);
            }

            return redirect('admin/setting/banner/edit/'.$id)->withSuccess(trans('localize.Banner_is_updated'));
        }
        return view('admin.setting.banner_edit', compact('banner', 'bannertypes', 'countries', 'edit_permission'));
    }

    public function banner_delete($id)
    {
        $type = $this->banner->delete_banner($id);

        return \redirect('admin/setting/banner/manage/'.$type)->with('success', trans('localize.Banner_is_deleted'));
    }

    public function state_by_country($co_id)
    {

        $country = $this->country->find($co_id);
        $states = $this->state->all_by_country_id($co_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $add_permission = in_array('settingcountriescreate', $admin_permission);
        $delete_permission = in_array('settingcountriesdelete', $admin_permission);

        return view('admin.setting.state_by_country', compact('states','country','add_permission', 'delete_permission'));
    }

    public function state_add($co_id)
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => trans('localize.Name'),
                'country`' => trans('localize.country'),
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'country' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $state = $this->state->add($data);
            $this->buildCountryStateJs();

            return \redirect('admin/setting/state/'.$state->country_id)->with('success', trans('localize.State_is_created'));
        }

        $country = $this->country->find($co_id);
        return view('admin.setting.state_add', compact('country'));
    }

    public function state_edit($id)
    {
        $state = $this->state->find($id);
        $countries = $this->country->all();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingcountriesedit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name' => trans('localize.Name'),
                'country`' => trans('localize.country'),
                );
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
                'country' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $state = $this->state->update($id,$data);
            $this->buildCountryStateJs();

            return redirect('admin/setting/state/edit/'.$id)->withSuccess(trans('localize.State_is_updated'));
        }
        return view('admin.setting.state_edit', compact('edit_permission'))->withState($state)->withCountries($countries);
    }

    public function state_delete($id,$co_id)
    {
        $state = $this->state->delete($id);
        $this->buildCountryStateJs();

        return \redirect('admin/setting/state/'.$co_id)->with('success', trans('localize.State_is_deleted'));
    }

    public function category_by_parent($parent_id = 0)
    {
        $categories = CategoryRepo::all_category_by_parent($parent_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);

		if(in_array('settingcategoriesonlinelist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        $add_permission = in_array('settingcategoriesonlinecreate', $admin_permission);
        $edit_permission = in_array('settingcategoriesonlineedit', $admin_permission);
        $delete_permission = in_array('settingcategoriesonlinedelete', $admin_permission);

        foreach ($categories as $category) {
            $category['count'] = CategoryRepo::count_child_category($category->id);
            $category['filter'] = FilterRepo::count_category_filter($category->id);
        }

        return view('admin.setting.category.listing', compact('categories', 'parent_id', 'add_permission', 'edit_permission', 'delete_permission'));
    }

    public function add_category($parent_id = 0)
    {
        $parent = CategoryRepo::get_category_by_id($parent_id);
        $wallets = WalletRepo::get_wallet_online_category();

        \Validator::extend('without_spaces', function($attr, $value){
            return preg_match('/^\S*$/u', $value);
        });

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
				'name_en' => trans('localize.Name').' ('. trans('localize.English') .')',
                'name_cn' => trans('localize.Name').' ('. trans('localize.Simplified') .')',
                'name_cnt' => trans('localize.Name').' ('. trans('localize.Traditional') .')',
                'name_my' => trans('localize.Name').' ('. trans('localize.Malay') .')',
                'url_slug' => trans('localize.Url_Slug'),
                'url_slug.without_spaces' => 'No spaces allowed',
                'wallet_id' => trans('localize.wallet_type'),
                );
            $v = \Validator::make($data, [
                'name_en' => 'required|max:255',
                'url_slug' => 'nullable|alpha_dash|max:255|unique:categories,url_slug|without_spaces',
                'wallet_id' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            if ($parent) {
                // get parent_list and add parent_id into list
                $parent_list = $parent->parent_list;
                $data['parent_list'] = ($parent_list) ? $parent_list . ',' . $parent_id : $parent_id;
            }

            try {
                // Upload Image
                if (isset($data['image'])) {
                    $image = $data['image'];
                    $image_name = $image->getClientOriginalName();
                    $image_details = explode('.', $image_name);
                    $new_image_name = date('Ymd').'_'.str_random(4).'.'.$image_details[1];
                    $image_path = 'category/image/';
                    if(@file_get_contents($data['image']) && !S3ClientRepo::IsExisted($image_path, $new_image_name))
                        $upload = S3ClientRepo::Upload($image_path, $data['image'], $new_image_name);

                    $data['image'] = $new_image_name;
                }

                // Upload Banner
                if (isset($data['banner'])) {
                    $banner = $data['banner'];
                    $banner_name = $banner->getClientOriginalName();
                    $banner_details = explode('.', $banner_name);
                    $new_banner_name = date('Ymd').'_'.str_random(4).'.'.$banner_details[1];
                    $banner_path = 'category/banner/';
                    if(@file_get_contents($data['banner']) && !S3ClientRepo::IsExisted($banner_path, $new_banner_name))
                        $upload = S3ClientRepo::Upload($banner_path, $data['banner'], $new_banner_name);

                    $data['banner'] = $new_banner_name;
                }

                $category = CategoryRepo::add_category($data);

                if ($parent) {
                    // Update parent category child_list
                    DB::statement("UPDATE categories SET child_list = IF(child_list IS NULL," . $category->id . ", CONCAT(child_list, ',' , " . $category->id . ")) where id IN (" . $category->parent_list . ");");
                }

                if($category->parent_id == 0 && $data['default_ticket'] == 1) {
                    CategoryRepo::set_default_ticket($category->id);
                }

            } catch (\Exception $e) {
                return back()->withErrors( trans('localize.Unable_to_create_category') );
            }

            return \redirect('admin/setting/category/listing/'.$parent_id)->with('success', trans('localize.Category_is_created'));
        }

        return view('admin.setting.category.add', compact('parent', 'wallets'));
    }

    public function edit_category($id)
    {
        $category = CategoryRepo::get_category_by_id($id);
        $wallets = WalletRepo::get_wallet_online_category();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingcategoriesonlineedit');

        \Validator::extend('without_spaces', function($attr, $value){
            return preg_match('/^\S*$/u', $value);
        });

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name_en' => trans('localize.Name').' ('. trans('localize.English') .')',
                'name_cn' => trans('localize.Name').' ('. trans('localize.Simplified') .')',
                'name_cnt' => trans('localize.Name').' ('. trans('localize.Traditional') .')',
                'name_my' => trans('localize.Name').' ('. trans('localize.Malay') .')',
                'url_slug' => trans('localize.Url_Slug'),
                'wallet_id' => trans('localize.wallet_type'),
                );
            $v = \Validator::make($data, [
                'name_en' => 'required|max:255',
                //'url_slug' => 'required|alpha_dash|max:255|unique:categories,slug',
                'url_slug' => [
                    'nullable',
                    'max:255',
                    'alpha_dash',
                    'without_spaces',
                    Rule::unique('categories')->ignore($category->id),
                ],
                'wallet_id' => 'required',
            ],[
                'url_slug.without_spaces' => 'No spaces allowed',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            // Upload Image
            if (isset($data['image'])) {
                $image_path = 'category/image/';
                if(S3ClientRepo::IsExisted($image_path, $category->image)){
                    S3ClientRepo::delete($image_path, $category->image);
                }

                $image = $data['image'];
                $image_name = $image->getClientOriginalName();
                $image_details = explode('.', $image_name);
                $new_image_name = date('Ymd').'_'.str_random(4).'.'.$image_details[1];
                if(@file_get_contents($data['image']) && !S3ClientRepo::IsExisted($image_path, $new_image_name))
                    $upload = S3ClientRepo::Upload($image_path, $data['image'], $new_image_name);

                $data['image'] = $new_image_name;
            }

            // Upload Banner
            if (isset($data['banner'])) {
                $banner_path = 'category/banner/';
                if(S3ClientRepo::IsExisted($banner_path, $category->banner)){
                    S3ClientRepo::delete($banner_path, $category->banner);
                }

                $banner = $data['banner'];
                $banner_name = $banner->getClientOriginalName();
                $banner_details = explode('.', $banner_name);
                $new_banner_name = date('Ymd').'_'.str_random(4).'.'.$banner_details[1];
                if(@file_get_contents($data['banner']) && !S3ClientRepo::IsExisted($banner_path, $new_banner_name))
                    $upload = S3ClientRepo::Upload($banner_path, $data['banner'], $new_banner_name);

                $data['banner'] = $new_banner_name;
            }

            $category = CategoryRepo::edit_category($id,$data);

            if ($data['all_child'] > 0) {
                CategoryRepo::update_all_child($category->wallet_id, $category->child_list);
            }

            if($category->parent_id == 0 && isset($data['default_ticket']) && $data['default_ticket'] == 1) {
                CategoryRepo::set_default_ticket($category->id);
            }

            return redirect('admin/setting/category/edit/'.$id)->withSuccess( trans('localize.Category_is_updated') );
        }
        return view('admin.setting.category.edit', compact('category', 'edit_permission', 'wallets'));
    }

    public function delete_category($id)
    {
        $category = CategoryRepo::get_category_by_id($id);
        $ids = ($category->child_list) ? $category->child_list.','.$id : $id;
        try {
            $delete = CategoryRepo::delete_category($ids);
        } catch (\Exception $e) {
            return back()->withErrors(trans('localize.Unable_to_delete_category'));
        }
        return \redirect('admin/setting/category/listing/'.$category->parent_id)->with('success', trans('localize.Category_is_deleted'));
    }

    public function commission()
    {
        $setting = AdminSetting::first();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
		$admin_permission = Controller::adminPermissionList($adm_id);

		if(in_array('settingcommissionlist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingcommissionedit');

        return view('admin.setting.commision', compact('setting', 'edit_permission'));
    }

    public function commission_submit()
    {
        $setting = AdminSetting::first();
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'platform_charge' => 'required|numeric',
                'service_charge' => 'required|numeric',
                'offline_platform_charge' => 'required|numeric',
                'offline_service_charge' => 'required|numeric',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $update = AdminSetting::where('id', $setting->id)
            ->update([
                'platform_charge' => $data['platform_charge'],
                'service_charge' => $data['service_charge'],
                'offline_platform_charge' => $data['offline_platform_charge'],
                'offline_service_charge' => $data['offline_service_charge'],
            ]);

            if (!$update)
                return back()->withInput()->withErrors([trans('localize.Unable_to_update_commission')]);

            //export setting to file
            $settings = json_decode(AdminSetting::first());

            $blacklisted = ['created_at', 'updated_at'];
            $contents = "<?php return array(\n\n";
            foreach ($settings as $key => $setting) {
                if (!in_array($key, $blacklisted)) {
                    $contents .= "\t'$key' => '$setting',\n";
                }
                // if($key == 'id' || $key =='platform_charge' || $key == 'service_charge') {
                //     $contents .= "'$key' => '$setting',\n\n";
                // }
            }
            $contents .= "\n); ?>";
            $file = config_path().'/settings.php';
            \File::put($file, $contents);

            return redirect('admin/setting/commission')->withSuccess(trans('localize.Commission_is_updated'));
        }
    }

    public function offline_category_by_parent($parent_id = 0)
    {
        $categories = OfflineCategoryRepo::all_by_parent_id($parent_id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $add_permission = in_array('settingcategoriesofflinecreate', $admin_permission);
        $delete_permission = in_array('settingcategoriesofflinedelete', $admin_permission);

		if(in_array('settingcategoriesofflinelist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        foreach ($categories as $cat) {
            $cat['count'] = OfflineCategoryRepo::count_childs($cat->id);
        }

        return view('admin.setting.offline_category.listing', compact('categories', 'parent_id', 'add_permission', 'delete_permission'));
    }

    public function add_offline_category($parent_id = 0)
    {
        $parent = OfflineCategoryRepo::get_detail_by_id($parent_id);
        $wallets = WalletRepo::get_wallet_offline_category();

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name_en' => trans('localize.Name').' ('. trans('localize.English') .')' ,
                'name_cn`' => trans('localize.Name').' ('. trans('localize.Chinese') .')' ,
                'name_my' => trans('localize.Name').' ('. trans('localize.Malay') .')' ,
                'wallet_id' => trans('localize.wallet_type'),
                );
            $v = \Validator::make($data, [
                'name_en' => 'required|max:255',
                'wallet_id' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            if ($parent) {
                // get parent_list and add parent_id into list
                $parent_list = $parent->parent_list;
                $data['parent_list'] = ($parent_list) ? $parent_list . ',' . $parent_id : $parent_id;
            }

            try {
                // Upload Image
                if (isset($data['image'])) {
                    $image = $data['image'];
                    $image_name = $image->getClientOriginalName();
                    $image_details = explode('.', $image_name);
                    $new_image_name = date('Ymd').'_'.str_random(4).'.'.$image_details[1];
                    $image_path = 'offline-category/image/';
                    if(@file_get_contents($data['image']) && !S3ClientRepo::IsExisted($image_path, $new_image_name))
                        $upload = S3ClientRepo::Upload($image_path, $data['image'], $new_image_name);

                    $data['image'] = $new_image_name;
                }

                // Upload Banner
                if (isset($data['banner'])) {
                    $banner = $data['banner'];
                    $banner_name = $banner->getClientOriginalName();
                    $banner_details = explode('.', $banner_name);
                    $new_banner_name = date('Ymd').'_'.str_random(4).'.'.$banner_details[1];
                    $banner_path = 'offline-category/banner/';
                    if(@file_get_contents($data['banner']) && !S3ClientRepo::IsExisted($banner_path, $new_banner_name))
                        $upload = S3ClientRepo::Upload($banner_path, $data['banner'], $new_banner_name);

                    $data['banner'] = $new_banner_name;
                }

                $category = OfflineCategoryRepo::add_category($data);

                if ($parent) {
                    // Update parent category child_list
                    DB::statement("UPDATE categories SET child_list = IF(child_list IS NULL," . $category->id . ", CONCAT(child_list, ',' , " . $category->id . ")) where id IN (" . $category->parent_list . ");");
                }
            } catch (\Exception $e) {
                return back()->withErrors( trans('localize.Unable_to_create_category') );
            }

            return \redirect('admin/setting/offline_category/listing/'.$parent_id)->with('success', trans('localize.Offline_Category_is_created'));
        }

        return view('admin.setting.offline_category.add', compact('parent', 'wallets'));
    }

    public function edit_offline_category($id)
    {
        $category = OfflineCategoryRepo::get_detail_by_id($id);
        $wallets = WalletRepo::get_wallet_offline_category();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingcategoriesofflineedit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $niceNames = array(
                'name_en' => trans('localize.Name').' ('. trans('localize.English') .')' ,
                'name_cn`' => trans('localize.Name').' ('. trans('localize.Chinese') .')' ,
                'name_my' => trans('localize.Name').' ('. trans('localize.Malay') .')' ,
                'wallet_id' => trans('localize.wallet_type'),
                );
            $v = \Validator::make($data, [
                'name_en' => 'required|max:255',
                'wallet_id' => 'required',
            ]);
            $v->setAttributeNames($niceNames);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            // Upload Image
            if (isset($data['image'])) {
                $image_path = 'offline-category/image/';
                if(S3ClientRepo::IsExisted($image_path, $category->image)){
                    S3ClientRepo::delete($image_path, $category->image);
                }

                $image = $data['image'];
                $image_name = $image->getClientOriginalName();
                $image_details = explode('.', $image_name);
                $new_image_name = date('Ymd').'_'.str_random(4).'.'.$image_details[1];
                if(@file_get_contents($data['image']) && !S3ClientRepo::IsExisted($image_path, $new_image_name))
                    $upload = S3ClientRepo::Upload($image_path, $data['image'], $new_image_name);

                $data['image'] = $new_image_name;
            }

            // Upload Banner
            if (isset($data['banner'])) {
                $banner_path = 'offline-category/banner/';
                if(S3ClientRepo::IsExisted($banner_path, $category->banner)){
                    S3ClientRepo::delete($banner_path, $category->banner);
                }

                $banner = $data['banner'];
                $banner_name = $banner->getClientOriginalName();
                $banner_details = explode('.', $banner_name);
                $new_banner_name = date('Ymd').'_'.str_random(4).'.'.$banner_details[1];
                if(@file_get_contents($data['banner']) && !S3ClientRepo::IsExisted($banner_path, $new_banner_name))
                    $upload = S3ClientRepo::Upload($banner_path, $data['banner'], $new_banner_name);

                $data['banner'] = $new_banner_name;
            }

            $category = OfflineCategoryRepo::edit_category($id,$data);

            if ($data['all_child'] > 0) {
                OfflineCategoryRepo::update_all_child($category->wallet_id, $category->child_list);
            }

            return redirect('admin/setting/offline_category/edit/'.$id)->withSuccess(trans('localize.Offline_Category_is_updated'));
        }
        return view('admin.setting.offline_category.edit', compact('category', 'edit_permission', 'wallets'));
    }

    public function delete_offline_category($id)
    {
        $category = OfflineCategoryRepo::get_detail_by_id($id);
        $ids = ($category->child_list) ? $category->child_list.','.$id : $id;
        try {
            $delete = OfflineCategoryRepo::delete_category($ids);
        } catch (\Exception $e) {
            return back()->withErrors(trans('localize.Unable_to_delete_offline_category'));
        }
        return \redirect('admin/setting/offline_category/listing/'.$category->parent_id)->with('success', trans('localize.Offline_Category_is_deleted'));
    }

    public function filter()
    {
        $filters = FilterRepo::filter_all();

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $add_permission = in_array('settingfiltercreate', $admin_permission);

		if(in_array('settingfilterlist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}

        foreach ($filters as $key => $filter) {
            $filter->count = FilterRepo::count_filter_item($filter->id);
        }

        return view('admin.setting.filter.manage', compact('filters', 'add_permission'));
    }

    public function filter_add()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            try {
                $category = FilterRepo::add_filter($data);
            } catch (\Exception $e) {
                return back()->withErrors(trans('localize.Unable_to_create_filter'));
            }

            return \redirect('admin/setting/filter')->with('success', trans('localize.Filter_is_created'));
        }

        return view('admin.setting.filter.add');
    }

    public function filter_edit($id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingfilteredit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            try {
                $category = FilterRepo::edit_filter($id,$data);
            } catch (\Exception $e) {
                return back()->withErrors(trans('localize.Unable_to_edit_filter'));
            }

            return \redirect('admin/setting/filter')->with('success', trans('localize.Filter_is_updated'));
        }

        $filter = FilterRepo::find_filter($id);
        return view('admin.setting.filter.edit', compact('filter', 'edit_permission'));
    }

    public function filter_item($id)
    {
        $filters = FilterRepo::get_filter_item($id);
        $parent = FilterRepo::find_filter($id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $add_permission = in_array('settingfiltercreate',$admin_permission);

        return view('admin.setting.filter.item.manage', compact('filters', 'parent', 'add_permission'));
    }

    public function filter_item_add($id)
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            try {
                $category = FilterRepo::add_filter_item($id,$data);
            } catch (\Exception $e) {
                return back()->withErrors(trans('localize.Unable_to_create_filter_item'));
            }

            return \redirect('admin/setting/filter/item/'.$id)->with('success', trans('localize.Filter_item_is_added'));
        }

        $parent = FilterRepo::find_filter($id);
        return view('admin.setting.filter.item.add', compact('parent'));
    }

    public function filter_item_edit($id)
    {
        $filter = FilterRepo::find_filter_item($id);

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::checkAdminPermission($adm_id, 'settingfilteredit');

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'name' => 'required|max:255',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            try {
                $update = FilterRepo::edit_filter_item($id,$data);
            } catch (\Exception $e) {
                return back()->withErrors(trans('localize.Unable_to_edit_filter_item'));
            }

            return \redirect('admin/setting/filter/item/'.$filter->parent_id)->with('success', trans('localize.Filter_item_is_updated'));
        }


        return view('admin.setting.filter.item.edit', compact('filter', 'edit_permission'));
    }

    public function category_filter($cat_id)
    {
        $filters = FilterRepo::get_selected_filter_by_category_id($cat_id);
        $category = CategoryRepo::get_category_by_id($cat_id);

        return view('admin.setting.category.filter', compact('filters','category'));
    }

    public function sorting_cms_footer()
    {
        $footers = CmsRepo::get_footer();
        return view('admin.setting.cms.sorting', compact('footers'))->render();
    }

    public function update_footer_sorting()
    {
        $input = \Request::only('newOrder');
        $save = CmsRepo::update_footer_sorting($input);
        return $save;
    }

    public function check_category()
    {
        $input = \Request::only('category_id');
        $category = CategoryRepo::get_category_by_id($input['category_id']);

        if(is_null($category->child_list)) {
            $check = ProductCategory::where('category_id', $input['category_id'])->count();
        } else {
            $check = ProductCategory::whereIn('category_id', explode(',', $category->child_list))->count();
        }

        return $check;
    }

    public function check_offline_category()
    {
        $input = \Request::only('category_id');
        $category = OfflineCategoryRepo::get_detail_by_id($input['category_id']);

        if(is_null($category->child_list)) {
            $check = StoreCategory::where('offline_category_id', $input['category_id'])->count();
        } else {
            $check = StoreCategory::whereIn('offline_category_id', explode(',', $category->child_list))->count();
        }

        return $check;
    }

    public function buildCountryStateJs()
    {
        $countries = CountryRepo::get_countries_id_name()->keyBy('id');

        foreach($countries as $country)
        {
            $country->states = StateRepo::get_states_by_country_id_name($country->id)->keyBy('id')->toArray();
        }
        $countryArray = array("country"=>$countries->toArray());

        $fileName = public_path('backend/js/country_state_lib.js');
        file_put_contents($fileName, "var myJson = ".json_encode($countryArray));

        return;
    }
}