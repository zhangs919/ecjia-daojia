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
 * ECJIA 文章自动发布管理
 *  @author songqian
 */
class admin_article_auto extends ecjia_admin {
	public function __construct() {
		parent::__construct();
		
		RC_Loader::load_app_func('admin_article');
		Ecjia\App\Article\Helper::assign_adminlog_content();
		
		/*加载全局JS及CSS*/
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('bootstrap-placeholder');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('bootstrap-editable.min', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/js/bootstrap-editable.min.js'));
		RC_Style::enqueue_style('bootstrap-editable', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/css/bootstrap-editable.css'));
		RC_Script::enqueue_script('moment.min', RC_Uri::admin_url('statics/lib/moment_js/moment.min.js'));
		RC_Script::enqueue_script('jquery-uniform');
		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Style::enqueue_style('chosen');
		
		//时间控件
		RC_Script::enqueue_script('bootstrap-datepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datepicker.min.js'));
		RC_Style::enqueue_style('datepicker', RC_Uri::admin_url('statics/lib/datepicker/datepicker.css'));
		
		RC_Script::enqueue_script('article_auto', RC_App::apps_url('statics/js/article_auto.js', __FILE__));
		
		RC_Script::localize_script('article_auto', 'js_lang', config('app-article::jslang.article_auto_page'));
	}

	public function init() {
		$this->admin_priv('article_auto_manage');
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('文章自动发布', 'article')));
		
		$this->assign('ur_here', __('文章自动发布', 'article'));
		$this->assign('search_action', RC_Uri::url('article/admin_article_auto/init'));
	
		$crons_enable = RC_Api::api('cron', 'cron_info', array('cron_code' => 'cron_auto_manage'));
		$this->assign('crons_enable', $crons_enable['enable']);
		
		$list = $this->get_auto_articles();
		$this->assign('list', $list);

        return $this->display('article_auto.dwt');
	}
	
	public function batch() {
		$this->admin_priv('article_auto_manage', ecjia::MSGTYPE_JSON);
		
		$type         = !empty($_GET['type'])         ? $_GET['type']         : '';
		$article_id   = !empty($_POST['article_id'])  ? $_POST['article_id']  : '';
		$time 		  = !empty($_POST['select_time']) ? RC_Time::local_strtotime($_POST['select_time']) : '';
		
		if (empty($article_id)) {
			return $this->showmessage(__('请先选中要批量发布的文章', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
			
		if (empty($time)) {
			return $this->showmessage(__('请选择时间', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		if ($type == 'batch_start') {
			$message	= __('批量发布成功', 'article');
			$time_type 	= 'starttime';
		} elseif ($type == 'batch_end') {
			$message 	= __('批量取消成功', 'article');
			$time_type 	= 'endtime';
		}
		
		$article_list = RC_DB::table('auto_manage')->where('type', 'article')->lists('item_id');
		$id_arr = explode(',', $article_id);
		
		if (!empty($id_arr)) {
			foreach ($id_arr as $k => $v) {
				$data = array(
					'item_id' 	=> $v,
					'type'	  	=> 'article',
					$time_type	=> $time
				);
				if (!empty($article_list)) {
					if (in_array($v, $article_list)) {
						RC_DB::table('auto_manage')->where('item_id', $v)->where('type', 'article')->update($data);
					} else {
						RC_DB::table('auto_manage')->insert($data);
					}
				} else {
					RC_DB::table('auto_manage')->insert($data);
				}
			}
			$title_list = RC_DB::table('article')->whereIn('article_id', $id_arr)->lists('title');
		}
		
		if (!empty($title_list)) {
			foreach ($title_list as $v) {
				ecjia_admin::admin_log(__('时间格式不正确', 'article').$v, $type, 'article');
			}
		}
		return $this->showmessage($message, ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('article/admin_article_auto/init')));
	}
	
	//撤销
	public function del() {
		$this->admin_priv('article_auto_manage', ecjia::MSGTYPE_JSON);
		
		$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
		
		$title = RC_DB::table('article')->where('article_id', $id)->pluck('title');
		RC_DB::table('auto_manage')->where('item_id', $id)->where('type', 'article')->delete();
		
// 		ecjia_admin::admin_log(__('文章名称是', 'article').$title, 'cancel', 'article_auto');
		ecjia_admin::admin_log(sprintf(__('文章名称是：%s', 'article'), $title), 'cancel', 'article_auto');
		
		return $this->showmessage(__('操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
	}
	
	public function edit_starttime() {
		$this->admin_priv('article_auto_manage', ecjia::MSGTYPE_JSON);
		
		$id		= !empty($_POST['pk']) 		? intval($_POST['pk']) 	: 0;
		$value 	= !empty($_POST['value']) 	? trim($_POST['value']) : '';
		
		$val = '';
		if (!empty($value)) {
			$val = RC_Time::local_strtotime($value);
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) || $value == '0000-00-00' || $val <= 0) {
			return $this->showmessage(__('时间格式不正确', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$count = RC_DB::table('auto_manage')->where('item_id', $id)->where('type', 'article')->count();
		
		$data = array(
			'item_id'	=> $id,
			'type'		=> 'article',
			'starttime' => $val
		);
		
		if ($count == 0) {
            RC_DB::table('auto_manage')->insert($data);
		} else {
            RC_DB::table('auto_manage')->where('item_id', $id)->where('type', 'article')->update($data);
		}
		return $this->showmessage(__('操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('article/admin_article_auto/init')));
	}
	
	public function edit_endtime() {
		$this->admin_priv('article_auto_manage', ecjia::MSGTYPE_JSON);
		
		$id		= !empty($_POST['pk'])       ? intval($_POST['pk'])  : 0;
		$value 	= !empty($_POST['value'])    ? trim($_POST['value']) : '';
		
		$val = '';
		if (!empty($value)) {
			$val = RC_Time::local_strtotime($value);
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) || $value == '0000-00-00' || $val <= 0) {
			return $this->showmessage(__('时间格式不正确', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$count = RC_DB::table('auto_manage')->where('item_id', $id)->where('type', 'article')->count();
		
		$data = array(
			'item_id'    => $id,
			'type'		 => 'article',
			'endtime'    => $val
		);
		
		if ($count == 0) {
            RC_DB::table('auto_manage')->insert($data);
		} else {
            RC_DB::table('auto_manage')->where('item_id', $id)->where('type', 'article')->update($data);
		}
		return $this->showmessage(__('操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('article/admin_article_auto/init')));
	}
	
	/**
	 * 获取自动发布文章
	 * @return array
	 */
	private function get_auto_articles() {
		$db_article_view = RC_DB::table('article as a')
			->leftJoin('article_cat as ac', RC_DB::raw('ac.cat_id'), '=', RC_DB::raw('a.cat_id'))
			->leftJoin('auto_manage as am', function($join) {
				$join->where(RC_DB::raw('a.article_id'), '=', RC_DB::raw('am.item_id'))
				->where(RC_DB::raw('am.type'), '=', 'article');
		});		
		
		$keywords = !empty($_GET['keywords']) ? trim(htmlspecialchars($_GET['keywords'])) : '';
		$where = '';
	
		if ($keywords) {
			$db_article_view->where(RC_DB::raw('a.title'), 'like', "%". mysql_like_quote($keywords). "%");
			
		}
		//不获取系统帮助文章的过滤
		$db_article_view->where(RC_DB::raw('a.cat_id'), '!=', 0)->where(RC_DB::raw('ac.cat_type'), 'article');
		
		$count = $db_article_view->select('article_cat')->count();
		
		$page = new ecjia_page($count, 10, 5);
		
		$data = $db_article_view
			->select(RC_DB::raw('a.*, am.starttime, am.endtime'))
			->take(10)
			->skip($page->start_id-1)
			->orderBy(RC_DB::raw('a.add_time'), 'desc')
			->get();
	
		$list = array();
		if (!empty($data)) {
			foreach ($data as $rt) {
				if (!empty($rt['starttime'])) {
					$rt['starttime'] = RC_Time::local_date('Y-m-d', $rt['starttime']);
				}
				if (!empty($rt['endtime'])) {
					$rt['endtime'] = RC_Time::local_date('Y-m-d', $rt['endtime']);
				}
				$list[] = $rt;
			}
		}
		return array('item' => $list, 'page' => $page->show(2), 'desc' => $page->page_desc());
	}
}

// end