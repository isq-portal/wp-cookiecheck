<?php
/*
Plugin Name: GDPR Cookie
Plugin URI: https://southcoastweb.co.uk/demos/scw-cookie.php
Description: Add a cookie control popup on the footer of your website.
Version: 1.0
Author: South Coast Web Design Ltd
Author URI: https://southcoastweb.co.uk/
License: MIT
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

include 'gdpr-cookie-ajax.php';

class GDPRCookie
{
    private $decisionMade = false;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->decisionMade = GDPRCookieAjax::getCookie('scwCookieHidden') == 'true';

        if (!session_id()) {
            session_start();
        }

        if (isset($_POST['save-gdpr-cookie'])) {
            $this->saveChanges($_POST);
            wp_redirect(admin_url('admin.php?page=gdpr-cookie'));
            exit;
        }

        add_action('admin_menu', array($this, 'addAdminMenuItem'));

        $plugin = plugin_basename(__FILE__);
        add_filter('plugin_action_links_'.$plugin, array($this, 'addPluginSettingsLink'));

        add_action('wp_footer', array($this, 'displayPopup'), 1000);
        add_action('wp_footer', array($this, 'displaySnippets'), 1000);
    }

    public function saveChanges($values)
    {
        $configUpdates = array(
            'domain_name'           => $_POST['domain_name'],
            'cookie_policy_url'     => $_POST['cookie_policy_url'],
            'panel_toggle_position' => $_POST['panel_toggle_position'],
            'default_setting'       => $_POST['default_setting'],            
        );
        foreach ($configUpdates as $name => $value) {
            $this->wpdb->update( 
                $this->wpdb->prefix.'isq_cookiecheck_config',
                array('value' => $value), 
                array('name' => $name)
            );
        }


        foreach ($_POST['cookies'] as $id => $values) {
            $this->wpdb->update( 
                $this->wpdb->prefix.'isq_cookiecheck_snippets',
                array(
                    'display' => isset($values['display']) && $values['display'] == 'on' ? 1 : 0 ,
                    'label' => $values['label'],
                ), 
                array('id' => $id)
            );

            if (!isset($values['variables'])) {
                $values['variables'] = array();
            }

            foreach ($values['variables'] as $slug => $value) {
                $this->wpdb->update( 
                    $this->wpdb->prefix.'isq_cookiecheck_snippet_variables',
                    array('value' => $value), 
                    array('slug' => $slug, 'snippet_id' => $id)
                );
            }

            $_SESSION['gdpr-cookie-admin-message'] = array('Saved successfully', 'updated');
        }
    }

    public function getChoices()
    {
        if (GDPRCookieAjax::getCookie('scwCookie') !== false) {
            $cookie = GDPRCookieAjax::getCookie('scwCookie');
            $cookie = GDPRCookieAjax::decrypt($cookie);
            return $cookie;
        }

        $return = [];
        foreach ($this->enabledSnippets() as $name => $label) {
            $return[$name] = $this->config['default_setting'];
        }
        return $return;
    }
 
    public function addPluginSettingsLink($links)
    {
        array_push(
            $links,
            '<a href="admin.php?page=gdpr-cookie">'.__('Settings').'</a>'
        );
        return $links;
    }

    public function addAdminMenuItem()
    {
        add_menu_page(
            'ISQ Cookiecheck',
            'ISQ Cookiecheck',
            'manage_options',
            'gdpr-cookie',
            array($this, 'displayAdminPage'),
            plugins_url('gdpr-cookie/icon.png'),
            100
        );
    }
 
    public function displayAdminPage()
    {
        // Load config
        $this->loadConfig();

        // Load snippets
        $this->loadSnippets();

        if (isset($_SESSION['gdpr-cookie-admin-message'])) {
            $message = $_SESSION['gdpr-cookie-admin-message'];
            unset($_SESSION['gdpr-cookie-admin-message']);
        }

        // CSS
        wp_register_style('gdpr-admin', plugin_dir_url(__FILE__).'html/admin/css/admin.css');
        wp_register_style('gdpr-admin-responsive', plugin_dir_url(__FILE__).'html/admin/css/admin.responsive.css');
        wp_enqueue_style('gdpr-admin');
        wp_enqueue_style('gdpr-admin-responsive');

        // JS
        wp_register_script('gdpr-admin', plugin_dir_url(__FILE__).'html/admin/js/admin.js');
        wp_enqueue_script('gdpr-admin');

        // HTML
        include 'html/admin/page.php';
    }
 
    public function displayPopup()
    {
        // Load config
        $this->loadConfig();

        // Load snippets
        $this->loadSnippets();

        $this->pluginDir = plugin_dir_url(__FILE__);

        if (isset($_SESSION['gdpr-cookie-public-message'])) {
            $message = $_SESSION['gdpr-cookie-public-message'];
            unset($_SESSION['gdpr-cookie-public-message']);
        }

        $this->config['showLiveChatMessage'] = true;

        // CSS
        // wp_register_style('gdpr-public', plugin_dir_url(__FILE__).'html/public/css/public.css');
        // wp_register_style('gdpr-public-responsive', plugin_dir_url(__FILE__).'html/public/css/public.responsive.css');
        // wp_enqueue_style('gdpr-public');
        // wp_enqueue_style('gdpr-public-responsive');

        // JS
        // wp_register_script('gdpr-public', plugin_dir_url(__FILE__).'html/public/js/public.js');
        // wp_enqueue_script('gdpr-public');

        // HTML
        include 'html/public/popup.php';
    }
 
    public function displaySnippets()
    {
        foreach ($this->enabledSnippets() as $slug => $label) {
            if (!$this->isAllowed($slug)) {
                continue;
            }
            include 'html/public/snippets/'.$slug.'.php';
        }
    }

    public function getVariable($snippetSlug, $variableSlug)
    {
        $snippetsTable          = $this->wpdb->prefix.'isq_cookiecheck_snippets';
        $snippetVariablesTable = $this->wpdb->prefix.'isq_cookiecheck_snippet_variables';
        $return = $this->wpdb->get_row(
            "SELECT v.value
            FROM $snippetVariablesTable v
            LEFT JOIN $snippetsTable s
                ON s.id = v.snippet_id
            WHERE v.slug = '$variableSlug'
                AND s.slug = '$snippetSlug'"
        );
        return $return ? $return->value : '';
    }

    private function loadConfig()
    {
        $configTable = $this->wpdb->prefix.'isq_cookiecheck_config';
        $dbConfigs = $this->wpdb->get_results("SELECT * FROM $configTable");
        foreach ($dbConfigs as $dbConfig) {
            $this->config[$dbConfig->name] = $dbConfig->value;
        }
    }

    public function getConfig($name)
    {
        if (!isset($this->config[$name])) {
            return false;
        }
        return $this->config[$name];
    }

    public function loadSnippets()
    {
        $snippetsTable          = $this->wpdb->prefix.'isq_cookiecheck_snippets';
        $snippetVariablesTable = $this->wpdb->prefix.'isq_cookiecheck_snippet_variables';

        $dbSnippets = $this->wpdb->get_results("SELECT * FROM $snippetsTable");

        foreach ($dbSnippets as $key => $dbSnippet) {
            $snippetId = $dbSnippet->id;
            $dbSnippets[$key]->variables = $this->wpdb->get_results(
                "SELECT *
                FROM $snippetVariablesTable
                WHERE snippet_id = $snippetId"
            );
        }

        $this->snippets = $dbSnippets;
    }

    public function enabledSnippets()
    {
        if (!isset($this->snippets)) {
            $this->loadSnippets();
        }
        $return = array();
        foreach ($this->snippets as $snippet) {
            if ($snippet->display != 1) {
                continue;
            }
            $return[$snippet->slug] = $snippet->label;
        }
        return $return;
    }

    public function disabledSnippets()
    {
        if (!isset($this->snippets)) {
            $this->loadSnippets();
        }
        $return = array();
        foreach ($this->snippets as $snippet) {
            if ($snippet->display != 0) {
                continue;
            }
            $return[$snippet->slug] = $snippet->label;
        }
        return $return;
    }

    public function isAllowed($slug)
    {
        $choices = $this->getChoices();
        return isset($choices[$slug]) && $choices[$slug] == 'enabled';
    }
}
// Initialise out class
add_action('init', 'initialise_gdpr_cookie');

include 'classes/bootstrap.php';
register_activation_hook(__FILE__, array('GDPRCookie\Activate', 'activate'));
register_uninstall_hook(__FILE__, array('GDPRCookie\Uninstall', 'uninstall'));

function initialise_gdpr_cookie()
{
    new GDPRCookie();
}
