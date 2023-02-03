<?php
namespace App\Http\Controllers\Admin;

use DB;
use Validator;
use App\Http\Controllers\Admin\Controller;
use App\Repositories\CustomerRepo;
use App\Repositories\InquiriesRepo;
use App\Models\Customer;
use App\Models\Inquiries;
use App\Models\City;
use App\Models\Country;
use App\Models\Wallet;
use App\Models\CustomerWallet;
use App\Repositories\CountryRepo;
use App\Repositories\StateRepo;
use App\Providers\ActivationServiceProvider;
use App\Repositories\VcoinLogRepo;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Repositories\SmsRepo;
use App\Repositories\LimitRepo;
use App\Repositories\ActivationRepository;

class CustomerController extends Controller
{
    public function __construct(CustomerRepo $customerrepo, InquiriesRepo $inquiriesrepo, CountryRepo $countryrepo, StateRepo $staterepo, ActivationServiceProvider $activationService, VcoinLogRepo $VcoinLogRepo,Mailer $mailer, ActivationRepository $activationRepo) {
        $this->customer = $customerrepo;
        $this->inquiries = $inquiriesrepo;
        $this->country = $countryrepo;
        $this->state = $staterepo;
        $this->activationService = $activationService;
        $this->vcoin = $VcoinLogRepo;
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;

        $titles = [
            '1' => 'Ms.', '2' => 'Mr.', '3' => 'Mrs.', '4' => 'Dr.', '5' => 'Dato Sri', '6' => 'Datin', '7' => 'Datuk', '8' => 'Professor',
        ];

        $incomes = [
            '1' => 'Below 3,000', '2' => '3,000 to 5,000', '3' => '5,000 to 10,000', '4' => '10,000 to 15,000', '5' => 'Above 15,000', '6' => 'None',
        ];

        $races = [
            '1' => 'Malay', '2' => 'Chinese', '3' => 'Indian', '4' => 'Others',
        ];

        $maritals = [
            '1' => 'Single', '2' => 'Married', '3' => 'Divorced', '4' => 'Widowed',
        ];

        $jobs = [
            '1' => 'Employee', '2' => 'Supervisor', '3' => 'Executive', '4' => 'Manager', '5' => 'Self Employed', '6' => 'Director', '7' => 'Student', '8' => 'None',
        ];

        $educations = [
            '1' => 'SPM', '2' => 'Diploma', '3' => 'Bachelor Degree', '4' => 'Undergraduate', '5' => 'Master Degree', '6' => 'Professional Degree', '7' => 'Doctorate', '8' => 'None',
        ];

        $religions = [
            '1' => 'Christianity', '2' => 'Islam', '3' => 'Hinduism', '4' => 'Buddhism', '5' => 'Atheism', '6' => 'Confucianism', '7' => 'Taoism', '8' => 'Others',
        ];

        $nationality = [
            'afghan' => 'Afghan', 'albanian' => 'Albanian', 'algerian' => 'Algerian', 'american' => 'American', 'andorran' => 'Andorran', 'angolan' => 'Angolan', 'antiguans' => 'Antiguans', 'argentinean' => 'Argentinean', 'armenian' => 'Armenian', 'australian' => 'Australian', 'austrian' => 'Austrian', 'azerbaijani' => 'Azerbaijani', 'bahamian' => 'Bahamian', 'bahraini' => 'Bahraini', 'bangladeshi' => 'Bangladeshi', 'barbadian' => 'Barbadian', 'barbudans' => 'Barbudans', 'batswana' => 'Batswana', 'belarusian' => 'Belarusian', 'belgian' => 'Belgian', 'belizean' => 'Belizean', 'beninese' => 'Beninese', 'bhutanese' => 'Bhutanese', 'bolivian' => 'Bolivian', 'bosnian' => 'Bosnian', 'brazilian' => 'Brazilian', 'british' => 'British', 'bruneian' => 'Bruneian', 'bulgarian' => 'Bulgarian', 'burkinabe' => 'Burkinabe', 'burmese' => 'Burmese', 'burundian' => 'Burundian', 'cambodian' => 'Cambodian', 'cameroonian' => 'Cameroonian', 'canadian' => 'Canadian', 'cape verdean' => 'Cape Verdean', 'central african' => 'Central African', 'chadian' => 'Chadian', 'chilean' => 'Chilean', 'chinese' => 'Chinese', 'colombian' => 'Colombian', 'comoran' => 'Comoran', 'congolese' => 'Congolese', 'costa rican' => 'Costa Rican', 'croatian' => 'Croatian', 'cuban' => 'Cuban', 'cypriot' => 'Cypriot', 'czech' => 'Czech', 'danish' => 'Danish', 'djibouti' => 'Djibouti', 'dominican' => 'Dominican', 'dutch' => 'Dutch', 'east timorese' => 'East Timorese', 'ecuadorean' => 'Ecuadorean', 'egyptian' => 'Egyptian', 'emirian' => 'Emirian', 'equatorial guinean' => 'Equatorial Guinean', 'eritrean' => 'Eritrean', 'estonian' => 'Estonian', 'ethiopian' => 'Ethiopian', 'fijian' => 'Fijian', 'filipino' => 'Filipino', 'finnish' => 'Finnish', 'french' => 'French', 'gabonese' => 'Gabonese', 'gambian' => 'Gambian', 'georgian' => 'Georgian', 'german' => 'German', 'ghanaian' => 'Ghanaian', 'greek' => 'Greek', 'grenadian' => 'Grenadian', 'guatemalan' => 'Guatemalan', 'guinea-bissauan' => 'Guinea-Bissauan', 'guinean' => 'Guinean', 'guyanese' => 'Guyanese', 'haitian' => 'Haitian', 'herzegovinian' => 'Herzegovinian', 'honduran' => 'Honduran', 'hungarian' => 'Hungarian', 'icelander' => 'Icelander', 'indian' => 'Indian', 'indonesian' => 'Indonesian', 'iranian' => 'Iranian', 'iraqi' => 'Iraqi', 'irish' => 'Irish', 'israeli' => 'Israeli', 'italian' => 'Italian', 'ivorian' => 'Ivorian', 'jamaican' => 'Jamaican', 'japanese' => 'Japanese', 'jordanian' => 'Jordanian', 'kazakhstani' => 'Kazakhstani', 'kenyan' => 'Kenyan', 'kittian and nevisian' => 'Kittian and Nevisian', 'kuwaiti' => 'Kuwaiti', 'kyrgyz' => 'Kyrgyz', 'laotian' => 'Laotian', 'latvian' => 'Latvian', 'lebanese' => 'Lebanese', 'liberian' => 'Liberian', 'libyan' => 'Libyan', 'liechtensteiner' => 'Liechtensteiner', 'lithuanian' => 'Lithuanian', 'luxembourger' => 'Luxembourger', 'macedonian' => 'Macedonian', 'malagasy' => 'Malagasy', 'malawian' => 'Malawian', 'malaysian' => 'Malaysian', 'maldivan' => 'Maldivan', 'malian' => 'Malian', 'maltese' => 'Maltese', 'marshallese' => 'Marshallese', 'mauritanian' => 'Mauritanian', 'mauritian' => 'Mauritian', 'mexican' => 'Mexican', 'micronesian' => 'Micronesian', 'moldovan' => 'Moldovan', 'monacan' => 'Monacan', 'mongolian' => 'Mongolian', 'moroccan' => 'Moroccan', 'mosotho' => 'Mosotho', 'motswana' => 'Motswana', 'mozambican' => 'Mozambican', 'namibian' => 'Namibian', 'nauruan' => 'Nauruan', 'nepalese' => 'Nepalese', 'new zealander' => 'New Zealander', 'ni-vanuatu' => 'Ni-Vanuatu', 'nicaraguan' => 'Nicaraguan', 'nigerien' => 'Nigerien', 'north korean' => 'North Korean', 'northern irish' => 'Northern Irish', 'norwegian' => 'Norwegian', 'omani' => 'Omani', 'pakistani' => 'Pakistani', 'palauan' => 'Palauan', 'panamanian' => 'Panamanian', 'papua new guinean' => 'Papua New Guinean', 'paraguayan' => 'Paraguayan', 'peruvian' => 'Peruvian', 'polish' => 'Polish', 'portuguese' => 'Portuguese', 'qatari' => 'Qatari', 'romanian' => 'Romanian', 'russian' => 'Russian', 'rwandan' => 'Rwandan', 'saint lucian' => 'Saint Lucian', 'salvadoran' => 'Salvadoran', 'samoan' => 'Samoan', 'san marinese' => 'San Marinese', 'sao tomean' => 'Sao Tomean', 'saudi' => 'Saudi', 'scottish' => 'Scottish', 'senegalese' => 'Senegalese', 'serbian' => 'Serbian', 'seychellois' => 'Seychellois', 'sierra leonean' => 'Sierra Leonean', 'singaporean' => 'Singaporean', 'slovakian' => 'Slovakian', 'slovenian' => 'Slovenian', 'solomon islander' => 'Solomon Islander', 'somali' => 'Somali', 'south african' => 'South African', 'south korean' => 'South Korean', 'spanish' => 'Spanish', 'sri lankan' => 'Sri Lankan', 'sudanese' => 'Sudanese', 'surinamer' => 'Surinamer', 'swazi' => 'Swazi', 'swedish' => 'Swedish', 'swiss' => 'Swiss', 'syrian' => 'Syrian', 'taiwanese' => 'Taiwanese', 'tajik' => 'Tajik', 'tanzanian' => 'Tanzanian', 'thai' => 'Thai', 'togolese' => 'Togolese', 'tongan' => 'Tongan', 'trinidadian or tobagonian' => 'Trinidadian or Tobagonian', 'tunisian' => 'Tunisian', 'turkish' => 'Turkish', 'tuvaluan' => 'Tuvaluan', 'ugandan' => 'Ugandan', 'ukrainian' => 'Ukrainian', 'uruguayan' => 'Uruguayan', 'uzbekistani' => 'Uzbekistani', 'venezuelan' => 'Venezuelan', 'vietnamese' => 'Vietnamese', 'welsh' => 'Welsh', 'yemenite' => 'Yemenite', 'zambian' => 'Zambian', 'zimbabwean' => 'Zimbabwean',
        ];

        $this->selectOption = [
            'person_titles' => $titles,
            'incomes' => $incomes,
            'races' => $races,
            'marital' => $maritals,
            'jobs' => $jobs,
            'educations' => $educations,
            'religions' => $religions,
            'nationality' => $nationality,
        ];
    }

    public function manage()
    {
        $input = \Request::only('id', 'search', 'status', 'sort', 'type', 'email', 'phone','countries');
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
		$input['admin_country_id_list'] = $admin_country_id_list;
        $customers = $this->customer->all($input);
        $countries = CountryRepo::get_countries($admin_country_id_list);

		if(in_array('customermanagelist', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}


        return view('admin.customer.manage', compact('customers','input','countries','admin_permission'));
    }

    public function inquiries()
    {
        $input = \Request::only('sort', 'type','search');

        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $delete_permission = Controller::checkAdminPermission($adm_id, 'customerinquiriesdelete');

        return view('admin.customer.inquiries', compact('delete_permission'), ['inquiries'=>$this->inquiries->all($input), 'input'=>$input]);
    }

    public function add()
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $countries = $this->country->get_countries($admin_country_id_list);
        $select = $this->selectOption;

        return view('admin.customer.add', compact('countries','select'));
    }

    public function add_submit()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $data['password'] = str_random(6);

            $niceNames = [
                'name' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.Name') ,
                'username' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.Username') ,
                'email' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.email') ,
                'cus_children' => trans('localize.other').' '. trans('localize.information') .' : '. trans('localize.Children') ,
                'cus_phone' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.Phone') ,
                'cus_postalcode' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.zipcode'),
                'phone' => trans('localize.shipping_information') .' : ' . trans('localize.Phone'),
                'zipcode' => trans('localize.shipping_information') .' : ' . trans('localize.zipcode'),
            ];

            $validator = \Validator::make($data, [
                'name' => 'required',
                // 'username' => 'required|username|min:4|unique:nm_customer,username',
                'email' => 'nullable|email|max:255|unique:nm_customer,email',
                'cus_children' => 'numeric',
                'cus_phone' => 'required|numeric|unique:nm_customer,cus_phone',
                'cus_postalcode' => 'nullable|numeric',
                'phone' => 'nullable|numeric',
                'zipcode' => 'nullable|numeric',
            ],[
                'email.unique' => trans('localize.Email_address_already_taken'),
                'username.unique' => trans('localize.Username_already_taken_by_someone_else'),
                'cus_phone.required' => trans('localize.phoneInput'),
                'name.required' => trans('localize.Please_enter_customer_name'),
                'username.required' => trans('localize.Please_Enter_Username'),
                'email.required' => trans('localize.Please_enter_email_address'),
                'cus_children.numeric' => trans('localize.No_of_Children_must_be_a_number'),
            ])->setAttributeNames($niceNames)->validate();

            try {
                $phone = $data['areacode'].$data['cus_phone'];

                $send_sms = SmsRepo::send_direct_sms(0, $phone, 'admin_register_customer', $data);
                if(!$send_sms) {
                    return back()->withInput()->with('error', trans('localize.sms.phone.not_service'));
                }

                $customer = $this->customer->create_customer($data);
                if(!$customer) {
                    return back()->with('error', trans('localize.internal_server_error.title'));
                }

                CustomerRepo::verified_cellphone($customer->cus_id);

                // $add_shipping_address = $this->customer->update_shipping_address($customer->cus_id, $data);
            } catch (Exception $e) {
                return redirect('admin/customer/manage')->with('error', trans('localize.Failed_to_add_customer'));
            }

            if(!empty($data['email'])) {
                $this->activationService->sendActivationMail_byAdmin($customer,$data['password']);
            }

            return redirect('admin/customer/manage')->with('status', trans('localize.Successfully_add_new_customer'));
        }
    }

    public function edit($cus_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $edit_permission = Controller::adminPermissionList($adm_id);

		$admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $edit_customer_email_permission = in_array('customermanageseteditemail', $edit_permission);
        $edit_customer_phone_permission = in_array('customermanageseteditcustomerphone', $edit_permission);
        $micredit_report_permission = in_array('customermanagemicreditreport', $edit_permission);
        $manage_micredit_permission = in_array('customermanagemmicreditedit', $edit_permission);
        $softreset_password = in_array('customermanagesoftresetpassword', $edit_permission);
        $hard_reset_password = in_array('customermanagehardresetpassword', $edit_permission);
        $hard_reset_secure_code = in_array('customermanagehardresetsecurecode', $edit_permission);
        $soft_reset_secure_code = in_array('customermanagesoftresetsecurecode', $edit_permission);
        $online_order_report = in_array('transactiononlineorderslist', $edit_permission);
        $offline_order_report = in_array('transactionofflineorderslist', $edit_permission);

        $customer = $this->customer->get_customer_details($cus_id);

        $customer_country_id = isset($customer['cus_country']) ?  $customer['cus_country'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($customer_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        $shipping = $this->customer->get_customer_shipping_default($cus_id);
        $countries = $this->country->get_countries($admin_country_id_list);

		$customer_wallets = $this->customer->get_customer_available_wallet($cus_id);
        $wallets = Wallet::get();
        $select = $this->selectOption;

        return view('admin.customer.edit', compact('edit_customer_phone_permission','edit_customer_email_permission','soft_reset_secure_code','hard_reset_secure_code','softreset_password','hard_reset_password','manage_micredit_permission','micredit_report_permission','offline_order_report','online_order_report','countries', 'customer','shipping','select', 'customer_wallets', 'wallets'));
    }

    public function edit_submit()
    {
        if(\Request::isMethod('post'))
        {
            $adm_id = \Auth::guard('admins')->user()->adm_id;
            $edit_permission = Controller::adminPermissionList($adm_id);
            $edit_customer_email_permission = in_array('customermanageseteditemail', $edit_permission);
            $edit_customer_phone_permission = in_array('customermanageseteditcustomerphone', $edit_permission);

            $data = \Request::all();
            $customer = $this->customer->get_customer_by_id($data['cus_id']);
            if(!$customer)
                return back()->with('error', trans('localize.invalid_operation'));

            $cus_id = $customer->cus_id;

            $niceNames = [
				'name' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.Name') ,
                'username' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.Username') ,
                'email' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.email') ,
                'cus_children' => trans('localize.other').' '. trans('localize.information') .' : '. trans('localize.Children') ,
                'cus_phone' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.Phone') ,
                'cus_postalcode' => trans('localize.customer').' '. trans('localize.information') .' : '. trans('localize.zipcode'),
                'phone' => trans('localize.shipping_information') .' : ' . trans('localize.Phone'),
                'zipcode' => trans('localize.shipping_information') .' : ' . trans('localize.zipcode'),
            ];

            //Permission update fields
            if(!$edit_customer_email_permission){
                $data["email"] = isset($customer->email) ? $customer->email : ''  ;
            }
            if(!$edit_customer_phone_permission){
                $data["cus_phone"] = isset($customer->cus_phone) ? $customer->cus_phone : ''  ;
            }

            $validator = \Validator::make($data, [
                'name' => 'required',
                // 'username' => 'required|username|min:4|unique:nm_customer,username,'.$customer->cus_id.',cus_id',
                'email' => 'sometimes|nullable|email|max:255|unique:nm_customer,email,'.$customer->cus_id.',cus_id',
                'cus_phone' => 'sometimes|required|numeric|unique:nm_customer,cus_phone,'.$customer->cus_id.',cus_id',
	            'cus_children' => 'numeric',
                'cus_postalcode' => 'nullable|numeric',
                'zipcode' => 'nullable|numeric',
            ],[
				'email.unique' => trans('localize.Email_address_already_taken'),
                'username.unique' => trans('localize.Username_already_taken_by_someone_else'),
				'username.regex' => trans('localize.Username_only_accept_letters_and_number'),
                'cus_phone.required' => trans('localize.phoneInput'),
                'cus_phone.unique' => 'Phone already taken',
                'name.required' => trans('localize.Please_enter_customer_name'),
                'username.required' => trans('localize.Please_Enter_Username'),
                'email.required' => trans('localize.Please_enter_email_address'),
                'cus_children.numeric' => trans('localize.No_of_Children_must_be_a_number'),
                'cus_gender.required' => trans('localize.Please_select_gender'),
            ])->setAttributeNames($niceNames)->validate();

            try {

                $reverified_phone = false;
                if(isset($data['areacode']) && isset($data['cus_phone'])) {
                    $phone = $data['areacode'].$data['cus_phone'];
                    $old_phone = $customer->phone_area_code.$customer->cus_phone;
                    if($phone != $old_phone) {
                        $reverified_phone = true;
                    }
                }

                if($reverified_phone) {
                    $data['phone'] = $phone;
                    $send_sms = SmsRepo::send_direct_sms($cus_id, $phone, 'admin_update_phone', $data);
                    if(!$send_sms) {
                        return back()->withInput()->with('error', trans('localize.sms.phone.not_service'));
                    }
                }

                $update = $this->customer->update_customer_details($data);
                if(!$update) {
                    return back()->with('error', trans('localize.internal_server_error.title'));
                }

                if($reverified_phone) {
                    CustomerRepo::verified_cellphone($cus_id);
                }

                return back()->with('success', trans('localize.Successfully_Edit_Customer'));

            } catch (Exception $e) {
                return back()->with('error', trans('localize.internal_server_error.title'));
            }
        }
    }

    public function delete($cus_id)
    {
        $customer = Customer::findOrFail($cus_id);
        $customer->delete();
        return \redirect('admin/customer/manage')->with('success', trans('localize.Customer_is_deleted'));
    }

    public function deleteinquiries($iq_id)
    {

        $inquiries = Inquiries::findOrFail($iq_id);
        $inquiries->delete();
        return \redirect('admin/customer/inquiries')->with('success', trans('localize.Inquiry_is_deleted'));
    }

    public function change_customer_status($cus_id,$type)
    {
        $customers = $this->customer->get_customer_status($cus_id,$type);
        return back()->with('status', trans('localize.Success_to').' '.$type.' customer');
    }

    public function detail($id)
    {
        return view('modals.product_detail', ['product'=>$this->product->detail($id)]);
    }

    public function sold()
    {
        return view('admin.product.sold', ['solds'=>$this->product->sold()]);
    }

    public function view($cus_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $edit_permission = in_array('customermanageedit', $admin_permission);
        $mi_credit_report_permission = in_array('customermanagemicreditreport', $admin_permission);
        $online_order_report = in_array('transactiononlineorderslist', $admin_permission);
        $offline_order_report = in_array('transactionofflineorderslist', $admin_permission);

        $customer = $this->customer->get_customer_details($cus_id);

        $customer_country_id = isset($customer['cus_country']) ?  $customer['cus_country'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($customer_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        $shipping = $this->customer->get_customer_shipping_detail($cus_id);
        $country = $this->country->get_all_countries();
        $state = $this->state->get_states();
        $select = $this->selectOption;
        $customer_wallets = $this->customer->get_customer_available_wallet($cus_id);

        return view('admin.customer.view',compact('edit_permission','mi_credit_report_permission','offline_order_report','online_order_report','customer','shipping','country','state','select', 'customer_wallets'));
    }

    public function credit_log($cus_id)
    {

		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $export_permission = in_array('customermanagemicreditreportexport', $admin_permission);

        $input = \Request::only('id', 'ofid', 'start', 'end', 'sort','status','remark', 'wallet');

        $status_list = array(
            '' => trans('localize.all'),
            '1' => trans('localize.order'),
            '2' => trans('localize.order_offline')
        );

        $logs = $this->vcoin->get_vcoin_log_with_input($cus_id, $input);
        $customer = $this->vcoin->get_customer_detail($cus_id);
        $wallets = Wallet::get();

        return view('admin.customer.credit_log', compact('export_permission','status_list','logs','customer','input','cus_id', 'wallets'));

    }

    public function gamepoint_log($cus_id)
    {
        $input = \Request::only('id', 'start', 'end', 'sort','status','remark');

        $logs = $this->vcoin->gamepoint_log_with_input($cus_id, $input);
        $customer = $this->vcoin->get_customer_detail($cus_id);

        return view('admin.customer.gamepoint_log', compact('customer','logs','input','cus_id'));
    }

    public function reset_password()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $validator = \Validator::make($data, [
                'password' => 'required|min:6|confirmed|password',
            ],[
                'password.required' => trans('localize.Please_fill_password_field'),
                'password.confirmed' => trans('localize.Password_confirmation_does_not_match')
            ]);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            try {
                $customer = $this->customer->reset_password($data);

                if ($customer->email_verified) {
                    $this->mailer->send('front.emails.reset_customer_password_by_admin', ['customer'=>$customer,'password'=>$data['password']], function (Message $m) use ($customer) {
                        $m->to($customer->email, $customer->cus_name)->cc('operation@meihome.asia', 'MeiHome Operation')->subject(trans('email.admin.reset_password', ['mall_name' => trans('common.mall_name')]));
                    });
                }

                return back()->with('success', trans('localize.Successfully_reset_password'));
            } catch (Exception $e) {
                return back()->with('error', $e);
            }
        }
    }

    public function reset_secure_code()
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $validator = \Validator::make($data, [
                'securecode' => 'required|numeric|digits:6|confirmed',
            ])->validate();

            try {
                $customer = $this->customer->reset_secure_code($data);

                if ($customer->email_verified) {
                    $this->mailer->send('front.emails.reset_customer_secure_code_by_admin', ['customer'=>$customer,'newcode'=>$data['securecode']], function (Message $m) use ($customer) {
                        $m->to($customer->email, $customer->cus_name)->cc('operation@meihome.asia', 'MeiHome Operation')->subject(trans('email.admin.reset_secure_code', ['mall_name' => trans('common.mall_name')]));
                    });
                }

                return back()->with('success', trans('localize.Successfully_reset_payment_secure_code'));
            } catch (Exception $e) {
                return back()->with('error', $e);
            }


        }
    }

    public function manage_credit($cus_id)
    {
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $validator = \Validator::make($data, [
                'amount' => 'required',
                'remark' => 'required',
                'wallet' => 'required',
            ],[
                'amount.required' => trans('localize.Please_fill'). ' Mei Point '. trans('localize.amount_field'),
                'remark.required' => trans('localize.Please_fill_remarks_field')
            ]);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            if($data['type'] == 'debit')
            {
                $customer = $this->customer->get_customer_details($cus_id);
                $cus_vc = round($customer->v_token, 4);

                if(round($data['amount'], 4) > $cus_vc)
                    return back()->with('error', trans('localize.Insufficient').' Mei Point '. trans('localize.to') .' '. trans('localize.debit'));

                // check wallet enough to debit or not
                $customer_wallet = CustomerWallet::where('customer_id', $cus_id)->where('wallet_id', $data['wallet'])->first();

                if (round($data['amount'], 4) > $customer_wallet->credit)
                    return back()->with('error', trans('localize.Insufficient').' '. trans('localize.svi_wallet') . ' ' . trans('localize.to') .' '. trans('localize.debit'));
            }

            try {
                $customer = $this->customer->manage_credit($cus_id, $data);
                $message = trans('localize.Successfully').' '. trans('localize.credit') .' '. round($data['amount'], 4).' Mei Point '. trans('localize.to') .' '. trans('localize.customer');
                if($data['type'] == 'debit')
                    $message = trans('localize.Successfully').' '. trans('localize.credit') .' '. round($data['amount'], 4).' Mei Point '. trans('localize.from'). ' '. trans('localize.customer');

                return back()->with('success', $message);

            } catch (Exception $e) {
                return back()->with('error', $e);
            }
        }
    }

    public function soft_reset_password($cus_id)
    {
        try {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $data = [
                'cus_id' => $cus_id,
                'password' => substr(str_shuffle($chars), 0, 8)
            ];

            $customer = $this->customer->get_customer_by_id($cus_id);

            if ($customer->email_verified) {
                $reset_customer_password = $this->customer->reset_password($data);

                $this->mailer->send('front.emails.reset_customer_password_by_admin', ['customer'=>$customer,'password'=>$data['password']], function (Message $m) use ($customer) {
                    $m->to($customer->email, $customer->cus_name)->cc('operation@meihome.asia', 'MeiHome Operation')->subject(trans('email.admin.reset_password', ['mall_name' => trans('common.mall_name')]));
                });

                return back()->with('success', trans('localize.Successfully_reset_password'));
            }

            return back()->with('error', trans('localize.customer_email_not_verified'));
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

    public function soft_reset_secure_code($cus_id)
    {
        try {
            $data = [
                'cus_id' => $cus_id,
                'securecode' => mt_rand(100000,999999)
            ];

            $customer = $this->customer->get_customer_by_id($cus_id);

            if ($customer->email_verified) {
                $reset_customer_securecode = $this->customer->reset_secure_code($data);

                $this->mailer->send('front.emails.reset_customer_secure_code_by_admin', ['customer'=>$customer,'newcode'=>$data['securecode']], function (Message $m) use ($customer) {
                    $m->to($customer->email, $customer->cus_name)->cc('operation@meihome.asia', 'MeiHome Operation')->subject(trans('email.admin.reset_secure_code', ['mall_name' => trans('common.mall_name')]));
                });

                return back()->with('success', trans('localize.Successfully_reset_payment_secure_code'));
            }

            return back()->with('error', trans('localize.customer_email_not_verified'));
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

    public function limit($customer_id)
    {
        $customer = CustomerRepo::get_customer_by_id($customer_id);
        if(!$customer)
            return redirect("admin/customer/manage")->with('error', trans('localize.invalid_request'));

        if(!$customer->limit)
            $customer = LimitRepo::init_customer_limit($customer_id);

        for ($type=2; $type <= 5; $type++)
        {
            switch ($type) {
                case 2:
                    $current_amount = $customer->limit->daily;
                    $current_count = $customer->limit->daily_count;
                    break;

                case 3:
                    $current_amount = $customer->limit->weekly;
                    $current_count = $customer->limit->weekly_count;
                    break;

                case 4:
                    $current_amount = $customer->limit->monthly;
                    $current_count = $customer->limit->monthly_count;
                    break;

                case 5:
                    $current_amount = $customer->limit->yearly;
                    $current_count = $customer->limit->yearly_count;
                    break;
            }

            $transactions[$type] = [
                'current_amount' => $current_amount,
                'block_amount' => false,
                'limit_amount_exceed' => 0,
                'limit_amount_usage' => 0,

                'current_count' => $current_count,
                'block_count' => false,
                'limit_count_exceed' => 0,
                'limit_count_usage' => 0,
            ];

            if(!$customer->limit->blocked->isEmpty())
            {
                $block = $customer->limit->blocked->where('type', $type)->first();
                if($block) {
                    if($block->amount > 0)
                    {
                        $transactions[$type]['block_amount'] = true;
                        $transactions[$type]['limit_amount_exceed'] = $block->amount;
                        $transactions[$type]['limit_amount_usage'] = round(($transactions[$type]['current_amount'] / $block->amount) * 100, 2);
                    }

                    if($block->number_transaction > 0)
                    {
                        $transactions[$type]['block_count'] = true;
                        $transactions[$type]['limit_count_exceed'] = $block->number_transaction;
                        $transactions[$type]['limit_count_usage'] = round(($transactions[$type]['current_count'] / $block->number_transaction) * 100, 2);
                    }
                }
            }
        }

        $types = [
            '1' => trans('localize.single_limit'),
            '2' => trans('localize.daily_limit'),
            '3' => trans('localize.weekly_limit'),
            '4' => trans('localize.monthly_limit'),
            '5' => trans('localize.yearly_limit'),
        ];

        $actions = [
            '1' => trans('localize.alert'),
            '2' => trans('localize.block'),
        ];

        return view('admin.customer.limit', compact('customer', 'types', 'actions', 'transactions'));
    }

    public function limit_create($customer_id, $limit_id)
    {
        $customer = CustomerRepo::get_customer_by_id($customer_id);
        if(!$customer)
            return redirect("admin/customer/manage")->with('error', trans('localize.invalid_request'));

        $data = \Request::all();
        $v = \Validator::make($data, [
            'type' => 'required|integer|in:1,2,3,4,5',
            'action' => 'required|integer|in:1,2',
            'amount' => 'required_if:type,1|numeric|min:1',
            'number_transaction' => (isset($data['number_transaction']) && !empty($data['number_transaction']))? 'integer|min:1' : '',
        ]);

        if ($v->fails())
            return back()->withInput()->withErrors($v);

        $data['type'] = (integer)$data['type'];
        $data['action'] = (integer)$data['action'];
        if(empty($data['amount']) && empty($data['number_transaction']))
        {
            return back()->with('error', 'Please insert either amount or number of transaction');
        }

        if(!$customer->limit->actions->isEmpty() && $customer->limit->actions->where('type', $data['type'])->where('action', $data['action'])->count() > 0)
        {
            return back()->with('error', trans('localize.Selected_limit_and_action_already_exist'));
        }

        //check limit block sequence
        if($data['type'] > 1 && $data['action'] == 2 && !$customer->limit->blocked->isEmpty())
        {
            $types = [
                '1' => 'Single Limit',
                '2' => 'Daily Limit',
                '3' => 'Weekly Limit',
                '4' => 'Monthly Limit',
                '5' => 'Yearly Limit',
            ];

            if(!empty($data['amount']))
            {
                $min = LimitRepo::find_min_max_blocked($customer->limit->id, $data['type'], true, true);
                $max = LimitRepo::find_min_max_blocked($customer->limit->id, $data['type'], false, true);

                if($min && $min->type > 1 && $data['amount'] < $min->amount)
                {
                    return back()->with('error', 'Amount must higher than '.$types[$min->type].' value, Minimum amount is '.$min->amount);
                }

                if($max && $max->type > 1 && $data['amount'] > $max->amount)
                {
                    return back()->with('error', 'Amount must less than '.$types[$max->type].' value, Maximum amount is '.$max->amount);
                }
            }

            if(!empty($data['number_transaction']))
            {
                $min = LimitRepo::find_min_max_blocked($customer->limit->id, $data['type'], true, false);
                $max = LimitRepo::find_min_max_blocked($customer->limit->id, $data['type'], false, false);

                if($min && $min->type > 1 && $data['number_transaction'] < $min->number_transaction)
                {
                    return back()->with('error', 'Number transaction must higher than '.$types[$min->type].' value, Minimum number of transaction is '.$min->number_transaction);
                }

                if($max && $max->type > 1 && $data['number_transaction'] > $max->number_transaction)
                {
                    return back()->with('error', 'Number transaction must less than '.$types[$max->type].' value, Maximum number of transaction is '.$max->number_transaction);
                }
            }
        }

        $data['per_user'] = 0;
        LimitRepo::add_action($limit_id, $data);

        return back()->with('success', trans('localize.Successfully_created_new_limit'));
    }

    public function limit_edit($customer_id, $action_id)
    {
        $customer = CustomerRepo::get_customer_by_id($customer_id);
        if(!$customer)
            return redirect("admin/customer/manage")->with('error', trans('localize.invalid_request'));

        $action = $customer->limit->actions->keyBy('id')->get($action_id);
        if(!$action)
            return \Response::json(0);

        return view('modals.edit_customer_limit', compact('customer_id', 'action_id', 'action'))->render();
    }

    public function limit_edit_submit($customer_id, $action_id)
    {
        $customer = CustomerRepo::get_customer_by_id($customer_id);
        if(!$customer)
            return redirect("admin/customer/manage")->with('error', trans('localize.invalid_request'));

        $action = $customer->limit->actions->keyBy('id')->get($action_id);
        if(!$action)
            return back()->with('error', trans('localize.invalid_request'));

        $data = \Request::all();
        $v = \Validator::make($data, [
            'amount' => ($action->type == 1)? 'required|numeric|min:1' : 'nullable|numeric|min:1',
            'number_transaction' => (isset($data['number_transaction']) && !empty($data['number_transaction']))? 'integer|min:1' : 'nullable|integer|min:1',
        ]);

        if ($v->fails())
            return back()->withInput()->withErrors($v);

        if($action->type > 1 && empty($data['amount']) && empty($data['number_transaction']))
        {
            return back()->with('error', 'Please insert either amount or number of transaction');
        }

        //check limit block sequence
        if($action->type > 1 && $action->action == 2 && !$customer->limit->blocked->isEmpty())
        {
            $types = [
                '1' => 'Single Limit',
                '2' => 'Daily Limit',
                '3' => 'Weekly Limit',
                '4' => 'Monthly Limit',
                '5' => 'Yearly Limit',
            ];

            if(!empty($data['amount'])) {
                $min = LimitRepo::find_min_max_blocked($customer->limit->id, $action->type, true, true);
                $max = LimitRepo::find_min_max_blocked($customer->limit->id, $action->type, false, true);

                if($min && $min->type > 1 && $data['amount'] < $min->amount) {
                    return back()->with('error', 'Amount must higher than '.$types[$min->type].' value, Minimum amount is '.$min->amount);
                }

                if($max && $max->type > 1 && $data['amount'] > $max->amount) {
                    return back()->with('error', 'Amount must less than '.$types[$max->type].' value, Maximum amount is '.$max->amount);
                }
            }

            if(!empty($data['number_transaction'])) {
                $min = LimitRepo::find_min_max_blocked($customer->limit->id, $action->type, true, false);
                $max = LimitRepo::find_min_max_blocked($customer->limit->id, $action->type, false, false);

                if($min && $min->type > 1 && $data['number_transaction'] < $min->number_transaction) {
                    return back()->with('error', 'Number transaction must higher than '.$types[$min->type].' value, Minimum number of transaction is '.$min->number_transaction);
                }

                if($max && $max->type > 1 && $data['number_transaction'] > $max->number_transaction) {
                    return back()->with('error', 'Number transaction must less than '.$types[$max->type].' value, Maximum number of transaction is '.$max->number_transaction);
                }
            }
        }

        $data['per_user'] = 0;
        $update = LimitRepo::edit_action($action->id, $data);

        return back()->with('success', trans('localize.Successfully_updated_limit_action'));
    }

    public function limit_action_delete($customer_id, $action_id)
    {
        $customer = CustomerRepo::get_customer_by_id($customer_id);
        if(!$customer)
            return redirect("admin/customer/manage")->with('error', trans('localize.invalid_request'));

        $action = $customer->limit->actions->keyBy('id')->get($action_id);
        if(!$action)
            return back()->with('error', trans('localize.invalid_request'));

        $delete = LimitRepo::delete_action($action_id);

        return back()->with('success', trans('localize.Successfully_deleted_limit_action'));
    }

    public function resend_verification_email($cus_id)
    {
        $customer = $this->customer->get_customer_by_id($cus_id);

        if (!$customer->email_verified) {
            $token = $this->activationRepo->createActivation($customer);

            $link = route('email.verify', $token);
            $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

            $this->mailer->send('front.emails.activation', array('activation_link'=>$link), function (Message $m) use ($customer) {
                $m->to($customer->email, $customer->cus_name)->subject(trans('localize.member.account_activation.title'));
            });

            return back()->with('success', trans('localize.verify_email_send'));
        }

        return back()->with('error', trans('localize.customer_email_already_verified'));
    }

}
