/*
 * @version    SVN:<SVN_ID>
 * @package    com_tjdashboard
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

var TJDashboardService = {
	api_dashboard_url : 'index.php?option=com_api&app=tjdashboard&resource=dashboard&id=',
	api_widget_url : 'index.php?option=com_api&app=tjdashboard&resource=widget&id=',
	get_renderers_url : 'index.php?option=com_tjdashboard&task=widget.getSupportedRenderers',
	get_widget_params : 'index.php?option=com_tjdashboard&task=widget.getWidgetParams',

	postData: function(url, params, formData) {

		params['url']		= url;
		params['data'] 		= formData;
		params['headers']	= {'x-auth':'session'};
		params['type'] 		= typeof params['type'] != "undefined" ? params['type'] : 'POST';
		params['dataType'] 	= typeof params['datatype'] != "undefined" ? params['datatype'] : 'json';

		var promise = jQuery.ajax(params);
		return promise;
	},

	getDashboard: function(id) {
		var params = {};
		params.method = 'GET';
		/** global: root_url */

		return this.postData(root_url + this.api_dashboard_url + id, params);
	},

	getWidget: function(id) {
		var params = {};
		params.method = 'GET';
		/** global: root_url */
		return this.postData(root_url + this.api_widget_url + id, params);
	},

	getRenderers: function(selectedDataPlugin) {
		var formData = {};
		var params = {};
		formData.pluginName = selectedDataPlugin;
		/** global: root_url */
		return this.postData(root_url + this.get_renderers_url, params, formData);
	},

	getWidgetParams : function (selectedDataPlugin,widgetId) {
		var formData = {};
		var params = {};
		formData.pluginName = selectedDataPlugin;
		formData.widgetId   = widgetId;
		/** global: root_url */
		return this.postData(root_url + this.get_widget_params, params, formData);		
	}
}
