<?php

namespace App\Http\Controllers\Api\FaveChic;

use App\Http\Controllers\Controller;
use App\Models\ApiImportLog;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Country;
use App\Models\ProductPricing;

class ImportController extends Controller {
    private $merchant_id = 405;
    private $store_id = 369;

    public function import($startPage, $endPage = null)
    {
        if (!isset($startPage))
            $startPage = 1;

        if (isset($endPage)) {
            if ($endPage < $startPage)
                abort(500, 'endPage argument is less than startPage');
        } else {
            $endPage = $startPage + 59;
        }

        // temp set execution time limit to unlimited for this api
        set_time_limit(0);

        $fcKey = env('FAVECHIC_API_KEY');
        $fcURL = env('FAVECHIC_API_URL');

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Basic " . $fcKey
            ]
        ]);

        $merchant_id = $this->merchant_id;

        // Api import starts
        $log = new ApiImportLog;
        $log->merchant_id = $merchant_id;
        $log->remark = 'FC product import starts on page ' . $startPage;
        $log->status = 1;
        $log->save();

        try {
            do {
                try {
                    // download the xml
                    $content = file_get_contents($fcURL . '?page=' . $startPage, false, $context);

                    // for item debug
                    // $content = file_get_contents($fcURL . '?itemno=521875831543', false, $context);

                    $content = $this->specialReplacements($content);

                    // for debug
                    // file_put_contents('api.txt', $content);

                    // load xml as string
                    $xml = simplexml_load_string($content);
                    if (!isset($totalPages)) {
                        $totalPages = (int)$xml->TotalPages;

                        // page count readjustment
                        if ($totalPages < $endPage)
                            $endPage = $totalPages;
                    }

                    // main operation
                    $errorFlag = $this->insertProducts($xml);
                    if ($errorFlag === true) {
                        throw new \Exception;
                    }

                    // increment page count
                    $startPage += 1;
                } catch (\Exception $e) {
                    // Api import in error
                    $log = new ApiImportLog;
                    $log->merchant_id = $merchant_id;
                    $log->remark = 'FC api import error found on page ' . $startPage . ' of ' . $endPage;
                    $log->status = 0;
                    $log->save();

                    $startPage += 1;
                    continue;
                }
            } while ($startPage <= $endPage);

            // Api import ends
            $log = new ApiImportLog;
            $log->merchant_id = $merchant_id;
            $log->remark = 'FC product import ends on page ' . $endPage;
            $log->status = 1;
            $log->save();
        } catch (\Exception $e) {
            // Api import in error
            $log = new ApiImportLog;
            $log->merchant_id = $merchant_id;
            $log->remark = 'FC api import halted in error on page ' . $startPage . ' of ' . $endPage;
            $log->status = 0;
            $log->save();

            return $e->getMessage();
        }
    }

    private function specialReplacements($content)
    {
        $content = str_replace('<EnAttributes>', '<EnAttributes><![CDATA[', $content);
        $content = str_replace('</EnAttributes>', ']]></EnAttributes>', $content);
        $content = str_replace('<CnAttributes>', '<CnAttributes><![CDATA[', $content);
        $content = str_replace('</CnAttributes>', ']]></CnAttributes>', $content);

        $content = preg_replace('/(FW045).*(67).*(29b)/i', '$1 $2 $3', $content);

        return $content;
    }

    private function insertProducts($xml)
    {
        $arrProducts = [];
        $status = false;

        foreach ($xml->ProductArray->Product as $product) {
            $attributesEn = json_decode((string)$product->EnAttributes);
            $attributesCn = json_decode((string)$product->CnAttributes);

            // treating each attribute as a product as this stage
            // each complete sku is considered a product
            $sku = json_decode($product->SKU, true);
            $sku = $sku['skuMap'];

            $skuCount = 1;

            if (count($sku) > 0) {
                foreach ($sku as $attKey => $arrPriceAndStock) {
                    try {
                        $descEn = $this->descriptionBuilder($attributesEn, $attKey);

                        if (is_null($descEn))
                            continue;

                        $newProduct = [];

                        // title
                        $newProduct['detail']['pro_title_en'] = trim((string)$product->EnTitle);
                        $newProduct['detail']['pro_title_cn'] = trim((string)$product->CnTitle);

                        // categories
                        $arrCategoryCode = explode(':', (string)$product->CategoryCode);
                        if (isset($arrCategoryCode[0]))
                            $newProduct['detail']['pro_mc_id'] = $arrCategoryCode[0];

                        if (isset($arrCategoryCode[1]))
                            $newProduct['detail']['pro_smc_id'] = $arrCategoryCode[1];

                        if (isset($arrCategoryCode[2]))
                            $newProduct['detail']['pro_sb_id'] = $arrCategoryCode[2];

                        if (isset($arrCategoryCode[3]))
                            $newProduct['detail']['pro_ssb_id'] = $arrCategoryCode[3];

                        // description
                        $newProduct['detail']['short_desc_en'] = $descEn;
                        $newProduct['detail']['short_desc_cn'] = $this->descriptionBuilder($attributesCn, $attKey);

                        $newProduct['detail']['pro_desc_en'] = $product->Description;
                        $newProduct['detail']['pro_desc_cn'] = $product->Description;

                        $newProduct['detail']['pro_isspec'] = 2;
                        $newProduct['detail']['pro_delivery'] = 7;
                        $newProduct['detail']['pro_mr_id'] = $this->merchant_id;
                        $newProduct['detail']['pro_sh_id'] = $this->store_id;

                        $newProduct['detail']['pro_mkeywords'] = str_replace(' ', ',', $newProduct['detail']['pro_title_en']);
                        $newProduct['detail']['pro_mdesc'] = $newProduct['detail']['pro_title_en'];
                        $newProduct['detail']['pro_postfacebook'] = 0;
                        $newProduct['detail']['pro_Img'] = str_replace(',', '/**/', $product->Images);

                        // if this product already exists in db, skip its status assignment
                        // if (Product::where('merchant_item_no', (string)$product->ItemNo)
                        //             ->where('merchant_sku', trim($attKey))
                        //             ->exists() == false) {
                        //     $newProduct['detail']['pro_status'] = 1;
                        // }

                        $newProduct['detail']['pro_status'] = (int)$product->Status;

                        $newProduct['detail']['pro_vtoken_value'] = (double)str_replace(',', '', $arrPriceAndStock['cnprice']);
                        $newProduct['detail']['pro_vcoin_value'] = (double)str_replace(',', '', $arrPriceAndStock['myprice']);
                        $newProduct['detail']['pro_image_count'] = substr_count($newProduct['detail']['pro_Img'], 'http://');
                        $newProduct['detail']['pro_qty'] = (int)$arrPriceAndStock['stock'];
                        $newProduct['detail']['merchant_item_no'] = (string)$product->ItemNo;
                        $newProduct['detail']['merchant_sku'] = trim($attKey);

                        $newProduct['detail']['pro_price'] = round($newProduct['detail']['pro_vtoken_value'] * 1.1, 2);
                        $newProduct['detail']['pro_disprice'] = 0;
                        $newProduct['detail']['pro_inctax'] = 0;
                        $newProduct['detail']['pro_shippamt'] = 0;

                        $newProduct['price']['pro_price_my'] = (double)str_replace(',', '', $arrPriceAndStock['mypricemyr']);
                        $newProduct['price']['pro_price_cn'] = (double)str_replace(',', '', $arrPriceAndStock['cnpricermb']);

                        // push new product into the array
                        array_push($arrProducts, $newProduct);

                        $skuCount += 1;
                        // break loop if more than 3 sku variants
                        // only 3 variants are kept
                        if ($skuCount > 1)
                            break;
                    } catch (\Exception $e) {
                        // Api import in error
                        $log = new ApiImportLog;
                        $log->merchant_id = $this->merchant_id;
                        $log->remark = 'Product error on ' . (string)$product->ItemNo;
                        $log->status = 0;
                        $log->save();

                        $status = true;

                        continue;
                    }
                }
            } else {
                try {
                    $subj = (string)$product->EnAttributes;

                    try {
                        $match = preg_match('/"(\d+:\d+)"/', $subj, $matches);

                        $attKey = $matches[1];

                        $descEn = $this->descriptionBuilder($attributesEn, $attKey);

                        if (is_null($descEn))
                            continue;
                    } catch (\Exception $e) {}


                    $newProduct = [];

                    // title
                    $newProduct['detail']['pro_title_en'] = trim((string)$product->EnTitle);
                    $newProduct['detail']['pro_title_cn'] = trim((string)$product->CnTitle);

                    // categories
                    $arrCategoryCode = explode(':', (string)$product->CategoryCode);
                    if (isset($arrCategoryCode[0]))
                        $newProduct['detail']['pro_mc_id'] = $arrCategoryCode[0];

                    if (isset($arrCategoryCode[1]))
                        $newProduct['detail']['pro_smc_id'] = $arrCategoryCode[1];

                    if (isset($arrCategoryCode[2]))
                        $newProduct['detail']['pro_sb_id'] = $arrCategoryCode[2];

                    if (isset($arrCategoryCode[3]))
                        $newProduct['detail']['pro_ssb_id'] = $arrCategoryCode[3];

                    // description
                    $newProduct['detail']['short_desc_en'] = $descEn;

                    try {
                        $newProduct['detail']['short_desc_cn'] = $this->descriptionBuilder($attributesCn, $attKey);
                    } catch (\Exception $e) {}

                    $newProduct['detail']['pro_desc_en'] = $product->Description;
                    $newProduct['detail']['pro_desc_cn'] = $product->Description;

                    $newProduct['detail']['pro_isspec'] = 2;
                    $newProduct['detail']['pro_delivery'] = 7;
                    $newProduct['detail']['pro_mr_id'] = $this->merchant_id;
                    $newProduct['detail']['pro_sh_id'] = $this->store_id;

                    $newProduct['detail']['pro_mkeywords'] = str_replace(' ', ',', $newProduct['detail']['pro_title_en']);
                    $newProduct['detail']['pro_mdesc'] = $newProduct['detail']['pro_title_en'];
                    $newProduct['detail']['pro_postfacebook'] = 0;
                    $newProduct['detail']['pro_Img'] = str_replace(',', '/**/', $product->Images);

                    // if this product already exists in db, skip its status assignment
                    // if (Product::where('merchant_item_no', (string)$product->ItemNo)
                    //             ->where('merchant_sku', trim($attKey))
                    //             ->exists() == false) {
                    //     $newProduct['detail']['pro_status'] = 1;
                    // }

                    $newProduct['detail']['pro_status'] = (int)$product->Status;

                    $newProduct['detail']['pro_vtoken_value'] = (double)$product->CnPrice;
                    $newProduct['detail']['pro_vcoin_value'] = (double)$product->MyPrice;
                    $newProduct['detail']['pro_image_count'] = substr_count($newProduct['detail']['pro_Img'], 'http://');
                    $newProduct['detail']['pro_qty'] = 100;
                    $newProduct['detail']['merchant_item_no'] = (string)$product->ItemNo;
                    $newProduct['detail']['merchant_sku'] = trim($attKey);

                    $newProduct['detail']['pro_price'] = round($newProduct['detail']['pro_vtoken_value'] * 1.1, 2);
                    $newProduct['detail']['pro_disprice'] = 0;
                    $newProduct['detail']['pro_inctax'] = 0;
                    $newProduct['detail']['pro_shippamt'] = 0;

                    $newProduct['price']['pro_price_my'] = (double)$product->MyPriceMYR;
                    $newProduct['price']['pro_price_cn'] = (double)$product->CnPriceRMB;

                    // push new product into the array
                    array_push($arrProducts, $newProduct);
                } catch (\Exception $e) {
                    // Api import in error
                    $log = new ApiImportLog;
                    $log->merchant_id = $this->merchant_id;
                    $log->remark = 'Product error on ' . (string)$product->ItemNo;
                    $log->status = 0;
                    $log->save();

                    $status = true;

                    continue;
                }
            }

            // insert or update operation
            foreach ($arrProducts as $product) {
                $arrSearch = [
                    'merchant_item_no' => $product['detail']['merchant_item_no'],
                    'merchant_sku' => $product['detail']['merchant_sku']
                ];

                $item = Product::updateOrCreate($arrSearch, $product['detail']);

                try {
                    // product image
                    if (!empty($item)) {
                        $item_id = $item->pro_id;
                        $image_max_order = null;
                        $arrProductImageUrl = explode('/**/', $product['detail']['pro_Img']);
                        $arrProductImageUrl = array_filter($arrProductImageUrl);

                        $product_images = ProductImage::where('pro_id', $item_id)->get();
                        if (!$product_images->isEmpty()) {
                            $image_max_order = $product_images->max('order') ?: 0;

                            foreach ($arrProductImageUrl as $url) {
                                $product_image = $product_images->where('pro_id', $item_id)
                                                                ->where('image', $url)
                                                                ->first();
                                if (empty($product_image)) {
                                    $newProductImage = [
                                        'pro_id' => $item_id,
                                        'title' => $item->pro_title_en,
                                        'image' => $url,
                                        'status' => 1,
                                        'order' => ++$image_max_order,
                                        'main' => 0
                                    ];

                                    ProductImage::create($newProductImage);
                                }
                            }
                        } else {
                            $order = 1;
                            $main = 1;

                            foreach ($arrProductImageUrl as $url) {
                                $newProductImage = [
                                    'pro_id' => $item_id,
                                    'title' => $item->pro_title_en,
                                    'image' => $url,
                                    'status' => 1,
                                    'order' => $order++,
                                    'main' => $main
                                ];
                                $main = 0;

                                ProductImage::create($newProductImage);
                            }
                        }
                    }
                } catch (\Exception $e) {}

                // Product Pricing
                if (!empty($item)) {
                    try {
                        // Malaysia
                        $my = Country::where('co_code', 'my')->first();
                        $check_my = ProductPricing::where('pro_id', $item->pro_id)->where('country_id', $my->co_id)->first();
                        if ($check_my) {
                            // Update
                            $update_my = ProductPricing::where('pro_id', $item->pro_id)->where('country_id', $my->co_id)->update([
                                'currency_rate' => $my->co_rate,
                                'price' => round($product['price']['pro_price_my'], 2)
                            ]);
                        } else {
                            // Create
                            $create_my = ProductPricing::create([
                                'pro_id' => $item->pro_id,
                                'country_id' => $my->co_id,
                                'currency_rate' => $my->co_rate,
                                'price' => round($product['price']['pro_price_my'], 2),
                                'status' => 1
                            ]);
                        }
                        // $item = ProductPricing::updateOrCreate(['pro_id' => $item->pro_id, 'country_id' => $my->co_id], [
                        //     'currency_rate' => $my->co_rate,
                        //     'price' => round($product['price']['pro_price_my'], 2),
                        //     'status' => 1,
                        // ]);

                        // China
                        $cn = Country::where('co_code', 'cn')->first();
                        $check_cn = ProductPricing::where('pro_id', $item->pro_id)->where('country_id', $cn->co_id)->first();
                        if ($check_cn) {
                            // Update
                            $update_cn = ProductPricing::where('pro_id', $item->pro_id)->where('country_id', $cn->co_id)->update([
                                'currency_rate' => $cn->co_rate,
                                'price' => round($product['price']['pro_price_cn'], 2)
                            ]);
                        } else {
                            // Create
                            $create_cn = ProductPricing::create([
                                'pro_id' => $item->pro_id,
                                'country_id' => $cn->co_id,
                                'currency_rate' => $cn->co_rate,
                                'price' => round($product['price']['pro_price_cn'], 2),
                                'status' => 1
                            ]);
                        }
                        // $item = ProductPricing::updateOrCreate(['pro_id' => $item->pro_id, 'country_id' => $cn->co_id], [
                        //     'currency_rate' => $cn->co_rate,
                        //     'price' => round($product['price']['pro_price_cn'], 2),
                        //     'status' => 1,
                        // ]);
                    } catch (\Exception $e) {}
                }
            }
        }

        return $status;
    }

    private function descriptionBuilder($dictAttributes, $key)
    {
        try {
            $innerDesc = null;

            $desc = '<dl>';
            $arrAttributes = explode(';', $key);
            foreach ($arrAttributes as $attribute) {
                $innerDesc = $this->attributeMapper($dictAttributes, $attribute);
                $desc .= $innerDesc;
            }
            $desc .= '</dl>';

            if (is_null($innerDesc))
                return null;

            return $desc;
        } catch (\Exception $e) {
            return 'descriptionBuilder got problem';
        }
    }

    private function attributeMapper($dictAttributes, $attribute)
    {
        try {
            foreach ($dictAttributes as $attributeType => $item) {
                foreach ($item as $attKey => $attValue) {
                    if (trim($attribute) == trim($attKey)) {
                        if (is_null($attValue))
                            return null;

                        return '<dt>' . $attributeType . ':</dt>' . '<dd>' . $attValue . '</dd>';
                    }
                }
            }
        } catch (\Exception $e) {
            return 'attributeMapper got problem';
        }
    }
}