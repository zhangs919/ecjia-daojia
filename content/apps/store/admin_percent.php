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
 * 商家佣金设置
 */
class admin_percent extends ecjia_admin {
	public function __construct() {
		parent::__construct();
		
		RC_Loader::load_app_func('global');
		Ecjia\App\Store\Helper::assign_adminlog_content();
		
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('bootstrap-placeholder');
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Style::enqueue_style('chosen');
		RC_Script::enqueue_script('jquery-uniform');
		RC_Style::enqueue_style('uniform-aristo');
		
		RC_Script::enqueue_script('bootstrap-editable-script', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/js/bootstrap-editable.min.js'));
		RC_Style::enqueue_style('bootstrap-editable-css', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/css/bootstrap-editable.css'));
		
		RC_Script::enqueue_script('commission', RC_App::apps_url('statics/js/commission.js', __FILE__), array(), false, 1);
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('佣金比例', 'store'),RC_Uri::url('store/admin_percent/init')));
		
		//js语言包
		RC_Script::localize_script('commission', 'js_lang', config('app-store::jslang.store_commission_page'));
		
		ecjia_admin_log::instance()->add_object('merchants_percent', __('佣金比例', 'store'));
	}
	
	/**
	 * 商家佣金列表
	 */
	public function init() {
		$this->admin_priv('store_percent_manage');
		
		ecjia_screen::get_current_screen()->remove_last_nav_here();
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('佣金比例', 'store')));
		
		$this->assign('ur_here', __('佣金比例', 'store')); // 当前导航				
		$this->assign('add_percent', array('href' => RC_Uri::url('store/admin_percent/add'), 'text' => __('添加佣金比例', 'store')));
		
		$percent_list = $this->get_percent_list();
		$this->assign('percent_list',$percent_list);
		
		/* 显示模板 */
		$this->assign_lang();
        return $this->display('store_percent_list.dwt');
	}
	
	/**
	 * 添加佣金百分比页面
	 */
	public function add() {
		$this->admin_priv('store_percent_add');

		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('添加佣金比例', 'store')));		
		
		$this->assign('ur_here', __('添加佣金比例', 'store'));
		$this->assign('action_link', array('href' =>RC_Uri::url('store/admin_percent/init'), 'text' => __('佣金比例', 'store')));
		$this->assign('form_action', RC_Uri::url('store/admin_percent/insert'));

		$this->assign_lang();
        return $this->display('store_percent_info.dwt');
	}
	
	/**
	 * 添加佣金百分比加载
	 */
	public function insert() {
		$this->admin_priv('store_percent_add', ecjia::MSGTYPE_JSON);
		
		if (empty($_POST['percent_value'])) {
			return $this->showmessage(__('奖励额度不能为空', 'store'),ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		if (!is_numeric($_POST['percent_value'])) {
			return $this->showmessage(__('奖励额度必须为数字', 'store'),ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		if ($_POST['percent_value'] > 100 || $_POST['percent_value'] < 0) {
		    return $this->showmessage(__('奖励额度范围为0-100，请修改', 'store'),ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		$data = array(
			'percent_value'	=> trim($_POST['percent_value']),
			'sort_order'	=> trim($_POST['sort_order']),
			'add_time'		=> RC_Time::gmtime()
		);
		
		$result = RC_DB::table('store_percent')
					->where('percent_value', $data['percent_value'])
					->get();
		if (!empty($result)) {
			return $this->showmessage(__('该奖励额度已存在！', 'store'),ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
			
		$percent_id = RC_DB::table('store_percent')->insertGetId($data);
		if ($percent_id) {
			ecjia_admin::admin_log($_POST['percent_value'].'%', 'add', 'merchants_percent');
			$links = array(
				array('href' => RC_Uri::url('store/admin_percent/init'), 'text' => __('返回佣金比例列表', 'store')),
				array('href' => RC_Uri::url('store/admin_percent/add'), 'text' => __('继续添加佣金比例', 'store')),
			);
			return $this->showmessage(__('添加佣金比例成功！', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS , array('links' => $links , 'pjaxurl' => RC_Uri::url('store/admin_percent/edit',array('id' => $percent_id))));	
		} else {
			return $this->showmessage(__('添加佣金比例失败！', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}
	
	/**
	 * 佣金百分比编辑页面
	 */
	public function edit() {
		$this->admin_priv('store_percent_update');
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('编辑佣金比例', 'store')));
		
		$this->assign('ur_here', __('编辑佣金比例', 'store'));
		$this->assign('action_link', array('href' =>RC_Uri::url('store/admin_percent/init'), 'text' => __('佣金比例', 'store')));
		$this->assign('form_action', RC_Uri::url('store/admin_percent/update'));
		
		/* 取得奖励额度信息 */
		$id = $_GET['id'];
		$this->assign('id',$id);
		
		$percent = RC_DB::table('store_percent')
					->where('percent_id', $id)
					->first();
		if ($percent['add_time']) {
			$percent['add_time'] = RC_Time::local_strtotime($percent['add_time']);
		}
		$this->assign('percent',$percent);
		
		$this->assign_lang();
        return $this->display('store_percent_info.dwt');
	}
	
	/**
	 * 佣金百分比 编辑 加载
	 */
	public function update() {
		$this->admin_priv('store_percent_update', ecjia::MSGTYPE_JSON);
		
		$percent_id = $_POST['id'];
		if ($_POST['percent_value'] > 100 || $_POST['percent_value'] < 0) {
		    return $this->showmessage(__('奖励额度范围为0-100，请修改', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		$data = array( 
			'percent_value' => trim($_POST['percent_value']),
			'sort_order' => trim($_POST['sort_order'])
		);
		
		/* 取得奖励额度信息 */
		$percent_info = RC_DB::table('store_percent')->where('percent_id', $percent_id)->first();
		
		/* 判断名称是否重复 */
		$is_only = RC_DB::table('store_percent')
					->where('percent_value', $data['percent_value'])
					->where('percent_id', '<>', $percent_id)
					->first();
		if (!empty($is_only)) {
			return $this->showmessage(__('该奖励额度已存在！', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		/* 保存奖励额度信息 */
		$percent_update = RC_DB::table('store_percent')->where('percent_id', $percent_id)->update($data);
		/* 提示信息 */
		ecjia_admin::admin_log($_POST['percent_value'].'%', 'edit', 'merchants_percent');
		return $this->showmessage(__('编辑成功！', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS , array('pjaxurl' => RC_Uri::url('store/admin_percent/edit',array('id' => $percent_id))));
	}
	
	//删除佣金百分比
	public function remove() {
		$this->admin_priv('store_percent_delete', ecjia::MSGTYPE_JSON);
		
		$id = $_GET['id'];
		$percent_value = RC_DB::table('store_percent')->where('percent_id', $id)->pluck('percent_value');
		$percent_delete = RC_DB::table('store_percent')->where('percent_id', $id)->delete();
						
		if ($percent_delete) {
			ecjia_admin::admin_log($percent_value.'%', 'remove', 'merchants_percent');
			return $this->showmessage(__('删除成功', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
		} else {
			return $this->showmessage(__('删除失败', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}
	
	//批量删除佣金百分比
	public function batch() {
		$this->admin_priv('store_percent_delete', ecjia::MSGTYPE_JSON);
		
		/* 对批量操作进行权限检查  END */
		$id = $_POST['id'];
		$ids = explode(',', $id);
		
		$info = RC_DB::table('store_percent')->whereIn('percent_id', $ids)->select('percent_value')->get();
		
		$percent_delete = RC_DB::table('store_percent')->whereIn('percent_id', $ids)->delete();
		
		foreach ($info as $v) {
			ecjia_admin::admin_log($v['percent_value'].'%', 'batch_remove', 'merchants_percent');
		}
		return $this->showmessage(__('批量删除成功！', 'store'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('store/admin_percent/init')));
		
	}
	
	//获取佣金百分比列表
	private function get_percent_list() {
		$filter['sort_by']    = empty($_GET['sort_by']) ? 'sort_order' : trim($_GET['sort_by']);
		$filter['sort_order'] = empty($_GET['sort_order']) ? 'asc' : trim($_GET['sort_order']);
		
		$count = RC_DB::table('store_percent')->count();
		$page = new ecjia_page($count,20,5);
		
		$data = RC_DB::table('store_percent')
				->orderBy($filter['sort_by'], $filter['sort_order'])
				->take($page->page_size)
				->skip($page->start_id - 1)
				->get();
		
		if (!empty($data)) {
			foreach ($data as $k => $v) {
				$data[$k]['add_time'] = RC_Time::local_date('Y-m-d',$v['add_time']);
			}
		}
		
		return array('item' => $data, 'filter' => $filter, 'page' => $page->show(2), 'desc' => $page->page_desc(), 'current_page' => $page->current_page);
	}
}

//end