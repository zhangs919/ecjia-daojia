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

use Ecjia\App\Platform\Frameworks\Contracts\PluginPageInterface;
use Ecjia\App\Platform\Frameworks\Controller\PluginPageController;

class mp_dzp_init extends PluginPageController implements PluginPageInterface
{

    public function init()
    {
        //设置插件目录
        $this->setPluginPath(__FILE__);

        //设置插件资源URL
        ecjia_front::$controller->assign('bootstrap_min_js', RC_Plugin::plugins_url('js/bootstrap.min.js', __FILE__));
        ecjia_front::$controller->assign('framework7_min_js', RC_Plugin::plugins_url('js/framework7.min.js', __FILE__));
        ecjia_front::$controller->assign('jquery_easing_min_js', RC_Plugin::plugins_url('js/jquery.easing.min.js', __FILE__));
        ecjia_front::$controller->assign('jquery_js', RC_Plugin::plugins_url('js/jquery.js', __FILE__));
        ecjia_front::$controller->assign('jQueryRotate_js', RC_Plugin::plugins_url('js/jQueryRotate.2.2.js', __FILE__));

        $this->assginPluginStyleUrl('activity_style_css', 'css/activity-style.css');
        $this->assginPluginStyleUrl('bootstrap_min_css', 'css/bootstrap.min.css');
        $this->assginPluginStyleUrl('models_css', 'css/models.css');

        $this->assginPluginStyleUrl('my_prize_png', 'images/my_prize.png');

        if (!ecjia_is_weixin()) {
            $uuid = trim($_GET['uuid']);
            $url = with(new Ecjia\App\Wechat\Authorize\WechatAuthorize($uuid))->getAuthorizeUrl(RC_Uri::current_url());
            return $this->redirect($url);
        }
    }

    public function action()
    {
        //初始化资源URL加载
        $this->init();

        // 获取GET请求数据
        $openid = trim($_GET['openid']);
        $uuid = trim($_GET['uuid']);

        ecjia_front::$controller->assign('form_action', RC_Uri::url('platform/plugin/show', array('handle' => 'mp_dzp/init_action', 'openid' => $openid, 'uuid' => $uuid, 'name' => 'mp_dzp')));

        $code = 'wechat_dazhuanpan';
        $platform_account = new Ecjia\App\Platform\Frameworks\Platform\Account($uuid);

        $wechat_id = $platform_account->getAccountID();
        $store_id = $platform_account->getStoreId();

        try {
            $MarketActivity = new Ecjia\App\Market\Prize\MarketActivity($code, $store_id, $wechat_id);
        } catch (Ecjia\App\Market\Exceptions\ActivityException $e) {
            return $this->showErrorMessage($e->getMessage());
        }

        $name = $MarketActivity->getActivityName();

        ecjia_front::$controller->assign('title', sprintf('%s - %s - %s', $name, $platform_account->getAccountName(), ecjia::config('shop_name')));

        //活动描述
        $description = $MarketActivity->getActivityDescription();
        ecjia_front::$controller->assign('description', $description);

        //奖品url
        $prize_url = RC_Uri::url('market/mobile_prize/prize_init', array('openid' => $openid, 'uuid' => $uuid, 'activity_id' => $MarketActivity->getActivityId()));
        ecjia_front::$controller->assign('prize_url', $prize_url);

        //获取用户剩余抽奖次数
        $prize_num = $MarketActivity->getLotteryOverCount($openid);
        if ($prize_num == -1) {
            $prize_num = __('无限次', 'mp_dzp');
        }
        ecjia_front::$controller->assign('prize_num', $prize_num);
        //奖品列表
        $prize_list = $MarketActivity->getPrizes()->toArray();
        ecjia_front::$controller->assign('prize', $prize_list);

        //当前活动的中奖记录
        $list = $MarketActivity->getActivityWinningLog()->toArray();
        ecjia_front::$controller->assign('list', $list);

        $countprize = count($prize_list);
        ecjia_front::$controller->assign('countprize', $countprize);

        $js_lang_array = array(
        		'reconnect'		=>	__('再接再厉', 'mp_dzp'),
        		'no_heart'		=>	__('不要灰心', 'mp_dzp'),
        		'no_pumping'	=> __('没有抽中', 'mp_dzp'),
        		'thank_you'		=> __('谢谢参与', 'mp_dzp'),
        		'good_luck'		=> __('祝您好运', 'mp_dzp'),
        		'give_up'		=> __('不要放弃', 'mp_dzp'),
        		'just_little'	=> __('就差一点', 'mp_dzp'),
        		'congratulations'	=> __('恭喜中了', 'mp_dzp'),
        		'get_award'			=> __('快去领奖吧', 'mp_dzp'),
        		'no_pumping'		=> __('没有抽中', 'mp_dzp'),
        		'ok'				=> __('确定', 'mp_dzp'),
        		'tip'				=> __('提示', 'mp_dzp'),
        		'go_to_award'		=> __('去领奖', 'mp_dzp'),
        		'winning'			=> __('中奖啦', 'mp_dzp'),
        		'recollect_later'	=> __('稍后再领', 'mp_dzp'),
        );
        ecjia_front::$controller->assign('js_lang', json_encode($js_lang_array));
        
        return ecjia_front::$controller->display($this->getPluginFilePath('templates/dzp_index.dwt.php'));
    }
}

// end
