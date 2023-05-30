<?php
require $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
// require_once('gdpr-cookie-ajax.php');

$GDPRCookieAjax = new GDPRCookieAjax();
$GDPRCookie = new GDPRCookie();

if (!isset($_POST['action'])) {
    header('HTTP/1.0 403 Forbidden');
    throw new Exception("Action not specified");
}

switch ($_POST['action']) {
    case 'hide':
        // Set cookie
        GDPRCookieAjax::setCookie('scwCookieHidden', 'true', 52, 'weeks');
        header('Content-Type: application/json');
        die(json_encode(['success' => true]));
        break;

    case 'toggle':
        $return    = [];

        // Update if cookie allowed or not
        $choices = GDPRCookieAjax::getCookie('scwCookie');
        if ($choices == false) {
            $choices = [];
            $enabledCookies = $GDPRCookie->enabledSnippets();
            foreach ($enabledCookies as $name => $label) {
                $choices[$name] = $GDPRCookieAjax->getConfig('default_setting');
            }
            GDPRCookieAjax::setCookie('scwCookie', GDPRCookieAjax::encrypt($choices), 52, 'weeks');
        } else {
            $choices = GDPRCookieAjax::decrypt($choices);
        }
        $choices[$_POST['name']] = $_POST['value'] == 'true' ? 'enabled' : 'disabled';

        // Remove cookies if now disabled
        if ($choices[$_POST['name']] == 'disabled') {
            $removeCookies = $GDPRCookieAjax->clearCookieGroup($_POST['name']);
            $return['removeCookies'] = $removeCookies;
        }

        $choices = GDPRCookieAjax::encrypt($choices);
        GDPRCookieAjax::setCookie('scwCookie', $choices, 52, 'weeks');

        header('Content-Type: application/json');
        die(json_encode($return));
        break;

    case 'load':
        $return    = [];

        $removeCookies = [];

        foreach ($GDPRCookie->enabledSnippets() as $cookie => $label) {
            if (!$GDPRCookie->isAllowed($cookie)) {
                $removeCookies = array_merge($removeCookies, $GDPRCookieAjax->clearCookieGroup($cookie));
            }
        }
        $return['removeCookies'] = $removeCookies;

        header('Content-Type: application/json');
        die(json_encode($return));
        break;

    default:
        header('HTTP/1.0 403 Forbidden');
        throw new Exception("Action not recognised");
        break;
}
