/*
 * @version    SVN:<SVN_ID>
 * @package    com_tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

var TJDashboardUI = {

	initDashboard : function(id){

	/** global: TJDashboardService */
	var promise = TJDashboardService.getDashboard(id);

	promise.done(function(response) {
			if(!response.data.dashboard_id)
			{
				return false;
			}

			if (response.data.widget_data.length <= 0)
			{
				jQuery('<div class="alert alert-info">' + Joomla.JText._("COM_TJDASHBOARD_WIDGETS_NOTSHOW_ERROR_MESSAGE") + '</div>').appendTo('.tjdashboard');
				return false;
			}

			var divSpan = 0;
			var i = 0;
			var j = 1;

			jQuery('<div class="row dashboard-widget-row-'+j+'">').appendTo('.tjdashboard');
			jQuery.each (response.data.widget_data, function(index, value)
			{
				var colorClass = "panel-default";
				var icon = "";
				var showTitle = true;
				var widgetHeadingHtml = '';

				if(value.params)
				{
					try
					{
						value.params = JSON.parse(value.params);

						if(value.params.color){
							colorClass=value.params.color;
						}

						if(value.params.icon){
							icon = value.params.icon;
						}

						if (typeof(value.params.show_title) !== "undefined")
						{
							showTitle = value.params.show_title;
						}
					  }
					  catch(e)
					  {
							value.params = {};
					  }
				}

				if (icon != '' || showTitle == true)
				{
					widgetHeadingHtml = '<div class="widget-title panel-heading">';

					if (icon != '')
					{
						widgetHeadingHtml += '<span class="' + icon + '" aria-hidden="true"></span> ';
					}

					if (showTitle == true)
					{
						widgetHeadingHtml += '<b>' + value.title + '</b>';
					}

					widgetHeadingHtml += '<span id="view-all-' + value.dashboard_widget_id + '" class="pull-right"></span></div>';
				}

				jQuery('<div class="col-xs-' +value.size+'"><div class="widget-data panel '+colorClass+'">' + widgetHeadingHtml + '<div data-dashboard-widget-id="'+value.dashboard_widget_id+'" id="dashboard-widget-'+value.dashboard_widget_id+'" class=""></div></div></div>').appendTo('.dashboard-widget-row-'+j);

				TJDashboardUI.initWidget(value);
				i++;
				divSpan = parseInt(divSpan) + parseInt(value.size);

				if (divSpan === 12 && response.data.widget_data.length !== i)
				{
					j++;
					jQuery('</div><div class="row dashboard-widget-row-'+j+'">').appendTo('.tjdashboard');
					divSpan = 0;
				}

				if (response.data.widget_data.length === i)
				{
					jQuery('</div>').appendTo('.tjdashboard');
				}
			});

			return true;
		});
	},

	initWidget : function(widgetData){
		/** global: TJDashboardService */

		var promise = TJDashboardService.getWidget(widgetData.dashboard_widget_id);
		promise.done(function(response) {

			if(!response.data.dashboard_widget_id)
			{
				jQuery('<div class="alert alert-info">' + Joomla.JText._("COM_TJDASHBOARD_NO_DATA_AVAILABLE_MESSAGE") + '</div>').appendTo('#dashboard-widget-'+widgetData.dashboard_widget_id);
				return false;
			}

			if (!TJDashboardUI._validWidget(response.data.widget_render_data) || response.data.widget_render_data.length==0)
			{
				jQuery('<div class="alert alert-info">' + Joomla.JText._("COM_TJDASHBOARD_NO_DATA_AVAILABLE_MESSAGE") + '</div>').appendTo('#dashboard-widget-'+response.data.dashboard_widget_id);
				return false;
			}

			jQuery(window).trigger('resize');
			var sourceData = [];
			sourceData['element'] = 'dashboard-widget-'+response.data.dashboard_widget_id;
			sourceData['data'] = response.data.widget_render_data;
			sourceData['params'] = widgetData.params;

			var redererDetail = response.data.renderer_plugin.split(".");
			var library = redererDetail[0];
			var method = redererDetail[1];

			if ((!sourceData) && (!response.data.renderer_plugin))
			{
				return false;
			}
			var linkArray  =  [];
			var linkArrayCount = 0;
			var renderData = JSON.parse(sourceData['data']);
			var showLinks ='';

			if (renderData.links)
			{
				linkArrayCount = renderData.links.length;
				linkArray  = renderData.links;
				for(var cnt=0;cnt<linkArrayCount;cnt++)
				{
					 showLinks = showLinks + ' <a href="'+linkArray[cnt].link +'">'+linkArray[cnt].title+'</a> ';
				}
				jQuery("#view-all-"+response.data.dashboard_widget_id).replaceWith('<span id="view-all-'+response.data.dashboard_widget_id+'" class="pull-right">'+showLinks+'</span>');
			}

			var libraryClassName = 'TJDashboard'+TJDashboardUI._jsUcFirst(library);
			TJDashboardUI._addCssFiles(response.data.widget_css);

			/*The rendering of the widget itself is done in the below
			method. Later the rendering might be decoupled from
			loading of the JS*/
			TJDashboardUI._addJsFiles(response.data.widget_js,method,sourceData,libraryClassName);

			return true;
		});
	},

	_addCssFiles: function(cssObj){
		jQuery.each(cssObj,function(index,value){
			var style = document.createElement('link');
			style.href = value;
			style.type = 'text/css';
			style.rel = 'stylesheet';
			if(jQuery.find("link [href='"+value+"']").length==0){
				jQuery('head').append(style);
			}
		});
	},

	_addJsFiles: function(jsObj,method,sourceData,libraryClassName){
		jQuery.each(jsObj,function(index,value){
			jQuery.getScript(value, function() {
			   window[libraryClassName].renderData(method,sourceData);
			});
		});
	},

	_validWidget: function (widgetJson) {
		try {
			JSON.parse(widgetJson);
		} catch (e) {
			return false;
		}
    return true;
	},

	_jsUcFirst: function(library)
	{
		return library.charAt(0).toUpperCase() + library.slice(1);
	},

	_setRenderers: function()
	{
		var selectedDataPlugin = jQuery('#jform_data_plugin').val();
		var defaultValue = jQuery('#jform_renderer_plugin').val();
		/** global: TJDashboardService */
		var promise = TJDashboardService.getRenderers(selectedDataPlugin);
		jQuery('#jform_renderer_plugin').replaceWith('<select id="jform_renderer_plugin" name="jform[renderer_plugin]" class="required" required="required" aria-required="true"><option value="">' + Joomla.JText._("COM_TJDASHBOARD_WIDGET_FORM_RENDERER_PLUGIN") + '</option></select>');
		jQuery('#jform_renderer_plugin').find('option').not(':first').remove();
		promise.done(function(response) {

			// Append option to plugin dropdown list.
			var list = jQuery("#jform_renderer_plugin");
			/** global: Option */
			jQuery.each(response.data, function(index, item) {
				list.append(new Option(item,index));
			});
			jQuery('#jform_renderer_plugin').val(defaultValue);
		});

		var widgetId      = jQuery('#jform_dashboard_widget_id').val();
		var promiseParams = TJDashboardService.getWidgetParams(selectedDataPlugin,widgetId);
		promiseParams.done(function(response) {
			jQuery('#jform_params').val(response.data);
		});
	},

	_setSize:function() {
		var defaultValue = jQuery('#jform_size').val();
		jQuery('#jform_size').replaceWith('<select id="jform_size" name="jform[size]" class="inputbox required" required="required" aria-required="true"><option value="">Select Size</option><option value="12">' + Joomla.JText._("COM_TJDASHBOARD_WIDGET_FORM_FULL_WIDTH") + '</option><option value="6">' + Joomla.JText._("COM_TJDASHBOARD_WIDGET_FORM_HALF_WIDTH") + '</option><option value="4">' + Joomla.JText._("COM_TJDASHBOARD_WIDGET_FORM_ONE_THIRD_WIDTH") + '</option><option value="3">' + Joomla.JText._("COM_TJDASHBOARD_WIDGET_FORM_ONE_FOURTH_WIDTH") +'</option></select>');
		jQuery('#jform_size').val(defaultValue);
	}

}
