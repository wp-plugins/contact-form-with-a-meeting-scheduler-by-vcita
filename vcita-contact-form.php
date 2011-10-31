<?php
/*
 * vCita Plugin Description
 *
 * Plugin Name: Contact Form with a Meeting Scheduler by vCita
 * Plugin URI: http://www.vcita.com
 * Description: Don't miss another visitor - an inviting Contact Form with built-in Appointment Scheduler and Video Meetings
 * Author: vCita.com
 * Version: 1.2.1
 * Author URI: http://www.vcita.com
*/


/* --- Static initializer for Wordpress hooks --- */

add_action('plugins_loaded', 'vcita_init');
add_shortcode('vCitaContact','vcita_add_contact');
add_action('admin_menu', 'vcita_admin_actions');
add_filter('plugin_action_links', 'add_settings_link', 10, 2 );

/* --- Wordpress Hooks Implementations --- */

/**
 *  Add the vCita widget to the "Settings" Side Menu
 */
function vcita_admin_actions() {
    if ( function_exists('add_options_page') ) {
        add_options_page("vcitaContact", "vCita Contact Form", 8, __FILE__, 'vcita_settings_menu');
    }
}

/**
 * Create the Main vCita Settings form content.
 *
 * The form is constructed from a list of input fields and a preview for the result
 */
function vcita_settings_menu() {

	extract(prepare_widget_settings($_POST['vcita_widget-type'], "settings"));

	// Check the dedicated page flag - If it is on, make sure a page is available, if not - Trash the page
	if ($update_made) {
		if ($_POST['Submit'] == "Disable Page") {
			trash_page($vcita_widget['page_id']);
		} else {
			$vcita_widget = make_sure_page_published($vcita_widget);
		}
	}

	if (is_page_available($vcita_widget)) {

		$page_action = "Disable Page";
		$page_status = "Contact page has been <span style='color:green;'><b>created</b></span>";
		$page_available = true;
	} else {
		$page_action = "Activate Page";
		$page_status = "Contact page is <span style='color:red;'><b>disabled</b></span>";
		$page_available = false;
	}


	if ($first_time) {
		$form_hidden = "";
	} else {
		$form_hidden = "display:none";
	}

	embed_toggle_preview_visibility();
    ?>
    <div class="wrap" style="max-width:830px;">
        <h2><img src="http://www.vcita.com/images/logo.png"></h2>
        <?php echo create_user_message($vcita_widget, $update_made); ?>

        <?php if ($first_time) { ?>
            <div><p>To Create your Contact Form please provide your details below:</p></div>
		<?php } ?>

        <div style="float:left;margin-right:20px;width:300px;margin-top:5px;">

        <form name="vcita_form" method="post" id="vcita_form_<?php echo $form_uid;?>" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="<?php echo $form_hidden; ?>">
			<div style="width:300px">
				<div style="display:block;clear:both;">
					<span style="display:inline-block;width:110px;"><?php _e("Email: " ); ?></span>
                    <input type="text" onkeypress="clearNames();" name="vcita_email" value="<?php echo $vcita_widget['email']; ?>" size="25">
                </div>

                <div style="display:block;clear:both;">
                    <span style="display:inline-block;width:110px;"><?php _e("First name: " ); ?></span>
                    <input type="text" name="vcita_first-name" id="vcita_first-name_<?php echo $form_uid;?>" <?php echo $disabled; ?> value="<?php echo $vcita_widget['first_name']; ?>" size="25">
                </div>

                <div style="display:block;clear:both;">
                    <span style="display:inline-block;width:110px;"><?php _e("Last user: " ); ?></span>
                    <input type="text" name="vcita_last-name" id="vcita_last-name_<?php echo $form_uid;?>" <?php echo $disabled; ?> value="<?php echo $vcita_widget['last_name']; ?>" size="25">
                </div>

                <div style="display:block;clear:both;">
                    <span style="display:inline-block;width:110px;"><?php _e("Professional Title: " ); ?></span>
                    <input type="text" name="vcita_prof-til" id="vcita_prof-til_<?php echo $form_uid;?>" <?php echo $disabled; ?> value="<?php echo $vcita_widget['prof_title']; ?>" size="25">
                </div>

                <input type="hidden" name="vcita_contact" value="Y">
                <p class="submit" style="padding-top:5px;">
                    <input type="submit" style="float:left;" name="Submit" value="<?php _e('Save Settings') ?>" />
                    <?php if (!$first_time) { ?>
                        <a href="#" style="text-align:left;float:left;padding:5px 0 0 5px;"
                            onclick="document.getElementById('vcita_active_<?php echo $form_uid;?>').style.display='block';document.getElementById('vcita_form_<?php echo $form_uid;?>').style.display= 'none';">Cancel</a>
                    <?php } ?>
                </p>
            </div>
        </form>

        <?php if (!$first_time) { ?>
			<div>
                <div id="vcita_active_<?php echo $form_uid;?>">
                    Account:&nbsp;<b><?php echo $vcita_widget['email']; ?></b>
                    <input type="button"
                            style="margin-left:10px;float:right;width:100px;"
                            name="Change"
                            onclick="document.getElementById('vcita_active_<?php echo $form_uid;?>').style.display= 'none';document.getElementById('vcita_form_<?php echo $form_uid;?>').style.display= 'block'; "
                            value="<?php _e('Change Account') ?>" />
                </div>

                <?php echo $config_html; ?>

                <h4 style="clear:both;border-bottom:1px solid gray;padding-top:10px;margin:0px;">vCita Contact Page:</h4>
				<div style="margin-top:5px;">
                <div style="float:left;line-height:20px;margin-right:5px;"><?php echo $page_status;?></div>

				<form name="vcita_page_control_form" style="float:right;" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
					<input type="hidden" name="vcita_page_control" value="Y" />
					<input type="submit" style="float:left;width:100px;" name="Submit" value="<?php _e($page_action) ?>" />
				</form>
				</div>

				<?php if ($page_available) { ?>
					<div style="display:block;clear:both;"><a href="<?php echo get_page_edit_link($vcita_widget['page_id']); ?>">Edit your page</a></div>
			        <?php } ?>
			</div>

        <?php } ?>

        <?php if (!$first_time) { ?>
            <h4 style="clear:both;border-bottom:1px solid gray;padding-top: 25px;margin:0px;">Add vCita to ALL pages as a sidebar:</h4>
            <div>
                <ol>
                    <li><b>Click</b> the "Widgets" menu on the left (Under "Appearance"). <br/></li>
                    <li><b>Drag</b> the "vCita Contact Widget" to the desired location on the right (Recommended as Sidebar).</li>
                </ol>
            </div>

            <h4 style="clear:both;border-bottom:1px solid gray;padding-top: 25px;margin:0px;">Add vCita to an existing page:</h4>
                <div style="clear:both;margin-top:5px;">
                    You can also use the following code to manually add vCita to any post or page :

                    <div style="display:block;margin-top:10px;">
                        <input id="vcita_embed_type_contact" checked="checked" name="vcita_embed_type" type="radio" style="border:0 none;outline:0 none;" value="contact" onclick="vcita_toggle_preview('contact', '<?php echo $form_uid;?>');">
			            <label for="vcita_embed_type_contact" onclick="vcita_toggle_preview('contact', '<?php echo $form_uid;?>');">Contact Form</label>
                        <input id="vcita_embed_type_widget" name="vcita_embed_type" type="radio" value="widget" style="border:0 none;outline:0 none;" onclick="vcita_toggle_preview('widget', '<?php echo $form_uid;?>');">
			            <label for="vcita_embed_type_widget" onclick="vcita_toggle_preview('widget', '<?php echo $form_uid;?>');">Widget</label>
                    </div>

                    <input readonly type="text" id="vcita_embed_contact_<?php echo $form_uid;?>" style="width:300px;height:30px;margin: 10px 0;" onclick="this.select();" value="[vCitaContact]"</input>
                    <input readonly type="text" id="vcita_embed_widget_<?php echo $form_uid;?>" style="width:300px;display:none;height:30px;margin: 10px 0;" onclick="this.select();" value="[vCitaContact type=widget height=350]"</input>

                </div>
        <?php } ?>

            <div style="display:block;clear:both;">
                <br/>
                <p><b>vCita has a lot more to offer! </b> <br/>
                    <a href="http://www.vcita.com/?autoplay=1&no_redirect=true" target="_blank">To learn more Take a Tour</a>
                </p>
            </div>
        </div>

        <div style="float:left;">
            <h4 style="clear:both;border-bottom:1px solid gray;padding-top: 0px;margin:0px;width:500px;">Preview:</h4>
            <p>
                <div id="vcita_preview_contact_<?php echo $form_uid;?>" style="width:500px;">
                    <?php echo create_embed_code("contact", $vcita_widget['uid'], $vcita_widget['email'], $vcita_widget['first_name'], $vcita_widget['last_name'], '500px', '400px', $vcita_widget['prof_title'], empty($vcita_widget['uid'])) ?>
                </div>
                <div id="vcita_preview_widget_<?php echo $form_uid;?>" style="display:none;width:500px;">
                    <?php echo create_embed_code("widget", $vcita_widget['uid'], $vcita_widget['email'], $vcita_widget['first_name'], $vcita_widget['last_name'], '200px', '400px', $vcita_widget['prof_title'], empty($vcita_widget['uid'])) ?>
                </div>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Create the vCita floatting widget Settings form content.
 *
 * This is based on Wordpress guidelines for creating a single widget.
 */
function vcita_widget_admin() {
    extract(prepare_widget_settings("", "widget"));

    ?>

    <div id="vcita_config">
        <label for="vcita_title">Title:</label>
        <input type="text" value="<?php echo $vcita_widget['title']; ?>" name="vcita_title" id="vcita_title" class="widefat">

        <hr style="margin: 15px 0;"/>
        <div id="vcita_config_params" style="text-align:right;">
            <label style="display:block;line-height:30px;text-align:left;"> Email:
                <input type="text" onkeypress="clearNames();" style="float:right;" id="vcita_email" name="vcita_email" value="<?php echo ($vcita_widget['email']); ?>" />
            </label>
            <label style="display:block;line-height:30px;text-align:left;"> First Name:
                <input type="text" style="float:right;" id="vcita_first-name_<?php echo $form_uid;?>" name="vcita_first-name"  <?php echo $disabled; ?> value="<?php echo ($vcita_widget['first_name']); ?>" />
            </label>
            <label style="display:block;line-height:30px;text-align:left;"> Last Name:
                <input type="text" style="float:right;" id="vcita_last-name_<?php echo $form_uid;?>" name="vcita_last-name"  <?php echo $disabled; ?> value="<?php echo ($vcita_widget['last_name']); ?>" />
            </label>
			<label style="display:block;line-height:30px;text-align:left;"> Prof. Title:
                <input type="text" style="float:right;" id="vcita_prof-til_<?php echo $form_uid;?>" name="vcita_prof-til"  <?php echo $disabled; ?> value="<?php echo ($vcita_widget['prof_title']); ?>" />
            </label>

            <?php echo create_user_message($vcita_widget, $update_made); ?>
            <?php echo $config_html; ?>
        </div>
    </div>

    <?php
}

/**
 * Use the current settings and create the vCita widget. - simply call the main vcita_add_contact function with the required parameters
 */
function vcita_widget_content($args) {
    $vcita_widget = (array) get_option('vcita_widget');

    echo vcita_add_contact( array('type' => 'widget', 'title' => $vcita_widget['title'], 'height' => '430px'));
}

/**
 * Main function for creating the widget html representation.
 * Transforms the shorcode parameters to the desired iframe call.
 *
 * Syntax as follows:
 * shortcode name - vCitaContact
 *
 * Arguments:
 * @param  type - Type of widget, can be "contact" or "widget". default is "contact"
 * @param email - The associated expert email. default is the currently saved "UID"
 * @param first_name - The first name of the expert. default is using the name of the associated Expert's UID
 * @param last_name - The last name of the expert. default is using the name of the associated Expert's UID
 * @param uid - The Unique identification for the user - if this is used it overrides the email / first name / last name
 * @param title - The title which will be above the widget. default is empty
 * @param widget - The width of the widget. default is "100%"
 * @param height - The height of the widget. default is "450px"
 *
 */
function vcita_add_contact($atts) {
    $vcita_widget = (array) get_option('vcita_widget');

    extract(shortcode_atts(array(
        'type' => 'contact',
        'email' => '',
        'first_name' => '',
        'last_name' => '',
        'prof_title' => '',
        'uid' => $vcita_widget['uid'],
        'id' => $vcita_widget['uid'],
        'title' => '',
        'width' => '100%',
        'height' => '400px',
    ), $atts));

    if (!empty($title)) {
        echo "<h2 style=\"margin-bottom:8px;\">$title</h2>";
    }

    if (empty($id)) {
        $id = $uid;
    }

    return create_embed_code($type, $id, $email, $first_name, $last_name, $width, $height, $prof_title, false);
}

/**
 * Initialize the vCita widget by registering the widget hooks
 */
function vcita_init() {
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ){
        return;
    }

    wp_register_sidebar_widget('vcita_widget_id', 'vCita Contact Widget', 'vcita_widget_content', array('description' => "Encourage visitors to contact or schedule meetings with you."));
    wp_register_widget_control('vcita_widget_id', 'vCita Contact Widget', 'vcita_widget_admin', array('description' => "Encourage visitors to contact or schedule meetings with you."));

    register_uninstall_hook(__FILE__, 'vcita_uninstall');
    register_deactivation_hook(__FILE__, 'vcita_uninstall');
}

/**
 * Remove the vCita widget and page if available
 */
function vcita_uninstall() {
    $vcita_widget = (array) get_option('vcita_widget');
    trash_page($vcita_widget["page_id"]);

    update_option("vcita_widget", "");
}

/**
 * Update the settings link to point to the correct location
 */
function add_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

	if ($file == $this_plugin){
		$settings_link = '<a href="' . admin_url("options-general.php?page=contact-form-with-a-meeting-scheduler-by-vcita/vcita-contact-form.php") . '">Settings</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
 }


/* --- Internal Methods --- */

/**
 * Get the edit link to the requested page
 */
function get_page_edit_link($page_id) {
	$page = get_page($page_id);
	return get_edit_post_link($page_id);
}

/**
 * Prepare all the common parameters for creating the vCita settings.
 *
 * It also initializes the widget for the first time and stores the form data after the user saves the changes
 *
 * @param widget_type - The type of widget to be stored for next usage
 */
function prepare_widget_settings($widget_type, $type) {
    $form_uid = rand();
    $uninitialized = false;

    if(empty($_POST)) {
        //Normal page display
        $vcita_widget = (array) get_option('vcita_widget');
        $update_made = false;


        // Create a initial parameters
        if (is_null($vcita_widget['created'])) {
            $vcita_widget = create_initial_parameters();
	    $uninitialized = true;
        }

    } else {
        //Form data sent
        $update_made = true;

        if ($_POST["vcita_page_control"] == "Y") {
            $vcita_widget = (array) get_option('vcita_widget');
        } else {
            $vcita_widget = (array) save_user_params($widget_type);
        }
    }

    if ($type == "widget") {
        $config_floating = "";
    } else {
        $config_floating = "float:left;";
    }

    if (!$uninitialized) {
        $vcita_widget = (array) generate_or_validate_user($vcita_widget);
        update_option('vcita_widget', $vcita_widget);
    }

    $config_html = "<div style='clear:both;text-align:left;display:block;padding-top:5px;'></div>";

    if (!empty($vcita_widget["uid"])) {
        $first_time = false;
        $disabled= "disabled=true title='To change your details please use the \"Edit Profile\" link bellow.'";

        if ($vcita_widget['confirmed']) {
            $config_html = "<div style='clear:both;".$config_floating."text-align:left;display:block;padding:5px 0 10px 0;'>
                            <div style='margin-right:5px;".$config_floating."'><a href='http://www.vcita.com/settings?section=profile' target='_blank'>Edit Profile</a></div>
                            <div style='margin-right:5px;".$config_floating."'><a href='http://www.vcita.com/settings?section=schedule' target='_blank'>Edit Availability</a></div>
                            <div style='margin-right:5px;".$config_floating."'><a href='http://www.vcita.com/settings?section=configuration' target='_blank'>Meeting Preferences</a></div></div>";
        }

    } else {
        $disabled = "";
        $first_time = true;
    }

    embed_clear_names(array("vcita_first-name_".$form_uid, "vcita_last-name_".$form_uid, "vcita_prof-til_".$form_uid));

    return compact('vcita_widget', 'disabled', 'config_html', 'form_uid', 'update_made', 'first_time');
}

/**
 * Save the form data into the Wordpress variable
 */
function save_user_params($widget_type) {
    $vcita_widget = (array) get_option('vcita_widget');

    if (!is_null($_POST['vcita_title'])) {
        $vcita_widget['title'] = stripslashes($_POST['vcita_title']);
    }

    $vcita_widget['created'] = 1;
    $vcita_widget['email'] = $_POST['vcita_email'];
    $vcita_widget['first_name'] = stripslashes($_POST['vcita_first-name']);
    $vcita_widget['last_name'] = stripslashes($_POST['vcita_last-name']);
    $vcita_widget['prof_title'] = stripslashes($_POST['vcita_prof-til']);

    if ($_POST['vcita_contact'] == 'Y') {
        $vcita_widget['dedicated_page'] = $_POST['vcita_dedicated-page'];
    }

    if (!empty($widget_type)) {
        $vcita_widget['widget_type'] = $widget_type;
    }

    update_option('vcita_widget', $vcita_widget);

    return $vcita_widget;
}

/**
 *  Use the vCita API to get a user, either create a new one or get the id of an available user
 *
 * @return array of the user name, id and if he finished the registration or not
 */
function generate_or_validate_user($widget_params) {
    extract(get_contents("http://www.vcita.com/experts/widgets/otf?email=" .
                        urlencode($widget_params['email']).
                        "&first_name=" .urlencode($widget_params['first_name']).
                        "&last_name=" . urlencode($widget_params['last_name']).
                        "&professional_title=".urlencode($widget_params['prof_title']).
                        "&api=true&enforce=true&ref=wp"));

    $widget_params['uid'] = '';

    if (!$success) {
        $widget_params['last_error'] = "Temporary problems, please try again";

    } else {
        $data = json_decode($raw_data);
        $widget_params['last_error'] = "Temporary problems, please try again";

        if ($data->{'success'} == 1) {
            $widget_params['first_name'] = $data->{'first_name'};
            $widget_params['last_name'] = $data->{'last_name'};
            $widget_params['prof_title'] = $data->{'title'};
            $widget_params['confirmed'] = $data->{'confirmed'};
            $widget_params['last_error'] = "";
            $widget_params['uid'] = $data->{'id'};

        } else {
            $widget_params['last_error'] = $data-> {'error'};

        }
    }

    return $widget_params;
}

/**
 *  Perform an HTTP GET Call to retrieve the data for the required content.
 * @param $url
 * @return array - raw_data and a success flag
 */
function get_contents($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
    curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT  ,0);

    $raw_data = curl_exec($ch);
    $success = true;

    if (curl_errno($ch)) {
        $success = false;
        $raw_data = curl_error($ch);
    }


    curl_close($ch);
    return compact('raw_data', 'success');
}

/**
 * Initials the vCita Widget parameters
 */
function create_initial_parameters() {
    $widget_params = array('title' => "Contact me using vCita", 'dedicated_page' => 'on', 'prof_title' => "Consultant");

    return $widget_params;
}

/**
 * Create the The iframe HTML Tag according to the given paramters
 */
function create_embed_code($type, $uid, $email, $first_name, $last_name, $width, $height, $prof_title, $preview) {
    $preview_text = "";

    if ($preview) {
        $preview_text = "&preview=wp";
    }

    // If No ID is present - use the OTF Interface to create or get the associated error, Otherwise use the normal API.
    if (empty($uid) || (!empty($email) && !empty($first_name))) {
	    $title = (empty($prof_title) ? "" : "&professional_title=".urlencode($prof_title));

        $code = "<iframe frameborder='0' src='http://www.vcita.com/experts/widgets/otf/?email=".urlencode($email).
                "&first_name=".urlencode($first_name)."&last_name=".urlencode($last_name)."&enforce=true&widget=" . $type.
                $title."&ref=wp".$preview_text."' width='".$width."' height='".$height."'></iframe>";

    } else {
        $code = "<iframe frameborder='0' src='http://www.vcita.com/" . urlencode($uid) . "/" . $type . "/?ref=wp".
        $preview_text."' width='".$width."' height='".$height."'></iframe>";
    }

	return $code;
}

/*
 * Make sure the page is published:
 * 1. If none available - Create a new one
 * 2. If page is in the Trash - Restore it
 * 3. If page is in a different state - Create a new one
 */
function make_sure_page_published($vcita_widget) {
    $page_id = $vcita_widget['page_id'];
	$page = get_page($page_id);

	if (empty($page)) {
		$page_id = add_contact_page();

	} elseif ($page->{"post_status"} == "trash") {
		wp_untrash_post($page_id);

	} elseif ($page->{"post_status"} != "publish") {
		$page_id = add_contact_page();
	}

    $vcita_widget['page_id'] = $page_id;
	update_option('vcita_widget', $vcita_widget);

	return $vcita_widget;
}

/**
 * Check that the page is available and published
 */


function is_page_available($vcita_widget) {
	$page_id = $vcita_widget['page_id'];
	$page = get_page($page_id);

	return !empty($page) && $page->{"post_status"} == "publish";
}

/**
 * Add A new contact page with vCita widget content in it.
 */
function add_contact_page() {
    return wp_insert_post(array(
        'post_name' => 'Contact',
        'post_title' => 'Contact',
        'post_type' => 'page',
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'post_content' => '[vCitaContact]'));
}

/**
 * Move a page to the Trash according to its ID.
 * This only takes place if the given page is available and currently published.
 */
function trash_page($page_id) {
	$page = get_page($page_id);

	if (!empty($page) && $page->{"post_status"} == "publish") {
		wp_trash_post($page_id);
	}
}

/**
 * Create the message which will be displayed to the user after performing an update to the widget settings.
 * The message is created according to if an error had happen and if the user had finished the registration or not.
 */
function create_user_message($vcita_widget, $update_made) {

    if (!empty($vcita_widget['uid'])) {

        // If update wasn't made, keep the message without info about the last change
        if ($update_made) {
            $message = "<b>Changes saved</b>";
        } else {
            $message = "";
        }

        $message_type = "updated below-h2";

        if (!$vcita_widget['confirmed']) {
            if ($update_made) {
                $message .= "<br/><br/>";
            }

            $message .= "An email was sent to: <b>".$vcita_widget['email'].
                        "</b>, please follow instructions in the email to complete vCita configuration.";
        }

    } elseif (!empty($vcita_widget['last_error'])) {
        $message = "<b>".$vcita_widget['last_error']."</b>";
        $message_type = "error below-h2";
    }

    if (empty($message)) {
        return "";
    } else {
        return "<div class='".$message_type."' style='padding:5px;text-align:left;'>".$message."</div>";
    }
}

/**
 * Embed the function for toggling the preview visibility
 */
function embed_toggle_preview_visibility() {
	?>
	<script type='text/javascript'>
	    function vcita_toggle_preview(type, rand) {
		    var widgetVisibility = (type == 'widget') ? 'block' : 'none';
		    var contactVisibility = (type == 'contact') ? 'block' : 'none';

		    document.getElementById('vcita_embed_contact_' + rand).style.display = contactVisibility;
		    document.getElementById('vcita_embed_widget_' + rand).style.display = widgetVisibility;
		    document.getElementById('vcita_preview_contact_' + rand).style.display = contactVisibility;
		    document.getElementById('vcita_preview_widget_' + rand).style.display = widgetVisibility;
	    }
	</script>
	<?php
}

/**
 * Create a Javascript function which go over on all the given ids and for each, clear the field and enable it
 */
function embed_clear_names($ids) {
	?>
	<script type='text/javascript'>
		function clearNames() {

	<?php
		foreach ($ids as $id) {
			embed_clear_name($id);
		}
	?>

	    }
	</script>
	<?php
}

/**
 * Create a Javascript snippet which will take the id and will clear the field.
 * By clear it will do the following: erase the field content, enable the field and clear the title element.
 *
 * This only changes fields which are disabled
 */
function embed_clear_name($id) {
	?>
    element = document.getElementById("<?php echo $id ?>");

    if (element.disabled) {
        element.value = '';
        element.removeAttribute('disabled');
        element.removeAttribute('title');
    }

	<?php
}