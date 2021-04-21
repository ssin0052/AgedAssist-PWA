<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_Element_Report')):

/**
 * Listdom Abuse Element Class.
 *
 * @class LSD_Element_Abuse
 * @version	1.0.0
 */
class LSD_Element_Abuse extends LSD_Element
{
    public $key = 'abuse';
    public $label;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
        // Call the parent constructor
        parent::__construct();

        $this->label = esc_html__('Report Abuse', 'listdom');
	}

    public function get($post_id = NULL)
    {
        if(is_null($post_id))
        {
            global $post;
            $post_id = $post->ID;
        }

        // Generate output
        ob_start();
        include lsd_template('elements/abuse.php');
        return ob_get_clean();
    }
}

endif;