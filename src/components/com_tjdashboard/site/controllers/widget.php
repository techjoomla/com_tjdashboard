<?php
/**
 * @package     TJDashboard
 * @subpackage  com_tjdashboard
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * The widget controller
 *
 * @since  1.0.0
 */
class TjDashboardControllerWidget extends JControllerForm
{
	/**
	 * Function to get all the respective renderers for given data source
	 *
	 * @return  object  object
	 */
	public function getSupportedRenderers()
	{
		try
		{
			$app     = JFactory::getApplication();
			$jinput  = $app->input;
			$formData   = $jinput->post->get('pluginName', '', 'string');
			$dataPlugin = explode('.', $formData);
			$pluginFolder = $dataPlugin[0] . '/' . $dataPlugin[0];
			$pluginFileName = $dataPlugin[1];
			require_once JPATH_PLUGINS . '/tjdashboardsource/' . $pluginFolder . '/' . $pluginFileName . '.php';
			$className = ucfirst($dataPlugin[0]) . ucfirst($pluginFileName) . 'Datasource';
			$dataSourceObject = new $className;
			$renderers = $dataSourceObject->getSupportedRenderers();
			$lang      = JFactory::getLanguage();

			foreach ($renderers as $key => $value)
			{
				$rendererName = explode(".", $key);
				$languageFilePath = JPATH_PLUGINS . '/tjdashboardrenderer/' . $rendererName[0];

				// Loading renderer language files for loading list of renderers available
				$lang->load("plg_tjdashboardrenderer_" . $rendererName[0], $languageFilePath, null, false, true) ||
				$lang->load("plg_tjdashboardrenderer_" . $rendererName[0], JPATH_ADMINISTRATOR, null, false, true);
				$renderers[$key] = JText::_($value);
			}

			echo new JResponseJson($renderers);
			jexit();
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}

	/**
	 * Function to get the params of respective data source
	 *
	 * @return  object  object
	 */
	public function getWidgetParams()
	{
		try
		{
			$app          = JFactory::getApplication();
			$jinput       = $app->input;
			$pluginName   = $jinput->post->get('pluginName', '', 'string');
			$widgetId     = $jinput->post->get('widgetId', '', 'INT');
			$model 		  = $this->getModel('Widget');
			$paramsObject = $model->getWidgetParams($pluginName, $widgetId);
			echo new JResponseJson($paramsObject);
			jexit();
		}
		catch (Exception $e)
		{
			echo new JResponseJson($e);
		}
	}
}
