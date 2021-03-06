<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;


/*
    IMPORTANT = this field has an UNPROTECTED dependency on the FirstLastNames plugin.
    This extension will break of that's not installed and enabled.
*/


#JFormHelper::loadFieldClass('groupedlist');
JFormHelper::loadFieldClass('list');
require_once JPATH_ROOT . '/administrator/components/com_researchprojects/helpers/researchprojects.php';

/**
 * Form field for a list of collaborators.
 */
#class JFormFieldCollaborators extends JFormFieldGroupedList
class JFormFieldCollaborators extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Collaborators';

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
        . 'data-custom_group_text="' . JText::_('COM_RESEARCHPROJECTS_COLLABORATOR_GROUP_LABEL') . '" '
        . 'data-no_results_text="' . JText::_('COM_RESEARCHPROJECTS_COLLABORATOR_ADD_NEW') . '"';

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
        $collaborators = array();
        $options = array();
        $db = JFactory::getDBO();
        
        
        // Get Staff members:
        $q  = 'SELECT u.id, u.name, up1.profile_value AS first_name, up2.profile_value AS last_name FROM `#__users` u ';
        $q .= 'JOIN `#__user_usergroup_map` ugm ON u.id = ugm.user_id ';
        $q .= 'JOIN `#__usergroups` ug ON ugm.group_id = ug.id ';
        $q .= 'JOIN `#__user_profiles` up1 ON u.id = up1.user_id AND up1.profile_key = "firstlastnames.firstname"';
        $q .= 'JOIN `#__user_profiles` up2 ON u.id = up2.user_id AND up2.profile_key = "firstlastnames.lastname"';
        $q .= 'WHERE ug.title = "Staff" ';
        $q .= 'AND u.block = 0 ';
        $q .= 'ORDER BY last_name, first_name;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $staff_members = $db->loadAssocList();        
        foreach ($staff_members as $staff_member) {
            $val  = $staff_member['last_name'] . ', ' . $staff_member['first_name'] . ' (NPEU)';
            $text = $staff_member['last_name'] . ', ' . $staff_member['first_name'] . ' (NPEU)';
            #$text = $staff_member['name'] . ' (NPEU)';
            $collaborators[$val] = $text;
        }
        
        // Get non staff members:
        $q  = 'SELECT collaborator FROM `#__researchprojects_collaborators`;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $nonstaff_members = $db->loadColumn();
        foreach ($nonstaff_members as $nonstaff_member) {
            $val  = $nonstaff_member;
            $t = ResearchProjectsHelper::parseCollaborator($val);
            $text = (empty($t['last_name']) ? '' : $t['last_name'] . ', ') . $t['first_name'] . (empty($t['institution']) ? '' : ' (' . $t['institution'] .')') . (empty($t['url']) ? '' : ' [' . $t['url'] . ']');
            $collaborators[$val] = $text;
        }

        ksort($collaborators);
        
        $i = 0;
        foreach ($collaborators as $val => $text) {
            $options[] = JHtml::_('select.option', $val, $text);
            $i++;
        }
        

        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = JText::_('COM_RESEARCHPROJECTS_COLLABORATORS_EMPTY');
        }
        return $options;
    }
}