<?php
/**
 * @package    Com_Tjdashboard
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  2017 Techjoomla
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Tjdashboard model class for widgets
 *
 * @since  1.0.0
 */
class TjdashboardModelWidgets extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelList
	 * @since   1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'dashboard_id',
				'dashboard_widget_id',
				'size'
				);
		}

		// Set the filters as default filters
		parent::__construct($config);
	}

	/**
	 * Get the master query for retrieving a list of requests to the senior.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.0
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*');
		$query->from($db->quoteName('#__tj_dashboard_widgets'));

		// Filter by dashboard_widget_id
		$id = $this->getState('filter.dashboard_widget_id');

		if (!empty($id))
		{
			$query->where($db->quoteName('dashboard_widget_id') . ' = ' . (int) $id);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('dashboard_widget_id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(title LIKE ' . $search . ')');
			}
		}

		// Filter by dashboard_id
		$dashboard_id = $this->getState('filter.dashboard_id');

		if (!empty($dashboard_id))
		{
			$query->where($db->quoteName('dashboard_id') . ' = ' . (int) $dashboard_id);
		}

		// Filter by size
		$size  = $this->getState('filter.size');

		if (!empty($size))
		{
			$query->where($db->quoteName('size') . ' = ' . (int) $size);
		}

		return $query;
	}
}
