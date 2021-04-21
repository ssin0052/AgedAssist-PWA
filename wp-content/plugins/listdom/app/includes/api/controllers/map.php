<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_API_Controllers_Map')):

/**
 * Listdom API Map Controller Class.
 *
 * @class LSD_API_Controllers_Map
 * @version	1.0.0
 */
class LSD_API_Controllers_Map extends LSD_API_Controller
{
    protected $id;
    protected $settings;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public function map(WP_REST_Request $request)
    {
        $id = $request->get_param('id');

        // Listing
        $listing = get_post($id);

        // Not Found!
        if(!$listing or ($listing and isset($listing->post_type) and $listing->post_type !== LSD_Base::PTYPE_LISTING)) return $this->response(array(
            'data' => new WP_Error('404', esc_html__('Listing not found!', 'listdom')),
            'status' => 404,
        ));

        // Details Page options
        $details_page_options = LSD_Options::details_page();

        $entity = new LSD_Entity_Listing($listing);
        $map = $entity->get_map(array
        (
            'provider'=>(isset($details_page_options['elements']['map']['map_provider']) ? $details_page_options['elements']['map']['map_provider'] : LSD_Map_Provider::def()),
            'style'=>(isset($details_page_options['elements']['map']['style']) ? $details_page_options['elements']['map']['style'] : NULL),
            'gplaces'=>(isset($details_page_options['elements']['map']['gplaces']) ? $details_page_options['elements']['map']['gplaces'] : 0),
            'mapcontrols'=>array
            (
                'zoom'=>(isset($details_page_options['elements']['map']['control_zoom']) ? $details_page_options['elements']['map']['control_zoom'] : 'RIGHT_BOTTOM'),
                'maptype'=>(isset($details_page_options['elements']['map']['control_maptype']) ? $details_page_options['elements']['map']['control_maptype'] : 'TOP_LEFT'),
                'streetview'=>(isset($details_page_options['elements']['map']['control_streetview']) ? $details_page_options['elements']['map']['control_streetview'] : 'RIGHT_BOTTOM'),
                'scale'=>0,
                'fullscreen'=>0,
            ),
            'args' => $details_page_options['elements']['map']
        ));

        // Include Map Assets
        LSD_Assets::map();

        // Response
        header('Content-Type: text/html');
        echo trim($this->iframe($map));
        exit;
    }

    public function upsert(WP_REST_Request $request)
    {
        $id = $request->get_param('id');

        // Listing
        $listing = get_post($id);

        // Not Found!
        if($id and (!$listing or ($listing and isset($listing->post_type) and $listing->post_type !== LSD_Base::PTYPE_LISTING))) return $this->response(array(
            'data' => new WP_Error('404', esc_html__('Listing not found!', 'listdom')),
            'status' => 404,
        ));

        // Include Map Assets
        LSD_Assets::map(true);

        // Include API Assets
        LSD_Assets::api();

        $this->id = $id;
        $this->settings = LSD_Options::settings();

        // Generate output
        ob_start();
        include lsd_template('maps/upsert.php');
        $map = ob_get_clean();

        // Response
        header('Content-Type: text/html');
        echo trim($this->iframe($map));
        exit;
    }

    public function permission(WP_REST_Request $request)
    {
        // Validate API Token
        if(!$this->validate->APIToken($request, $request->get_param('lsd-token'))) return new WP_Error('invalid_api_token', esc_html__('Invalid API Token!', 'listdom'));

        // Validate User Token
        if(!$this->validate->UserToken($request, $request->get_param('lsd-user'))) return new WP_Error('invalid_user_token', esc_html__('Invalid User Token!', 'listdom'));

        return true;
    }

    public function guest(WP_REST_Request $request)
    {
        // Validate API Token
        if(!$this->validate->APIToken($request, $request->get_param('lsd-token'))) return new WP_Error('invalid_api_token', esc_html__('Invalid API Token!', 'listdom'));

        // Set Current User if Token Provided
        $this->validate->UserToken($request, $request->get_param('lsd-user'));

        return true;
    }
}

endif;