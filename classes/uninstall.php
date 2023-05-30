<?php
namespace GDPRCookie;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Uninstall
{
    public function uninstall()
    {
        self::dropDatabaseTables();    
    }

    private function dropDatabaseTables() {
        global $wpdb;
        $dropTables = array(
            $wpdb->prefix.'isq_cookiecheck_config',
            $wpdb->prefix.'isq_cookiecheck_snippets',
            $wpdb->prefix.'isq_cookiecheck_snippet_cookies',
            $wpdb->prefix.'isq_cookiecheck_snippet_variables',
        );
        foreach ($dropTables as $dropTable) {
            $wpdb->query("DROP TABLE IF EXISTS $dropTable");
        }
    }
}
