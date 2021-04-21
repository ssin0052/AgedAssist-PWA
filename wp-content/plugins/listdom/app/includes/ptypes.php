<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_PTypes')):

/**
 * Listdom Post Types Class.
 *
 * @class LSD_PTypes
 * @version	1.0.0
 */
class LSD_PTypes extends LSD_Base
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}
    
    public function init()
    {
        // Listings Post Type
        $Listings = new LSD_PTypes_Listing();
        $Listings->init();

        // Shortcode Post Type
        $Shortcode = new LSD_PTypes_Shortcode();
        $Shortcode->init();
        
        // Render No Item Screen
        add_action('manage_posts_extra_tablenav', array($this, 'create_first_item'));
    }
    
    public function create_first_item($which)
    {
        global $post_type;

        // It's not one of Listdom Post Types
		if(!in_array($post_type, $this->postTypes()) or 'bottom' !== $which) return;
        
        $counts = (array) wp_count_posts($post_type);
        
        unset($counts['auto-draft']);
        $count = array_sum($counts);
        
        // Item found
        if($count > 0) return;
        
        echo '<div class="lsd-blank-state">';

        switch($post_type)
        {
            case LSD_Base::PTYPE_LISTING:
                ?>
                <p class="lsd-blank-state-message"><?php esc_html_e('Ready to start? Create your first listing here.', 'listdom'); ?></p>
                <a class="button button-primary button-hero" href="<?php echo admin_url('post-new.php?post_type='.$post_type); ?>"><?php esc_html_e('Create your first listing', 'listdom'); ?></a>
                <?php
                break;
            
            case LSD_Base::PTYPE_SHORTCODE:
                ?>
                <p class="lsd-blank-state-message"><?php esc_html_e("You can create different shortcodes by selecting the map skin, style and filtering listings.", 'listdom'); ?></p>
                <a class="button button-primary button-hero" href="<?php echo admin_url('post-new.php?post_type='.$post_type); ?>"><?php esc_html_e('Create your first shortcode', 'listdom'); ?></a>
                <?php
                break;

            case LSD_Base::PTYPE_SEARCH:
                ?>
                <p class="lsd-blank-state-message"><?php esc_html_e("You can create various search form by inserting different fields, rows etc.!", 'listdom'); ?></p>
                <a class="button button-primary button-hero" href="<?php echo admin_url('post-new.php?post_type='.$post_type); ?>"><?php esc_html_e('Create your first search form', 'listdom'); ?></a>
                <?php
                break;

            case LSD_Base::PTYPE_NOTIFICATION:
                ?>
                <p class="lsd-blank-state-message"><?php esc_html_e("You can create various notifications to be sent in different hooks!", 'listdom'); ?></p>
                <a class="button button-primary button-hero" href="<?php echo admin_url('post-new.php?post_type='.$post_type); ?>"><?php esc_html_e('Create your first notification', 'listdom'); ?></a>
                <?php
                break;
        }

        echo '</div>';
    }
}

endif;