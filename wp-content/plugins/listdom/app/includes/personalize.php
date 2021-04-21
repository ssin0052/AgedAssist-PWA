<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_Personalize')):

/**
 * Listdom Personalize Class.
 *
 * @class LSD_Personalize
 * @version	1.0.0
 */
class LSD_Personalize extends LSD_Base
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public static function generate()
    {
        $settings = LSD_Options::settings();

        $main = new LSD_Main();
        $raw = LSD_File::read($main->get_listdom_path().'/assets/css/personalized.raw');

        $CSS = str_replace('((dply_main_color))', $settings['dply_main_color'], $raw);
        $CSS = str_replace('((dply_secondary_color))', $settings['dply_secondary_color'], $CSS);

        $fonts = $main->get_fonts();
        $font = isset($fonts[$settings['dply_main_font']]) ? $fonts[$settings['dply_main_font']] : array('family' => 'Lato');
        $CSS = str_replace('((dply_main_font))', $font['family'], $CSS);

        // Write the generated CSS file
        LSD_File::write($main->get_listdom_path().'/assets/css/personalized.css', $CSS);
    }

    public function assets()
    {
        $settings = LSD_Options::settings();

        $fonts = $this->get_fonts();
        $font = isset($fonts[$settings['dply_main_font']]) ? $fonts[$settings['dply_main_font']] : array('code' => 'Lato');

        // Include the Font
        wp_enqueue_style('google-font-'.sanitize_title($font['code']), 'https://fonts.googleapis.com/css?family='.urlencode($font['code']));

        // Include Listdom personalized CSS file
        wp_enqueue_style('lsd-personalized', $this->lsd_asset_url('css/personalized.css'), array('lsd-frontend'), LSD_VERSION);
    }
}

endif;