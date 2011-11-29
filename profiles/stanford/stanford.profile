<?php
/**
* Return an array of the modules to enable when this profile is installed.
*
* @return
*  An array of modules to be enabled.
*/
function stanford_profile_modules() {

    # Main modules.
    $modules = array(
        # Enable required core modules first.
        'block', 'filter', 'node', 'system', 'user',

        # Enable optional core modules next.
        'color', 'comment', 'help', 'menu', 'taxonomy', 'dblog',
    );

    # Enables webauth module if requested.
    $fields = get_stanford_installer();
    if ($fields['webauth'] == 'on') {
        array_push ($modules, 'webauth');
    }
    if ($fields['proxy'] != '') {
        array_push ($modules, 'reverse_proxy');
    }

    return $modules;
}

/**
 * Return a description of the profile for the initial installation screen.
 *
 * @return
 *   An array with keys 'name' and 'description' describing this profile,
 *   and optional 'language' to override the language selection for
 *   language-specific profiles.
 */
function stanford_profile_details() {
    return array(
        'name'        => 'Stanford',
        'description' => 'Select this profile to install Stanford-specific modules.',
        'language'    => 'en',
    );
}

/**
 * Return a list of tasks that this profile supports.
 *
 * @return
 *   A keyed array of tasks the profile will perform during
 *   the final stage. The keys of the array will be used internally,
 *   while the values will be displayed to the user in the installer
 *   task list.
 */
function stanford_profile_task_list() {
}

/**
 * Perform any final installation tasks for this profile.
 *
 * The installer goes through the profile-select -> locale-select
 * -> requirements -> database -> profile-install-batch
 * -> locale-initial-batch -> configure -> locale-remaining-batch
 * -> finished -> done tasks, in this order, if you don't implement
 * this function in your profile.
 *
 * If this function is implemented, you can have any number of
 * custom tasks to perform after 'configure', implementing a state
 * machine here to walk the user through those tasks. First time,
 * this function gets called with $task set to 'profile', and you
 * can advance to further tasks by setting $task to your tasks'
 * identifiers, used as array keys in the hook_profile_task_list()
 * above. You must avoid the reserved tasks listed in
 * install_reserved_tasks(). If you implement your custom tasks,
 * this function will get called in every HTTP request (for form
 * processing, printing your information screens and so on) until
 * you advance to the 'profile-finished' task, with which you
 * hand control back to the installer. Each custom page you
 * return needs to provide a way to continue, such as a form
 * submission or a link. You should also set custom page titles.
 *
 * You should define the list of custom tasks you implement by
 * returning an array of them in hook_profile_task_list(), as these
 * show up in the list of tasks on the installer user interface.
 *
 * Remember that the user will be able to reload the pages multiple
 * times, so you might want to use variable_set() and variable_get()
 * to remember your data and control further processing, if $task
 * is insufficient. Should a profile want to display a form here,
 * it can; the form should set '#redirect' to FALSE, and rely on
 * an action in the submit handler, such as variable_set(), to
 * detect submission and proceed to further tasks. See the configuration
 * form handling code in install_tasks() for an example.
 *
 * Important: Any temporary variables should be removed using
 * variable_del() before advancing to the 'profile-finished' phase.
 *
 * @param $task
 *   The current $task of the install system. When hook_profile_tasks()
 *   is first called, this is 'profile'.
 * @param $url
 *   Complete URL to be used for a link or form action on a custom page,
 *   if providing any, to allow the user to proceed with the installation.
 *
 * @return
 *   An optional HTML string to display to the user. Only used if you
 *   modify the $task, otherwise discarded.
 */
function stanford_profile_tasks(&$task, $url) {

    # Change the authenticated user role from rid 3 (due to mysql server
    #  replication and autoincrement value) to 2.
    adjust_authuser_rid();

    # Insert default user-defined node types into the database. For a complete
    # list of available node type attributes, refer to the node type API
    # documentation at: http://api.drupal.org/api/HEAD/function/hook_node_info.
    $types = array(
        array(
            'type' => 'page',
            'name' => st('Page'),
            'module' => 'node',
            'description' => st("A <em>page</em>, similar in form to a <em>story</em>, is a simple methodfor creating and displaying information that rarely changes, such as an \"About us\" section of a website. By default, a <em>page</em> entry does not allow visitor comments and is not featured on thesite's initial home page."),
            'custom' => TRUE,
            'modified' => TRUE,
            'locked' => FALSE,
            'help' => '',
            'min_word_count' => '',
        ),
        array(
            'type' => 'story',
            'name' => st('Story'),
            'module' => 'node',
            'description' => st("A <em>story</em>, similar in form to a <em>page</em>, is ideal for creating and displaying content that informs or engages website visitors. Press releases, site announcements, and informal blog-like entries may all be created with a <em>story</em> entry. By default, a <em>story</em> entry is automatically featured on the site's initial home page, and provides the ability to post comments."),
            'custom' => TRUE,
            'modified' => TRUE,
            'locked' => FALSE,
            'help' => '',
            'min_word_count' => '',
        ),
    );

    foreach ($types as $type) {
        $type = (object) _node_type_set_defaults($type);
        node_type_save($type);
    }

    # Default page to not be promoted and have comments disabled.
    variable_set('node_options_page', array('status'));
    variable_set('comment_page', COMMENT_NODE_DISABLED);

    # Don't display date and author information for page nodes by default.
    $theme_settings = variable_get('theme_settings', array());
    $theme_settings['toggle_node_info_page'] = FALSE;
    variable_set('theme_settings', $theme_settings);

    # Set upload path.
    #variable_set('file_directory_path', 'upload');
    variable_set('file_downloads', 1);

    # Set files temp directory to sites/default/tmp/.
    $fields = get_stanford_installer();
    variable_set('file_directory_temp', $fields['tmpdir']);

    # Users should need admin approval by default.
    variable_set('user_register', 2);

    # These can now go away.
#    @db_query("DROP TABLE install_settings");

    # Update the menu router information.
    menu_rebuild();
}

/**
 * Implementation of hook_form_alter().
 *
 * Allows the profile to alter the site-configuration form. This is
 * called through custom invocation, so $form_state is not populated.
 */
function stanford_form_alter(&$form, $form_state, $form_id) {
    $fields = get_stanford_installer ();
    if ($form_id == 'install_configure') {

        # General form settings.
        $form['intro']['#value'] = st('Please fill out the following values:');
        $form['site_information']['#collapsible']  = TRUE;
        $form['admin_account']['#collapsible']     = TRUE;
        $form['admin_account']['#value']           = '';
        $form['admin_account']['markup']['#value'] = '';
        $form['admin_account']['#title']           = '';
        $form['site_information']['#title']        = '';

        # Site settings.
        $form['site_information']['site_name']['#default_value'] = $fields['site_name'];
        $form['site_information']['site_mail']['#default_value'] = $fields['site_mail'];
        $form['site_information']['site_name']['#type'] = 'hidden';
        $form['site_information']['site_mail']['#type'] = 'hidden';

        # Admin account settings.
        $form['admin_account']['account']['mail']['#default_value'] = $fields['user_email'];
        $form['admin_account']['account']['name']['#default_value'] = 'admin';
        $form['admin_account']['account']['pass']['#default_value'] = $fields['pass'];
        $form['admin_account']['account']['mail']['#type'] = 'hidden';
        $form['admin_account']['account']['name']['#type'] = 'hidden';
        $form['admin_account']['account']['pass']['#type'] = 'hidden';

        # Server settings.
        $form['server_settings']['clean_url']['#type'] = 'hidden';
        $form['server_settings']['clean_url']['#default_value'] = 1;
#        $form['server_settings']['update_status_module']['#type'] = 'hidden';
#        $form['server_settings']['update_status_module']['#default_value'] = 0;
        $form['server_settings']['date_default_timezone']['#type'] = 'hidden';
        $form['server_settings']['date_default_timezone']['#default_value'] = -25200;

/*
        # Original setup, for comparing/altering.
        $form['intro'] = array(
            '#value' => st('To configure your website, please provide the following information.'),
            '#weight' => -10,
        );
        $form['site_information'] = array(
            '#type' => 'fieldset',
            '#title' => st('Site information'),
            '#collapsible' => FALSE,
        );
        $form['site_information']['site_name'] = array(
            '#type' => 'textfield',
            '#title' => st('Site name'),
            '#required' => TRUE,
            '#weight' => -20,
        );
        $form['site_information']['site_mail'] = array(
            '#type' => 'textfield',
            '#title' => st('Site e-mail address'),
            '#default_value' => ini_get('sendmail_from'),
            '#description' => st("The <em>From</em> address in automated e-mails sent during registration and new password requests, and other notifications. (Use an address ending in your site's domain to help prevent this e-mail being flagged as spam.)"),
            '#required' => TRUE,
            '#weight' => -15,
        );
        $form['admin_account'] = array(
            '#type' => 'fieldset',
            '#title' => st('Administrator account'),
            '#collapsible' => FALSE,
        );
        $form['admin_account']['account']['#tree'] = TRUE;
        $form['admin_account']['markup'] = array(
            '#value' => '<p class="description">'. st('The administrator account has complete access to the site; it will automatically be granted all permissions and can perform any administrative activity. This will be the only account that can perform certain activities, so keep its credentials safe.') .'</p>',
            '#weight' => -10,
        );

        $form['admin_account']['account']['name'] = array('#type' => 'textfield',
            '#title' => st('Username'),
            '#maxlength' => USERNAME_MAX_LENGTH,
            '#description' => st('Spaces are allowed; punctuation is not allowed except for periods, hyphens, and underscores.'),
            '#required' => TRUE,
            '#weight' => -10,
        );

        $form['admin_account']['account']['mail'] = array('#type' => 'textfield',
            '#title' => st('E-mail address'),
            '#maxlength' => EMAIL_MAX_LENGTH,
            '#description' => st('All e-mails from the system will be sent to this address. The e-mail address is not made public and will only be used if you wish to receive a new password or wish to receive certain news or notifications by e-mail.'),
            '#required' => TRUE,
            '#weight' => -5,
        );
        $form['admin_account']['account']['pass'] = array(
            '#type' => 'password_confirm',
            '#required' => TRUE,
            '#size' => 25,
            '#weight' => 0,
        );

        $form['server_settings'] = array(
            '#type' => 'fieldset',
            '#title' => st('Server settings'),
            '#collapsible' => FALSE,
        );
        $form['server_settings']['date_default_timezone'] = array(
            '#type' => 'select',
            '#title' => st('Default time zone'),
            '#default_value' => 0,
            '#options' => _system_zonelist(),
            '#description' => st('By default, dates in this site will be displayed in the chosen time zone.'),
            '#weight' => 5,
        );

        $form['server_settings']['clean_url'] = array(
            '#type' => 'radios',
            '#title' => st('Clean URLs'),
            '#default_value' => 0,
            '#options' => array(0 => st('Disabled'), 1 => st('Enabled')),
            '#description' => st('This option makes Drupal emit "clean" URLs (i.e. without <code>?q=</code> in the URL).'),
            '#disabled' => TRUE,
            '#prefix' => '<div id="clean-url" class="install">',
            '#suffix' => '</div>',
            '#weight' => 10,
        );

        $form['server_settings']['update_status_module'] = array(
            '#type' => 'checkboxes',
            '#title' => st('Update notifications'),
            '#options' => array(1 => st('Check for updates automatically')),
            '#default_value' => array(1),
            '#description' => st('With this option enabled, Drupal will notify you when new releases are available. This will significantly enhance your site\'s security and is <strong>highly recommended</strong>. This requires your site to periodically send anonymous information on its installed components to <a href="@drupal">drupal.org</a>. For more information please see the <a href="@update">update notification information</a>.', array('@drupal' => 'http://drupal.org', '@update' => 'http://drupal.org/handbook/modules/update')),
            '#weight' => 15,
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => st('Save and continue'),
            '#weight' => 15,
        );
        $form['#action'] = $url;
        $form['#redirect'] = FALSE;
*/

    }
}

# Check the installed settings, by looking at a special table we created just
#  for that purpose in the Drupal DB.
function get_stanford_installer () {
    $fields = array ();
    $result = db_query("SELECT * FROM install_settings");
    while ($row = db_fetch_object($result)) {
        $fields[$row->name] = $row->value;
    }
    return $fields;
}

# Change the default rid for the authenticated user role.  Drupal expects it
#  to be 2, and while you can change the setting in a file, bad modules
#  apparently don't respect that setting.
function adjust_authuser_rid () {
    $result = db_query("UPDATE role SET rid='2' WHERE name='authenticated user'");
#    if ($result) {
#        $result = db_query("UPDATE users_roles SET rid='2' WHERE rid='3');
#    }
}

?>
