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
 * ECJIA 管理中心文章处理程序文件
 *  @author wu
 */

class merchant extends ecjia_merchant {
	public function __construct() {
		parent::__construct();
        
		RC_Loader::load_app_class('article_cat', 'article', false);
		RC_Loader::load_app_func('merchant_article');
		RC_Loader::load_app_func('merchant_goods', 'goods');
		Ecjia\App\Article\Helper::assign_adminlog_content();
		
		RC_Style::enqueue_style('jquery-placeholder');
		RC_Script::enqueue_script('jq_quicksearch', RC_Uri::admin_url() . '/statics/lib/multi-select/js/jquery.quicksearch.js', array('jquery'), false, true);
		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('jquery-ui');
		RC_Script::enqueue_script('jquery-form');
		
		RC_Script::enqueue_script('ecjia-mh-bootstrap-fileupload-js');
		RC_Style::enqueue_style('ecjia-mh-bootstrap-fileupload-css');
		
		RC_Script::enqueue_script('ecjia-mh-editable-js');
		RC_Style::enqueue_style('ecjia-mh-editable-css');
		
		RC_Script::enqueue_script('merchant_article_list', RC_App::apps_url('statics/js/merchant_article_list.js', __FILE__), array(), false, true);
		
		RC_Script::localize_script('merchant_article_list', 'js_lang', config('app-article::jslang.article_page'));
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('文章列表', 'article'), RC_Uri::url('article/merchant/init')));
		
		ecjia_merchant_screen::get_current_screen()->set_parentage('article', 'article/merhcant.php');
		
	}

	/**
	 * 文章列表
	 */
	public function init() {
		$this->admin_priv('mh_article_manage');
   
		ecjia_screen::get_current_screen()->remove_last_nav_here();
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('文章列表', 'article')));
		
		$this->assign('ur_here', __('文章列表', 'article'));
		$this->assign('action_link', array('text' => __('添加新文章', 'article'), 'href' => RC_Uri::url('article/merchant/add')));
		
		$result = ecjia_app::validate_application('goods');
		if (!is_ecjia_error($result)) {
			$this->assign('has_goods', 'has_goods');
		}
		
		/* 文章筛选时保留筛选的分类cat_id */
		$cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
		$this->assign('cat_select', article_cat::article_cat_list(0, $cat_id, false, 0, 'article'));
	
		$article_list = $this->get_articles_list();
		$this->assign('article_list', $article_list);
		$this->assign('type_count', $article_list['count']);
		$this->assign('filter', $article_list['filter']);
		$this->assign('type', $article_list['filter']['type']);
		
		$this->assign('form_action', RC_Uri::url('article/merchant/batch'));
		$this->assign('search_action', RC_Uri::url('article/merchant/init'));

        return $this->display('article_list.dwt');
	}

	/**
	 * 添加文章页面
	 */
	public function add() {
		$this->admin_priv('mh_article_update');

		RC_Script::enqueue_script('dropper-jq', RC_Uri::admin_url('statics/lib/dropper-upload/jquery.fs.dropper.js'), array(), false, true);
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('添加新文章', 'article')));
		$this->assign('ur_here', __('添加新文章', 'article'));
		$this->assign('action_link', array('text' => __('文章列表', 'article'), 'href' => RC_Uri::url('article/merchant/init')));
		
		$this->assign('cat_select', article_cat::article_cat_list(0, 0, false, 0, 'article'));
		$this->assign('form_action', RC_Uri::url('article/merchant/insert'));
		
		//加载配置中分类数据
		$article_type = RC_Loader::load_app_config('article_type');
		$this->assign('article_type', $article_type);

        return $this->display('article_info.dwt');
	}

	/**
	 * 添加文章
	 */
	public function insert() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);

		$title 	      = !empty($_POST['title'])     	? remove_xss($_POST['title'])         : '';
		$link     	  = !empty($_POST['link'])     		? remove_xss($_POST['link'])      	: '';
		
		$author       = !empty($_POST['author'])        ? remove_xss($_POST['author'])        : '';
		$author_email = !empty($_POST['author_email'])	? remove_xss($_POST['author_email'])  : '';
		
		$keywords     = !empty($_POST['keywords'])    	? remove_xss($_POST['keywords'])      : '';
		$description  = !empty($_POST['description'])  	? remove_xss($_POST['description'])   : '';
		$content      = !empty($_POST['content'])    	? $_POST['content']       		: '';
		
		$cat_id       = !empty($_POST['cat_id'])     	? intval($_POST['cat_id']) 		: 0;
		$article_type = !empty($_POST['article_type'])  ? remove_xss($_POST['article_type'])	: 'article';
		
		$file_name = '';
		//获取上传文件的信息
		$file = !empty($_FILES['file_url']) ? $_FILES['file_url'] : '';
		//判断用户是否选择了文件
		if (!empty($file)&&((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none'))) {
			$upload = RC_Upload::uploader('file', array('save_path' => 'data/article', 'auto_sub_dirs' => true));
			$upload->allowed_type(array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'txt', 'doc', 'docx', 'pdf', 'zip', 'rar'));
			$upload->allowed_mime(array('image/jpeg', 'image/png', 'image/gif', 'image/x-png', 'image/pjpeg',
					'application/x-MS-bmp', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'application/pdf', 'application/zip', 'application/x-rar-compressed'));
				
			$image_info = $upload->upload($file);
			/* 判断是否上传成功 */
			if (!empty($image_info)) {
				$file_name = $upload->get_position($image_info);
			} else {
				return $this->showmessage($upload->error(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}
		

		if (empty($cat_id)) {
			return $this->showmessage(__('请选择文章分类', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}

		if ($article_type == 'article' || $article_type == 'related') {
			if (empty($content)) {
				return $this->showmessage(__('请输入文章内容', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		} 
		
		if ($article_type == 'redirect') {
			if (empty($link) || $link == 'http://' || $link == 'https://') {
				return $this->showmessage(__('请输入链接', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		} 
		
		if ($article_type == 'download' || $article_type == 'related') {
			if (empty($file_name)) {
				return $this->showmessage(__('请上传文件', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}
		
		$is_only = article_title_exists($title);
		if ($is_only) {
			return $this->showmessage(sprintf(__('文章  %s 已经存在', 'article'), stripslashes($title)), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR );
		}
		
		$cover_image = '';
		//获取上传文件的信息
		$image_file = !empty($_FILES['cover_image']) ? $_FILES['cover_image'] : '';
		//判断用户是否选择了文件
		if (!empty($image_file)&&((isset($image_file['error']) && $image_file['error'] == 0) || (!isset($image_file['error']) && $image_file['tmp_name'] != 'none'))) {
			$upload = RC_Upload::uploader('file', array('save_path' => 'data/article', 'auto_sub_dirs' => true));
			$upload->allowed_type(array('jpg', 'jpeg', 'png', 'gif', 'bmp'));
			$upload->allowed_mime(array('image/jpeg', 'image/png', 'image/gif', 'image/x-png', 'image/pjpeg'));
				
			$image_info = $upload->upload($image_file);
			/* 判断是否上传成功 */
			if (!empty($image_info)) {
				$cover_image = $upload->get_position($image_info);
			} else {
				return $this->showmessage($upload->error(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}

		$store_info = get_store_info();
		
		$data = array(
			'store_id'	   => $_SESSION['store_id'],
			'title'        => $title,
			'link'         => $link,
			'author'  	   => $author,
			'author_email' => $author_email,
			'keywords'     => $keywords,
			'description'  => $description,
			'content'      => $content,
			'cat_id'  	   => $cat_id,
			'article_type' => $article_type,
			'add_time'     => RC_Time::gmtime(),
			'file_url'     => $file_name,
			'cover_image'  => $cover_image,
			'article_approved' => $store_info['manage_mode'] == 'self' ? 1 : 0, //自营店铺默认通过审核
		);
		$article_id = RC_DB::table('article')->insertGetId($data);
		
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$article_id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
		
		ecjia_merchant::admin_log($title, 'add', 'article');

		$links[] = array('text' => __('返回文章列表', 'article'), 'href'=> RC_Uri::url('article/merchant/init'));
		$links[] = array('text' => __('继续添加新文章', 'article'), 'href'=> RC_Uri::url('article/merchant/add'));
		return $this->showmessage(__('文章已经添加成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('links' => $links, 'pjaxurl' => RC_Uri::url('article/merchant/edit', array('id' => $article_id))));
		
	}

	/**
	 * 添加自定义栏目
	 */
	public function insert_term_meta() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);

		$article_id   = !empty($_POST['article_id'])  ? intval($_POST['article_id'])              : 0;
		$key          = !empty($_POST['key'])         ? htmlspecialchars(remove_xss($_POST['key']))     : '';
		$value        = !empty($_POST['value'])       ? htmlspecialchars(remove_xss($_POST['value']))   : '';

		$data = array(
			'object_id'		=> $article_id,
			'object_type'	=> 'ecjia.article',
			'object_group'	=> 'article',
			'meta_key'		=> $key,
			'meta_value'	=> $value,
		);
		RC_DB::table('term_meta')->insertGetId($data);
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$article_id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存

		$res = array(
			'key'        => $key,
			'value'      => $value,
			'pjaxurl'    => RC_Uri::url('article/merchant/edit', array('id' => $article_id))
		);
		return $this->showmessage(__('添加自定义栏目成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, $res);
	}

	/**
	 * 更新自定义栏目
	 */
	public function update_term_meta() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);

	    $article_id = !empty($_POST['article_id']) ? intval($_POST['article_id']) : 0;
		$meta_id    = !empty($_POST['meta_id'])    ? intval($_POST['meta_id'])    : 0;

		if (empty($meta_id)) {
			return $this->showmessage(__('缺少关键参数，更新失败', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}

		$key	= !empty($_POST['key'])     ? htmlspecialchars(trim($_POST['key']))     : '';
		$value	= !empty($_POST['value'])   ? htmlspecialchars(trim($_POST['value']))   : '';

		$data = array(
			'object_id'		=> $article_id,
			'object_type'	=> 'ecjia.article',
			'object_group'	=> 'article',
			'meta_key'		=> $key,
			'meta_value'	=> $value,
		);
		RC_DB::table('term_meta')->where('meta_id', $meta_id)->update($data);
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$article_id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
		
		$res = array(
			'key'		=> $key,
			'value'		=> $value,
			'pjaxurl'	=> RC_Uri::url('article/merchant/edit', array('id' => $article_id))
		);
		return $this->showmessage(__('更新自定义栏目成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, $res);
	}

	/**
	 * 删除自定义栏目
	 */
	public function remove_term_meta() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);
		
		$meta_id = !empty($_GET['meta_id']) ? intval($_GET['meta_id']) : 0;
		$article_id = RC_DB::table('term_meta')->where('meta_id', $meta_id)->where('object_type', 'ecjia.article')->where('object_group', 'article')->pluck('object_id');
		
		RC_DB::table('term_meta')->where('meta_id', $meta_id)->delete();
		
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$article_id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
		
		return $this->showmessage(__('删除自定义栏目成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
	}

	/**
	 * 编辑
	 */
	public function edit() {
		$this->admin_priv('mh_article_update');
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('编辑文章内容', 'article')));
		
		$this->assign('ur_here', __('编辑文章内容', 'article'));
		$this->assign('action_link', array('text' => __('文章列表', 'article'), 'href' => RC_Uri::url('article/merchant/init')));

		$result = ecjia_app::validate_application('goods');
		if (!is_ecjia_error($result)) {
			$this->assign('has_goods', 'has_goods');
		}
		$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
		
		$article = get_merchant_article_info($id);
		if (empty($article)) {
			$links = array('links' => array(array('text' => __('返回文章列表', 'article'), 'href' => RC_Uri::url('article/merchant/init'))));
			return $this->showmessage(__('没有找到该文章', 'article'), ecjia::MSGTYPE_HTML | ecjia::MSGSTAT_ERROR, $links);
		}
		if (!empty($article['content'])) {
			$article['content'] = stripslashes($article['content']);
		}
		
		if (!empty($article['cover_image'])) {
			$article['cover_image'] = RC_Upload::upload_url($article['cover_image']);
		}
		
		$data_term_meta = RC_DB::table('term_meta')->select('meta_id', 'meta_key', 'meta_value')
			->where('object_id', $id)
			->where('object_type', 'ecjia.article')
			->where('object_group', 'article')
			->get();
		$this->assign('data_term_meta', $data_term_meta);
		
		$term_meta_key_list = RC_DB::table('term_meta')->select('meta_key')
			->where('object_id', $id)
			->where('object_type', 'ecjia.article')
			->where('object_group', 'article')
			->groupby('meta_key')
			->get();
		$this->assign('term_meta_key_list', $term_meta_key_list);
	
		$this->assign('action',	'edit');
		$this->assign('cat_select', article_cat::article_cat_list(0, $article['cat_id'], false, 0, 'article'));
		$this->assign('article', $article);
		$this->assign('form_action', RC_Uri::url('article/merchant/update'));

		//加载配置中分类数据
		$article_type = RC_Loader::load_app_config('article_type');
		$this->assign('article_type', $article_type);

        return $this->display('article_info.dwt');
	}

	public function update() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);
	
		$id           = !empty($_POST['id']) 			? intval($_POST['id'])          : 0;
		$title 	      = !empty($_POST['title'])     	? remove_xss($_POST['title'])         : '';
		$link     	  = !empty($_POST['link'])     		? remove_xss($_POST['link'])      	: '';
		
		$author       = !empty($_POST['author'])        ? remove_xss($_POST['author'])        : '';
		$author_email = !empty($_POST['author_email'])	? remove_xss($_POST['author_email'])  : '';
		
		$keywords     = !empty($_POST['keywords'])    	? remove_xss($_POST['keywords'])      : '';
		$description  = !empty($_POST['description'])  	? remove_xss($_POST['description'])   : '';
		$content      = !empty($_POST['content'])    	? $_POST['content']       		: '';
		
		$cat_id       = !empty($_POST['cat_id'])     	? intval($_POST['cat_id']) 		: 0;
		$article_type = !empty($_POST['article_type'])  ? remove_xss($_POST['article_type'])	: 'article';

		$article = get_merchant_article_info($id);
		if (empty($article)) {
			return $this->showmessage(__('没有找到该文章', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$file_name = $article['file_url'];
		//获取上传文件的信息
		$file = !empty($_FILES['file_url']) ? $_FILES['file_url'] : '';
		//判断用户是否选择了文件
		if (!empty($file)&&((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none'))) {
			$upload = RC_Upload::uploader('file', array('save_path' => 'data/article', 'auto_sub_dirs' => true));
			$upload->allowed_type(array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'txt', 'doc', 'docx', 'pdf', 'zip', 'rar'));
			$upload->allowed_mime(array('image/jpeg', 'image/png', 'image/gif', 'image/x-png', 'image/pjpeg',
					'application/x-MS-bmp', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'application/pdf', 'application/zip', 'application/x-rar-compressed'));
		
			$image_info = $upload->upload($file);
			/* 判断是否上传成功 */
			if (!empty($image_info)) {
				$file_name = $upload->get_position($image_info);
				
				//删除旧文件
				if (!empty($article['file_url'])) {
					$upload->remove($article['file_url']);
				}
			} else {
				return $this->showmessage($upload->error(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}

		if (empty($cat_id)) {
			return $this->showmessage(__('请选择文章分类', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		if ($article_type == 'article' || $article_type == 'related') {
			if (empty($content)) {
				return $this->showmessage(__('请输入文章内容', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}
		
		if ($article_type == 'redirect') {
			if (empty($link) || $link == 'http://' || $link == 'https://') {
				return $this->showmessage(__('请输入链接', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}
		
		if ($article_type == 'download' || $article_type == 'related') {
			if (empty($file_name)) {
				return $this->showmessage(__('请上传文件', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}
		
		$is_only = article_title_exists($title, $id);
		if ($is_only) {
			return $this->showmessage(sprintf(__('文章  %s 已经存在', 'article'), stripslashes($title)), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR );
		}
		
		$cover_image = $article['cover_image'];
		//获取上传文件的信息
		$image_file = !empty($_FILES['cover_image']) ? $_FILES['cover_image'] : '';
		//判断用户是否选择了文件
		if (!empty($image_file)&&((isset($image_file['error']) && $image_file['error'] == 0) || (!isset($image_file['error']) && $image_file['tmp_name'] != 'none'))) {
			$upload = RC_Upload::uploader('file', array('save_path' => 'data/article', 'auto_sub_dirs' => true));
			$upload->allowed_type(array('jpg', 'jpeg', 'png', 'gif', 'bmp'));
			$upload->allowed_mime(array('image/jpeg', 'image/png', 'image/gif', 'image/x-png', 'image/pjpeg'));
		
			$image_info = $upload->upload($image_file);
			/* 判断是否上传成功 */
			if (!empty($image_info)) {
				$cover_image = $upload->get_position($image_info);
				
				//删除旧文件
				if (!empty($article['cover_image'])) {
					$upload->remove($article['cover_image']);
				}
			} else {
				return $this->showmessage($upload->error(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
		}

		$data = array(
			'title'        => $title,
			'link'         => $link,
			'author'  	   => $author,
			'author_email' => $author_email,
			'keywords'     => $keywords,
			'description'  => $description,
			'content'      => $content,
			'cat_id'  	   => $cat_id,
			'article_type' => $article_type,
			'add_time'     => RC_Time::gmtime(),
			'file_url'     => $file_name,
			'cover_image'  => $cover_image,
		);

		$query = RC_DB::table('article')->where('store_id', $_SESSION['store_id'])->where('article_id', $id)->update($data);
		
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
		
		if ($query) {
		    ecjia_merchant::admin_log($title, 'edit', 'article');
		    
			$note = sprintf(__('文章 %s 成功编辑', 'article'), stripslashes($title));
			return $this->showmessage($note, ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array( 'pjaxurl' => RC_Uri::url('article/merchant/edit', array('id' => $id))));
		} else {
			return $this->showmessage(__('文章编辑失败', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}
	
	/**
	 * 预览
	 */
	public function preview() {
		$this->admin_priv('mh_article_manage');
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('文章预览', 'article')));
		$this->assign('ur_here', __('文章预览', 'article'));
		
		$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
		RC_Hook::do_action('article_merchant_priview_handler', $id);

		$this->assign('action_linkedit', array('text' => __('编辑文章', 'article'), 'href' => RC_Uri::url('article/merchant/edit', array('id' => $id))));
		$this->assign('action_link', array('text' => __('文章列表', 'article'), 'href' => RC_Uri::url('article/merchant/init')));
		
		$article = get_merchant_article_info($id);
		if (empty($article)) {
			$links = array('links' => array(array('text' => __('返回文章列表', 'article'), 'href' => RC_Uri::url('article/merchant/init'))));
			return $this->showmessage(__('没有找到该文章', 'article'), ecjia::MSGTYPE_HTML | ecjia::MSGSTAT_ERROR, $links);
		}
		$this->assign('article', $article);

        return $this->display('preview.dwt');
	}

	/**
	 * 关联商品
	 */
	public function link_goods() {
		$this->admin_priv('mh_article_update');

		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('编辑关联商品', 'article')));
		
		$this->assign('action_link', array('href' => RC_Uri::url('article/merchant/init'), 'text' => __('文章列表', 'article')));
		$this->assign('ur_here', __('编辑关联商品', 'article'));
		
		$article_id = !empty($_GET['id']) ? intval($_GET['id']) : '';
		
		$article = get_merchant_article_info($article_id);
		if (empty($article)) {
			$links = array('links' => array(array('text' => __('返回文章列表', 'article'), 'href' => RC_Uri::url('article/merchant/init'))));
			return $this->showmessage(__('没有找到该文章', 'article'), ecjia::MSGTYPE_HTML | ecjia::MSGSTAT_ERROR, $links);
		}
		
		$linked_goods = RC_DB::table('goods_article')
			->leftJoin('goods', 'goods.goods_id', '=', 'goods_article.goods_id')
			->where('goods_article.article_id', $article_id)
			->select('goods.goods_id', 'goods.goods_name')
			->get();

		$this->assign('link_goods_list', $linked_goods);
		$this->assign('cat_list', merchant_cat_list(0, 0, false));

        return $this->display('link_goods.dwt');
	}

	/**
	 * 添加商品关联
	 */
	public function insert_link_goods() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);

		$article_id		= !empty($_GET['id']) 			? intval($_GET['id']) 	: 0;
		$linked_array 	= !empty($_GET['linked_array']) ? $_GET['linked_array'] : '';

		RC_DB::table('goods_article')->where('article_id', $article_id)->delete();

		$data = array();
		if (!empty($linked_array)) {
			foreach ($linked_array AS $val) {
				$data[] = array(
					'article_id'   => $article_id,
					'goods_id'     => $val['goods_id'],
					'admin_id'     => $_SESSION['staff_id'],
				);
			}
		}
		
		if (!empty($data)) {
			RC_DB::table('goods_article')->insert($data);
		}
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$article_id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
		
		$article = get_merchant_article_info($article_id);
		//TODO语言包升级
		ecjia_merchant::admin_log(__('关联商品', 'article').'，'.__('文章标题是', 'article').$article['title'], 'setup', 'article');
		return $this->showmessage(__('操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('article/merchant/link_goods', array('id' => $article_id))));
	}

	/**
	 * 编辑文章标题
	 */
	public function edit_title() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);

		$id    	= !empty($_POST['pk'])    ? intval($_POST['pk'])      : 0;
		$title 	= !empty($_POST['value']) ? remove_xss($_POST['value'])     : '';
		$cat_id = !empty($_POST['name'])  ? intval($_POST['name'])    : 0;

		if (empty($title)) {
			return $this->showmessage(__('文章标题不能为空', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$title_exist = article_title_exists($title, $id);
		if ($title_exist) {
			return $this->showmessage(sprintf(__('文章 %s 已经存在', 'article'), $title), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
	    $data = array(
	        'article_id'  => $id,
	        'title'       => $title
	    );
		$query = RC_DB::table('article')->where('article_id', $id)->where('store_id', $_SESSION['store_id'])->update($data);
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存

		if ($query) {
			ecjia_merchant::admin_log($title, 'edit', 'article');
			return $this->showmessage(__('文章标题编辑成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('content' => stripslashes($title)));
		} else {
			return $this->showmessage(__('文章标题编辑失败', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}

	/**
	 * 删除文章
	 */
	public function remove() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);

		$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
		
		$article = get_merchant_article_info($id);
		if (empty($article)) {
			return $this->showmessage(__('没有找到该文章', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}

		$delete = RC_DB::table('article')->where('article_id', $id)->where('store_id', $_SESSION['store_id'])->update(array('article_approved' => 'spam'));
		
		if ($delete) {
			/*释放文章缓存*/
			$orm_article_db = RC_Model::model('article/orm_article_model');
			$cache_article_info_key = 'article_info_'.$id;
			$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
			$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
			
			ecjia_merchant::admin_log(addslashes($article['title']), 'remove', 'article');
			return $this->showmessage(__('删除成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
		} else {
			return $this->showmessage(__('操作失败', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}
	
	/**
	 * 删除文章评论
	 */
	public function remove_comment() {
		$this->admin_priv('mh_article_comment_update', ecjia::MSGTYPE_JSON);
		
		$type = !empty($_GET['type']) ? remove_xss($_GET['type']) : '';
		$article_id = !empty($_GET['article_id']) ? intval($_GET['article_id']) : 0;
		
		//批量删除
		if ($type == 'batch') {
			$ids = !empty($_POST['id']) ? $_POST['id'] : '';
			if (!is_array($ids)){
				$ids = explode(',', $ids);
			}
			/*释放文章缓存*/
			$orm_article_db = RC_Model::model('article/orm_article_model');
			
			if (!empty($ids)) {
				$delete = RC_DB::table('discuss_comments')->whereIn('id', $ids)->where('store_id', $_SESSION['store_id'])->update(array('comment_approved' => 'spam'));

				if ($delete) {
					/*释放文章缓存*/
					$cache_article_info_key = 'article_info_'.$article_id;
					$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
					$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
					
					foreach ($ids as $v) {
						$comment_info = RC_DB::table('discuss_comments')->where('id', $v)->where('store_id', $_SESSION['store_id'])->first();
						$article = get_merchant_article_info($comment_info['id_value']);
						update_article_comment_count($comment_info['id_value']);
						//记录日志
						ecjia_merchant::admin_log('评论内容为：'.$comment_info['content'].'，'.__('文章标题是', 'article'). $article['title'], 'batch_remove', 'article_comment');
					}
					$pjaxurl = RC_Uri::url('article/merchant/article_comment');
					if (!empty($article_id)) {
						$pjaxurl .= '&id='.$article_id;
					}
					return $this->showmessage(__('批量删除操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => $pjaxurl));
				}
			}			
		} else {
			//删除单个
			$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
	
			$article = get_merchant_article_info($article_id);
			if (empty($article)) {
				return $this->showmessage(__('没有找到该文章', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
			
			$comment_info = RC_DB::table('discuss_comments')->where('id', $id)->where('store_id', $_SESSION['store_id'])->first();
			if (empty($comment_info)) {
				return $this->showmessage(__('没有找到该评论', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
			}
			$delete = RC_DB::table('discuss_comments')->where('id', $id)->where('store_id', $_SESSION['store_id'])->update(array('comment_approved' => 'spam'));
			
			if ($delete) {
				update_article_comment_count($article_id);
				/*释放文章缓存*/
				$orm_article_db = RC_Model::model('article/orm_article_model');
				$cache_article_info_key = 'article_info_'.$id;
				$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
				$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
					
				//记录日志
				ecjia_merchant::admin_log(__('文章标题是', 'article'). $article['title'], 'remove', 'article_comment');
				return $this->showmessage(__('删除成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
			}
		}
		return $this->showmessage(__('操作失败', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
	}

	/**
	 * 删除附件
	 */
	public function drop_file() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);

		$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
		$type = !empty($_GET['type']) ? remove_xss($_GET['type']) : '';
		
		$article = get_merchant_article_info($id);
		$disk = RC_Filesystem::disk();
		$disk->delete(RC_Upload::upload_path() . $article[$type]);
		
		$data = array(
		    $type => ''
		);
		RC_DB::table('article')->where('article_id', $id)->where('store_id', $_SESSION['store_id'])->update($data);
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		$cache_article_info_key = 'article_info_'.$id;
		$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
		$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
		
		return $this->showmessage(__('删除附件成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
	}

	/**
	 * 批量操作
	 */
	public function batch() {
		$this->admin_priv('mh_article_update', ecjia::MSGTYPE_JSON);
		
		$action = !empty($_GET['sel_action']) ? remove_xss($_GET['sel_action']) : 'move_to';
		$article_ids = !empty($_POST['article_id']) ? intval($_POST['article_id']) : '';
		
		if (!is_array($article_ids)){
			$article_ids = explode(',', $article_ids);
		}
		$info = RC_DB::table('article')->whereIn('article_id', $article_ids)->where('store_id', $_SESSION['store_id'])->get();
		
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		
		if (!empty($article_ids)) {
			switch ($action) {
				//批量删除
				case 'button_remove':
					RC_DB::table('article')->whereIn('article_id', $article_ids)->where('store_id', $_SESSION['store_id'])->update(array('article_approved' => 'spam'));

					foreach ($info as $v) {
						/*释放文章缓存*/
						$cache_article_info_key = 'article_info_'.$v['article_id'];
						$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
						$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
						
						ecjia_merchant::admin_log($v['title'], 'batch_remove', 'article');
					}

					return $this->showmessage(__('批量删除操作成功'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('article/merchant/init')));
					break;

				//批量转移分类
				case 'move_to' :
					$target_cat = intval($_GET['target_cat']);
					if ($target_cat <= 0) {
						return $this->showmessage(__('请选择文章分类', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
					}
					
					$data = array('cat_id' => $target_cat);
					RC_DB::table('article')->whereIn('article_id', $article_ids)->where('store_id', $_SESSION['store_id'])->update($data);
					$cat_name = RC_DB::table('article_cat')->where('cat_id', $target_cat)->pluck('cat_name');

					foreach ($info as $v) {
						/*释放文章缓存*/
						$cache_article_info_key = 'article_info_'.$v['article_id'];
						$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
						$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
						
						ecjia_merchant::admin_log(__('转移文章', 'article'). $v['title'] . __('至分类') . $cat_name, 'batch_setup', 'article');
					}
					return $this->showmessage(__('批量转移操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('article/merchant/init')));
					break;
				default :
					break;
			}
		}
	}
	
	/**
	 * 搜索商品，仅返回名称及ID
	 */
	public function get_goods_list() {
		$filter = $_GET['JSON'];
		/*商家条件*/
		if (!empty($_SESSION['store_id']) && $_SESSION['store_id'] > 0) {
			$filter['store_id'] = $_SESSION['store_id'];
		}
		$filter['is_on_sale'] = 1;
		$filter['is_delete'] = 0;
		$arr = get_merchant_goods_list($filter);
		$opt = array();
		if (!empty($arr)) {
			foreach ($arr AS $key => $val) {
				$opt[] = array(
					'value' => $val['goods_id'],
					'text'  => $val['goods_name'],
					'data'  => $val['shop_price']
				);
			}
		}
		return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('content' => $opt));
	}
	
	/**
	 * 文章评论列表
	 */
	public function article_comment() {
		$this->admin_priv('mh_article_comment_manage');
		
		$id = !empty($_GET['id']) ? intval($_GET['id']) : '';
		
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('文章评论列表', 'article')));
		$this->assign('ur_here', __('文章评论列表', 'article'));
		$this->assign('id', $id);
		
		$article_comment_list = $this->get_article_comment_list($id);
		
		$this->assign('article_list', RC_Uri::url('article/merchant/init'));
		$this->assign('data', $article_comment_list);
		$this->assign('filter', $article_comment_list['filter']);
		$this->assign('type_count', $article_comment_list['count']);
		$this->assign('type', $article_comment_list['filter']['type']);

        return $this->display('article_comment_list.dwt');
	}
	
	/**
	 * 评论状态审核
	 */
	public function comment_check() {
		$this->admin_priv('mh_article_comment_update', ecjia::MSGTYPE_JSON);
		
		$article_id = !empty($_POST['article_id']) ? intval($_POST['article_id']) : 0;
		$comment_id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
		$status = !empty($_POST['status']) ? remove_xss($_POST['status']) : 0;
		
		$type = !empty($_GET['type']) ? remove_xss($_GET['type']) : '';
		$keywords = !empty($_GET['keywords']) ? remove_xss($_GET['keywords']) : '';
		
		$pjax_url = RC_Uri::url('article/merchant/article_comment');
		if (!empty($type)) {
			$pjax_url .= '&type='.$type;
		}
		if (!empty($keywords)) {
			$pjax_url .= '&keywords='.$keywords;
		}
		
		$article = get_merchant_article_info($article_id);
		if (empty($article)) {
			return $this->showmessage(__('没有找到该文章', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$comment_info = get_merchant_comment_info($comment_id);
		
		if ($status == 1) {
			$message = sprintf(__('通过文章评论，评论内容为：%s，文章标题为：%s', 'article'), $comment_info['content'],$article['title']);
		}
		
		if ($status == 0) {
			$message = sprintf(__('驳回文章评论，评论内容为：%s，文章标题为：%s', 'article'), $comment_info['content'],$article['title']);
		}
		
		if ($status == 'spam') {
			$message = sprintf(__('设为垃圾评论，评论内容为：%s，文章标题为：%s', 'article'), $comment_info['content'],$article['title']);
		}
		
		$update = RC_DB::table('discuss_comments')->where('id', $comment_id)->where('store_id', $_SESSION['store_id'])->update(array('comment_approved' => $status));
		
		if ($update) {
			update_article_comment_count($article_id);
			
			/*释放文章缓存*/
			$orm_article_db = RC_Model::model('article/orm_article_model');
			$cache_article_info_key = 'article_info_'.$article_id;
			$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
			$orm_article_db->delete_cache_item($cache_id_info);
				
			//记录日志
			ecjia_merchant::admin_log($message, 'setup', 'article_comment');
			return $this->showmessage(__('操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => $pjax_url));
		}
	}
	
	/**
	 * 批量审核文章评论
	 */
	public function batch_check() {
		$this->admin_priv('mh_article_comment_update', ecjia::MSGTYPE_JSON);
		
		$type = !empty($_GET['type']) ? remove_xss($_GET['type']) : '';
		$ids = !empty($_POST['id']) ? $_POST['id'] : '';
		
		if (!is_array($ids)){
			$ids = explode(',', $ids);
		}
		/*释放文章缓存*/
		$orm_article_db = RC_Model::model('article/orm_article_model');
		
		if (!empty($ids)) {
			switch ($type) {
				case 'batch_check':
					RC_DB::table('discuss_comments')->whereIn('id', $ids)->where('store_id', $_SESSION['store_id'])->update(array('comment_approved' => 1));
					break;
				case 'batch_uncheck':
					RC_DB::table('discuss_comments')->whereIn('id', $ids)->where('store_id', $_SESSION['store_id'])->update(array('comment_approved' => 0));
					break;
				case 'batch_trash':
					RC_DB::table('discuss_comments')->whereIn('id', $ids)->where('store_id', $_SESSION['store_id'])->update(array('comment_approved' => 'trash'));
					break;
			}
			
			/*释放文章缓存*/
			$cache_article_info_key = 'article_info_'.$article_id;
			$cache_id_info = sprintf('%X', crc32($cache_article_info_key));
			$orm_article_db->delete_cache_item($cache_id_info);//释放article_info缓存
				
			foreach ($ids as $v) {
				$comment_info = RC_DB::table('discuss_comments')->where('id', $v)->where('store_id', $_SESSION['store_id'])->first();
				$article = get_merchant_article_info($comment_info['id_value']);
				update_article_comment_count($comment_info['id_value']);
				//记录日志
// 				ecjia_merchant::admin_log('评论内容为：'.$comment_info['content'].'，'.__('文章标题是', 'article'). $article['title'], 'batch_setup', 'article_comment');
				ecjia_merchant::admin_log(sprintf(__('评论内容为：%s，文章标题为：%s','article'), $comment_info['content'],$article['title']), 'batch_setup', 'article_comment');
			}
			$pjaxurl = RC_Uri::url('article/merchant/article_comment');
			if (!empty($article_id)) {
				$pjaxurl .= '&id='.$article_id;
			}
			return $this->showmessage(__('操作成功', 'article'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => $pjaxurl));
			
		}			
	}
	
	/**
	 * 获取文章评论列表
	 */
	private function get_article_comment_list($id) {
	    $filter = array();
	    $filter['keywords']   = empty($_GET['keywords'])      ? ''    	: remove_xss($_GET['keywords']);
	    $filter['sort_by']    = empty($_GET['sort_by'])       ? 'dc.id'	: remove_xss($_GET['sort_by']);
	    $filter['sort_order'] = empty($_GET['sort_order'])    ? 'DESC' 	: remove_xss($_GET['sort_order']);
	    $filter['type']   	  = empty($_GET['type'])      	  ? ''      : remove_xss($_GET['type']);
	    
	    $db_dc = RC_DB::table('discuss_comments as dc')
    	    ->leftJoin('article as a', RC_DB::raw('dc.id_value'), '=', RC_DB::raw('a.article_id'))
    	    ->where(RC_DB::raw('dc.comment_type'), 'article')
	    	->where(RC_DB::raw('dc.store_id'), $_SESSION['store_id'])
	    	->where(RC_DB::raw('dc.comment_approved'), '!=', 'spam');
	    
	    if (!empty($id)) {
	        $db_dc->where(RC_DB::raw('dc.id_value'), $id);
	    }
	    
	    if (!empty($filter['keywords'])) {
	        $db_dc->whereRaw('(dc.content like "%'.mysql_like_quote($filter['keywords']).'%" or dc.user_name like "%'.mysql_like_quote($filter['keywords']).'%")');
	    }
	    
	    $type_count = $db_dc->select(RC_DB::raw('SUM(IF(dc.comment_approved != "spam", 1, 0)) as count'),
	    		RC_DB::raw('SUM(IF(dc.comment_approved = "1", 1, 0)) as has_checked'),
	    		RC_DB::raw('SUM(IF(dc.comment_approved = "0", 1, 0)) as wait_check'),
	    		RC_DB::raw('SUM(IF(dc.comment_approved = "trash", 1, 0)) as trash'))->first();
	    
	    if ($filter['type'] == 'has_checked') {
	    	$db_dc->where(RC_DB::raw('dc.comment_approved'), 1);
	    }
	    
	    if ($filter['type'] == 'wait_check') {
	    	$db_dc->where(RC_DB::raw('dc.comment_approved'), 0);
	    }
	    
	    if ($filter['type'] == 'trash') {
	    	$db_dc->where(RC_DB::raw('dc.comment_approved'), 'trash');
	    }
	    
	    if ($filter['type'] == 'spam') {
	    	$db_dc->where(RC_DB::raw('dc.comment_approved'), 'spam');
	    }
	    
	    $count = $db_dc->select('id')->count();
	    $page  = new ecjia_merchant_page($count, 15, 5);

	    $result = $db_dc
    	    ->select(RC_DB::raw('dc.*'), RC_DB::raw('a.title'))
    	    ->orderby(RC_DB::raw($filter['sort_by']), $filter['sort_order'])
	        ->take(15)
		    ->skip($page->start_id-1)
		    ->get();

	    $arr = array();
	    if (!empty($result)) {
	        foreach ($result as $rows) {
	            if (isset($rows['add_time'])) {
	                $rows['date'] = RC_Time::local_date(ecjia::config('time_format'), $rows['add_time']);
	            }
	            $arr[] = $rows;
	        }
	    }
	    return array('arr' => $arr, 'filter' => $filter, 'page' => $page->show(2), 'desc' => $page->page_desc(), 'count' => $type_count);
	}
	
	/**
	 * 获取文章列表
	 */
	private function get_articles_list() {
		$filter = array();
		$filter['keywords']   = empty($_GET['keywords'])      ? ''                : remove_xss($_GET['keywords']);
		$filter['cat_id']     = empty($_GET['cat_id'])        ? 0                 : intval($_GET['cat_id']);
		
		$filter['sort_order'] = empty($_GET['sort_order'])    ? 'DESC'            : remove_xss($_GET['sort_order']);
		$filter['type']   	  = empty($_GET['type'])      	  ? ''                : remove_xss($_GET['type']);
		$filter['sort_by'] 	  = 'a.article_id';
		
		if (!empty($_GET['sort_by'])) {
			if ($_GET['sort_by'] == 'like_count') {
				$filter['sort_by'] = 'a.like_count';
			}
			if ($_GET['sort_by'] == 'comment_count') {
				$filter['sort_by'] = 'a.comment_count';
			}
		}
		
		$db_article = RC_DB::table('article as a')
		    ->where(RC_DB::raw('a.store_id'), $_SESSION['store_id'])
			->leftJoin('article_cat as ac', RC_DB::raw('ac.cat_id'), '=', RC_DB::raw('a.cat_id'))
			->where(RC_DB::raw('a.article_approved'), '!=', 'spam');
		
		//不获取系统帮助文章的过滤
		$db_article->where(RC_DB::raw('ac.cat_type'), 'article');
		
		if (!empty($filter['keywords'])) {
			$db_article ->where(RC_DB::raw('title'), 'like', '%' . mysql_like_quote($filter['keywords']) . '%');
		}
		if ($filter['cat_id'] && ($filter['cat_id'] > 0)) {
			$db_article ->whereIn(RC_DB::raw('a.cat_id'), article_cat::get_children_list($filter['cat_id']));
		}
		
		$type_count = $db_article->select(RC_DB::raw('SUM(IF(a.article_approved != "spam", 1, 0)) as count'),
			RC_DB::raw('SUM(IF(a.article_approved = "1", 1, 0)) as has_checked'),
			RC_DB::raw('SUM(IF(a.article_approved = "0", 1, 0)) as wait_check'),
			RC_DB::raw('SUM(IF(a.article_approved = "trash", 1, 0)) as trash'))->first();

		if ($filter['type'] == 'has_checked') {
			$db_article->where(RC_DB::raw('a.article_approved'), 1);
		}

		if ($filter['type'] == 'wait_check') {
			$db_article->where(RC_DB::raw('a.article_approved'), 0);
		}

		if ($filter['type'] == 'trash') {
			$db_article->where(RC_DB::raw('a.article_approved'), 'trash');
		}

		$count = $db_article->select(RC_DB::raw('a.article_id'))->count();
		$page = new ecjia_merchant_page($count, 15, 5);
		
		$result = $db_article->select(RC_DB::raw('a.*'), RC_DB::raw('ac.cat_id'), RC_DB::raw('ac.cat_name'), RC_DB::raw('ac.cat_type'), RC_DB::raw('ac.sort_order'))
			->orderby(RC_DB::raw($filter['sort_by']), $filter['sort_order'])
			->take(15)
		    ->skip($page->start_id-1)
		    ->get();

		$arr = array();
		if (!empty($result)) {
			foreach ($result as $rows) {
				if (isset($rows['add_time'])) {
					$rows['date'] = RC_Time::local_date(ecjia::config('time_format'), $rows['add_time']);
				}
				$arr[] = $rows;
			}
		}
		
		return array('arr' => $arr, 'filter' => $filter, 'page' => $page->show(2), 'desc' => $page->page_desc(), 'count' => $type_count);
	}
}

// end