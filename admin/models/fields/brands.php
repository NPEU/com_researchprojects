<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_siteareas
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of brands.
 */
class JFormFieldBrands extends JFormFieldList
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
        $db = JFactory::getDBO();
        $q  = 'SELECT id, name FROM #__brands WHERE catid = 151 ORDER BY name';


        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $brands = $db->loadAssocList();

        $i = 0;
        foreach ($brands as $brand) {
            $options[] = JHtml::_('select.option', $brand['id'], $brand['name']);
            $i++;
        }

        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = JText::_('COM_SITEAREAS_BRAND_DEFAULT_NO_BRANDS');
        }
        return $options;
    }
}