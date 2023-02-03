<?php namespace App\Http\Controllers\Front;

use App\Http\Controllers\Front\Controller;
use App\Repositories\CountryRepo;
use App\Repositories\S3ClientRepo;
use App\Repositories\CustomerRepo;
use App\Repositories\SecurityQuestionRepo;
use App\Repositories\OrderRepo;
use App\Repositories\SmsRepo;
use App\Repositories\ActivationRepository;
use App\Repositories\LimitRepo;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Hash;
use File;

class ProfileController extends Controller
{
    public function __construct(Mailer $mailer, ActivationRepository $activationRepo)
    {
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;
    }

    public function account(){
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);

        return view('front.profile.main', compact('customer'));

    }

    public function accountInfo()
    {
    	$cus_id = Auth::user()->cus_id;
    	$customer = CustomerRepo::get_customer_by_id($cus_id);
        $countries = CountryRepo::get_all_countries();

        $titles = [
            '1' => trans('option.ms'), '2' => trans('option.mr'), '3' => trans('option.mrs'), '4' => trans('option.dr'), '5' => trans('option.dato_sri'), '6' => trans('option.datin'), '7' => trans('option.datuk'), '8' => trans('option.professor'),
        ];

        $incomes = [
            '1' => trans('option.income_below', ['amount' => '3,000']), '2' => trans('option.income_between', ['from' => '3,000', 'to' => '5,000']), '3' => trans('option.income_between', ['from' => '5,000', 'to' => '10,000']), '4' => trans('option.income_between', ['from' => '10,000', 'to' => '15,000']), '5' => trans('option.income_above', ['from' => '15,000']), '6' => trans('option.income_none'),
        ];

        $races = [
            '1' => trans('option.malay'), '2' => trans('option.chinese'), '3' => trans('option.indian'), '4' => trans('option.others'),
        ];

        $maritals = [
            '1' => trans('option.single'), '2' => trans('option.married'), '3' => trans('option.divorced'), '4' => trans('option.widowed'),
        ];

        $jobs = [
            '1' => trans('option.employee'), '2' => trans('option.supervisor'), '3' => trans('option.executive'), '4' => trans('option.manager'), '5' => trans('option.self_employed'), '6' => trans('option.director'), '7' => trans('option.student'), '8' => trans('option.income_none'),
        ];

        $educations = [
            '1' => trans('option.spm'), '2' => trans('option.diploma'), '3' => trans('option.bachelor_degreee'), '4' => trans('option.undergraduate'), '5' => trans('option.master_degree'), '6' => trans('option.professional_degree'), '7' => trans('option.doctorate'), '8' => trans('option.none'),
        ];

        $religions = [
            '1' => trans('option.christianity'), '2' => trans('option.islam'), '3' => trans('option.hinduism'), '4' => trans('option.buddhism'), '5' => trans('option.atheism'), '6' => trans('option.confucianism'), '7' => trans('option.taoism'), '8' => trans('option.others'),
        ];

        $nationality = [
            'afghan' => 'Afghan', 'albanian' => 'Albanian', 'algerian' => 'Algerian', 'american' => 'American', 'andorran' => 'Andorran', 'angolan' => 'Angolan', 'antiguans' => 'Antiguans', 'argentinean' => 'Argentinean', 'armenian' => 'Armenian', 'australian' => 'Australian', 'austrian' => 'Austrian', 'azerbaijani' => 'Azerbaijani', 'bahamian' => 'Bahamian', 'bahraini' => 'Bahraini', 'bangladeshi' => 'Bangladeshi', 'barbadian' => 'Barbadian', 'barbudans' => 'Barbudans', 'batswana' => 'Batswana', 'belarusian' => 'Belarusian', 'belgian' => 'Belgian', 'belizean' => 'Belizean', 'beninese' => 'Beninese', 'bhutanese' => 'Bhutanese', 'bolivian' => 'Bolivian', 'bosnian' => 'Bosnian', 'brazilian' => 'Brazilian', 'british' => 'British', 'bruneian' => 'Bruneian', 'bulgarian' => 'Bulgarian', 'burkinabe' => 'Burkinabe', 'burmese' => 'Burmese', 'burundian' => 'Burundian', 'cambodian' => 'Cambodian', 'cameroonian' => 'Cameroonian', 'canadian' => 'Canadian', 'cape verdean' => 'Cape Verdean', 'central african' => 'Central African', 'chadian' => 'Chadian', 'chilean' => 'Chilean', 'chinese' => 'Chinese', 'colombian' => 'Colombian', 'comoran' => 'Comoran', 'congolese' => 'Congolese', 'costa rican' => 'Costa Rican', 'croatian' => 'Croatian', 'cuban' => 'Cuban', 'cypriot' => 'Cypriot', 'czech' => 'Czech', 'danish' => 'Danish', 'djibouti' => 'Djibouti', 'dominican' => 'Dominican', 'dutch' => 'Dutch', 'east timorese' => 'East Timorese', 'ecuadorean' => 'Ecuadorean', 'egyptian' => 'Egyptian', 'emirian' => 'Emirian', 'equatorial guinean' => 'Equatorial Guinean', 'eritrean' => 'Eritrean', 'estonian' => 'Estonian', 'ethiopian' => 'Ethiopian', 'fijian' => 'Fijian', 'filipino' => 'Filipino', 'finnish' => 'Finnish', 'french' => 'French', 'gabonese' => 'Gabonese', 'gambian' => 'Gambian', 'georgian' => 'Georgian', 'german' => 'German', 'ghanaian' => 'Ghanaian', 'greek' => 'Greek', 'grenadian' => 'Grenadian', 'guatemalan' => 'Guatemalan', 'guinea-bissauan' => 'Guinea-Bissauan', 'guinean' => 'Guinean', 'guyanese' => 'Guyanese', 'haitian' => 'Haitian', 'herzegovinian' => 'Herzegovinian', 'honduran' => 'Honduran', 'hungarian' => 'Hungarian', 'icelander' => 'Icelander', 'indian' => 'Indian', 'indonesian' => 'Indonesian', 'iranian' => 'Iranian', 'iraqi' => 'Iraqi', 'irish' => 'Irish', 'israeli' => 'Israeli', 'italian' => 'Italian', 'ivorian' => 'Ivorian', 'jamaican' => 'Jamaican', 'japanese' => 'Japanese', 'jordanian' => 'Jordanian', 'kazakhstani' => 'Kazakhstani', 'kenyan' => 'Kenyan', 'kittian and nevisian' => 'Kittian and Nevisian', 'kuwaiti' => 'Kuwaiti', 'kyrgyz' => 'Kyrgyz', 'laotian' => 'Laotian', 'latvian' => 'Latvian', 'lebanese' => 'Lebanese', 'liberian' => 'Liberian', 'libyan' => 'Libyan', 'liechtensteiner' => 'Liechtensteiner', 'lithuanian' => 'Lithuanian', 'luxembourger' => 'Luxembourger', 'macedonian' => 'Macedonian', 'malagasy' => 'Malagasy', 'malawian' => 'Malawian', 'malaysian' => 'Malaysian', 'maldivan' => 'Maldivan', 'malian' => 'Malian', 'maltese' => 'Maltese', 'marshallese' => 'Marshallese', 'mauritanian' => 'Mauritanian', 'mauritian' => 'Mauritian', 'mexican' => 'Mexican', 'micronesian' => 'Micronesian', 'moldovan' => 'Moldovan', 'monacan' => 'Monacan', 'mongolian' => 'Mongolian', 'moroccan' => 'Moroccan', 'mosotho' => 'Mosotho', 'motswana' => 'Motswana', 'mozambican' => 'Mozambican', 'namibian' => 'Namibian', 'nauruan' => 'Nauruan', 'nepalese' => 'Nepalese', 'new zealander' => 'New Zealander', 'ni-vanuatu' => 'Ni-Vanuatu', 'nicaraguan' => 'Nicaraguan', 'nigerien' => 'Nigerien', 'north korean' => 'North Korean', 'northern irish' => 'Northern Irish', 'norwegian' => 'Norwegian', 'omani' => 'Omani', 'pakistani' => 'Pakistani', 'palauan' => 'Palauan', 'panamanian' => 'Panamanian', 'papua new guinean' => 'Papua New Guinean', 'paraguayan' => 'Paraguayan', 'peruvian' => 'Peruvian', 'polish' => 'Polish', 'portuguese' => 'Portuguese', 'qatari' => 'Qatari', 'romanian' => 'Romanian', 'russian' => 'Russian', 'rwandan' => 'Rwandan', 'saint lucian' => 'Saint Lucian', 'salvadoran' => 'Salvadoran', 'samoan' => 'Samoan', 'san marinese' => 'San Marinese', 'sao tomean' => 'Sao Tomean', 'saudi' => 'Saudi', 'scottish' => 'Scottish', 'senegalese' => 'Senegalese', 'serbian' => 'Serbian', 'seychellois' => 'Seychellois', 'sierra leonean' => 'Sierra Leonean', 'singaporean' => 'Singaporean', 'slovakian' => 'Slovakian', 'slovenian' => 'Slovenian', 'solomon islander' => 'Solomon Islander', 'somali' => 'Somali', 'south african' => 'South African', 'south korean' => 'South Korean', 'spanish' => 'Spanish', 'sri lankan' => 'Sri Lankan', 'sudanese' => 'Sudanese', 'surinamer' => 'Surinamer', 'swazi' => 'Swazi', 'swedish' => 'Swedish', 'swiss' => 'Swiss', 'syrian' => 'Syrian', 'taiwanese' => 'Taiwanese', 'tajik' => 'Tajik', 'tanzanian' => 'Tanzanian', 'thai' => 'Thai', 'togolese' => 'Togolese', 'tongan' => 'Tongan', 'trinidadian or tobagonian' => 'Trinidadian or Tobagonian', 'tunisian' => 'Tunisian', 'turkish' => 'Turkish', 'tuvaluan' => 'Tuvaluan', 'ugandan' => 'Ugandan', 'ukrainian' => 'Ukrainian', 'uruguayan' => 'Uruguayan', 'uzbekistani' => 'Uzbekistani', 'venezuelan' => 'Venezuelan', 'vietnamese' => 'Vietnamese', 'welsh' => 'Welsh', 'yemenite' => 'Yemenite', 'zambian' => 'Zambian', 'zimbabwean' => 'Zimbabwean',
        ];

        $select = [
            'person_titles' => $titles,
            'incomes' => $incomes,
            'races' => $races,
            'marital' => $maritals,
            'jobs' => $jobs,
            'educations' => $educations,
            'religions' => $religions,
            'nationality' => $nationality,
        ];

    	if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            $v = Validator::make($data, [
	            'cus_title' => 'required',
	            'name' => 'required|max:255',
	            'address1' => 'required',
	            'zipcode' => 'required',
	            'country' => 'required',
	            'state' => 'required',
	            'city' => 'required',
	            'cus_job' => 'required',
	            'cus_incomes' => 'required',
	            'cus_education' => 'required',
	            'cus_gender' => 'required',
	            'cus_dob' => 'required',
	            'cus_nationality' => 'required',
	            'cus_race' => 'required',
	            'cus_religion' => 'required',
	            'cus_marital' => 'required',
	            'cus_title' => 'required',
	            ], [
	            'numeric' => 'Incorrect :attribute number',
            ])->validate();

            $customer = CustomerRepo::update_customer_accountInfo($cus_id, $data);
            return view('front.profile.account_info', ['customer' => $customer, 'status'=> trans('localize.accountupdated'), 'countries'=>$countries, 'select' => $select]);
        }

        return view('front.profile.account_info', compact('customer','countries', 'select'));

    }

    public function passwordUpdate()
    {
      $cus_id = Auth::user()->cus_id;
      $customer = CustomerRepo::get_customer_by_id($cus_id);

      if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
              return Hash::check($value, current($parameters));
            });

            $message = [
                'old_password.old_password' => trans('localize.old_password_validation'),
                'old_password.required' => trans('localize.oldpasswordInput'),
                'password.required' => trans('localize.newpasswordInput'),
                'old_password.min' => trans('localize.minpassword'),
                'password.confirmed' => trans('localize.matchpassword'),
            ];

            $rules = [
                'type' => 'required|in:old,tac',
                'password' => 'required|min:6|confirmed|password',
                'old_password' => ($data['type'] == 'old')? 'required|min:6|old_password:'.$customer->password : '',
                'tac' => ($data['type'] == 'tac')? 'required|numeric|digits:6' : '',
            ];

            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails())
                return back()->withInput()->withErrors($validator);

            if($data['type'] == 'tac') {
                // Check Tac Validation
                $phone = $customer->phone_area_code . $customer->cus_phone;
                $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_password_update', 0, $phone, $customer->email);
                if (!$check_tac) {
                    return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
                }

                // Set TAC as verified
                $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_password_update', 1, $phone, $customer->email);
            }

            $customer->password= bcrypt($data['password']);
            $customer->save();

            return back()->with('status', trans('localize.passwordupdated'));
        }

        return view('front.profile.password_update', compact('customer'));
    }

    public function shippingAddressUpdate($id)
    {
        $cus_id = Auth::user()->cus_id;
        $countries = CountryRepo::get_all_countries();
    	$shipping = CustomerRepo::get_customer_shipping_info($id);
        $customer = CustomerRepo::get_customer_by_id($cus_id);

        if(!$shipping)
            return back()->with('error', trans('localize.internal_server_error.title'));

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            if(array_key_exists("isdefault", $data)){
                $data['isdefault'] = "1";
            }else{
                $data['isdefault'] = "0";
            }

            $niceNames = array(
                'phone' => 'phone number',
                'address1' => 'Address 1',
                'address2' => 'Address 2',
                'zipcode' => 'Zip Code'
                );

            $v = Validator::make($data, [
                'phone' => 'required|numeric',
                'address1' => 'required|max:255',
                'zipcode' => 'required|numeric',
	            'country' => 'required',
	            'state' => 'required',
	            'city_name' => 'required',
	            'ship_name' => 'required',
            ], [
                'numeric' => 'Incorrect :attribute',
            ])->setAttributeNames($niceNames)->validate();

            $shipping = CustomerRepo::update_shipping_address($cus_id, $data);
            if (!$shipping) {
                return redirect('profile/shippingaddress')->with('status', trans('localize.internal_server_error.title'));
            }

            return back()->with('success', trans('localize.shippingaddress_updated'));
        }

        return view('front.profile.shipping_edit', compact('shipping', 'countries'));
    }

    public function shippingList()
    {
        $cus_id = Auth::user()->cus_id;
        $shipping = CustomerRepo::get_customer_shipping_detail($cus_id);

        return view('front.profile.shipping_list', compact('shipping'));
    }

    public function shippingAddressAdd()
    {
        $cus_id = Auth::user()->cus_id;
        $countries = CountryRepo::get_all_countries();
        $shipping = CustomerRepo::get_customer_shipping_detail($cus_id);

        if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            if(array_key_exists("isdefault", $data)){
                $data['isdefault'] = "1";
            }else{
                $data['isdefault'] = "0";
            }

            $niceNames = array(
                'phone' => 'phone number',
                'address1' => 'Address 1',
                'address2' => 'Address 2',
                'zipcode' => 'Zip Code',
                'ship_name' => 'name',
                );

            $v = Validator::make($data, [
                'phone' => 'required|numeric',
                'address1' => 'required|max:255',
                'zipcode' => 'required|numeric',
                'country' => 'required',
                'state' => 'required',
                'city_name' => 'required',
                'ship_name' => 'required',
            ], [
                'numeric' => 'Incorrect :attribute',
            ])->setAttributeNames($niceNames)->validate();

            $shipping = CustomerRepo::create_shipping_address($cus_id, $data);
            if (!$shipping) {
                return redirect('profile/shippingaddress')->with('status', trans('localize.internal_server_error.title'));
            }

            return redirect('profile/shippingaddress')->with('status', trans('localize.address_added'));
        }

        return view('front.profile.shipping_add', compact('countries'));
    }

    public function shippingAddressDelete($id){

        $ship = CustomerRepo::delete_shipping_address($id);
        return \redirect('profile/shippingaddress')->with('success', trans('localize.shippingaddress_updated'));
    }

    public function shippingAddressDefault($id){

        $cus_id = Auth::user()->cus_id;
        $ship = CustomerRepo::set_shipping_default($id,$cus_id);
        return \redirect('profile/shippingaddress')->with('success', trans('localize.shippingaddress_updated'));
    }

    public function orderHistory()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);
        $perPage = (\Request::get('items')) ? \Request::get('items') : 15;
        $search = (\Request::get('searchorder')) ? \Request::get('searchorder') : '';
        $orderdetails = CustomerRepo::get_orderHistory($cus_id, $perPage, $search);

        return view('front.profile.order_history',['orders'=>$orderdetails, 'perPage'=>$perPage, 'searchorder'=>$search]);
    }

    public function auctionHistory()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);
        $perPage = (\Request::get('items')) ? \Request::get('items') : 5;
        $search = (\Request::get('searchauction')) ? \Request::get('searchauction') : '';

        $auctiondetails = CustomerRepo::get_auctionHistory($cus_id, $perPage, $search);

        return view('front.profile.auction_history',['auctiondetails' => $auctiondetails, 'customer'=>$customer, 'perPage'=>$perPage, 'searchauction'=>$search]);
    }

    public function creditLog()
    {
        $cus_id = Auth::user()->cus_id;
        $perPage = (\Request::get('items')) ? \Request::get('items') : 15;
        $logs = CustomerRepo::get_vtokenlog($cus_id, $perPage);
        return view('front.profile.vcoin_log',['logs'=>$logs, 'perPage'=>$perPage]);
    }

    public function gamePointLog()
    {
        $cus_id = Auth::user()->cus_id;
        $perPage = (\Request::get('items')) ? \Request::get('items') : 5;
        $logs = CustomerRepo::get_gamepointlog($cus_id, $perPage);
        return view('front.profile.gamepoint_log',['logs'=>$logs,'perPage'=>$perPage]);
    }


    public function upload_avatar()
    {   $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);
        $old_file_name = $customer->cus_pic;
        if(\Request::isMethod('post'))
        {
            $data = \Request::all();
            $v = \Validator::make($data, [
                'file' => 'required|image|mimes:jpeg,jpg,png|max:1000'
            ]);
            if ($v->fails())
                return back()->withInput()->withErrors($v);

            $file = $data['file'];
            $file_name = $file->getClientOriginalName();
            $file_details = explode('.', $file_name);
            //$new_file_name = date('Ymd').'_'.str_random(4).'.'.$file_details[1];
            $new_file_name = $cus_id.'_'.date('YmdHis').'.'.$file_details[1];
            // $move_file = $file->move(public_path('images/customer'), $new_file_name);
            $path = 'avatar/'.$cus_id;
            if(@file_get_contents($file) && !S3ClientRepo::IsExisted($path, $new_file_name))
                S3ClientRepo::Upload($path, $file, $new_file_name);

            // \File::delete('images/customer/'.$old_file_name);
            S3ClientRepo::Delete($path, $old_file_name);

            $customer->cus_pic = $new_file_name;
            $customer->save();

            return redirect('/profile/account')->with('status',trans('localize.avatar_upload_success'));
        }

        return redirect('/profile/account');
    }

    public function securecode()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);

        if(\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'type' => 'required|in:old,tac',
                'securecode' => 'required|numeric|digits:6|confirmed',
                'old_securecode' => ($data['type'] == 'old')? 'required|numeric|digits:6||valid_hash:'.$customer->payment_secure_code : '',
                'tac' => ($data['type'] == 'tac')? 'required|numeric|digits:6' : '',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            if($data['type'] == 'tac') {
               // Check Tac Validation
                $phone = $customer->phone_area_code . $customer->cus_phone;
                $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_securecode_update', 0, $phone, $customer->email);
                if (!$check_tac) {
                    return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
                }

                // Set TAC as verified
                $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_securecode_update', 1, $phone, $customer->email);
            }

            CustomerRepo::update_secure_code($cus_id, $data['securecode']);

            return back()->with('status',trans('localize.securecode_saved'));
        }

        return view('front.profile.secure_code', compact('customer'));
    }

    public function acceptorders($order_id, $status)
    {
        $cus_id = Auth::user()->cus_id;
        $order = OrderRepo::get_order_by_id($order_id);

        if (empty($order) || $order->order_status != 3 || $order->order_cus_id != $cus_id)
            return \Redirect::to('profile/order')->withErrors(['msg' => 'Shipment Acceptant Failed. Please try again.']);

        $remarks = null;
        if($order->product_shipping_fees_type == 3)
            $remarks = 'Self pickup updated by customer';

        $result = OrderRepo::completing_merchant_order($order_id, $remarks);
//        $update = OrderRepo::update_order_status($order_id, 4);
//
//        if (!$update)
//            return \Redirect::to('profile/order')->withErrors(['msg' => 'Shipment Acceptant Failed. Please try again.']);
//
//        $update_merchant = OrderRepo::update_merchant_order($order->order_id, $order->mer_id, $order->mer_vtoken, $order->order_vtokens);
         if ($result)
            {
                //refered to completing_merchant_order @ OrderRepo calculation
                $original_vtoken = round((($order->product_original_price * $order->order_qty) / $order->currency_rate), 4);
                $credit_amount = $original_vtoken - $order->merchant_charge_vtoken;

                $data = array(
                    // 'type'=>'gamepoint',
                    'merchant_firstname' => $order->mer_fname,
                    'merchant_lastname' => $order->mer_lname,
                    'product_title' => $order->pro_title_en,
                    'order_qty' => $order->order_qty,
                    'order_vtokens' => $order->order_vtokens,
                    'transaction_id' => $order->transaction_id,
                    'order_commission' => $order->merchant_charge_percentage,
                    'merchant_earn_vtokens' => number_format($credit_amount,4),
                );

                $this->mailer->send('front.emails.merchant_order_completed', $data, function($message) use ($order)
                {

                    $message->to($order->email, $order->mer_fname)->subject('Order is Completed!');
                });
            }

//        $checkouts[] = array(
//            'name' => $order->pro_title_en,
//            'quantity' => $order->order_qty,
//            'vtoken' => $order->pro_vtoken_value,
//            'total' => $order->order_vtokens,
//            'trans_id' => $order->transaction_id,
//            'order_commission' => $order->merchant_charge_percentage,
//            'merchant_earn_vtokens' => $order->order_vtokens-$order->merchant_charge_vtoken,
//            'date' => date('Y-m-d H:i:s'),
//        );
//
//        $data = array(
//            'email' => $order->email,
//            'name' => $order->mer_fname,
//            'checkouts' => $checkouts
//        );
//
//        $this->mailer->send('front.emails.received_shipment', $data, function (Message $m) use ($order) {
//            $m->to($order->email, $order->mer_fname)->subject('Order is completed.');
//        });

        return \Redirect::to('profile/order');
    }

    // public function resetsecurecode()
    // {
    //     $cus_id = Auth::user()->cus_id;
    //     $customer = CustomerRepo::get_customer_by_id($cus_id);

    //     if (!$customer->email_verified)
    //         return back()->with('error_', trans('localize.email_not_verified'));

    //     $new_secure_code = mt_rand(100000,999999);
    //     CustomerRepo::update_secure_code($cus_id, $new_secure_code);

    //     $data = [
    //         'securecode' => $new_secure_code,
    //         'email' => $customer->email,
    //     ];

    //     $this->mailer->send('front.emails.reset_secure_code', $data, function($message) use ($customer)
    //     {
    //         $message->to($customer->email, $customer->cus_name)->subject('Payment secure code reset');
    //     });

    //     return redirect('/profile/updatesecurecode')->with('status',trans('localize.secure_code_email'));
    // }

    public function shippingAddress(){
        $cus_id = Auth::user()->cus_id;
        $shipping = CustomerRepo::get_customer_shipping_detail($cus_id);

        return $shipping;
    }

    public function phone()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);

        if(\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'tac' => 'required|digits:6',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            // Check Tac Validation
            $phone = $customer->phone_area_code . $customer->cus_phone;
            $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_phone_verification', 0, $phone, $customer->email);
            if (!$check_tac) {
                return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
            }

            // Set TAC as verified
            $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_phone_verification', 1, $phone, $customer->email);

            $countries = CountryRepo::get_all_countries();
            return view('front.profile.phone_update', compact('customer', 'countries'));
        }

        return view('front.profile.phone', compact('customer'));
    }

    public function phoneUpdate()
    {
        if(\Request::isMethod('post')) {
            $data = \Request::all();
            $cus_id = Auth::user()->cus_id;
            $customer = CustomerRepo::get_customer_by_id($cus_id);

            $v = Validator::make($data, [
                'areacode' => 'required'    ,
                'phone' => 'required|numeric',
                'tac' => 'required|digits:6',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            // Check Tac Validation
            $phone = $data['areacode'] . $data['phone'];
            $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_phone_update', 0, $phone);
            if (!$check_tac) {
                return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
            }

            // update
            $customer->phone_area_code = $data['areacode'];
            $customer->cus_phone = $data['phone'];
            $customer->cellphone_verified = true;
            $customer->save();

            // Set TAC as verified
            $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_phone_update', 1, $phone);

            return redirect('/profile/phone')->with('success',trans('localize.phone_updated'));
        }
    }

    public function emailUpdate()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);

        if(\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'email' => 'required|email|max:255|unique:nm_customer,email',
                'tac' => 'required|digits:6',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            // Check Tac Validation
            $phone = $customer->phone_area_code . $customer->cus_phone;
            $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_email_update', 0, $phone, $customer->email);
            if (!$check_tac) {
                return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
            }

            // update
            $customer->email = $data['email'];
            $customer->email_verified = false;
            $customer->save();

            // Set TAC as verified
            $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_email_update', 1, $phone, $customer->email);

            $this->sendVerificationMail($customer);

            return back()->with('success', trans('localize.email_updated').' '.trans('localize.reset_success'));
        }

        return view('front.profile.email_update', compact('customer'));
    }

    public function security_question()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);
        $questions = SecurityQuestionRepo::all();

        return view('front.profile.security_question', compact('customer','questions'));
    }

    public function security_question_submit()
    {
        $customer = Auth::user();
        $cus_id = $customer->cus_id;

        $data = \Request::all();
        $v = Validator::make($data, [
            'security_question_1' => 'required|not_in:0',
            'security_question_2' => 'required|not_in:0',
            'security_question_3' => 'required|not_in:0',
            'security_answer_1' => 'required',
            'security_answer_2' => 'required',
            'security_answer_3' => 'required',
            'tac' => 'required|digits:6',
        ])->validate();

        $check = [$data['security_question_1'], $data['security_question_2'], $data['security_question_3']];
        if(count(array_unique($check)) < 3)
            return back()->with('error', 'Please choose difference questions.');

        // Check Tac Validation
        $phone = $customer->phone_area_code . $customer->cus_phone;
        $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_security_question_update', 0, $phone, $customer->email);
        if (!$check_tac) {
            return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
        }

        $update = SecurityQuestionRepo::update_customer_security($cus_id, $data);

        // Set TAC as verified
        $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_security_question_update', 1, $phone, $customer->email);

        return back()->with('success', trans('localize.accountupdated'));
    }

    public function sendVerificationMail($user = null)
    {
        $return = false;
        if($user == null) {
            $user = CustomerRepo::get_customer_by_id(Auth::user()->cus_id);
            $return = true;
        }

        if ($user->email_verified) {
            return;
        }

        $token = $this->activationRepo->createActivation($user);

        $link = route('email.verify', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

        $this->mailer->send('front.emails.activation', array('activation_link'=>$link), function (Message $m) use ($user) {
            $m->to($user->email, $user->cus_name)->subject(trans('localize.member.account_activation.title'));
        });

        if($return)
            return redirect('/profile/email')->with('success', trans('localize.reset_success'));
    }

    public function verifyEmail($token)
    {
        $activation = $this->activationRepo->getActivationByToken($token);

        if ($activation) {
            $user = CustomerRepo::get_customer_by_id($activation->user_id);
            $user->email_verified = true;
            $user->save();

            $this->activationRepo->deleteActivation($token);

            if (Auth::user()) {
                return redirect('/profile/email')->with('success', trans('localize.success_verified', ['type' => trans('localize.email')]));
            }

            return view('auth.activation_success');
        } else {
            if (Auth::user()) {
                return redirect('/profile/email')->with('error', trans('localize.invalid_token'));
            }

            return view('auth.activation_failed');
        }
    }

    public function verifyPhone()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);
        if($customer->cellphone_verified || empty($customer->cus_phone) || empty($customer->phone_area_code))
            return redirect('/profile/phone')->with('error', trans('localize.invalid_operation'));

        return view('front.profile.phone_verify', compact('customer'));
    }

    public function verifyPhoneSubmit()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);

        $data = \Request::all();
        $v = Validator::make($data, [
            'tac' => 'required|digits:6',
        ])->validate();

        // Check Tac Validation
        $phone = $customer->phone_area_code . $customer->cus_phone;
        $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_phone_verification', 0, $phone);
        if (!$check_tac) {
            return back()->withInput()->with('warning', trans('localize.tacNotMatch'));
        }

        $customer->cellphone_verified = true;
        $customer->save();

        // Set TAC as verified
        $check_tac = SmsRepo::check_tac($cus_id, $data['tac'], 'tac_phone_verification', 1, $phone);

        return redirect('/profile/phone')->with('success', trans('localize.success_verified', ['type' => trans('phone')]));
    }

    public function update_info()
    {
        $cus_id = Auth::user()->cus_id;
        $customer = CustomerRepo::get_customer_by_id($cus_id);
        $countries = CountryRepo::get_all_countries();

        if(\Request::isMethod('post')) {
            $data = \Request::all();

            $v = Validator::make($data, [
                'cus_name' => 'required',
                'identity_card' => 'required',
                'declaration' => 'required',
	            'address1' => 'required',
	            'zipcode' => 'required',
	            'country' => 'required',
	            'state' => 'required',
	            'city' => 'required',
            ]);

            if ($v->fails())
                return back()->withInput()->withErrors($v);

            // Update customer
            $update = Customer::where('cus_id', $customer->cus_id)
            ->update([
                'cus_name' => $data['cus_name'],
                'identity_card' => $data['identity_card'],
                'update_flag' => 1,
                'cus_address1' => $data['address1'],
                'cus_address2' => $data['address2'],
                'cus_country' => $data['country'],
                'cus_state' => $data['state'],
                'cus_city_name' => $data['city'],
                'cus_postalcode' => $data['zipcode'],
            ]);

            if ($update) {
                return redirect('/profile/update/success');
            }

            return back()->withInput()->with('error', 'fail to update');
        }

        return view('front.profile.update_info', compact('customer', 'countries'));
    }

    public function update_success()
    {
        return view('front.profile.update_success');
    }

    public function limit()
    {
        $customer = \Auth::user();

        if(!$customer->limit)
            $customer = LimitRepo::init_customer_limit($customer_id);

        for ($type=1; $type <= 5; $type++)
        {
            switch ($type) {
                case 1:
                    $current_amount = 0;
                    $current_count = 0;
                    break;

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
        // dd($transactions);
        return view('front.profile.limit', compact('types', 'actions', 'transactions', 'customer'));
    }
}
