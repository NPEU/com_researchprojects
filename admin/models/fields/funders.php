<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of funders.
 */
class JFormFieldFunders extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Funders';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
        $input = parent::getInput();
        
        $attr = 'class="chzn-custom-value" '
        . 'data-custom_group_text="' . JText::_('COM_RESEARCHPROJECTS_FUNDER_GROUP_LABEL') . '" '
        . 'data-no_results_text="' . JText::_('COM_RESEARCHPROJECTS_FUNDER_ADD_NEW') . '"';

        
        $input = str_replace('<select', '<select ' . $attr,  $input);
        return $input;
    }
    
    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $options = array();
        $db = JFactory::getDBO();
        $q  = 'SELECT funder FROM `#__researchprojects_funders` ORDER BY funder;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $funders = $db->loadColumn();

        $i = 0;
        foreach ($funders as $funder) {
            $val = $funder;
            $options[] = JHtml::_('select.option', $val, $val);
            $i++;
        }
        
        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = JText::_('COM_RESEARCHPROJECTS_FUNDER_EMPTY');
        }
        return $options;
    }
}