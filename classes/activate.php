<?php
namespace IsqPortal\WpCookiecheck;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Activate
{

    public function activate()
    {
        /**
        $dbVersion          = '0.0.4';
        $installedDbVersion = get_option("isq_cookiecheck_db_version");
        if ($installedDbVersion != $dbVersion) {
            self::clearDatabaseTables();
            self::buildDatabaseTables();
            self::importDatabaseData();
            update_option('isq_cookiecheck_db_version', $dbVersion);
        }
         **/
    }

    private function clearDatabaseTables()
    {
        global $wpdb;
        $wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'isq_cookiecheck_config');
        $wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'isq_cookiecheck_snippets');
        $wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'isq_cookiecheck_snippet_cookies');
        $wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'isq_cookiecheck_snippet_variables');
    }

    private function buildDatabaseTables()
    {
        global $wpdb;
        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix.'isq_cookiecheck_config';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(50) NOT NULL,
            value varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);
        $sql ="ALTER TABLE $table_name ADD PRIMARY KEY (id);";
        dbDelta($sql);

        $table_name = $wpdb->prefix.'isq_cookiecheck_snippets';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(50) NOT NULL,
            slug varchar(50) NOT NULL,
            label varchar(100) NOT NULL,
            display tinyint(2) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql);
        $sql ="ALTER TABLE $table_name ADD PRIMARY KEY (id);";
        dbDelta($sql);

        $table_name = $wpdb->prefix.'isq_cookiecheck_snippet_cookies';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            snippet_id int(11) NOT NULL,
            name varchar(255) NOT NULL,
            path varchar(50) NOT NULL,
            domain varchar(255) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);
        $sql ="ALTER TABLE $table_name ADD PRIMARY KEY (id);";
        dbDelta($sql);

        $table_name = $wpdb->prefix.'isq_cookiecheck_snippet_variables';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            snippet_id int(11) NOT NULL,
            label varchar(50) NOT NULL,
            slug varchar(50) NOT NULL,
            description varchar(100) NOT NULL,
            value varchar(255) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);
        $sql ="ALTER TABLE $table_name ADD PRIMARY KEY (id);";
        dbDelta($sql);
    }



    private function importDatabaseData() {
        global $wpdb;        
        $imports = array(
            'isq_cookiecheck_config' => array(
                array(
                    'name'  => 'cookie_policy_url',
                    'value' => '/cookie-policy'
                ),
                array(
                    'name'  => 'panel_toggle_position',
                    'value' => 'left',
                ),
                array(
                    'name'  => 'default_setting',
                    'value' => 'enabled',
                ),
                array(
                    'name'  => 'domain_name',
                    'value' => '',
                ),
            ),
        );
        foreach ($imports as $table => $rows) {
            foreach ($rows as $row) {
                $wpdb->insert($wpdb->prefix.$table, $row);
            }
        }

        $snippets = array(
            array(
                'title'   => 'Google Analytics',
                'slug'    => 'google_analytics',
                'label'   => 'Google Analytics',
                'display' => '0',
                'cookies' => array(
                    'defaults' => array(
                        'path'   => '/',
                        'domain' => '.{* config.domain_name *}',
                    ),
                    array('name' => '_ga'),
                    array('name' => '_gid'),
                    array('name' => '_gat'),
                    array('name' => '_gat_gtag_{* snippets.google_analytics.variables.tracking_id|hyp-unds *}'),
                    array('name' => '__utma'),
                    array('name' => '__utmt'),
                    array('name' => '__utmb'),
                    array('name' => '__utmc'),
                    array('name' => '__utmz'),
                    array('name' => '__utmv'),
                    array('name' => '__utmx'),
                    array('name' => '__utmxx'),
                    array('name' => '_gaexp'),
                    array('name' => '_utm.gif'),
                ),
                'variables' => array(
                    array(
                        'label'       => 'Tracking ID',
                        'slug'        => 'tracking_id',
                        'description' => 'Your unique tracking ID provided by Google',
                    ),
                ),
            )
        );

        foreach ($snippets as $snippet) {
            $wpdb->insert($wpdb->prefix.'isq_cookiecheck_snippets', array(
                'title'   => $snippet['title'],
                'slug'    => $snippet['slug'],
                'label'   => $snippet['label'],
                'display' => $snippet['display'],
            ));
            $snippetId = $wpdb->insert_id;

            $cookieDefaults = array(
                'path'   => '/',
                'domain' => '{* config.domain_name *}',
            );
            $cookieDefaults = isset($snippet['cookies']['defaults'])
                ? array_merge($cookieDefaults, $snippet['cookies']['defaults'])
                : $cookieDefaults;

            foreach ($snippet['cookies'] as $key => $cookie) {
                if ($key === 'defaults') {
                    continue;
                }
                $cookie = array_merge($cookieDefaults, $cookie);
                $wpdb->insert($wpdb->prefix.'isq_cookiecheck_snippet_cookies', array(
                    'snippet_id' => $snippetId,
                    'name'       => $cookie['name'],
                    'path'       => $cookie['path'],
                    'domain'     => $cookie['domain'],
                ));
            }
            foreach ($snippet['variables'] as $variable) {
                $wpdb->insert($wpdb->prefix.'isq_cookiecheck_snippet_variables', array(
                    'snippet_id'  => $snippetId,
                    'label'       => $variable['label'],
                    'slug'        => $variable['slug'],
                    'description' => $variable['description'],
                ));
            }
        }

    }
}
