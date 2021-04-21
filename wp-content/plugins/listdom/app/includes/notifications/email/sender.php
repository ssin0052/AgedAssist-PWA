<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('LSD_Notifications_Email_Sender')):

/**
 * Listdom Notifications Email Sender Class.
 *
 * @class LSD_Notifications_Email_Sender
 * @version	1.0.0
 */
class LSD_Notifications_Email_Sender extends LSD_Notifications
{
    public $to;
    public $cc;
    public $bcc;
    public $subject;
    public $content;
    public $id = 0;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public function boot($id)
    {
        $this->id = $id;
        return $this;
    }

    public function to($to)
    {
        $this->to = $this->toArray($to);
        return $this;
	}

    public function cc($cc)
    {
        $this->cc = $this->toArray($cc);
        return $this;
    }

    public function bcc($bcc)
    {
        $this->bcc = $this->toArray($bcc);
        return $this;
    }

    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function content($content)
    {
        // Auto Paragraph
        $content = wpautop($content);

        // Do Shortcodes
        $content = do_shortcode($content);

        $this->content = $content;
        return $this;
    }

    public function send()
    {
        // Set Email Type to HTML
        add_filter('wp_mail_content_type', array($this, 'html'));

        // Headers
        $headers = $this->headers();

        // Attachments
        $attachments = $this->attachments();

        // Send the mail
        $response = wp_mail($this->to, html_entity_decode(stripslashes($this->subject), ENT_HTML5), stripslashes($this->content), $headers, $attachments);

        // Remove the HTML Email filter
        remove_filter('wp_mail_content_type', array($this, 'html'));

        return $response;
	}

    public function html()
    {
        return 'text/html';
    }

    public function render($listing_id = NULL)
    {
        // Site Options
        $site_name = get_bloginfo('name');
        $site_url = get_home_url();
        $site_description = get_bloginfo('description');
        $admin_email = get_bloginfo('admin_email');

        // Date Options
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        $date = current_time($date_format);
        $time = current_time($time_format);
        $datetime = trim($date.' '.$time);

        $status = new stdClass();
        $publish_date = '';
        $listing_title = '';

        // Listing Option
        if($listing_id)
        {
            $listing = get_post($listing_id);
            $status = get_post_status_object($listing->post_status);
            $publish_date = get_the_date(NULL, $listing);
            $listing_title = get_the_title($listing);
        }

        foreach(array('subject', 'content') as $item)
        {
            // Website Placeholders
            $this->{$item} = str_replace('#site_name#', $site_name, $this->{$item});
            $this->{$item} = str_replace('#site_url#', $site_url, $this->{$item});
            $this->{$item} = str_replace('#site_description#', $site_description, $this->{$item});
            $this->{$item} = str_replace('#site_link#', '<a href="'.esc_url($site_url).'">'.esc_html($site_name).'</a>', $this->{$item});
            $this->{$item} = str_replace('#admin_email#', $admin_email, $this->{$item});

            // Date & Time Placeholders
            $this->{$item} = str_replace('#date#', $date, $this->{$item});
            $this->{$item} = str_replace('#time#', $time, $this->{$item});
            $this->{$item} = str_replace('#datetime#', $datetime, $this->{$item});

            // Listing Placeholders
            if($listing_id)
            {
                $this->{$item} = str_replace('#listing_title#', $listing_title, $this->{$item});
                $this->{$item} = str_replace('#listing_status#', $status->label, $this->{$item});
                $this->{$item} = str_replace('#listing_date#', $publish_date, $this->{$item});

                $this->{$item} = str_replace('#listing_link#', '<a href="'.get_permalink($listing_id).'">'.get_the_title($listing_id).'</a>', $this->{$item});
                $this->{$item} = str_replace('#admin_link#', '<a href="'.$this->get_edit_post_link($listing_id).'">'.get_the_title($listing_id).'</a>', $this->{$item});
            }
        }

        // Apply Filters
        return apply_filters('lsd_notification_after_render', $this, $listing_id);
    }

    private function headers()
    {
        $headers = array();

        // Content Type
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        // CC
        foreach($this->cc as $cc) if(is_email($cc)) $headers[] = 'Cc: '.$cc;

        // BCC
        foreach($this->bcc as $bcc) if(is_email($bcc)) $headers[] = 'Bcc: '.$bcc;

        // Apply Filters
        return apply_filters('lsd_email_headers', $headers);
    }

    private function attachments()
    {
        $attachments = array();

        // Apply Filters
        return apply_filters('lsd_email_attachments', $attachments);
    }

    private function toArray($string)
    {
        if(is_array($string)) return $string;
        else return explode(',', trim($string, ', '));
    }
}

endif;