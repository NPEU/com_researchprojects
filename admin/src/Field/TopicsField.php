<?php
namespace NPEU\Component\Researchprojects\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\Database\DatabaseInterface;

use NPEU\Component\Researchprojects\Administrator\Helper\ResearchprojectsHelper;

defined('_JEXEC') or die;


#JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of topics.
 */
class TopicsField extends ListField
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Topics';


    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     * @since  4.0.0
     */
    protected $layout = 'joomla.form.field.list-fancy-select';

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
        . 'data-custom_group_text="' . Text::_('COM_RESEARCHPROJECTS_CONFIG_TOPICS_GROUP_LABEL') . '" '
        . 'data-no_results_text="' . Text::_('COM_RESEARCHPROJECTS_CONFIG_TOPICS_ADD_NEW') . '"';
        #. 'data-placeholder="' . Text::_('COM_RESEARCHPROJECTS_CONFIG_TOPICS_TYPE_SELECT') . '"';

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
        $db = Factory::getDBO();
        $q  = 'SELECT topic FROM `#__researchprojects_topics`;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            throw new GenericDataException($db->stderr(), 500);
            return false;
        }

        $topics = $db->loadColumn();

        $i = 0;
        foreach ($topics as $topic) {
            $val = $topic;
            $options[] = HTMLHelper::_('select.option', $val, $val);
            $i++;
        }

        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = Text::_('COM_RESEARCHPROJECTS_CONFIG_TOPICS_EMPTY');
        }
        return $options;
    }

    /**
     * Method to get the field option groups.
     *
     * @return  array  The field option objects as a nested array in groups.
     */
    /*protected function getGroups()
    {
        $groups = array();

        // Add the staff group:
        $groups['NPEU Staff'] = array();

        $db = Factory::getDBO();
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

        $i = 0;
        foreach ($staff_members as $staff_member) {
            $groups['NPEU Staff'][] = HTMLHelper::_('select.option', $staff_member['id'], $staff_member['name']);
            $i++;
        }

        /*if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = Text::_('COM_SITEAREAS_CONTACT_DEFAULT_NO_STAFF');
        }
        return $options;*


        // Merge any additional groups in the XML definition.
        $groups = array_merge(parent::getGroups(), $groups);

        return $groups;
    }*/
}