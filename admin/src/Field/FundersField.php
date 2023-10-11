<?php
namespace NPEU\Component\Researchprojects\Administrator\Field;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Field\ComboField;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;

use NPEU\Component\Researchprojects\Administrator\Helper\ResearchprojectsHelper;


defined('_JEXEC') or die;

#JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of funders.
 */
class FundersField extends ComboField
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

        $attr = 'class="form-select form-control" '
        . 'data-custom_group_text="' . Text::_('COM_RESEARCHPROJECTS_FUNDER_GROUP_LABEL') . '" '
        . 'data-no_results_text="' . Text::_('COM_RESEARCHPROJECTS_FUNDER_ADD_NEW') . '"';


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
        $q  = 'SELECT funder FROM `#__researchprojects_funders` ORDER BY funder;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            throw new GenericDataException($db->stderr(), 500);
            return false;
        }

        $funders = $db->loadColumn();

        $i = 0;
        foreach ($funders as $funder) {
            $val = $funder;
            $options[] = HTMLHelper::_('select.option', $val, str_replace(',', '&#65104;', $val));
            $i++;
        }

        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = Text::_('COM_RESEARCHPROJECTS_FUNDER_EMPTY');
        }
        return $options;
    }
}