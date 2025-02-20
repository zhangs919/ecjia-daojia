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
 * 获取店铺基本信息
 */
function get_merchant_info($store_id = 0)
{
    if (empty($store_id)) {
        $store_id = $_SESSION['store_id'];
    }
    if (empty($store_id)) {
        return array();
    }
    $data = array('shop_kf_mobile' => '', 'shop_nav_background' => '', 'shop_logo' => '', 'shop_banner_pic' => '', 'shop_trade_time' => '', 'shop_description' => '', 'shop_notice' => '', 'express_assign_auto' => '');
    $data = get_merchant_config('', $data);
    if (!empty($data['shop_trade_time'])) {
        $shop_time = unserialize($data['shop_trade_time']);
        unset($data['shop_trade_time']);
        $sart_time = explode(':', $shop_time['start']);
        $end_time = explode(':', $shop_time['end']);
        $s_time = $sart_time[0] * 60 + $sart_time[1];
        $e_time = $end_time[0] * 60 + $end_time[1];
    } else {
        // 默认时间点 8:00-21:00
        $s_time = 480;
        $e_time = 1260;
    }
    $data['shop_trade_time'] = get_store_trade_time($store_id);
    $data['shop_nav_background'] = !empty($data['shop_nav_background']) ? RC_Upload::upload_url($data['shop_nav_background']) : '';
    $data['shop_logo'] = !empty($data['shop_logo']) ? RC_Upload::upload_url($data['shop_logo']) : '';
    $data['shop_banner_pic'] = !empty($data['shop_banner_pic']) ? RC_Upload::upload_url($data['shop_banner_pic']) : '';
    $data['shop_time_value'] = $s_time . "," . $e_time;
    return $data;
}

function get_store_trade_time($store_id = 0) {
    if (empty($store_id)) {
        $store_id = $_SESSION['store_id'];
    }
    if (empty($store_id)) {
        return false;
    }
    
    $trade_time = get_merchant_config('shop_trade_time', '', $store_id);
    if (empty($trade_time)) {
        return '暂未设置';
    }
    $trade_time = unserialize($trade_time);
    if (empty($trade_time)) {
        return '暂未设置';
    }
    $sart_time = $trade_time['start'];
    $end_time = explode(':', $trade_time['end']);
    if ($end_time[0] >= 24) {
        $end_time[0] = '次日'. ($end_time[0] - 24);
    }
    
    return $sart_time . '--' . $end_time[0] . ':' . $end_time[1];
    
    }
/*
 * 获取店铺配置信息
 */
function get_merchant_config($code = '', $arr = '', $store_id = 0)
{
    if (empty($store_id)) {
        $store_id = $_SESSION['store_id'];
    }
    if (empty($store_id)) {
        return array();
    }
    //$merchants_config = RC_Model::model('merchant/merchants_config_model');
    if (empty($code)) {
        if (is_array($arr)) {
            $config = RC_DB::table('merchants_config')->where('store_id', $store_id)->select('code', 'value')->get();
            foreach ($config as $key => $value) {
                $arr[$value['code']] = $value['value'];
            }
            return $arr;
        } else {
            return;
        }
    } else {
        //$config = $merchants_config->where(array('store_id' => $store_id, 'code' => $code))->get_field('value');
    	$config = RC_DB::table('merchants_config')->where('store_id', $store_id)->where('code', $code)->pluck('value');
        return $config;
    }
}
/*
 * 上传图片
 *  @param string $path 上传路径
 *  @param string $code 接收图片参数
 *  @param string $old_images 旧图片
 */
function merchant_file_upload_info($path, $code, $old_images = '')
{
    $code = empty($code) ? $path : $code;
    $upload = RC_Upload::uploader('image', array('save_path' => 'merchant/' . $_SESSION['store_id'] . '/data/' . $path, 'auto_sub_dirs' => true));
    $file = $_FILES[$code];
    if (!empty($file) && (isset($file['error']) && $file['error'] == 0 || !isset($file['error']) && $file['tmp_name'] != 'none')) {
        // 检测图片类型是否符合
        if (!$upload->check_upload_file($file)) {
            return new ecjia_error('upload_error', $upload->error());
        } else {
            $image_info = $upload->upload($file);
            if (empty($image_info)) {
                return new ecjia_error('upload_error', $upload->error());
            }
            // 删除旧的图片
            if (!empty($old_images)) {
                $upload->remove($old_images);
            }
            $img_path = $upload->get_position($image_info);
        }
        return $img_path;
    }
}
/*
 * 设置店铺配置信息
 */
function set_merchant_config($code = '', $value = '', $arr = '')
{
    //$merchants_config = RC_Model::model('merchant/merchants_config_model');
    if (empty($code)) {
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                //$count = $merchants_config->where(array('store_id' => $_SESSION['store_id'], 'code' => $key))->count();
            	$count = RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', $key)->count();
                if (empty($count)) {
                    RC_DB::table('merchants_config')->insert(array('store_id' => $_SESSION['store_id'], 'code' => $key, 'value' => $val));
                } else {
                    //$merchants_config->where(array('store_id' => $_SESSION['store_id'], 'code' => $key))->update(array('value' => $val));
                	RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', $key)->update(array('value' => $val));
                }
            }
            return true;
        } else {
            return new ecjia_error(101, '参数错误');
        }
    } else {
        //$count = $merchants_config->where(array('store_id' => $_SESSION['store_id'], 'code' => $code))->count();
    	$count = RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', $code)->count();
        if (empty($count)) {
            RC_DB::table('merchants_config')->insert(array('store_id' => $_SESSION['store_id'], 'code' => $code, 'value' => $value));
        } else {
            //$merchants_config->where(array('store_id' => $_SESSION['store_id'], 'code' => $code))->update(array('value' => $value));
        	RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', $code)->update(array('value' => $value));
        }
        return true;
    }
}
/**
* 清除用户购物车
*/
function clear_cart_list($store_id = 0)
{
    if (empty($store_id)) {
        return false;
    }
    // 清除所有用户购物车内商家的商品
    RC_DB::table('cart')->where('store_id', $store_id)->delete();
}

// end