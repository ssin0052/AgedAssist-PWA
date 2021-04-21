<?php
// no direct access
defined('ABSPATH') or die();

/** @var LSD_Widgets_Search $this */

$search_id = isset($this->instance['search_id']) ? $this->instance['search_id'] : NULL;
if(!trim($search_id)) return;
?>
<div class="lsd-search-widget">
    <?php echo do_shortcode('[listdom-search id="'.esc_attr($search_id).'"]'); ?>
</div>