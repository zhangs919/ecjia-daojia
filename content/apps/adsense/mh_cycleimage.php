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

class mh_cycleimage extends ecjia_merchant {
    
    public function __construct() {
		parent::__construct();
		
		
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('ecjia-mh-editable-js');
		RC_Style::enqueue_style('ecjia-mh-editable-css');

		RC_Script::enqueue_script('bootstrap-fileupload-script', dirname(RC_App::app_dir_url(__FILE__)) . '/merchant/statics/assets/bootstrap-fileupload/bootstrap-fileupload.js', array());
		RC_Style::enqueue_style('bootstrap-fileupload', dirname(RC_App::app_dir_url(__FILE__)) . '/merchant/statics/assets/bootstrap-fileupload/bootstrap-fileupload.css', array(), false, false);
		
		RC_Script::enqueue_script('mh_cycleimage', RC_App::apps_url('statics/js/mh_cycleimage.js', __FILE__), array(), false, 1);
		RC_Style::enqueue_style('merchant', RC_App::apps_url('statics/styles/merchant.css', __FILE__), array());

        RC_Script::localize_script('mh_cycleimage', 'js_lang', config('app-adsense::jslang.adsense_page'));

        ecjia_merchant_screen::get_current_screen()->set_parentage('adsense', 'adsense/mh_cycleimage.php');
		
	}
    
    public function init() {
    	$this->admin_priv('mh_cycleimage_manage');
    	
    	ecjia_screen::get_current_screen()->remove_last_nav_here();
    	ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('轮播图管理', 'adsense')));
    	$this->assign('ur_here', __('轮播图列表', 'adsense'));
    
    	//获取轮播组
    	$position = new Ecjia\App\Adsense\Merchant\PositionManage('cycleimage', $_SESSION['store_id']);
    	$data = $position->getAllPositions();
    	
    	if (empty($data)) {
    		$data = RC_Loader::load_app_config('merchant_cycleimage');
    		$this->assign('cycimage_config', 'cycimage_config');
    	}
    	$this->assign('data', $data);
    	
    	$position_id = intval($_GET['position_id']);
    	if (empty($position_id) && !empty($data)) {
    		$position_id = head($data)['position_id'];
    		$position_code = head($data)['position_code'];
    	}
    	$this->assign('position_id', $position_id);
    	
    	if ($position_id > 0) {
    		//获取投放平台
    		$ad = new Ecjia\App\Adsense\Repositories\MerchantAdRepository('cycleimage');
    		$client_list = $ad->getAllClients();
    		$available_clients = $ad->getAvailableClients($position_id);
    	
    		$this->assign('client_list', $client_list);
    		$this->assign('available_clients', $available_clients);
    	
    		$show_client = intval($_GET['show_client']);
    		if (empty($show_client) && !empty($available_clients)) {
    			$show_client = $client_list[head(array_keys($available_clients))];
    		}
    		$this->assign('show_client', $show_client);
    	
    		//对应的轮播图列表
    		$cycleimage_list = $ad->getSpecialAds($position_id, $show_client);
    		$this->assign('cycleimage_list', $cycleimage_list);
    	
    		$position_code = RC_DB::table('merchants_ad_position')->where('store_id', $_SESSION['store_id'])->where('position_id', $position_id)->pluck('position_code');
    	}
    	
    	$this->assign('position_code', $position_code);

        return $this->display('mh_cycleimage_list.dwt');
    }
    
    
//     public function add_group() {
//     	$this->admin_priv('mh_cycleimage_update');
//     	ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here('添加轮播组'));
//     	$this->assign('ur_here', '添加轮播组');
//     	$this->assign('action_link', array('href' => RC_Uri::url('adsense/mh_cycleimage/init'), 'text' => '轮播图设置'));
    	
//     	$this->assign('form_action', RC_Uri::url('adsense/mh_cycleimage/insert_group'));
    	
//     	$this->display('mh_cycleimage_group_info.dwt');
//     }

    //启用轮播组
    public function insert_group() {
    	$this->admin_priv('mh_cycleimage_update');
    	
    	$data 	  = RC_Loader::load_app_config('merchant_cycleimage');
    	$position_code = array_keys($data);    
    	
    	foreach ($data as $row) {
    		$position_name 	= $row['position_name'];
    		$position_code 	= $position_code[0];
    		$position_desc	= $row['position_desc'];
    		$ad_width		= $row['ad_width'];
    		$ad_height 		= $row['ad_height'];
    		$max_number 	= $row['max_number'];
    		$sort_order 	= $row['sort_order'];
    		
    		$data = array(
    			'store_id'		=> $_SESSION['store_id'],
    			'position_name' => $position_name,
    			'position_code' => $position_code,
    			'position_desc' => $position_desc,
    			'ad_width'      => $ad_width,
    			'ad_height'     => $ad_height,
    			'max_number'    => $max_number,
    			'type' 			=> 'cycleimage',
    			'sort_order' 	=> $sort_order,
    		);
    		RC_DB::table('merchants_ad_position')->insertGetId($data);
    	}
    
    	ecjia_merchant::admin_log($position_name, 'add', 'group_cycleimage');
    	return $this->showmessage(__('启用轮播组成功', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('adsense/mh_cycleimage/init')));
    }   
     
    //关闭轮播组
    public function delete_group() {
    	$this->admin_priv('mh_cycleimage_delete');
    	 
    	$position_id = intval($_GET['position_id']);
    	$position_name = RC_DB::table('merchants_ad_position')->where('store_id', $_SESSION['store_id'])->where('position_id', $position_id)->pluck('position_name');
    	if (RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('position_id', $position_id)->count() > 0) {
    		return $this->showmessage(__('该轮播组已存在轮播图，暂不能关闭！', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
    	} else {
    		RC_DB::table('merchants_ad_position')->where('store_id', $_SESSION['store_id'])->where('position_id', $position_id)->delete();
    		ecjia_merchant::admin_log($position_name, 'remove', 'group_cycleimage');
    		return $this->showmessage(__('关闭轮播组成功', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS,array('pjaxurl' => RC_Uri::url('adsense/mh_cycleimage/init')));
    	}
    }

//     public function edit_group() {
//     	$this->admin_priv('mh_cycleimage_update');
    	
//     	ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here('编辑轮播组'));
//     	$this->assign('ur_here', '编辑轮播组');
    	
//     	$position_id = intval($_GET['position_id']);
//     	$this->assign('action_link', array('href' => RC_Uri::url('adsense/mh_cycleimage/init',array('position_id' => $position_id)), 'text' => '轮播图设置'));
//     	$this->assign('position_id', $position_id);
   
//     	$data = RC_DB::table('merchants_ad_position')->where('store_id', $_SESSION['store_id'])->where('position_id', $position_id)->first();
//     	$this->assign('data', $data);
    	
//     	$this->assign('form_action', RC_Uri::url('adsense/mh_cycleimage/update_group'));
    	 
//     	$this->display('mh_cycleimage_group_info.dwt');
//     }
    
//     public function update_group() {
//     	$this->admin_priv('mh_cycleimage_update');
    	
//     	$position_id   = intval($_POST['position_id']);
//     	$position_name = !empty($_POST['position_name']) ? trim($_POST['position_name']) : '';
//     	$position_desc = !empty($_POST['position_desc']) ? nl2br(htmlspecialchars($_POST['position_desc'])) : '';
//     	$max_number    = !empty($_POST['max_number']) ? intval($_POST['max_number']) : 0;
//     	$sort_order    = !empty($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
//     	$ad_width      = !empty($_POST['ad_width']) ? intval($_POST['ad_width']) : 0;
//     	$ad_height     = !empty($_POST['ad_height']) ? intval($_POST['ad_height']) : 0;
    
//     	$data = array(
//     		'position_name' => $position_name,
//     		'position_desc' => $position_desc,
//     		'max_number'    => $max_number,
//     		'sort_order' 	=> $sort_order,
//     		'ad_width'      => $ad_width,
//     		'ad_height'     => $ad_height,
//     	);
    	
//     	RC_DB::table('merchants_ad_position')->where('store_id', $_SESSION['store_id'])->where('position_id', $position_id)->update($data);
//     	ecjia_merchant::admin_log($position_name, 'edit', 'group_cycleimage');
//     	return $this->showmessage('编辑轮播组成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('adsense/mh_cycleimage/edit_group', array('position_id' => $position_id))));
//     }
    
   

    
    /**
     * 处理轮播图
     */
    public function add() {
    	$this->admin_priv('mh_cycleimage_update');
    	    	 
    	$position_id = intval($_GET['position_id']);
    	$this->assign('position_id', $position_id);
    
    	ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('添加轮播图', 'adsense')));
    	$this->assign('ur_here', __('添加轮播图', 'adsense'));
    	$this->assign('action_link', array('href' => RC_Uri::url('adsense/mh_cycleimage/init',array('position_id' => $position_id)), 'text' => __('轮播图列表', 'adsense')));
    
    	$info = RC_DB::table('merchants_ad_position')->where('store_id', $_SESSION['store_id'])->where('position_id', $position_id)->select('ad_width', 'ad_height')->first();
    	$data['ad_width'] = $info['ad_width'];
    	$data['ad_height'] = $info['ad_height'];
    	$data['enabled'] = 1;
    	$this->assign('data', $data);
    
    	$client_list = \Ecjia\App\Adsense\Client::displayClients();
    	$this->assign('client_list', $client_list);
    
    	$this->assign('form_action', RC_Uri::url('adsense/mh_cycleimage/insert'));

        return $this->display('mh_cycleimage_info.dwt');
    
    }
    
    public function insert() {
    	$this->admin_priv('mh_cycleimage_update');
    	 
    	$position_id   = !empty($_POST['position_id']) ? intval($_POST['position_id']) : 0;
    	$ad_name       = !empty($_POST['ad_name']) ? remove_xss($_POST['ad_name']) : '';
    	$sort_order    = !empty($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    	 
    	if (!empty($_FILES['ad_code']['name'])) {
    		if (isset($_FILES['ad_code']['error']) && $_FILES['ad_code']['error'] == 0 || ! isset($_FILES['ad_code']['error']) && isset($_FILES['ad_code']['tmp_name']) && $_FILES['ad_code']['tmp_name'] != 'none') {
    			$save_path = 'merchant/' . $_SESSION['store_id'] . '/data/cycleimage';
    			$upload = RC_Upload::uploader('image', array('save_path' => $save_path, 'auto_sub_dirs' => true));
    			$image_info = $upload->upload($_FILES['ad_code']);
    			if (!empty($image_info)) {
    				$ad_code = $upload->get_position($image_info);
    			} else {
    				return $this->showmessage($upload->error(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
    			}
    		}
    	}else{
    		return $this->showmessage(__('请上传轮播图片', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
    	}
    	 
    	if (empty($_POST['show_client'])) {
    		return $this->showmessage(__('请选择投放平台', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
    	} else {
    		$show_client = Ecjia\App\Adsense\Client::clientSelected(array_map("intval", ($_POST['show_client'])));
    	}
    	$data = array(
    		'store_id'		=> $_SESSION['store_id'],
    		'position_id' 	=> $position_id,
    		'ad_code' 		=> $ad_code,
    		'ad_link' 		=> remove_xss($_POST['ad_link']),
    		'ad_name' 		=> $ad_name,
    		'show_client'   => $show_client,
    		'enabled' 		=> remove_xss($_POST['enabled']),
    		'sort_order' 	=> $sort_order,
    	);
    	$id = RC_DB::table('merchants_ad')->insertGetId($data);
    	ecjia_merchant::admin_log($ad_name, 'add', 'cycleimage');
    	return $this->showmessage(__('添加轮播图成功', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('adsense/mh_cycleimage/edit', array('id' => $id))));
    }
    
    public function edit() {
    	$this->admin_priv('mh_cycleimage_update');
    	 
    	ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('编辑轮播图', 'adsense')));
    	$this->assign('ur_here', __('编辑轮播图', 'adsense'));
    
    	$id = intval($_GET['id']);
    	$data = RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('ad_id', $id)->first();
    	 
    	$info = RC_DB::table('merchants_ad_position')->where('store_id', $_SESSION['store_id'])->where('position_id', $data['position_id'])->select('ad_width', 'ad_height')->first();
    	$data['ad_width'] 	= $info['ad_width'];
    	$data['ad_height']  = $info['ad_height'];
    	 
    	$show_client = intval($_GET['show_client']);
    	$this->assign('show_client', $show_client);
    	 
    	$this->assign('action_link', array('href' => RC_Uri::url('adsense/mh_cycleimage/init',array('position_id' => $data['position_id'], 'show_client' => $show_client)), 'text' => __('轮播图列表', 'adsense')));
    	 
    	$client_list = \Ecjia\App\Adsense\Client::displayClients();
    	$this->assign('client_list', $client_list);
    	 
    	$data['show_client'] = Ecjia\App\Adsense\Client::clients($data['show_client']);
    	$this->assign('data', $data);
    	 
    	$this->assign('form_action', RC_Uri::url('adsense/mh_cycleimage/update'));

        return $this->display('mh_cycleimage_info.dwt');
    }
    
    public function update() {
    	$this->admin_priv('mh_cycleimage_update');
    	 
    	$id 		= intval($_POST['id']);
    	$ad_name	= !empty($_POST['ad_name']) 	? remove_xss($_POST['ad_name']) 		: '';
    	$sort_order = !empty($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    	 
    	$old_pic = RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('ad_id', $id)->pluck('ad_code');
    	if (isset($_FILES['ad_code']['error']) && $_FILES['ad_code']['error'] == 0 || ! isset($_FILES['ad_code']['error']) && isset($_FILES['ad_code']['tmp_name']) && $_FILES['ad_code']['tmp_name'] != 'none') {
    		$save_path = 'merchant/' . $_SESSION['store_id'] . '/data/cycleimage';
    		$upload = RC_Upload::uploader('image', array('save_path' => $save_path, 'auto_sub_dirs' => true));
    		$image_info = $upload->upload($_FILES['ad_code']);
    		if (!empty($image_info)) {
    			$upload->remove($old_pic);
    			$ad_code = $upload->get_position($image_info);
    		} else {
    			return $this->showmessage($upload->error(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
    		}
    	} else {
    		$ad_code = $old_pic;
    	}
    	 
    	if (empty($_POST['show_client'])) {
    		return $this->showmessage(__('请选择投放平台', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
    	} else {
    		$show_client = Ecjia\App\Adsense\Client::clientSelected(array_map("intval", ($_POST['show_client'])));
    	}
    	 
    	$data = array(
    		'ad_code' 		=> $ad_code,
    		'ad_link' 		=> remove_xss($_POST['ad_link']),
    		'ad_name' 		=> $ad_name,
    		'show_client'   => $show_client,
    		'enabled' 		=> intval($_POST['enabled']),
    		'sort_order' 	=> $sort_order,
    	);
    	RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('ad_id', $id)->update($data);
    	ecjia_merchant::admin_log($ad_name, 'edit', 'cycleimage');
    	$show_client = intval($_POST['show_client_value']);
    	return $this->showmessage(__('编辑轮播图成功', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('adsense/mh_cycleimage/edit', array('id' => $id, 'show_client' => $show_client))));
    }
    
    public function delete() {
    	$this->admin_priv('merchant_cycleimage_delete');
    	 
    	$id = intval($_GET['id']);
    	$data = RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('ad_id', $id)->select('ad_name', 'ad_code')->first();
    	$disk = RC_Filesystem::disk();
    	$disk->delete(RC_Upload::upload_path() . $data['ad_code']);
    	RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('ad_id', $id)->delete();
    	 
    	ecjia_merchant::admin_log($data['ad_name'], 'remove', 'cycleimage');
    	return $this->showmessage(__('成功删除轮播图', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
    }
    
    /**
     * 切换是否显示
     */
    public function toggle_show() {
    	$this->admin_priv('merchant_cycleimage_update');
    	 
    	$id       = intval($_POST['id']);
    	$val      = intval($_POST['val']);
    	$position_id  = intval($_GET['position_id']);
    	$show_client  = intval($_GET['show_client']);
    	 
    	RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('ad_id', $id)->update(array('enabled'=> $val));
    	return $this->showmessage(__('切换成功', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS,array('pjaxurl' => RC_Uri::url('adsense/mh_cycleimage/init', array('position_id' => $position_id, 'show_client' => $show_client))));
    }
    
    /**
     * 编辑排序
     */
    public function edit_sort() {
    	$this->admin_priv('merchant_cycleimage_update');
    
    	$id    = intval($_POST['pk']);
    	$sort_order   = intval($_POST['value']);
    	$position_id  = intval($_GET['position_id']);
    	$show_client  = intval($_GET['show_client']);
    	 
    	RC_DB::table('merchants_ad')->where('store_id', $_SESSION['store_id'])->where('ad_id', $id)->update(array('sort_order'=> $sort_order));
    	return $this->showmessage(__('编辑排序成功', 'adsense'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS,array('pjaxurl' => RC_Uri::url('adsense/mh_cycleimage/init', array('position_id' => $position_id, 'show_client' => $show_client))));
    }
    
}