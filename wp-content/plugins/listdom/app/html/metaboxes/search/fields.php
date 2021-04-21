<?php
// no direct access
defined('ABSPATH') or die();

$meta_fields = get_post_meta($post->ID, 'lsd_fields', true);
if(!is_array($meta_fields)) $meta_fields = array();

// Add a default row
if(!count($meta_fields)) $meta_fields[] = array('type'=>'row', 'buttons'=>1);

// Reset Keys
$meta_fields = array_values($meta_fields);

$builder = new LSD_Search_Builder();
$fields = $builder->getAvailableFields($meta_fields);

// Add JS codes to footer
$assets = new LSD_Assets();
$assets->footer('<script>
jQuery(document).ready(function()
{
    jQuery(".lsd-search-fields-metabox").listdomSearchBuilder(
    {
        ajax_url: "'.admin_url('admin-ajax.php', NULL).'"
    });
});
</script>');
?>
<div class="lsd-metabox lsd-search-fields-metabox">
    <div class="lsd-search-top-guide lsd-mt-4">
        <div class="lsd-alert lsd-info">
            <ul>
                <li><?php esc_html_e("You can create as many row as you like and put any amount of fields into each row.", 'listdom'); ?></li>
                <li><?php esc_html_e('Drag the fields from "Available Fields" section into rows.', 'listdom'); ?></li>
                <li><?php echo sprintf(esc_html__('To put some fields in %s section you can put a "More Options" row above of them.', 'listdom'), '<strong>'.esc_html__("More Options", 'listdom').'</strong>'); ?></li>
            </ul>
        </div>
    </div>
    <div class="lsd-search-top-buttons">
        <div class="lsd-row">
            <div class="lsd-col-12">
                <ul>
                    <li><button type="button" class="button" id="lsd_search_add_row"><?php esc_html_e('Add row', 'listdom'); ?></button></li>
                    <li><button type="button" class="button" id="lsd_search_more_options"><?php esc_html_e('More Options', 'listdom'); ?></button></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="lsd-search-container">
        <div class="lsd-row">
           <div class="lsd-col-9 lsd-search-sandbox">
                <?php foreach($meta_fields as $i=>$row): $i = $i + 1; ?>
                <div class="<?php echo ($row['type'] == 'more_options' ? 'lsd-search-more-options' : 'lsd-search-row'); ?>" id="lsd_search_row_<?php echo esc_attr($i); ?>" data-i="<?php echo esc_attr($i); ?>">
                    <input type="hidden" name="lsd[fields][<?php echo esc_attr($i); ?>][type]" value="<?php echo (isset($row['type']) and trim($row['type'])) ? $row['type'] : 'row'; ?>">

                    <div class="lsd-search-filters">
                        <?php if(isset($row['filters']) and is_array($row['filters'])) foreach($row['filters'] as $key=>$data) echo LSD_Kses::form($builder->params($key, $data, $i)); ?>
                    </div>

                    <ul class="lsd-search-row-actions">
                        <li class="lsd-search-row-actions-sort lsd-row-handler"><i class="lsd-icon fas fa-arrows-alt"></i></li>
                        <li class="lsd-search-row-actions-delete" data-confirm="0" data-i="<?php echo esc_attr($i); ?>"><i class="lsd-icon fas fa-trash-alt"></i></li>
                    </ul>
                    <?php if($row['type'] == 'row') echo LSD_Kses::form($builder->row($row, $i)); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="lsd-col-3 lsd-search-available-fields">
                <h3><?php esc_html_e('Available Fields', 'listdom'); ?></h3>
                <div id="lsd_search_available_fields">
                    <?php foreach($fields as $field): ?>
                    <div class="lsd-search-field" id="lsd_search_available_fields_<?php echo esc_attr($field['key']); ?>" data-key="<?php echo esc_attr($field['key']); ?>"><?php echo esc_html($field['title']); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>