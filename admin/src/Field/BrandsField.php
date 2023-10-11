<?php
namespace NPEU\Component\Researchprojects\Administrator\Field;

use Joomla\CMS\Form\Field\ListField;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;

use NPEU\Component\Researchprojects\Administrator\Helper\ResearchprojectsHelper;

defined('_JEXEC') or die;

#JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of brands.
 */
class BrandsField extends ListField
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Brands';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $options = array();
        $db = Factory::getDBO();
        $q  = 'SELECT id, name FROM #__brands WHERE catid = 171 ORDER BY name';


        $db->setQuery($q);
        if (!$db->execute($q)) {
            throw new GenericDataException($db->stderr(), 500);
            return false;
        }

        $brands = $db->loadAssocList();

        $i = 0;
        foreach ($brands as $brand) {
            $options[] = HTMLHelper_('select.option', $brand['id'], $brand['name']);
            $i++;
        }

        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = Text::_('COM_RESEARCHPROJECTS_BRAND_DEFAULT');
        }
        return $options;
    }
}