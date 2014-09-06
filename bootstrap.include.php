<?php

/**
 * ContactForm
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/miniShop
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

use phpManufaktur\Basic\Control\CMS\EmbeddedAdministration;

// add a access point for the ContactForm
$entry_points = $app['security.role_entry_points'];
$entry_points['ROLE_ADMIN'][] = array(
    'route' => '/admin/form',
    'name' => 'ContactForm',
    'info' => $app['translator']->trans('Contact forms for the kitFramework'),
    'icon' => array(
        'path' => '/extension/phpmanufaktur/phpManufaktur/ContactForm/extension.jpg',
        'url' => MANUFAKTUR_URL.'/ContactForm/extension.jpg'
    )
);
$app['security.role_entry_points'] = $entry_points;

/**
 * Use the EmbeddedAdministration feature to connect the extension with the CMS
 *
 * @link https://github.com/phpManufaktur/kitFramework/wiki/Extensions-%23-Embedded-Administration
 */
$app->get('/form/cms/{cms_information}', function ($cms_information) use ($app) {
    $administration = new EmbeddedAdministration($app);
    return $administration->route('/admin/form', $cms_information);
});

/**
 * ADMIN routes
 */

$admin->get('/form/setup',
    'phpManufaktur\ContactForm\Data\Setup\Setup::Controller');
$admin->get('/form/update',
    'phpManufaktur\ContactForm\Data\Setup\Update::Controller');
$admin->get('/form/uninstall',
    'phpManufaktur\ContactForm\Data\Setup\Uninstall::Controller');


$app->get('/admin/form',
    'phpManufaktur\ContactForm\Control\Admin\Admin::ControllerSelectDefaultTab');
$app->get('/admin/form/about',
    'phpManufaktur\ContactForm\Control\Admin\About::Controller');
