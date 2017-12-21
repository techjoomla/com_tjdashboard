<?php
/**
 * @package    Com_Tjdashboard
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  2017 Techjoomla
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Unauthorized Access');

/**
 * Widget class.  Handles all application interaction with a Widget
 *
 * @since  11.1
 */
class TjdashboardWidget extends JObject
{
	public $dashboard_widget_id = null;

	public $dashboard_id = 0;

	public $title = "";

	public $ordering = 0;

	public $state = 1;

	public $data_plugin = "";

	public $renderer_plugin = "";

	public $size = null;

	public $params = "";

	public $autorefresh = 0;

	public $created_on = null;

	public $created_by = 0;

	public $modified_on = null;

	public $modified_by = 0;

	public $widget_render_data = array();

	protected static $widgetObj = array();

	/**
	 * Constructor activating the default information of the Widget
	 *
	 * @param   int  $id  The unique event key to load.
	 *
	 * @since   1.0
	 */
	public function __construct($id = 0)
	{
		if (!empty($id))
		{
			$this->load($id);
		}
	}

	/**
	 * Returns the global widget object
	 *
	 * @param   integer  $id  The primary key of the widget_id to load (optional).
	 *
	 * @return  Widget  The widget object.
	 *
	 * @since   1.0
	 */
	public static function getInstance($id = 0)
	{
		// @Todo- Check the comments for this function
		if (!$id)
		{
			return new TjdashboardWidget;
		}

		if (empty(self::$widgetObj[$id]))
		{
			$widget = new TjdashboardWidget($id);
			self::$widgetObj[$id] = $widget;
			self::$widgetObj[$id]->widget_render_data = $widget->getWidgetData($id);
		}

		return self::$widgetObj[$id];
	}

	/**
	 * Method to load a widget object by widget id
	 *
	 * @param   int  $id  The widget id
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function load($id)
	{
		$table = TjdashboardFactory::table("widgets");

		if (!$table->load($id))
		{
			return false;
		}

		$widgetData = $this->getWidgetData($id);

		$this->setProperties($table->getProperties());
		$this->set('widget_render_data', $widgetData);

		return true;
	}

	/**
	 * Save the current object properties to database
	 *
	 * @param   array  $data  The associative array to bind to the object
	 *
	 * @return	String
	 *
	 * @since 	1.0
	 **/
	public function save($data = array())
	{
		$data = array_filter($data);

		if (empty($data))
		{
			$this->setError(JText::_("COM_TJDASHBOARD_EMPTY_DATA"));

			return false;
		}

		$model = TjdashboardFactory::model("Widget");
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_tjdashboard/models/forms');
		$form = $model->getForm();

		if (!$form)
		{
			$this->setError($model->getError());

			return false;
		}

		$data = $model->validate($form, $data);

		if (!$data)
		{
			$this->setError(JText::_("COM_TJDASHBOARD_EMPTY_REQUIRED_DATA"));

			return false;
		}

		$save = $model->save($data);

		if (!$save)
		{
			$this->setError($model->getError());

			return false;
		}
		else
		{
			return $this->load($model->getState("dashboard_widget_id"));
		}
	}

	/**
	 * Get the widget data
	 *
	 * @return	Array
	 *
	 * @since 	1.0
	 **/
	protected function getWidgetData()
	{
		$widgetModel = TjdashboardFactory::model("widgets", array("ignore_request" => 1));
		$widgetModel->setState('filter.dashboard_widget_id', $this->dashboard_widget_id);
		$widgetData = $widgetModel->getItems();

		JLoader::import("/components/com_tjdashboard/helpers/dashboard", JPATH_ADMINISTRATOR);
		$tjDashboardHelper = new DashboardHelper;
		$result = $tjDashboardHelper->getWidgetRendererData($widgetData);

		if ($result && $result['status'])
		{
			return $result['data'];
		}
	}
}
