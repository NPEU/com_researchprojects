<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
#use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
#use NPEU\Component\Researchprojects\Site\Helper\ResearchprojectHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;

/**
 * Researchproject Component Model
 */
class ResearchprojectModel extends \NPEU\Component\Researchprojects\Administrator\Model\ResearchprojectModel {

    /**
     * @var object item
     */
    protected $item;

    protected $item_state;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return    void
     * @since    2.5
     */
    protected function populateState()
    {
        $app = Factory::getApplication();

        // Get the researchproject id
        $jinput = $app->input;
        $id     = $jinput->get('id', 1, 'INT');
        $this->setState('researchproject.id', $id);

        // Load the parameters.
        $this->setState('params', Factory::getApplication()->getParams());
        parent::populateState();
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        // Note we're using the form to provice assosiated labels to data fields for display.
        // To DRY use the admin form use:
        // JPATH_COMPONENT_ADMINISTRATOR . '/forms/researchproject.xml',
        // or if you need a separate site form, use:
        // JPATH_COMPONENT_SITE . '/forms/researchproject.xml',
        $form = $this->loadForm(
            'com_researchprojects.form',
            JPATH_COMPONENT_ADMINISTRATOR . '/forms/researchproject.xml',
            [
                'control' => 'jform',
                'load_data' => $loadData
            ]
        );

        if (empty($form)) {
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }

        return $form;
    }
}