<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_Base')):

/**
 * Listdom Base Class.
 *
 * @class LSD_Base
 * @version	1.0.0
 */
class LSD_Base
{
    const PTYPE_LISTING = 'listdom-listing';
    const PTYPE_SHORTCODE = 'listdom-shortcode';
    const PTYPE_SEARCH = 'listdom-search';
    const PTYPE_NOTIFICATION = 'listdom-notification';
    const TAX_LOCATION = 'listdom-location';
    const TAX_CATEGORY = 'listdom-category';
    const TAX_TAG = 'listdom-tag';
    const TAX_FEATURE = 'listdom-feature';
    const TAX_ATTRIBUTE = 'listdom-attribute';
    const TAX_LABEL = 'listdom-label';
    const STATUS_EXPIRED = 'expired';
    const STATUS_HOLD = 'hold';
    const STATUS_OFFLINE = 'offline';
    const EP_LISTING = 701;
    const DB_VERSION = 1;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}

    public function include_html_file($file = NULL, $args = array())
    {
        // File is empty
        if(!trim($file)) return esc_html__('HTML file is empty!', 'listdom');

        // Core File
        $path = $this->get_listdom_path().'/app/html/'.ltrim($file, '/');

        // Apply Filter
        $path = apply_filters('lsd_include_html_file', $path, $file, $args);
        
        // File is not exists
        if(!file_exists($path)) return esc_html__('HTML file is not exists! Check the file path please.', 'listdom');
        
        // Return the File Path
        if(isset($args['return_path']) and $args['return_path']) return $path;

        // Parameters passed
        if(isset($args['parameters']) and is_array($args['parameters']) and count($args['parameters'])) extract($args['parameters']);

        // Start buffering
        ob_start();
        
        // Include Once
        if(isset($args['include_once']) and $args['include_once']) include_once $path;
        else include $path;
        
        // Get Buffer
        $output = ob_get_clean();
            
        // Return the File OutPut
        if(isset($args['return_output']) and $args['return_output']) return $output;
        
        // Print the output
        echo trim($output);
    }
    
    public function get_listdom_path()
    {
        return LSD_ABSPATH;
    }

    public function get_upload_path()
    {
        // Create
        LSD_Folder::create(LSD_UP_DIR);

        return LSD_UP_DIR;
    }

    public function get_upload_url()
    {
        // WordPress Upload Directory
        $upload_dir = wp_upload_dir();
        return $upload_dir['baseurl'].'/listdom/';
    }
    
    public function lsd_url()
    {
        return plugins_url().'/'.LSD_DIRNAME;
    }
    
    public function lsd_asset_url($asset)
	{
		return $this->lsd_url().'/assets/'.trim($asset, '/ ');
	}

    public function lsd_asset_path($asset)
    {
        return $this->get_listdom_path().'/assets/'.trim($asset, '/ ');
    }

    public function current_ip()
    {
        return (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '');
    }

    public function get_post_meta($post_id)
    {
        $raw_data = get_post_meta($post_id, '', true);

        $data = array();
        foreach($raw_data as $key=>$val) $data[$key] = isset($val[0]) ? (!is_serialized($val[0]) ? $val[0] : unserialize($val[0])) : NULL;

        return $data;
    }

    public function get_term_meta($term_id)
    {
        $raw_data = get_term_meta($term_id, '', true);

        $data = array();
        foreach($raw_data as $key=>$val) $data[$key] = isset($val[0]) ? (!is_serialized($val[0]) ? $val[0] : unserialize($val[0])) : NULL;

        return $data;
    }

    public function get_user_meta($user_id)
    {
        $raw_data = get_user_meta($user_id, '', true);

        $data = array();
        foreach($raw_data as $key=>$val) $data[$key] = isset($val[0]) ? (!is_serialized($val[0]) ? $val[0] : unserialize($val[0])) : NULL;

        return $data;
    }

    public function get_map_control_positions()
    {
        // Positions
        return array(
            'TOP_LEFT'=>esc_html__('Top Left', 'listdom'), 'TOP_CENTER'=>esc_html__('Top Center', 'listdom'), 'TOP_RIGHT'=>esc_html__('Top Right', 'listdom'),
            'LEFT_TOP'=>esc_html__('Left Top', 'listdom'), 'LEFT_CENTER'=>esc_html__('Left Center', 'listdom'), 'LEFT_BOTTOM'=>esc_html__('Left Bottom', 'listdom'),
            'RIGHT_TOP'=>esc_html__('Right Top', 'listdom'), 'RIGHT_CENTER'=>esc_html__('Right Center', 'listdom'), 'RIGHT_BOTTOM'=>esc_html__('Right Bottom', 'listdom'),
            'BOTTOM_LEFT'=>esc_html__('Bottom Left', 'listdom'), 'BOTTOM_CENTER'=>esc_html__('Bottom Center', 'listdom'), 'BOTTOM_RIGHT'=>esc_html__('Bottom Right', 'listdom')
        );
    }

    public function get_available_sort_options()
    {
        // Sort Options
        $options = array(
            'post_date' => array(
                'status' => 1,
                'name' => esc_html__('List Date', 'listdom'),
                'order' => 'DESC',
            ),
            'title' => array(
                'status' => 1,
                'name' => esc_html__('Listing Title', 'listdom'),
                'order' => 'ASC',
            ),
            'modified' => array(
                'status' => 1,
                'name' => esc_html__('Last Update', 'listdom'),
                'order' => 'DESC',
            ),
            'comment_count' => array(
                'status' => 1,
                'name' => esc_html__('Comments', 'listdom'),
                'order' => 'DESC',
            ),
            'ID' => array(
                'status' => 1,
                'name' => esc_html__('Listing ID', 'listdom'),
                'order' => 'DESC',
            ),
            'author' => array(
                'status' => 1,
                'name' => esc_html__('Author', 'listdom'),
                'order' => 'ASC',
            ),
            'rand' => array(
                'status' => 0,
                'name' => esc_html__('Random', 'listdom'),
                'order' => 'ASC',
            )
        );

        return apply_filters('lsd_sort_options', $options);
    }

    public static function alert($message, $type = 'info')
    {
        if(!trim($message)) return '';
        return '<div class="lsd-alert lsd-'.esc_attr($type).'">'.$message.'</div>';
    }

    public static function parse_args($a, $b)
    {
        $a = (array) $a;
        $b = (array) $b;

        $result = $a;
        foreach($b as $k=>$v)
        {
            if(is_array($v) && isset($result[$k])) $result[$k] = self::parse_args($result[$k], $v);
            elseif(!isset($result[$k])) $result[$k] = $v;
        }

        return $result;
    }

    public static function get_text_color($bg_color = NULL)
    {
        return LSD_Color::text_color($bg_color);
    }

    public static function get_text_class($color = 'main')
    {
        return LSD_Color::text_class($color);
    }

    public function get_sf($default = array())
    {
        $vars = array_merge($_GET, $_POST);

        // Sanitization
        array_walk_recursive($vars, 'sanitize_text_field');

        $sf = array();
        foreach($vars as $key=>$value)
        {
            if(strpos($key, 'sf-') === false) continue;

            $parameter = substr($key, 3);

            // Attribute
            if(strpos($parameter, 'att-') !== false)
            {
                if(!isset($sf['attributes'])) $sf['attributes'] = array();

                $parameter = substr($parameter, 4);
                if(strpos($parameter, 'price-bt-') !== false)
                {
                    if(!isset($sf['attributes']['price-bt'])) $sf['attributes']['price-bt'] = '';
                    $sf['attributes']['price-bt'] .= sanitize_text_field($value).':';
                }
                else $sf['attributes'][$parameter] = is_array($value) ? $value : sanitize_text_field($value);
            }
            // Radius
            elseif(strpos($parameter, 'circle-') !== false)
            {
                if(!isset($sf['circle'])) $sf['circle'] = array();

                $parameter = substr($parameter, 7);
                $sf['circle'][$parameter] = sanitize_text_field($value);
            }
            elseif(in_array($parameter, array('s', 'shortcode')))
            {
                $sf[$parameter] = sanitize_text_field($value);
            }
            elseif(in_array($parameter, array('adults-eq', 'children-eq')))
            {
                $sf[substr($parameter, 0, -3)] = sanitize_text_field($value);
            }
            elseif($parameter == 'period')
            {
                // Dates
                $ex = explode(' - ', sanitize_text_field($value));

                // Main Library
                $main = new LSD_Main();

                // Format
                $settings = LSD_Options::settings();
                $format = (isset($settings['datepicker_format']) and trim($settings['datepicker_format'])) ? $settings['datepicker_format'] : 'yyyy-mm-dd';

                $sf[$parameter] = array($main->standardize_format($ex[0], $format), $main->standardize_format($ex[1], $format));
            }
            else
            {
                if(in_array($parameter, array('label', 'location', 'tag', 'category', 'feature'))) $parameter = 'listdom-'.$parameter;

                $values = array();
                if(is_array($value))
                {
                    foreach($value as $term)
                    {
                        if(is_numeric($term)) $values[] = sanitize_text_field($term);
                        else $values[] = LSD_Taxonomies::id(sanitize_text_field($term), $parameter);
                    }
                }

                // Force to Integer
                if(!is_array($value) and trim($value) != '' and !is_numeric($value)) $value = LSD_Taxonomies::id($value, $parameter);

                $sf[$parameter] = (is_array($value) and count($values)) ? $values : sanitize_text_field($value);
            }
        }

        if(isset($sf['attributes']) and isset($sf['attributes']['price-bt'])) $sf['attributes']['price-bt'] = trim($sf['attributes']['price-bt'], ': ');

        return count($sf) ? $sf : $default;
    }

    public function taxonomies()
    {
        return array(
            LSD_Base::TAX_CATEGORY,
            LSD_Base::TAX_LOCATION,
            LSD_Base::TAX_TAG,
            LSD_Base::TAX_FEATURE,
            LSD_Base::TAX_LABEL
        );
    }

    public function postTypes()
    {
        return array(
            LSD_Base::PTYPE_LISTING,
            LSD_Base::PTYPE_SHORTCODE,
            LSD_Base::PTYPE_SEARCH,
            LSD_Base::PTYPE_NOTIFICATION
        );
    }

    public static function isPro()
    {
        return class_exists('LSD_Pro') ? true : false;
    }

    public static function isLite()
    {
        return !LSD_Base::isPro();
    }

    public static function upgradeMessage($type = 'long')
    {
        if($type == 'short') return sprintf(esc_html__("Upgrade to %s first!", 'listdom'), '<a href="'.LSD_Base::getUpgradeURL().'" target="_blank"><strong>'.esc_html__('Pro Version', 'listdom').'</strong></a>');
        elseif($type == 'tiny') return sprintf(esc_html__("%s needed!", 'listdom'), '<a href="'.LSD_Base::getUpgradeURL().'" target="_blank"><strong>'.esc_html__('Upgrade', 'listdom').'</strong></a>');
        else return sprintf(esc_html__("You're using %s of listdom. You should upgrade to %s now to enjoy all features of listdom!", 'listdom'), '<strong>'.esc_html__('Lite Version', 'listdom').'</strong>', '<a href="'.LSD_Base::getUpgradeURL().'" target="_blank"><strong>'.esc_html__('Pro Version', 'listdom').'</strong></a>');
    }

    public static function missFeatureMessage($feature = NULL, $multiple = false)
    {
        if($multiple) return sprintf(esc_html__('%s are not included in lite version. You can upgrade to %s now to enjoy all features of listdom!', 'listdom'), ($feature ? '<strong>'.esc_html($feature).'</strong>' : esc_html__('This feature', 'listdom')), '<a href="'.LSD_Base::getUpgradeURL().'" target="_blank"><strong>'.esc_html__('Pro Version', 'listdom').'</strong></a>');
        else return sprintf(esc_html__('%s is not included in lite version. You can upgrade to %s now to enjoy all features of listdom!', 'listdom'), ($feature ? '<strong>'.esc_html($feature).'</strong>' : esc_html__('This feature', 'listdom')), '<a href="'.LSD_Base::getUpgradeURL().'" target="_blank"><strong>'.esc_html__('Pro Version', 'listdom').'</strong></a>');
    }

    public static function getUpgradeURL()
    {
        return 'https://totalery.com/upgrade/listdom.php';
    }

    public static function addons()
    {
        return apply_filters('lsd_addons', array());
    }

    public static function getShortcodes($minified = false)
    {
        $query = array('post_type'=>LSD_Base::PTYPE_SHORTCODE, 'posts_per_page'=>'-1');
        $shortcodes = get_posts($query);

        if($minified)
        {
            $rendered = array();
            foreach($shortcodes as $shortcode) $rendered[] = array('id' => $shortcode->ID, 'title' => $shortcode->post_title);

            return $rendered;
        }

        return $shortcodes;
    }

    public static function isPastFromInstallationTime($seconds = 86400)
    {
        $installation_time = get_option('lsd_installation_time', NULL);
        if(!$installation_time) return false;

        return ($installation_time + $seconds) < time();
    }

    public function current_url()
    {
        // get $_SERVER
        $server = $_SERVER;

        // Check protocol
        $page_url = 'http';
        if(isset($server['HTTPS']) and $server['HTTPS'] == 'on') $page_url .= 's';

        // Get domain
        $site_domain = (isset($server['HTTP_HOST']) and trim($server['HTTP_HOST']) != '') ? $server['HTTP_HOST'] : $server['SERVER_NAME'];

        $page_url .= '://';
        $page_url .= $site_domain.$server['REQUEST_URI'];

        // Return full URL
        return $page_url;
    }

    public function remove_qs_var($key, $url = '')
    {
        if(trim($url) == '') $url = $this->current_url();

        return remove_query_arg($key, $url);
    }

    public function add_qs_var($key, $value, $url = '')
    {
        if(trim($url) == '') $url = $this->current_url();

        return add_query_arg($key, $value, $url);
    }

    public function add_qs_vars($vars, $url = '')
    {
        if(trim($url) == '') $url = $this->current_url();

        return add_query_arg($vars, $url);
    }

    public static function str_random($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $count = strlen($characters);

        $string = '';
        for($i = 0; $i < $length; $i++) $string .= $characters[rand(0, $count - 1)];

        return $string;
    }

    public function tax_checkboxes($args)
    {
        $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : LSD_Base::TAX_CATEGORY;
        $hide_empty = isset($args['hide_empty']) ? (boolean) $args['hide_empty'] : false;
        $parent = isset($args['parent']) ? $args['parent'] : 0;
        $current = isset($args['current']) ? $args['current'] : array();
        $name = isset($args['name']) ? $args['name'] : 'lsd_tax';
        $id_prefix = isset($args['id_prefix']) ? $args['id_prefix'] : '';

        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => $hide_empty,
            'parent' => $parent,
            'orderby' => 'name',
            'order' => 'ASC',
        ));

        $output = '';
        foreach($terms as $term)
        {
            $output .= '<li>';
            $output .= '<input type="checkbox" name="'.esc_attr($name).'['.esc_attr($term->term_id).']" id="lsd_categories_'.esc_attr($id_prefix.$term->term_id).'" value="1" '.((isset($current[$term->term_id]) and $current[$term->term_id]) ? 'checked="checked"' : '').'><label for="lsd_categories_'.esc_attr($id_prefix.$term->term_id).'">'.esc_html($term->name).'</label>';

            $children = get_term_children($term->term_id, $taxonomy);
            if(is_array($children) and count($children))
            {
                $output .= '<ul class="lsd-children">';
                $output .= $this->tax_checkboxes(array(
                    'taxonomy' => $taxonomy,
                    'parent' => $term->term_id,
                    'current' => $current,
                    'name' => $name,
                    'id_prefix' => $id_prefix,
                ));
                $output .= '</ul>';
            }

            $output .= '</li>';
        }

        return $output;
    }

    public static function minimize($number)
    {
        if($number < 1000) return round($number);
        elseif($number >= 1000 and $number < 100000) return round($number/1000, 1).'K';
        elseif($number >= 100000 and $number < 1000000) return round($number/1000).'K';
        elseif($number >= 1000000) return round($number/1000000, 2).'M';

        return round($number);
    }

    public static function indexify($array)
    {
        $trimmed = array();
        foreach($array as $key => $value)
        {
            if(is_numeric($key)) $trimmed[$key] = $value;
        }

        return $trimmed;
    }

    public static function getPostStatusLabel($status, $override = array())
    {
        // Return From Provided Labels
        if(isset($override[$status])) return $override[$status];

        // Return WP Label
        $obj = get_post_status_object($status);
        return $obj->label;
    }

    public static function stars($stars)
    {
        // Rounded Stars
        $rounded = round($stars);

        $output = '<span class="lsd-stars" title="'.sprintf(esc_html__('%s from 5', 'listdom'), $stars).'">';
        for($i = 1; $i <= 5; $i++) $output .= '<span><i class="lsd-icon '.($rounded >= $i ? 'fas fa-star' : 'far fa-star').'"></i></span>';
        $output .= '</span>';

        return $output;
    }

    public static function date_format()
    {
        return get_option('date_format');
    }

    public static function time_format()
    {
        return get_option('time_format');
    }

    public static function datetime_format()
    {
        return LSD_Base::date_format().' '.LSD_Base::time_format();
    }

    public static function date($time, $format = NULL)
    {
        if(!is_numeric($time)) $time = strtotime($time);
        if(is_null($format)) $format = LSD_Base::date_format();

        if(function_exists('wp_date')) return wp_date($format, $time);
        else return date($format, $time);
    }

    public static function time($time, $format = NULL)
    {
        if(!is_numeric($time)) $time = strtotime($time);
        if(is_null($format)) $format = LSD_Base::time_format();

        if(function_exists('wp_date')) return wp_date($format, $time);
        else return date($format, $time);
    }

    public static function datetime($time, $format = NULL)
    {
        if(!is_numeric($time)) $time = strtotime($time);
        if(is_null($format)) $format = LSD_Base::datetime_format();

        if(function_exists('wp_date')) return wp_date($format, $time);
        else return date($format, $time);
    }

    public static function diff($start, $end, $type = 'days')
    {
        try
        {
            $s = new DateTime($start);
            $e = new DateTime($end);

            // Interval
            $interval = $s->diff($e);

            // End is before Start!
            if(isset($interval->invert) and $interval->invert) return false;

            // Return Diff
            return (isset($interval->{$type}) ? $interval->{$type} : false);
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * We don't use WP get_edit_post_link function because it returns empty string
     * for guest users but we need it to be included in the email notifications
     * @param int $id
     * @return mixed|void
     */
    public function get_edit_post_link($id = 0)
    {
        $post = get_post($id);
        $post_type_object = get_post_type_object($post->post_type);

        $link = admin_url(sprintf($post_type_object->_edit_link . '&action=edit', $post->ID));
        return apply_filters('get_edit_post_link', $link, $post->ID, 'display');
    }

    public function render_price($price, $currency, $minimized = false)
    {
        // Return Free if price is 0
        if($price == '0') return esc_html__('Free', 'mec');

        // General Settings
        $settings = LSD_Options::settings();

        $thousand_separator = ',';
        $decimal_separator = '.';
        $currency_sign_position = ((isset($settings['currency_position']) and trim($settings['currency_position'])) ? $settings['currency_position'] : 'before');

        // Force to double
        if(is_string($price)) $price = (double) $price;

        // Disable decimals if not needed
        if(strpos($price, '.') === false) $decimal_separator = false;

        // Minimize
        if($minimized) $rendered = $this->minimize($price);
        else
        {
            $rendered = number_format($price, ($decimal_separator === false ? 0 : 2), ($decimal_separator === false ? '' : $decimal_separator), $thousand_separator);
        }

        $sign = $this->get_currency_sign($currency);

        if($currency_sign_position == 'after') $rendered = $rendered.$sign;
        elseif($currency_sign_position == 'after_ws') $rendered = $rendered.' '.$sign;
        elseif($currency_sign_position == 'before_ws') $rendered = $sign.' '.$rendered;
        else $rendered = $sign.$rendered;

        return $rendered;
    }

    public function get_currency_sign($currency)
    {
        $currencies = $this->get_currencies();

        $sign = array_search($currency, $currencies);
        $sign = (trim($sign) ? $sign : $currency);

        return apply_filters('lsd_currency_sign', $sign, $currency, $currencies);
    }

    /**
     * Returns MEC currencies
     * @return array
     */
    public static function get_currencies()
    {
        $currencies = array(
            '$'=>'USD',
            '€'=>'EUR',
            '£'=>'GBP',
            'CHF'=>'CHF',
            'CAD'=>'CAD',
            'AUD'=>'AUD',
            'JPY'=>'JPY',
            'SEK'=>'SEK',
            'GEL'=>'GEL',
            'AFN'=>'AFN',
            'ALL'=>'ALL',
            'DZD'=>'DZD',
            'AOA'=>'AOA',
            'ARS'=>'ARS',
            'AMD'=>'AMD',
            'AWG'=>'AWG',
            'AZN'=>'AZN',
            'BSD'=>'BSD',
            'BHD'=>'BHD',
            'BBD'=>'BBD',
            'BYR'=>'BYR',
            'BZD'=>'BZD',
            'BMD'=>'BMD',
            'BTN'=>'BTN',
            'BOB'=>'BOB',
            'BAM'=>'BAM',
            'BWP'=>'BWP',
            'BRL'=>'BRL',
            'BND'=>'BND',
            'BGN'=>'BGN',
            'BIF'=>'BIF',
            'KHR'=>'KHR',
            'CVE'=>'CVE',
            'KYD'=>'KYD',
            'XAF'=>'XAF',
            'CLP'=>'CLP',
            'COP'=>'COP',
            'KMF'=>'KMF',
            'CDF'=>'CDF',
            'NZD'=>'NZD',
            'CRC'=>'CRC',
            'HRK'=>'HRK',
            'CUC'=>'CUC',
            'CUP'=>'CUP',
            'CZK'=>'CZK',
            'DKK'=>'DKK',
            'DJF'=>'DJF',
            'RD$'=>'DOP',
            'XCD'=>'XCD',
            'EGP'=>'EGP',
            'ERN'=>'ERN',
            'EEK'=>'EEK',
            'ETB'=>'ETB',
            'FKP'=>'FKP',
            'FJD'=>'FJD',
            'GMD'=>'GMD',
            'GHS'=>'GHS',
            'GIP'=>'GIP',
            'GTQ'=>'GTQ',
            'GNF'=>'GNF',
            'GYD'=>'GYD',
            'HTG'=>'HTG',
            'HNL'=>'HNL',
            'HKD'=>'HKD',
            'HUF'=>'HUF',
            'ISK'=>'ISK',
            'INR'=>'INR',
            'IDR'=>'IDR',
            'IRR'=>'IRR',
            'IQD'=>'IQD',
            'ILS'=>'ILS',
            'JMD'=>'JMD',
            'JOD'=>'JOD',
            'KZT'=>'KZT',
            'KES'=>'KES',
            'KWD'=>'KWD',
            'KGS'=>'KGS',
            'LAK'=>'LAK',
            'LVL'=>'LVL',
            'LBP'=>'LBP',
            'LSL'=>'LSL',
            'LRD'=>'LRD',
            'LYD'=>'LYD',
            'LTL'=>'LTL',
            'MOP'=>'MOP',
            'MKD'=>'MKD',
            'MGA'=>'MGA',
            'MWK'=>'MWK',
            'MYR'=>'MYR',
            'MVR'=>'MVR',
            'MRO'=>'MRO',
            'MUR'=>'MUR',
            'MXN'=>'MXN',
            'MDL'=>'MDL',
            'MNT'=>'MNT',
            'MAD'=>'MAD',
            'MZN'=>'MZN',
            'MMK'=>'MMK',
            'NAD'=>'NAD',
            'NRs.'=>'NPR',
            'ANG'=>'ANG',
            'TWD'=>'TWD',
            'NIO'=>'NIO',
            'NGN'=>'NGN',
            'KPW'=>'KPW',
            'NOK'=>'NOK',
            'OMR'=>'OMR',
            'PKR'=>'PKR',
            'PAB'=>'PAB',
            'PGK'=>'PGK',
            'PYG'=>'PYG',
            'PEN'=>'PEN',
            'PHP'=>'PHP',
            'PLN'=>'PLN',
            'QAR'=>'QAR',
            'CNY'=>'CNY',
            'RON'=>'RON',
            'RUB'=>'RUB',
            'RWF'=>'RWF',
            'SHP'=>'SHP',
            'SVC'=>'SVC',
            'WST'=>'WST',
            'SAR'=>'SAR',
            'RSD'=>'RSD',
            'SCR'=>'SCR',
            'SLL'=>'SLL',
            'SGD'=>'SGD',
            'SBD'=>'SBD',
            'SOS'=>'SOS',
            'ZAR'=>'ZAR',
            'KRW'=>'KRW',
            'LKR'=>'LKR',
            'SDG'=>'SDG',
            'SRD'=>'SRD',
            'SZL'=>'SZL',
            'SYP'=>'SYP',
            'STD'=>'STD',
            'TJS'=>'TJS',
            'TZS'=>'TZS',
            'THB'=>'THB',
            'TOP'=>'TOP',
            'PRB'=>'PRB',
            'TTD'=>'TTD',
            'TND'=>'TND',
            'TRY'=>'TRY',
            'TMT'=>'TMT',
            'TVD'=>'TVD',
            'UGX'=>'UGX',
            'UAH'=>'UAH',
            'AED'=>'AED',
            'UYU'=>'UYU',
            'UZS'=>'UZS',
            'VUV'=>'VUV',
            'VEF'=>'VEF',
            'VND'=>'VND',
            'XOF'=>'XOF',
            'YER'=>'YER',
            'ZMK'=>'ZMK',
            'ZWL'=>'ZWL',
        );

        return apply_filters('lsd_currencies', $currencies);
    }

	public static function get_font_icons()
    {
        return array
        (
            'fas fa-glass-martini'                      => 'f000',
            'fa fa-music'                               => 'f001',
            'fa fa-search'                              => 'f002',
            'far fa-envelope'                          	=> 'f003',
            'fa fa-heart'                               => 'f004',
            'fa fa-star'                                => 'f005',
            'far fa-star'                              	=> 'f006',
            'fa fa-user'                                => 'f007',
            'fa fa-film'                                => 'f008',
            'fa fa-th-large'                            => 'f009',
            'fa fa-th'                                  => 'f00a',
            'fa fa-th-list'                             => 'f00b',
            'fa fa-check'                               => 'f00c',
            'fa fa-times'                               => 'f00d',
            'fa fa-search-plus'                         => 'f00e',
            'fa fa-search-minus'                        => 'f010',
            'fa fa-power-off'                           => 'f011',
            'fa fa-signal'                              => 'f012',
            'fa fa-cog'                                 => 'f013',
            'far fa-trash-alt'                        	=> 'f014',
            'fa fa-home'                                => 'f015',
            'far fa-file'                              	=> 'f016',
            'far fa-clock'                             	=> 'f017',
            'fa fa-road'                                => 'f018',
            'fa fa-download'                            => 'f019',
            'far fa-arrow-alt-circle-down'             	=> 'f01a',
            'far fa-arrow-alt-circle-up'              	=> 'f01b',
            'fa fa-inbox'                               => 'f01c',
            'far fa-play-circle'                       	=> 'f01d',
            'fas fa-redo-alt'                          	=> 'f01e',
            'fas fa-sync-alt'                          	=> 'f021',
            'fa fa-list-alt'                            => 'f022',
            'fa fa-lock'                                => 'f023',
            'fa fa-flag'                                => 'f024',
            'fa fa-headphones'                          => 'f025',
            'fa fa-volume-off'                          => 'f026',
            'fa fa-volume-down'                         => 'f027',
            'fa fa-volume-up'                           => 'f028',
            'fa fa-qrcode'                              => 'f029',
            'fa fa-barcode'                             => 'f02a',
            'fa fa-tag'                                 => 'f02b',
            'fa fa-tags'                                => 'f02c',
            'fa fa-book'                                => 'f02d',
            'fa fa-bookmark'                            => 'f02e',
            'fa fa-print'                               => 'f02f',
            'fa fa-camera'                              => 'f030',
            'fa fa-font'                                => 'f031',
            'fa fa-bold'                                => 'f032',
            'fa fa-italic'                              => 'f033',
            'fa fa-text-height'                         => 'f034',
            'fa fa-text-width'                          => 'f035',
            'fa fa-align-left'                          => 'f036',
            'fa fa-align-center'                        => 'f037',
            'fa fa-align-right'                         => 'f038',
            'fa fa-align-justify'                       => 'f039',
            'fa fa-list'                                => 'f03a',
            'fa fa-outdent'                             => 'f03b',
            'fa fa-indent'                              => 'f03c',
            'fas fa-video'                        		=> 'f03d',
            'far fa-image'                           	=> 'f03e',
            'fas fa-pencil-alt'                       	=> 'f040',
            'fa fa-map-marker'                          => 'f041',
            'fa fa-adjust'                              => 'f042',
            'fa fa-tint'                                => 'f043',
            'fas fa-edit'                     			=> 'f044',
            'fas fa-share-square'                      	=> 'f045',
            'far fa-check-square'                      	=> 'f046',
            'fas fa-arrows-alt'                      	=> 'f047',
            'fa fa-step-backward'                       => 'f048',
            'fa fa-fast-backward'                       => 'f049',
            'fa fa-backward'                            => 'f04a',
            'fa fa-play'                                => 'f04b',
            'fa fa-pause'                               => 'f04c',
            'fa fa-stop'                                => 'f04d',
            'fa fa-forward'                             => 'f04e',
            'fa fa-fast-forward'                        => 'f050',
            'fa fa-step-forward'                        => 'f051',
            'fa fa-eject'                               => 'f052',
            'fa fa-chevron-left'                        => 'f053',
            'fa fa-chevron-right'                       => 'f054',
            'fa fa-plus-circle'                         => 'f055',
            'fa fa-minus-circle'                        => 'f056',
            'fa fa-times-circle'                        => 'f057',
            'fa fa-check-circle'                        => 'f058',
            'fa fa-question-circle'                     => 'f059',
            'fa fa-info-circle'                         => 'f05a',
            'fa fa-crosshairs'                          => 'f05b',
            'far fa-times-circle'                      => 'f05c',
            'far fa-check-circle'                      => 'f05d',
            'fa fa-ban'                                 => 'f05e',
            'fa fa-arrow-left'                          => 'f060',
            'fa fa-arrow-right'                         => 'f061',
            'fa fa-arrow-up'                            => 'f062',
            'fa fa-arrow-down'                          => 'f063',
            'fa fa-share'                               => 'f064',
            'fa fa-expand'                              => 'f065',
            'fa fa-compress'                            => 'f066',
            'fa fa-plus'                                => 'f067',
            'fa fa-minus'                               => 'f068',
            'fa fa-asterisk'                            => 'f069',
            'fa fa-exclamation-circle'                  => 'f06a',
            'fa fa-gift'                                => 'f06b',
            'fa fa-leaf'                                => 'f06c',
            'fa fa-fire'                                => 'f06d',
            'fa fa-eye'                                 => 'f06e',
            'fa fa-eye-slash'                           => 'f070',
            'fa fa-exclamation-triangle'                => 'f071',
            'fa fa-plane'                               => 'f072',
            'fa fa-calendar'                            => 'f073',
            'fa fa-random'                              => 'f074',
            'fa fa-comment'                             => 'f075',
            'fa fa-magnet'                              => 'f076',
            'fa fa-chevron-up'                          => 'f077',
            'fa fa-chevron-down'                        => 'f078',
            'fa fa-retweet'                             => 'f079',
            'fa fa-shopping-cart'                       => 'f07a',
            'fa fa-folder'                              => 'f07b',
            'fa fa-folder-open'                         => 'f07c',
            'fas fa-arrows-alt-v'                     	=> 'f07d',
            'fas fa-arrows-alt-h'                     	=> 'f07e',
            'far fa-chart-bar'                         	=> 'f080',
            'fab fa-twitter-square'                    	=> 'f081',
            'fab fa-facebook-square'                  	=> 'f082',
            'fa fa-camera-retro'                        => 'f083',
            'fa fa-key'                                 => 'f084',
            'fa fa-cogs'                                => 'f085',
            'fa fa-comments'                            => 'f086',
            'far fa-thumbs-up'                         	=> 'f087',
            'far fa-thumbs-down'                       	=> 'f088',
            'fa fa-star-half'                           => 'f089',
            'far fa-heart'                             	=> 'f08a',
            'fas fa-sign-out-alt'                     	=> 'f08b',
            'fab fa-linkedin'                     		=> 'f08c',
            'fas fa-thumbtack'                          => 'f08d',
            'fas fa-external-link-alt'                	=> 'f08e',
            'fas fa-sign-in-alt'                      	=> 'f090',
            'fa fa-trophy'                              => 'f091',
            'fab fa-github-square'                      => 'f092',
            'fa fa-upload'                              => 'f093',
            'far fa-lemon'                             	=> 'f094',
            'fas fa-phone-alt'                         	=> 'f095',
            'far fa-square'                            	=> 'f096',
            'far fa-bookmark'                          	=> 'f097',
            'fas fa-phone-square-alt'                 	=> 'f098',
            'fab fa-twitter'                           	=> 'f099',
            'fab fa-facebook'                         	=> 'f09a',
            'fab fa-github'                           	=> 'f09b',
            'fa fa-unlock'                              => 'f09c',
            'fa fa-credit-card'                         => 'f09d',
            'fa fa-rss'                                 => 'f09e',
            'far fa-hdd'                               	=> 'f0a0',
            'fa fa-bullhorn'                            => 'f0a1',
            'fa fa-bell'                                => 'f0f3',
            'fa fa-certificate'                         => 'f0a3',
            'far fa-hand-point-right'                 	=> 'f0a4',
            'far fa-hand-point-left'                  	=> 'f0a5',
            'far fa-hand-point-up'                    	=> 'f0a6',
            'far fa-hand-point-down'                  	=> 'f0a7',
            'fa fa-arrow-circle-left'                   => 'f0a8',
            'fa fa-arrow-circle-right'                  => 'f0a9',
            'fa fa-arrow-circle-up'                     => 'f0aa',
            'fa fa-arrow-circle-down'                   => 'f0ab',
            'fa fa-globe'                               => 'f0ac',
            'fa fa-wrench'                              => 'f0ad',
            'fa fa-tasks'                               => 'f0ae',
            'fa fa-filter'                              => 'f0b0',
            'fa fa-briefcase'                           => 'f0b1',
            'fa fa-arrows-alt'                          => 'f0b2',
            'fa fa-users'                               => 'f0c0',
            'fa fa-link'                                => 'f0c1',
            'fa fa-cloud'                               => 'f0c2',
            'fa fa-flask'                               => 'f0c3',
            'fas fa-cut'                            	=> 'f0c4',
            'far fa-copy'                             	=> 'f0c5',
            'fa fa-paperclip'                           => 'f0c6',
            'far fa-save'                            	=> 'f0c7',
            'fa fa-square'                              => 'f0c8',
            'fa fa-bars'                                => 'f0c9',
            'fa fa-list-ul'                             => 'f0ca',
            'fa fa-list-ol'                             => 'f0cb',
            'fa fa-strikethrough'                       => 'f0cc',
            'fa fa-underline'                           => 'f0cd',
            'fa fa-table'                               => 'f0ce',
            'fa fa-magic'                               => 'f0d0',
            'fa fa-truck'                               => 'f0d1',
            'fab fa-pinterest'                        	=> 'f0d2',
            'fab fa-pinterest-square'                   => 'f0d3',
            'fab fa-google-plus-square'               	=> 'f0d4',
            'fab fa-google-plus-g'                    	=> 'f0d5',
            'far fa-money-bill-alt'                   	=> 'f0d6',
            'fa fa-caret-down'                          => 'f0d7',
            'fa fa-caret-up'                            => 'f0d8',
            'fa fa-caret-left'                          => 'f0d9',
            'fa fa-caret-right'                         => 'f0da',
            'fa fa-columns'                             => 'f0db',
            'fa fa-sort'                                => 'f0dc',
            'fas fa-sort-down'                        	=> 'f0dd',
            'fas fa-sort-up'                            => 'f0de',
            'fa fa-envelope'                            => 'f0e0',
            'fab fa-linkedin-in'                      	=> 'f0e1',
            'fa fa-undo'                                => 'f0e2',
            'fa fa-gavel'                               => 'f0e3',
            'fas fa-tachometer-alt'                   	=> 'f0e4',
            'far fa-comment'                           	=> 'f0e5',
            'far fa-comments'                        	=> 'f0e6',
            'fa fa-bolt'                                => 'f0e7',
            'fa fa-sitemap'                             => 'f0e8',
            'fa fa-umbrella'                            => 'f0e9',
            'fa fa-clipboard'                           => 'f0ea',
            'far fa-lightbulb'                         	=> 'f0eb',
            'fas fa-exchange-alt'                     	=> 'f0ec',
            'fas fa-cloud-download-alt'               	=> 'f0ed',
            'fas fa-cloud-upload-alt'                 	=> 'f0ee',
            'fa fa-user-md'                             => 'f0f0',
            'fa fa-stethoscope'                         => 'f0f1',
            'fa fa-suitcase'                            => 'f0f2',
            'far fa-bell'                              	=> 'f0a2',
            'fa fa-coffee'                              => 'f0f4',
            'fas fa-utensils'                          	=> 'f0f5',
            'far fa-file-alt'                         	=> 'f0f6',
            'far fa-building'                          	=> 'f0f7',
            'far fa-hospital'                          	=> 'f0f8',
            'fa fa-ambulance'                           => 'f0f9',
            'fa fa-medkit'                              => 'f0fa',
            'fa fa-fighter-jet'                         => 'f0fb',
            'fa fa-beer'                                => 'f0fc',
            'fa fa-h-square'                            => 'f0fd',
            'fa fa-plus-square'                         => 'f0fe',
            'fa fa-angle-double-left'                   => 'f100',
            'fa fa-angle-double-right'                  => 'f101',
            'fa fa-angle-double-up'                     => 'f102',
            'fa fa-angle-double-down'                   => 'f103',
            'fa fa-angle-left'                          => 'f104',
            'fa fa-angle-right'                         => 'f105',
            'fa fa-angle-up'                            => 'f106',
            'fa fa-angle-down'                          => 'f107',
            'fa fa-desktop'                             => 'f108',
            'fa fa-laptop'                              => 'f109',
            'fa fa-tablet'                              => 'f10a',
            'fa fa-mobile'                              => 'f10b',
            'far fa-circle'                            	=> 'f10c',
            'fa fa-quote-left'                          => 'f10d',
            'fa fa-quote-right'                         => 'f10e',
            'fa fa-spinner'                             => 'f110',
            'fa fa-circle'                              => 'f111',
            'fa fa-reply'                               => 'f112',
            'fab fa-github-alt'                       	=> 'f113',
            'far fa-folder'                            	=> 'f114',
            'far fa-folder-open'                       	=> 'f115',
            'far fa-smile'                             	=> 'f118',
            'far fa-frown'                             	=> 'f119',
            'far fa-meh'                               	=> 'f11a',
            'fa fa-gamepad'                             => 'f11b',
            'far fa-keyboard'                          	=> 'f11c',
            'far fa-flag'                              	=> 'f11d',
            'fa fa-flag-checkered'                      => 'f11e',
            'fa fa-terminal'                            => 'f120',
            'fa fa-code'                                => 'f121',
            'fa fa-reply-all'                           => 'f122',
            'fas fa-star-half-alt'                    	=> 'f123',
            'fa fa-location-arrow'                      => 'f124',
            'fa fa-crop'                                => 'f125',
            'fas fa-code-branch'                     	=> 'f126',
            'fas fa-unlink'                        		=> 'f127',
            'fa fa-question'                            => 'f128',
            'fa fa-info'                                => 'f129',
            'fa fa-exclamation'                         => 'f12a',
            'fa fa-superscript'                         => 'f12b',
            'fa fa-subscript'                           => 'f12c',
            'fa fa-eraser'                              => 'f12d',
            'fa fa-puzzle-piece'                        => 'f12e',
            'fa fa-microphone'                          => 'f130',
            'fa fa-microphone-slash'                    => 'f131',
            'fas fa-shield-alt'                       	=> 'f132',
            'far fa-calendar'                          	=> 'f133',
            'fa fa-fire-extinguisher'                   => 'f134',
            'fa fa-rocket'                              => 'f135',
            'fab fa-maxcdn'                           	=> 'f136',
            'fa fa-chevron-circle-left'                 => 'f137',
            'fa fa-chevron-circle-right'                => 'f138',
            'fa fa-chevron-circle-up'                   => 'f139',
            'fa fa-chevron-circle-down'                 => 'f13a',
            'fab fa-html5'                            	=> 'f13b',
            'fab fa-css3'                             	=> 'f13c',
            'fa fa-anchor'                              => 'f13d',
            'fa fa-unlock-alt'                          => 'f13e',
            'fa fa-bullseye'                            => 'f140',
            'fa fa-ellipsis-h'                          => 'f141',
            'fa fa-ellipsis-v'                          => 'f142',
            'fa fa-rss-square'                          => 'f143',
            'fa fa-play-circle'                         => 'f144',
            'fas fa-ticket-alt'                       	=> 'f145',
            'fa fa-minus-square'                        => 'f146',
            'far fa-minus-square'                      	=> 'f147',
            'fas fa-level-up-alt'                     	=> 'f148',
            'fas fa-level-down-alt'                   	=> 'f149',
            'fa fa-check-square'                        => 'f14a',
            'fas fa-pen-square'                       	=> 'f14b',
            'fas fa-external-link-square-alt'        	=> 'f14c',
            'fa fa-share-square'                        => 'f14d',
            'fa fa-compass'                             => 'f14e',
            'fas fa-caret-square-down'                 	=> 'f150',
            'fas fa-caret-square-up'                   	=> 'f151',
            'fas fa-caret-square-right'                	=> 'f152',
            'fas fa-euro-sign'                         	=> 'f153',
            'fas fa-pound-sign'                       	=> 'f154',
            'fas fa-dollar-sign'                      	=> 'f155',
            'fas fa-rupee-sign'                        	=> 'f156',
            'fas fa-yen-sign'                           => 'f157',
            'fas fa-ruble-sign'                        	=> 'f158',
            'fas fa-won-sign'                           => 'f159',
            'fab fa-btc'                           		=> 'f15a',
            'fa fa-file'                                => 'f15b',
            'fas fa-file-alt'                           => 'f15c',
            'fas fa-sort-alpha-down'                  	=> 'f15d',
            'fas fa-sort-alpha-down-alt'              	=> 'f15e',
            'fas fa-sort-amount-down-alt'             	=> 'f160',
            'fas fa-sort-amount-down'                 	=> 'f161',
            'fas fa-sort-numeric-down'                	=> 'f162',
            'fas fa-sort-numeric-down-alt'             	=> 'f163',
            'fa fa-thumbs-up'                           => 'f164',
            'fa fa-thumbs-down'                         => 'f165',
            'fab fa-youtube-square'                     => 'f166',
            'fab fa-youtube'                           	=> 'f167',
            'fab fa-xing'                               => 'f168',
            'fab fa-xing-square'                        => 'f169',
            'fab fa-apple-pay'                       	=> 'f16a',
            'fab fa-dropbox'                           	=> 'f16b',
            'fab fa-stack-overflow'                     => 'f16c',
            'fab fa-instagram'                         	=> 'f16d',
            'fab fa-flickr'                            	=> 'f16e',
            'fab fa-adn'                               	=> 'f170',
            'fab fa-bitbucket'                         	=> 'f171',
            'fas fa-place-of-worship'                  	=> 'f172',
            'fab fa-tumblr'                            	=> 'f173',
            'fab fa-tumblr-square'                     	=> 'f174',
            'fas fa-long-arrow-alt-down'              	=> 'f175',
            'fas fa-long-arrow-alt-up'               	=> 'f176',
            'fas fa-long-arrow-alt-left'              	=> 'f177',
            'fas fa-long-arrow-alt-right'             	=> 'f178',
            'fab fa-apple'                              => 'f179',
            'fab fa-windows'                            => 'f17a',
            'fab fa-android'                            => 'f17b',
            'fab fa-linux'                              => 'f17c',
            'fab fa-dribbble'                          	=> 'f17d',
            'fab fa-skype'                             	=> 'f17e',
            'fab fa-foursquare'                        	=> 'f180',
            'fab fa-trello'                            	=> 'f181',
            'fa fa-female'                              => 'f182',
            'fa fa-male'                                => 'f183',
            'fab fa-gratipay'                          	=> 'f184',
            'far fa-sun'                               	=> 'f185',
            'far fa-moon'                              	=> 'f186',
            'fa fa-archive'                             => 'f187',
            'fa fa-bug'                                 => 'f188',
            'fab fa-vk'                                 => 'f189',
            'fab fa-weibo'                              => 'f18a',
            'fab fa-renren'                            	=> 'f18b',
            'fab fa-pagelines'                         	=> 'f18c',
            'fab fa-stack-exchange'                    	=> 'f18d',
            'far fa-arrow-alt-circle-right'           	=> 'f18e',
            'far fa-arrow-alt-circle-left'             	=> 'f190',
            'far fa-caret-square-left'                 	=> 'f191',
            'far fa-dot-circle'                        	=> 'f192',
            'fa fa-wheelchair'                          => 'f193',
            'fab fa-vimeo-square'                      	=> 'f194',
            'fas fa-lira-sign'                    		=> 'f195',
            'far fa-plus-square'                       	=> 'f196',
            'fa fa-space-shuttle'                       => 'f197',
            'fab fa-slack'                             	=> 'f198',
            'fa fa-envelope-square'                     => 'f199',
            'fab fa-wordpress'                         	=> 'f19a',
            'fab fa-openid'                            	=> 'f19b',
            'fa fa-university'                          => 'f19c',
            'fa fa-graduation-cap'                      => 'f19d',
            'fab fa-yahoo'                             	=> 'f19e',
            'fab fa-google'                            	=> 'f1a0',
            'fab fa-reddit'                            	=> 'f1a1',
            'fab fa-reddit-square'                     	=> 'f1a2',
            'fab fa-stumbleupon-circle'                	=> 'f1a3',
            'fab fa-stumbleupon'                       	=> 'f1a4',
            'fab fa-delicious'                         	=> 'f1a5',
            'fab fa-digg'                              	=> 'f1a6',
            'fab fa-pied-piper-pp'                     	=> 'f1a7',
            'fab fa-pied-piper-alt'                    	=> 'f1a8',
            'fab fa-drupal'                            	=> 'f1a9',
            'fab fa-joomla'                            	=> 'f1aa',
            'fa fa-language'                            => 'f1ab',
            'fa fa-fax'                                 => 'f1ac',
            'fa fa-building'                            => 'f1ad',
            'fa fa-child'                               => 'f1ae',
            'fa fa-paw'                                 => 'f1b0',
            'fas fa-utensil-spoon'                     	=> 'f1b1',
            'fa fa-cube'                                => 'f1b2',
            'fa fa-cubes'                               => 'f1b3',
            'fab fa-behance'                            => 'f1b4',
            'fab fa-behance-square'                     => 'f1b5',
            'fab fa-steam'                              => 'f1b6',
            'fab fa-steam-square'                      	=> 'f1b7',
            'fa fa-recycle'                             => 'f1b8',
            'fa fa-car'                                 => 'f1b9',
            'fa fa-taxi'                                => 'f1ba',
            'fa fa-tree'                                => 'f1bb',
            'fab fa-spotify'                            => 'f1bc',
            'fab fa-deviantart'                         => 'f1bd',
            'fab fa-soundcloud'                        	=> 'f1be',
            'fa fa-database'                            => 'f1c0',
            'far fa-file-pdf'                          	=> 'f1c1',
            'far fa-file-word'                         	=> 'f1c2',
            'far fa-file-excel'                        	=> 'f1c3',
            'far fa-file-powerpoint'                   	=> 'f1c4',
            'far fa-file-image'                        	=> 'f1c5',
            'far fa-file-archive'                      	=> 'f1c6',
            'far fa-file-audio'                        	=> 'f1c7',
            'far fa-file-video'                        	=> 'f1c8',
            'far fa-file-code'                         	=> 'f1c9',
            'fab fa-vine'                               => 'f1ca',
            'fab fa-codepen'                            => 'f1cb',
            'fab fa-jsfiddle'                           => 'f1cc',
            'fa fa-life-ring'                           => 'f1cd',
            'fas fa-circle-notch'                      	=> 'f1ce',
            'fab fa-rebel'                              => 'f1d0',
            'fab fa-empire'                             => 'f1d1',
            'fab fa-git-square'                         => 'f1d2',
            'fab fa-git'                                => 'f1d3',
            'fab fa-hacker-news'                        => 'f1d4',
            'fab fa-tencent-weibo'                      => 'f1d5',
            'fab fa-qq'                                 => 'f1d6',
            'fab fa-weixin'                            	=> 'f1d7',
            'fa fa-paper-plane'                         => 'f1d8',
            'far fa-paper-plane'                       	=> 'f1d9',
            'fa fa-history'                             => 'f1da',
            'fas fa-icicles'                         	=> 'f1db',
            'fas fa-heading'                           	=> 'f1dc',
            'fa fa-paragraph'                           => 'f1dd',
            'fas fa-sliders-h'                         	=> 'f1de',
            'fa fa-share-alt'                           => 'f1e0',
            'fa fa-share-alt-square'                    => 'f1e1',
            'fa fa-bomb'                                => 'f1e2',
            'fas fa-futbol'                            	=> 'f1e3',
            'fa fa-tty'                                 => 'f1e4',
            'fa fa-binoculars'                          => 'f1e5',
            'fa fa-plug'                                => 'f1e6',
            'fab fa-slideshare'                        	=> 'f1e7',
            'fab fa-twitch'                            	=> 'f1e8',
            'fab fa-yelp'                              	=> 'f1e9',
            'far fa-newspaper'                         	=> 'f1ea',
            'fas fa-wifi'                               => 'f1eb',
            'fas fa-calculator'                         => 'f1ec',
            'fab fa-paypal'                             => 'f1ed',
            'fab fa-google-wallet'                      => 'f1ee',
            'fab fa-cc-visa'                            => 'f1f0',
            'fab fa-cc-mastercard'                      => 'f1f1',
            'fab fa-cc-discover'                        => 'f1f2',
            'fab fa-cc-amex'                            => 'f1f3',
            'fab fa-cc-paypal'                          => 'f1f4',
            'fab fa-cc-stripe'                         	=> 'f1f5',
            'fa fa-bell-slash'                          => 'f1f6',
            'far fa-bell-slash'                        	=> 'f1f7',
            'fa fa-trash'                               => 'f1f8',
            'fa fa-copyright'                           => 'f1f9',
            'fa fa-at'                                  => 'f1fa',
            'fas fa-eye-dropper'                       	=> 'f1fb',
            'fa fa-paint-brush'                         => 'f1fc',
            'fa fa-birthday-cake'                       => 'f1fd',
            'fas fa-chart-area'                         => 'f1fe',
            'fas fa-chart-pie'                         	=> 'f200',
            'fas fa-chart-line'                        	=> 'f201',
            'fab fa-lastfm'                            	=> 'f202',
            'fab fa-lastfm-square'                     	=> 'f203',
            'fa fa-toggle-off'                          => 'f204',
            'fa fa-toggle-on'                           => 'f205',
            'fa fa-bicycle'                             => 'f206',
            'fa fa-bus'                                 => 'f207',
            'fab fa-ioxhost'                          	=> 'f208',
            'fab fa-angellist'                       	=> 'f209',
            'far fa-closed-captioning'             		=> 'f20a',
            'fas fa-shekel-sign'                   		=> 'f20b',
            'fas fa-drumstick-bite'                   	=> 'f20c',
            'fab fa-buysellads'                         => 'f20d',
            'fab fa-connectdevelop'                     => 'f20e',
            'fab fa-dashcube'                           => 'f210',
            'fab fa-forumbee'                           => 'f211',
            'fab fa-leanpub'                            => 'f212',
            'fab fa-sellsy'                             => 'f213',
            'fab fa-shirtsinbulk'                       => 'f214',
            'fab fa-simplybuilt'                        => 'f215',
            'fab fa-skyatlas'                          	=> 'f216',
            'fa fa-cart-plus'                           => 'f217',
            'fa fa-cart-arrow-down'                     => 'f218',
            'far fa-gem'                             	=> 'f219',
            'fa fa-ship'                                => 'f21a',
            'fa fa-user-secret'                         => 'f21b',
            'fa fa-motorcycle'                          => 'f21c',
            'fa fa-street-view'                         => 'f21d',
            'fa fa-heartbeat'                           => 'f21e',
            'fa fa-venus'                               => 'f221',
            'fa fa-mars'                                => 'f222',
            'fa fa-mercury'                             => 'f223',
            'fa fa-transgender'                         => 'f224',
            'fa fa-transgender-alt'                     => 'f225',
            'fa fa-venus-double'                        => 'f226',
            'fa fa-mars-double'                         => 'f227',
            'fa fa-venus-mars'                          => 'f228',
            'fa fa-mars-stroke'                         => 'f229',
            'fa fa-mars-stroke-v'                       => 'f22a',
            'fa fa-mars-stroke-h'                       => 'f22b',
            'fa fa-neuter'                              => 'f22c',
            'fa fa-genderless'                          => 'f22d',
            'fab fa-facebook-f'                   		=> 'f230',
            'fab fa-pinterest-p'                        => 'f231',
            'fab fa-whatsapp'                           => 'f232',
            'fa fa-server'                              => 'f233',
            'fa fa-user-plus'                           => 'f234',
            'fa fa-user-times'                          => 'f235',
            'fa fa-bed'                                 => 'f236',
            'fab fa-viacoin'                            => 'f237',
            'fa fa-train'                               => 'f238',
            'fa fa-subway'                              => 'f239',
            'fab fa-medium'                             => 'f23a',
            'fab fa-y-combinator'                       => 'f23b',
            'fab fa-optin-monster'                      => 'f23c',
            'fab fa-opencart'                           => 'f23d',
            'fab fa-expeditedssl'                       => 'f23e',
            'fa fa-battery-full'                        => 'f240',
            'fa fa-battery-three-quarters'              => 'f241',
            'fa fa-battery-half'                        => 'f242',
            'fa fa-battery-quarter'                     => 'f243',
            'fa fa-battery-empty'                       => 'f244',
            'fa fa-mouse-pointer'                       => 'f245',
            'fa fa-i-cursor'                            => 'f246',
            'fa fa-object-group'                        => 'f247',
            'fa fa-object-ungroup'                      => 'f248',
            'fa fa-sticky-note'                         => 'f249',
            'far fa-sticky-note'                       	=> 'f24a',
            'fab fa-cc-jcb'                             => 'f24b',
            'fab fa-cc-diners-club'                     => 'f24c',
            'fa fa-clone'                               => 'f24d',
            'fa fa-balance-scale'                       => 'f24e',
            'far fa-hourglass'                         	=> 'f250',
            'fa fa-hourglass-start'                     => 'f251',
            'fa fa-hourglass-half'                      => 'f252',
            'fa fa-hourglass-end'                       => 'f253',
            'fa fa-hourglass'                           => 'f254',
            'far fa-hand-rock'                         	=> 'f255',
            'far fa-hand-paper'                        	=> 'f256',
            'far fa-hand-scissors'                     	=> 'f257',
            'far fa-hand-lizard'                       	=> 'f258',
            'far fa-hand-spock'                        	=> 'f259',
            'far fa-hand-pointer'                      	=> 'f25a',
            'far fa-hand-peace'                        	=> 'f25b',
            'fas fa-trademark'                          => 'f25c',
            'fas fa-registered'                         => 'f25d',
            'fab fa-creative-commons'                   => 'f25e',
            'fab fa-gg'                                 => 'f260',
            'fab fa-gg-circle'                          => 'f261',
            'fab fa-tripadvisor'                        => 'f262',
            'fab fa-odnoklassniki'                      => 'f263',
            'fab fa-odnoklassniki-square'               => 'f264',
            'fab fa-get-pocket'                         => 'f265',
            'fab fa-wikipedia-w'                        => 'f266',
            'fab fa-safari'                             => 'f267',
            'fab fa-chrome'                             => 'f268',
            'fab fa-firefox'                            => 'f269',
            'fab fa-opera'                              => 'f26a',
            'fab fa-internet-explorer'                  => 'f26b',
            'fas fa-tv'                          		=> 'f26c',
            'fab fa-contao'                            	=> 'f26d',
            'fab fa-500px'                             	=> 'f26e',
            'fab fa-amazon'                           	=> 'f270',
            'far fa-calendar-plus'                     	=> 'f271',
            'far fa-calendar-minus'                    	=> 'f272',
            'far fa-calendar-times'                    	=> 'f273',
            'far fa-calendar-check'                    	=> 'f274',
            'fa fa-industry'                            => 'f275',
            'fa fa-map-pin'                             => 'f276',
            'fa fa-map-signs'                           => 'f277',
            'far fa-map'                               	=> 'f278',
            'fa fa-map'                                 => 'f279',
            'fas fa-comment-dots'                     	=> 'f27a',
            'far fa-comment-dots'                      	=> 'f27b',
            'fab fa-houzz'                              => 'f27c',
            'fab fa-vimeo'                              => 'f27d',
            'fab fa-black-tie'                          => 'f27e',
            'fab fa-fonticons'                          => 'f280',
            'fab fa-reddit-alien'                       => 'f281',
            'fab fa-edge'                               => 'f282',
            'fas fa-credit-card'                    	=> 'f283',
            'fab fa-codiepie'                           => 'f284',
            'fab fa-modx'                               => 'f285',
            'fab fa-fort-awesome'                       => 'f286',
            'fab fa-usb'                                => 'f287',
            'fab fa-product-hunt'                       => 'f288',
            'fab fa-mixcloud'                           => 'f289',
            'fab fa-scribd'                             => 'f28a',
            'fa fa-pause-circle'                        => 'f28b',
            'far fa-pause-circle'                      	=> 'f28c',
            'fa fa-stop-circle'                         => 'f28d',
            'far fa-stop-circle'                       	=> 'f28e',
            'fa fa-shopping-bag'                        => 'f290',
            'fa fa-shopping-basket'                     => 'f291',
            'fa fa-hashtag'                             => 'f292',
            'fab fa-bluetooth'                         	=> 'f293',
            'fab fa-bluetooth-b'                       	=> 'f294',
            'fa fa-percent'                             => 'f295',
            'fab fa-gitlab'                             => 'f296',
            'fab fa-wpbeginner'                         => 'f297',
            'fab fa-wpforms'                           	=> 'f298',
            'fab fa-envira'                            	=> 'f299',
            'fa fa-universal-access'                    => 'f29a',
            'fab fa-accessible-icon'                   	=> 'f29b',
            'far fa-question-circle'                  	=> 'f29c',
            'fa fa-blind'                               => 'f29d',
            'fa fa-audio-description'                   => 'f29e',
            'fas fa-phone-volume'                		=> 'f2a0',
            'fa fa-braille'                             => 'f2a1',
            'fa fa-assistive-listening-systems'         => 'f2a2',
            'fa fa-american-sign-language-interpreting' => 'f2a3',
            'fa fa-deaf'                                => 'f2a4',
            'fab fa-glide'                              => 'f2a5',
            'fab fa-glide-g'                            => 'f2a6',
            'fas fa-sign-language'                      => 'f2a7',
            'fas fa-low-vision'                         => 'f2a8',
            'fab fa-viadeo'                             => 'f2a9',
            'fab fa-viadeo-square'                      => 'f2aa',
            'fab fa-snapchat'                           => 'f2ab',
            'fab fa-snapchat-ghost'                     => 'f2ac',
            'fab fa-snapchat-square'                    => 'f2ad',
            'fab fa-pied-piper'                         => 'f2ae',
            'fab fa-first-order'                        => 'f2b0',
            'fab fa-yoast'                              => 'f2b1',
            'fab fa-themeisle'                         	=> 'f2b2',
            'fab fa-google-plus'                		=> 'f2b3',
            'fab fa-font-awesome'                       => 'f2b4',
			'fas fa-khanda'								=> 'f66d'
        );
    }

    public static function get_map_styles()
    {
        $mapstyles = array(
            '' => esc_html__('Default', 'listdom'),
            'apple-maps-esque' => esc_html__('Apple Maps Esque', 'listdom'),
            'blue-essence' => esc_html__('Blue Essence', 'listdom'),
            'blue-water' => esc_html__('Blue Water', 'listdom'),
            'CDO' => esc_html__('CDO', 'listdom'),
            'facebook' => esc_html__('Facebook', 'listdom'),
            'intown-map' => esc_html__('Intown Map', 'listdom'),
            'light-dream' => esc_html__('Light Dream', 'listdom'),
            'midnight' => esc_html__('Midnight', 'listdom'),
            'pale-down' => esc_html__('Pale Down', 'listdom'),
            'shades-of-grey' => esc_html__('Shades of Grey', 'listdom'),
            'subtle-grayscale' => esc_html__('Subtle Grayscale', 'listdom'),
            'ultra-light' => esc_html__('Ultra Light', 'listdom'),
        );

        // Apply Filters
        return apply_filters('lsd_mapstyles', $mapstyles);
    }

    public static function get_clustering_icons()
    {
        $icons = array(
            'img/cluster1/m' => esc_html__('Classic Bubbles', 'listdom'),
            'img/cluster2/m' => esc_html__('Modern Bubbles', 'listdom')
        );

        // Apply Filters
        return apply_filters('lsd_clustering_icons', $icons);
    }

    public static function get_fonts()
    {
        return array(
            'lato' => array('label' => esc_html__('Lato', 'listdom'), 'code' => 'Lato', 'family' => 'Lato'),
            'roboto' => array('label' => esc_html__('Roboto', 'listdom'), 'code' => 'Roboto', 'family' => 'Roboto'),
            'raleway' => array('label' => esc_html__('Raleway', 'listdom'), 'code' => 'Raleway', 'family' => 'Raleway'),
            'open-sans' => array('label' => esc_html__('Open Sans', 'listdom'), 'code' => 'Open Sans', 'family' => 'Open Sans'),
            'poppins' => array('label' => esc_html__('Poppins', 'listdom'), 'code' => 'Poppins', 'family' => 'Poppins'),
        );
    }

    public static function get_colors()
    {
        return array(
            '#f2cc0c',
            '#ffa600',
            '#f76e09',
            '#f43d3d',
            '#ee0218',
            '#b2093c',
            '#ff1991',
            '#d40fb7',
            '#11c35d',
            '#188437',
            '#009e96',
            '#2b93ff',
        );
    }

    public function response(Array $response)
    {
        echo json_encode($response, JSON_NUMERIC_CHECK);
        exit;
	}

    public static function log($message, $append = true)
    {
        $path = LSD_LOG_DIR.'debug.log';
        if(LSD_File::exists($path) and !$append) LSD_File::delete($path);

        if($append) LSD_File::append($path, $message."\n\n");
        else LSD_File::write($path, $message."\n\n");
    }
}

endif;