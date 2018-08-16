<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	  
	/*--------------------------checking session exists or not -------------------------------*/ 	
	App::uses('CakeSession', 'Model/Datasource');
	$value = CakeSession::read('Auth.User');
	if($value){
		Router::connect('/', 					array('controller' => 'settings', 'action' => 'mywebsite'));
	}else{
		Router::connect('/', 					array('controller' => 'pages', 'action' => 'display'));
	}
	
	Router::connect('/contactus', 					array('controller' => 'pages', 'action' => 'contactus'));
	Router::connect('/login', 						array('controller' => 'Market', 'action' => 'login'));
	Router::connect('/register', 					array('controller' => 'Market', 'action' => 'register'));
	Router::connect('/signup/:plan', 				array('controller' => 'Market', 'action' => 'signup'), array('pass' => array('plan')));
	Router::connect('/marketplace', 				array('controller' => 'Market', 'action' => 'marketplace_home'));
	Router::connect('/ThankYouOrderCompleted/:plan', 	array('controller' => 'settings', 'action' => 'completedorder'), array('pass' => array('plan')));
	Router::connect('/build-an-app', 					array('controller' => 'Market', 'action' => 'buildapp'));
	Router::connect('/basic-ecommerce-themes', 			array('controller' => 'Market', 'action' => 'basictheme'));
	Router::connect('/free-landing-page', 				array('controller' => 'Market', 'action' => 'freetemplate'));
	Router::connect('/pricing', 						array('controller' => 'Market', 'action' => 'pricing'));
	Router::connect('/build-your-store',				array('controller' => 'Market', 'action' => 'buildstore'));
	Router::connect('/custom-built-stores',				array('controller' => 'Market', 'action' => 'custombuildstore'));
	Router::connect('/friends', 						array('controller' => 'pictures', 'action' => 'friends'));
	Router::connect('/newest', 							array('controller' => 'pictures', 'action' => 'newest'));
	Router::connect('/popular', 						array('controller' => 'pictures', 'action' => 'popular'));
	Router::connect('/hometest', 						array('controller' => 'pages', 'action' => 'hometest'));
	Router::connect('/:slug', 							array('controller' => 'pages', 'action' => 'profile') , array('pass' => array('slug')));
	Router::connect('/picture/:id/:slug', 				array('controller' => 'pictures', 'action' => 'detail') , array('pass' => array('id','slug')));
	Router::connect('/settings/solddetail/:id', 		array('controller' => 'settings', 'action' => 'solddetail') , array('pass' => array('id')));
	Router::connect('/:slug/followers', 	array('controller' => 'pages', 'action' => 'followers') , array('pass' => array('slug')));
	Router::connect('/:slug/following', 	array('controller' => 'pages', 'action' => 'following') , array('pass' => array('slug')));
	Router::connect('/:slug/likes', 		array('controller' => 'pages', 'action' => 'likes') , array('pass' => array('slug')));
	Router::connect('/:slug/story', 		array('controller' => 'pages', 'action' => 'stories') , array('pass' => array('slug')));
	Router::connect('/:slug/contact', 		array('controller' => 'pages', 'action' => 'contact') , array('pass' => array('slug')));
	Router::connect('/category/:id/:slug', 	array('controller' => 'categories', 'action' => 'maincategoryproducts') , array('pass' => array('id','slug')));
	Router::connect('/subcategory/:subcat/:catid/:parent_id/:userid/:slug', 	array('controller' => 'categories', 'action' => 'subcategoryproducts') , array('pass' => array('subcat','catid','parent_id','slug','userid')));
	Router::parseExtensions('xml');
	Router::connect('/sitemap', 		array('plugin' => 'sitemap', 'controller' => 'Sitemap', 'action' => 'index'));
	Router::promote();
	
	/*--------------------------load all the plugin here -------------------------------*/ 

	CakePlugin::loadAll(array(
		'sitemap' => array('routes' => true)
	));

		
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	//Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
