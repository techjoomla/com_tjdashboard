<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tjdashboard
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  2017 Techjoomla
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');
$app = JFactory::getApplication();
$input = $app->input;
$document = JFactory::getDocument();
$script = 'var root_url = "' . Juri::root() . '";';
$document->addScriptDeclaration($script, 'text/javascript');
$document->addScript('components/com_tjdashboard/assets/js/tjdashContentService.js');
$document->addScript('components/com_tjdashboard/assets/js/tjdashContentUI.js');
// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "widget.cancel" || document.formvalidator.isValid(document.getElementById("widget-form")))
		{
			jQuery("#permissions-sliders select").attr("disabled", "disabled");
			Joomla.submitform(task, document.getElementById("widget-form"));
		}
	};
');
?>
<div class="">
	<form action="<?php echo JRoute::_('index.php?option=com_tjdashboard&view=dashboard&layout=edit&dashboard_widget_id=' . (int) $this->item->dashboard_widget_id, false);?>" method="post" enctype="multipart/form-data" name="adminForm" id="widget-form" class="form-validate tjdashForm">
		<div class="form-horizontal">
		<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php 
						echo $this->form->getLabel('dashboard_id'); 
						echo $this->form->getInput('dashboard_id'); 
						echo $this->form->getLabel('state'); 
						echo $this->form->getInput('state'); 

						echo $this->form->getLabel('data_plugin'); 
						echo $this->form->getInput('data_plugin'); 

						echo $this->form->getLabel('renderer_plugin'); 
						echo $this->form->getInput('renderer_plugin'); 

						echo $this->form->getLabel('size'); 
						echo $this->form->getInput('size'); 

						echo $this->form->getLabel('autorefresh'); 
						echo $this->form->getInput('autorefresh'); 

						echo $this->form->getLabel('params'); 
						echo $this->form->getInput('params');

						echo $this->form->getLabel('created_by'); 
						echo $this->form->getInput('created_by'); 
						echo $this->form->getInput('ordering'); 
						echo $this->form->getInput('modified_on'); 
						echo $this->form->getInput('modified_by'); 
						echo $this->form->getInput('checked_out'); 
						echo $this->form->getInput('checked_out_time');
						?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<?php
				//echo $this->form->renderFieldset();
		?>
		<input type="hidden" id="task" name="task" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	</form>
</div>

