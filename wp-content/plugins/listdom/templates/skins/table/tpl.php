<?php
// no direct access
defined('ABSPATH') or die();

/** @var LSD_Skins_Table $this */

// Get HTML of Listings
$listings_html = $this->listings_html();

// Add List Skin JS codes to footer
$assets = new LSD_Assets();
$assets->footer('<script>
jQuery(document).ready(function()
{
    jQuery("#lsd_skin'.$this->id.'").listdomTableSkin(
    {
        id: "'.$this->id.'",
        load_more: '.($this->load_more ? 'true' : 'false').',
        ajax_url: "'.admin_url('admin-ajax.php', NULL).'",
        atts: "'.http_build_query(array('atts'=>$this->atts), '', '&').'",
        next_page: "'.$this->next_page.'",
        limit: "'.$this->limit.'",
    });
});
</script>');
?>
<div class="lsd-table-view-wrapper <?php echo esc_attr($this->html_class); ?> lsd-style-<?php echo esc_attr($this->style); ?> lsd-font-m" id="lsd_skin<?php echo esc_attr($this->id); ?>" data-next-page="<?php echo esc_attr($this->next_page); ?>">

    <?php if($this->sm_shortcode and $this->sm_position == 'top') echo LSD_Kses::form($this->get_search_module()); ?>

    <?php echo LSD_Kses::form($this->get_sortbar()); ?>

    <?php if($this->sm_shortcode and $this->sm_position == 'before_listview') echo LSD_Kses::form($this->get_search_module()); ?>

    <div class="lsd-table-view-listings-wrapper">
        <div class="lsd-listing-wrapper">
			<table class="lsd-listing-table">
				<thead>
					<tr class="lsd-listing-head">
						<th>
							<?php esc_html_e('Title', 'listdom'); ?>
						</th>
						<th>
							<?php esc_html_e('Address', 'listdom'); ?> 
						</th>
						<th>
							<?php esc_html_e('Price', 'listdom'); ?> 
						</th>
						<th>
							<?php esc_html_e('Availability', 'listdom'); ?> 
						</th>
						<th>
							<?php esc_html_e('Contact', 'listdom'); ?> 
						</th>
					</tr>
				</thead>
				<tbody>
					<?php echo LSD_Kses::page($listings_html); ?>
				</tbody>
			</table>
        </div>
    </div>

    <?php echo LSD_Kses::element($this->get_loadmore_button()); ?>

</div>