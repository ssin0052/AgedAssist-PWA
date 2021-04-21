<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_Shortcodes_Category')):

/**
 * Listdom Listdom Category Shortcode Class.
 *
 * @class LSD_Shortcodes_Category
 * @version	1.0.0
 */
class LSD_Shortcodes_Category extends LSD_Shortcodes_Taxonomy
{
    // Taxonomy
    protected $TX = LSD_Base::TAX_CATEGORY;

    // Valid Styles
    protected $valid_styles = array('image', 'simple', 'clean', 'carousel');

    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public function init()
    {
        add_shortcode('listdom_category', array($this, 'output'));
	}
}

endif;