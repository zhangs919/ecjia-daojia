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
/**
 * 加载手动加载的类
 */
return [
    'touch_function'    => 'extras/classes/utility/touch_function.class.php',
    'article_function'  => 'extras/classes/utility/article_function.class.php',
    'cart_function'     => 'extras/classes/utility/cart_function.class.php',
    'goods_function'    => 'extras/classes/utility/goods_function.class.php',
    'orders_function'   => 'extras/classes/utility/orders_function.class.php',
    'user_function'     => 'extras/classes/utility/user_function.class.php',
    'merchant_function' => 'extras/classes/utility/merchant_function.class.php',

    'user_front' => 'extras/classes/user/user_front.class.php',

    'touch_controller'             => 'extras/controller/touch_controller.php',
    'goods_controller'             => 'extras/controller/goods_controller.php',    //商品
    'merchant_controller'          => 'extras/controller/merchant_controller.php', //店铺
    'article_controller'           => 'extras/controller/article_controller.php',  //文章
    'cart_controller'              => 'extras/controller/cart_controller.php',     //购物车
    'user_controller'              => 'extras/controller/user_controller.php',     //会员
    'affiliate_controller'         => 'extras/controller/affiliate_controller.php',//推荐
    'franchisee_controller'        => 'extras/controller/franchisee_controller.php',   //商家入驻申请
    'user_privilege_controller'    => 'extras/controller/user_privilege_controller.php',   //登录注册
    'user_get_password_controller' => 'extras/controller/user_get_password_controller.php',//找回密码
    'user_account_controller'      => 'extras/controller/user_account_controller.php',
    'user_address_controller'      => 'extras/controller/user_address_controller.php', //用户收货地址
    'user_bonus_controller'        => 'extras/controller/user_bonus_controller.php',   //用户红包
    'user_order_controller'        => 'extras/controller/user_order_controller.php',   //订单
    'user_profile_controller'      => 'extras/controller/user_profile_controller.php', //用户资料
    'connect_controller'           => 'extras/controller/connect_controller.php',      //授权登录
    'mobile_controller'            => 'extras/controller/mobile_controller.php',
    'quickpay_controller'          => 'extras/controller/quickpay_controller.php',     //闪惠
    'payment_controller'           => 'extras/controller/payment_controller.php',     //支付

    'ecjia_location'            => 'extras/classes/ecjia_location.class.php',
    'ecjia_map'                 => 'extras/classes/ecjia_map.class.php',
    'ecjia_cart'                => 'extras/classes/ecjia_cart.class.php',
    'ecjia_cart_groupbuy'       => 'extras/classes/ecjia_cart_groupbuy.class.php',
    'ecjia_goods_specification' => 'extras/classes/ecjia_goods_specification.class.php',
    'ecjia_open_handler'        => 'extras/classes/ecjia_open_handler.class.php',
    'ecjia_theme_controller'    => 'extras/classes/ecjia_theme_controller.class.php',
    'ecjia_user_front_controller'    => 'extras/classes/ecjia_user_front_controller.class.php',

];
