<?php
/**
 * @package     TJDashboard
 * @subpackage  com_tjdashboard
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
JLoader::load(JPATH_COMPONENT_ADMINISTRATOR . '/includes/tjdashboard');

/**
 * Class TjdashboardController
 *
 * @since  1.0.0
 */
class TjdashboardController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.0.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/dashboard.php';
		$app  = JFactory::getApplication();
		$view = $app->input->getCmd('view', 'dashboards');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
