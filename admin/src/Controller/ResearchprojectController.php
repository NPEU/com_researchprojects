<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Versioning\VersionableControllerTrait;
use Joomla\CMS\Uri\Uri;


class ResearchprojectController extends FormController
{
    #use VersionableControllerTrait;

    /**
    * Implement to allowAdd or not
    *
    * Not used at this time (but you can look at how other components use it....)
    * Overwrites: JControllerForm::allowAdd
    *
    * @param array $data
    * @return bool
    */
    protected function allowAdd($data = array())
    {
        return parent::allowAdd($data);
    }
    /**
    * Implement to allow edit or not
    * Overwrites: JControllerForm::allowEdit
    *
    * @param array $data
    * @param string $key
    * @return bool
    */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
        if( !empty( $id ) )
        {
            return Factory::getApplication()->getIdentity()->authorise( "core.edit", "com_researchprojects.researchproject." . $id );
        }
    }

    /*public function batch($model = null)
    {
        $model = $this->getModel('researchproject');
        $this->setRedirect((string)Uri::getInstance());
        return parent::batch($model);
    }*/
}
