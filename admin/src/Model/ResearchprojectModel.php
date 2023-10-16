<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Administrator\Model;

defined('_JEXEC') or die;


#use Joomla\CMS\Form\Form;
#use Joomla\CMS\Helper\TagsHelper;
#use Joomla\CMS\Language\Associations;
#use Joomla\CMS\Language\LanguageHelper;
#use Joomla\CMS\UCM\UCMType;
#use Joomla\CMS\Versioning\VersionableModelTrait;
#use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
#use Joomla\String\StringHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Registry\Registry;



/**
 * Researchproject Model
 */
class ResearchprojectModel extends AdminModel
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\Table\Table  A \Joomla\CMS\Table\Table object
     */
    /*public function getTable($type = 'Researchprojects', $prefix = 'ResearchprojectsTable', $config = array())
    {
        return \Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
    }*/

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_researchprojects.researchproject',
            'researchproject',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            return false;
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data)) {
            // Disable fields for display.
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('state', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_researchprojects.edit.researchproject.data',
            array()
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            /*// Convert the metadata field to an array.
            $registry = new Registry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();

            // Convert the images field to an array.
            $registry = new Registry;
            $registry->loadString($item->images);
            $item->images = $registry->toArray();

            if (!empty($item->id)) {
                $item->tags = new JHelperTags;
                $item->tags->getTagIds($item->id, 'com_weblinks.weblink');
                $item->metadata['tags'] = $item->tags;
            }*/
            #echo '<pre>'; var_dump($item); echo '</pre>'; exit;
            #echo '<pre>'; var_dump($item->topics); echo '</pre>'; exit;
            if (!empty($item->topics)) {
                $registry = new Registry;
                $registry->loadObject($item->topics);
                $item->topics = $registry->toArray();
                #echo '<pre>'; var_dump($item->topics); echo '</pre>'; exit;

                // we need to convert the topics field to an array so the form select displays the
                // correct values, but we don't want to lose the titles, so copying it over:
                $item->topic_details = $item->topics;
                $item->topics = array_keys($item->topics);
            }
            // Convert the collaborators field to an array.
            /*echo '<pre>'; var_dump($item->collaborators); echo '</pre>'; exit;
            $registry = new Registry;
            $registry->loadString($item->collaborators);
            $item->collaborators = $registry->toArray();

            // Convert the funders field to an array.
            $registry = new Registry;
            $registry->loadString($item->funders);
            $item->funders = $registry->toArray();*/
        }

        return $item;
    }



    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param   \Joomla\CMS\Table\Table  $table  A reference to a \Joomla\CMS\Table\Table object.
     *
     * @return  void
     */
    protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->alias = ApplicationHelper::stringURLSafe($table->alias);

        if (empty($table->alias)) {
            $table->alias = ApplicationHelper::stringURLSafe($table->title);
        }

        $table->modified    = $date->toSql();
        $table->modified_by = $user->id;

        if (empty($table->id)) {
            $table->created    = $date->toSql();
            $table->created_by = $user->id;
        }

        /*if (empty($table->id)) {
            // Set the values

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db    = $this->getDbo();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from($db->quoteName('#__weblinks'));

                $db->setQuery($query);
                $max = $db->loadResult();

                $table->ordering = $max + 1;
            } else {
                // Set the values
                $table->modified    = $date->toSql();
                $table->modified_by = $user->id;
            }
        }

        // Increment the weblink version number.
        $table->version++;*/
    }

    /**
     * Method to prepare the saved data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    public function save($data)
    {
        $is_new = empty($data['id']);
        $app    = Factory::getApplication();
        $input  = $app->input;
        $db     = Factory::getDBO();


        // Get parameters:
        #$params = \Joomla\CMS\Component\ComponentHelper::getParams(JRequest::getVar('option'));
        $params = \Joomla\CMS\Component\ComponentHelper::getParams($input->get('option'));

        // For reference if needed:
        // By default we're only looking for and acting upon the 'email admins' setting.
        // If any other settings are related to this save method, add them here.
        /*$email_admins_string = $params->get('email_admins');
        if (!empty($email_admins_string) && $is_new) {
            $email_admins = explode(PHP_EOL, trim($email_admins_string));
            foreach ($email_admins as $email) {
                // Sending email as an array to make it easier to expand; it's quite likely that a
                // real app would need more info here.
                $email_data = array('email' => $email);
                $this->_sendEmail($email_data);
            }
        }*/

        // Special handling for pis and collaborators. We need to add to the collaborators table all new
        // values.
        // Pillay, Thillagavathie (Royal Wolverhampton NHS Trust)
        // Building something like this query:
        // REPLACE INTO `#__researchprojects_collaborators` VALUES ('Chris Gale (Imperial)'), ('Alex Heazell (Manchester)'), ('Tim Smith');
        $new_investigators = array();

        if (!empty($data['pi_1'])) {
            // If not NPEU: e.g. Fiona Alderdice (NPEU)
            if (!preg_match('/\(NPEU\)$/', $data['pi_1'])) {
                $new_investigators[] = '("' . md5($data['pi_1']) . '", "' . $data['pi_1'] . '")';
            }
        }

        if (!empty($data['pi_2'])) {
            // If not NPEU: e.g. Fiona Alderdice (NPEU)
            if (!preg_match('/\(NPEU\)$/', $data['pi_2'])) {
                $new_investigators[] = '("' . md5($data['pi_2']) . '", "' . $data['pi_2'] . '")';
            }
        }

        if (!empty($data['collaborators'])) {
            foreach ($data['collaborators'] as $key => $value) {
                if (empty($value['collaborator'])) {
                    unset($data['collaborators'][$key]);
                    continue;
                }
                // If not NPEU: e.g. Fiona Alderdice (NPEU)
                if (preg_match('/\(NPEU\)$/', $value['collaborator'])) {
                    continue;
                }
                $new_investigators[] = '("' . md5($value['collaborator']) . '", "' . $value['collaborator'] . '")';
            }
        }

        if (!empty($new_investigators)) {
            $q = 'REPLACE INTO `#__researchprojects_collaborators` VALUES ' . implode(", ", $new_investigators) . ';';
            $db->setQuery($q);
            if (!$db->execute($q)) {
                throw new GenericDataException($db->stderr(), 500);
                return false;
            }
        }

        // Special handling for funders. We need to add to the funders table all new values.
        $new_funders = array();
        if (!empty($data['funders']['funders0']['funder'])) {
            foreach ($data['funders'] as $key => $value) {
                // If not NPEU: e.g. Fiona Alderdice (NPEU)
                if (preg_match('/\(NPEU\)$/', $value['funder'])) {
                    continue;
                }
                $new_funders[] = '("' . md5($value['funder']) . '", "' . $value['funder'] . '")';
            }
        }
        if (!empty($new_funders)) {
            $q = 'REPLACE INTO `#__researchprojects_funders` VALUES ' . implode(", ", $new_funders) . ';';
            $db->setQuery($q);
            if (!$db->execute($q)) {
                throw new GenericDataException($db->stderr(), 500);
                return false;
            }
        }

        // Alter the title for save as copy
        if ($app->input->get('task') == 'save2copy') {
            list($title, $alias) = $this->generateNewTitle(false, $data['alias'], $data['title']);
            $data['title']    = $title;
            $data['alias']    = $alias;
            $data['state']    = 0;
        }

        // Automatic handling of alias for empty fields
        // Taken from com_content/models/article.php
        if (in_array($input->get('task'), array('apply', 'save', 'save2new'))) {
            if (empty($data['alias'])) {
                if (Factory::getConfig()->get('unicodeslugs') == 1) {
                    $data['alias'] = \Joomla\CMS\Filter\OutputFilter::stringURLUnicodeSlug($data['title']);
                } else {
                    $data['alias'] = \Joomla\CMS\Filter\OutputFilter::stringURLSafe($data['title']);
                }

                $table = $this->getMVCFactory()->createTable('Researchproject', 'Administrator');

                if ($table->load(array('alias' => $data['alias']))) {
                    $msg = \Joomla\CMSanguage\Text::_('COM_CONTENT_SAVE_WARNING');
                }

                list($title, $alias) = $this->generateNewTitle(false, $data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg)) {
                    Factory::getApplication()->enqueueMessage($msg, 'warning');
                }
            }
        }

        return parent::save($data);
    }

    /**
     * Method to change the title & alias.
     *
     * @param   integer  $category_id  The id of the parent.
     * @param   string   $alias        The alias.
     * @param   string   $name         The title.
     *
     * @return  array  Contains the modified title and alias.
     */
    protected function generateNewTitle($category_id, $alias, $name)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias))) {
            if ($name == $table->title) {
                $name = \Joomla\String\StringHelper::increment($name);
            }

            $alias = \Joomla\String\StringHelper::increment($alias, 'dash');
        }

        return array($name, $alias);
    }

    /**
     * Copied from libraries/src/MVC/Model/AdminModel.php because it uses a hard-coded field name:
     * catid.
     *
     * Method to change the title & alias.
     *
     * @param   string   $alias        The alias.
     * @param   string   $title        The title.
     *
     * @return  array  Contains the modified title and alias.
     */
    /*protected function generateNewResearchprojectsTitle($alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias)))
        {
            $title = StringHelper::increment($title);
            $alias = StringHelper::increment($alias, 'dash');
        }

        return array($title, $alias);
    }*/


    /**
     * Method to get the script that have to be included on the form
     *
     * @return string   Script files
     */
    /*public function getScript()
    {
        #return 'administrator/components/com_researchprojects/models/forms/researchprojects.js';
        return '';
    }*/

    /**
     * Delete this if not needed. Here for reference.
     * Method to get the data that should be injected in the form.
     *
     * @return  bool  Email success/failed to send.
     */
    /*private function _sendEmail($email_data)
    {
            $app        = Factory::getApplication();
            $mailfrom   = $app->getCfg('mailfrom');
            $fromname   = $app->getCfg('fromname');
            $sitename   = $app->getCfg('sitename');
            $email      = \Joomla\String\StringHelperPunycode::emailToPunycode($email_data['email']);

            // Ref: Text::sprintf('LANG_STR', $var, ...);

            $mail = Factory::getMailer();
            $mail->addRecipient($email);
            $mail->addReplyTo($mailfrom);
            $mail->setSender(array($mailfrom, $fromname));
            $mail->setSubject(Text::_('COM_RESEARCHPROJECTS_EMAIL_ADMINS_SUBJECT'));
            $mail->setBody(Text::_('COM_RESEARCHPROJECTS_EMAIL_ADMINS_BODY'));
            $sent = $mail->Send();

            return $sent;
    }*/
}
