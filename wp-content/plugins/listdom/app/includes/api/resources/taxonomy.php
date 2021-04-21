<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_API_Resources_Taxonomy')):

/**
 * Listdom API Taxonomy Resource Class.
 *
 * @class LSD_API_Resources_Taxonomy
 * @version	1.0.0
 */
class LSD_API_Resources_Taxonomy extends LSD_API_Resource
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public static function get($id)
    {
        // Resource
        $resource = new LSD_API_Resource();

        // Term
        $term = get_term($id);

        // Meta Values
        $metas = $resource->get_term_meta($id);

        // Data
        $data = array(
            'id' => $id,
            'name' => $term->name,
            'description' => $term->description,
            'parent' => $term->parent,
            'count' => $term->count,
            'slug' => $term->slug,
        );

        // Colors
        if(isset($metas['lsd_color']))
        {
            $data['color'] = array(
                'bg' => $metas['lsd_color'],
                'text' => $resource->get_text_color($metas['lsd_color'])
            );
        }

        if(isset($metas['lsd_required'])) $data['required'] = $metas['lsd_required'];
        if(isset($metas['lsd_editor'])) $data['editor'] = $metas['lsd_editor'];
        if(isset($metas['lsd_icon'])) $data['icon'] = $metas['lsd_icon'];
        if(isset($metas['lsd_symbol'])) $data['symbol'] = LSD_API_Resources_Image::get($metas['lsd_symbol']);
        if(isset($metas['lsd_image'])) $data['image'] = LSD_API_Resources_Image::get($metas['lsd_image']);
        if(isset($metas['lsd_schema'])) $data['schema'] = $metas['lsd_schema'];

        if(isset($metas['lsd_field_type'])) $data['field_type'] = $metas['lsd_field_type'];
        if(isset($metas['lsd_index'])) $data['index'] = $metas['lsd_index'];
        if(isset($metas['lsd_all_categories'])) $data['all_categories'] = $metas['lsd_all_categories'];
        if(isset($metas['lsd_categories'])) $data['categories'] = $metas['lsd_categories'];
        if(isset($metas['lsd_values']))
        {
            $values = explode(',', trim($metas['lsd_values'], ', '));

            $rendered = array();
            foreach($values as $value)
            {
                $rendered[] = array(
                    'key' => $value,
                    'label' => $value,
                );
            }

            $data['values'] = $rendered;
        }

        return apply_filters('lsd_api_resource_taxonomy', $data, $id);
    }

    public static function collection($terms)
    {
        $items = array();

        $i = 0;
        foreach($terms as $term)
        {
            $items[$i] = self::get($term['id']);
            if(isset($term['childs'])) $items[$i]['childs'] = self::collection($term['childs']);

            $i++;
        }

        return $items;
    }

    public static function minify($id)
    {
        // Resource
        $resource = new LSD_API_Resource();

        // Term
        $term = get_term($id);

        // Meta Values
        $metas = $resource->get_term_meta($id);

        // Data
        $data = array(
            'id' => $id,
            'name' => $term->name,
            'description' => $term->description,
        );

        // Colors
        if(isset($metas['lsd_color']))
        {
            $data['color'] = array(
                'bg' => $metas['lsd_color'],
                'text' => $resource->get_text_color($metas['lsd_color'])
            );
        }

        if(isset($metas['lsd_required'])) $data['required'] = $metas['lsd_required'];
        if(isset($metas['lsd_editor'])) $data['editor'] = $metas['lsd_editor'];
        if(isset($metas['lsd_icon'])) $data['icon'] = $metas['lsd_icon'];
        if(isset($metas['lsd_symbol'])) $data['symbol'] = LSD_API_Resources_Image::get($metas['lsd_symbol']);
        if(isset($metas['lsd_image'])) $data['image'] = LSD_API_Resources_Image::get($metas['lsd_image']);

        return apply_filters('lsd_api_resource_taxonomy', $data, $id);
    }

    public static function listing($id)
    {
        $taxonomies = array();
        foreach(array(
            LSD_Base::TAX_CATEGORY,
            LSD_Base::TAX_LABEL,
            LSD_Base::TAX_LOCATION,
            LSD_Base::TAX_FEATURE,
            LSD_Base::TAX_TAG,
        ) as $taxonomy)
        {
            $terms = get_the_terms($id, $taxonomy);
            if($terms and !is_wp_error($terms))
            {
                $t = array();
                foreach($terms as $term) $t[] = self::minify($term->term_id);

                $taxonomies[$taxonomy] = $t;
            }
        }

        return $taxonomies;
    }
}

endif;