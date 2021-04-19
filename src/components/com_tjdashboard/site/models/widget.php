<?php
/**
 * @package     TJDashboard
 * @subpackage  com_tjdashboard
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
JLoader::import('components.com_tjdashboard.includes.tjdashboard', JPATH_ADMINISTRATOR);

/**
 * Tjdashboard model class for widget
 *
 * @since  1.0.0
 */
class TjdashboardModelWidget extends JModelAdmin
{
	/**
	 * @var		object	The widget data.
	 * @since   1.0.0
	 */
	protected $data;

	/**
	 * Method to get the job form data.
	 *
	 * @param   int  $pk  An optional array of data for the form to interogate.
	 *
	 * @return  mixed  	Data object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		if ($this->data === null)
		{
			$data = parent::getItem($pk);
			$data->params = json_encode($data->params);

			return $data;
		}

		return $this->data;
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'widgets', $prefix = 'TjDashboardTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_tjdashboard/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_tjdashboard.widget', 'widget', array('control' => 'jform', 'load_data' => $loadData));

		return (empty($form)) ? false : $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		return $this->getItem();
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function save($data)
	{
		$pk   = (!empty($data['widget_dashboard_id'])) ? $data['widget_dashboard_id'] : (int) $this->getState('widget.widget_dashboard_id');
		$widget = TjdashboardWidget::getInstance($pk);
		$data['title'] = ucwords($data['title']);

		// Bind the data.
		if (!$widget->bind($data))
		{
			$this->setError($widget->getError());

			return false;
		}

		// Store the data.
		if (!$widget->save())
		{
			$this->setError($widget->getError());

			return false;
		}

		$this->setState('widget.dashboard_widget_id', $widget->dashboard_widget_id);

		return true;
	}

	/**
	 * Method to to get the params of respective data source
	 *
	 * @param   string  $pluginName  The plugin name 
	 * @param   string  $widgetId    widget id
	 * 
	 * @return string
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function getWidgetParams($pluginName, $widgetId = null)
	{
		// Initialize variables.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__tj_dashboard_widgets'));

		if ($widgetId)
		{
			$query->where($db->quoteName('dashboard_widget_id') . ' = ' . (int) $widgetId);
		}

		$query->where($db->quoteName('data_plugin') . ' = ' . $db->q($pluginName));
		$query->where($db->quoteName('params') . ' <> "" ');
		$query->limit(1);
		$db->setQuery($query);
		$params = $db->loadResult();

		return $params;
	}
}
