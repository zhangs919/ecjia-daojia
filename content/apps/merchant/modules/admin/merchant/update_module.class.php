<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 店铺update信息
 * @author 
 */
class admin_merchant_update_module extends api_admin implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) 
    { 
    	$this->authadminSession();
    	if ($_SESSION['staff_id'] <= 0) {
    		return new ecjia_error(100, 'Invalid session');
    	}
    	
    	$result = $this->admin_priv('merchant_manage');
    	if (is_ecjia_error($result)) {
    	    return $result;
    	}
		
		//$seller_category 	= $this->requestData('seller_category', '');
		$seller_telephone 	= $this->requestData('seller_telephone', '');
// 		$province		 	= $this->requestData('provice', '');
// 		$city				= $this->requestData('city', '');
// 		$seller_address		= $this->requestData('seller_address', '');
		$seller_description = $this->requestData('seller_description', '');
		$seller_notice		= $this->requestData('seller_notice');
		$trade_time			= $this->requestData('trade_time');
		
		
		if ($_SESSION['store_id'] > 0) {
			$result1 = $this->admin_priv('franchisee_manage');
			$result2 = $this->admin_priv('merchant_manage');
			
			if (is_ecjia_error($result1) || is_ecjia_error($result2)) {
				return $result1;
			}
			
			Ecjia\App\Store\Helper::assign_adminlog_content();
			
			$data_franchisee = array();
			
// 			if (isset($province) && !empty($province)) {
// 			 	$data_franchisee['province'] = $province;
// 			}
// 			if (isset($city)) {
// 			 	$data_franchisee['city'] = $city;
// 			}	
// 			if (isset($seller_address)) {
// 				$data_franchisee['address'] = $seller_address;
// 			}
			
			if (isset($trade_time['start']) && isset($trade_time['end']) && !empty($trade_time['start']) && !empty($trade_time['end'])) {
				$seller_trade_time = serialize($trade_time);
				RC_DB::table('merchants_config')->where(RC_DB::raw('store_id'), $_SESSION['store_id'])->where(RC_DB::raw('code'), 'shop_trade_time')->update(array('value' => $seller_trade_time));
			}
			
			/* 上传分类图片 */
			$upload = RC_Upload::uploader('image', array('save_path' => '/merchant/'.$_SESSION['store_id'].'/data/shop_logo', 'auto_sub_dirs' => true));
			if (isset($_FILES['seller_logo']) && $upload->check_upload_file($_FILES['seller_logo'])) {
			    $image_info = $upload->upload($_FILES['seller_logo']);
			    if (!empty($image_info)) {
			        $file_name = RC_DB::table('merchants_config')->where(RC_DB::raw('store_id'), $_SESSION['store_id'])->where(RC_DB::raw('code'), 'shop_logo')->pluck('value');
			        $upload->remove($file_name);
			        $new_logo = $upload->get_position($image_info);
			        RC_DB::table('merchants_config')->where(RC_DB::raw('store_id'), $_SESSION['store_id'])->where(RC_DB::raw('code'), 'shop_logo')->update(array('value' => $new_logo));
			    }
			}
			
			//$count_category = $msi_category_db->where($where1)->update($data_category);
			//$count_shopinfo = $ssi_db->where($where2)->update($data_shopinfo);
// 			RC_DB::table('store_franchisee')->where(RC_DB::raw('store_id'), $_SESSION['store_id'])->update($data_franchisee);
			if (!empty($seller_telephone)) {
			    RC_DB::table('merchants_config')->where(RC_DB::raw('store_id'), $_SESSION['store_id'])->where(RC_DB::raw('code'), 'shop_kf_mobile')->update(array('value' => $seller_telephone));
			}
			if (!empty($seller_description)) {
			    RC_DB::table('merchants_config')->where(RC_DB::raw('store_id'), $_SESSION['store_id'])->where(RC_DB::raw('code'), 'shop_description')->update(array('value' => $seller_description));
			}
			if (!empty($seller_notice)) {
			    RC_DB::table('merchants_config')->where(RC_DB::raw('store_id'), $_SESSION['store_id'])->where(RC_DB::raw('code'), 'shop_notice')->update(array('value' => $seller_notice));
			}
			
			RC_Api::api('merchant', 'admin_log', array('text'=>'店铺设置>基本信息设置【来源掌柜】', 'action'=>'edit', 'object'=>'config'));
	    	return true;
	    	
		} else {
			$result = $this->admin_priv('shop_config');
			if (is_ecjia_error($result)) {
				return $result;
			}
			if (isset($seller_telephone)) {
				ecjia_config::instance()->write_config('service_phone', $seller_telephone);
			}
			if(isset($province)){
				ecjia_config::instance()->write_config('shop_province', $province);
			}
			if(isset($city)){
				ecjia_config::instance()->write_config('shop_city', $city);
			}
			if (isset($seller_address)) {
				ecjia_config::instance()->write_config('shop_address', $seller_address);
			}
			if (isset($seller_description)) {
				ecjia_config::instance()->write_config('shop_notice', $seller_description);
			}
			ecjia_admin::admin_log('控制面板>系统设置>商店设置【来源掌柜】', 'edit', 'shop_config');
			return true;
		}
    }	
}

//end