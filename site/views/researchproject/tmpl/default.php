<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/administrator/components/com_researchprojects/helpers/researchprojects.php';
// Set page title
$page_title = $this->item->title;

$skip = array(
    'id',
    'alias',
    'title',
    'state'
);

function format_person($p) {
    $pp = ResearchProjectsHelper::parseCollaborator($p);
    return $pp['first_name'] . ' ' . $pp['last_name'] . (empty($pp['institution']) ? '' : ' (' . $pp['institution'] .')');
}

foreach ($this->form->getFieldsets() as $name => $fieldset): ?>
<?php /*<h2><?php echo JText::_($fieldset->label); ?></h2>*/ ?>
<dl>
    <?php foreach ($this->form->getFieldset($name) as $field) :
    $field_name  = $field->fieldname;
    $field_value = $field->value;
    if (in_array($field_name, $skip) || empty($field_value)) {continue;} ?>
    <dt><?php echo JText::_($field->getAttribute('label')); ?></dt>
    <dd><?php #echo '<pre>'; var_dump($field_value); echo '</pre>';
    if ($field_name == 'owner_user_id') {
        echo $this->item->owner_details->name;
    } elseif ($field_name == 'pi_1') {
        echo format_person($field_value);
    } elseif ($field_name == 'topics') {
        $i = 0;
        $c = count($this->item->topic_details) - 1;
        foreach($this->item->topic_details as $topic) {
            echo $topic . ($i <  $c ? ', ' : '');
            $i++;
        }
    } elseif ($field_name == 'collaborators') {
        $i = 0;
        $c = count($field_value) - 1;
        foreach($field_value as $collaborator) {
            echo format_person($collaborator['collaborator']) . ($i <  $c ? ', ' : '');
            $i++;
        }
    } elseif ($field_name == 'funders') {
        $i = 0;
        $c = count($field_value) - 1;
        foreach($field_value as $funder) {
            echo $funder['funder'] . ($i <  $c ? ', ' : '');
            $i++;
        }
    } elseif ($field_name == 'brand_id') {
        echo '<a href="' . $this->item->brand_details->alias . '"><img src="' . $this->item->brand_details->logo_svg_path . '" onerror="this.src=\'' . $this->item->brand_details->logo_png_path . '\'; this.onerror=null;" alt="Logo: NPEU CTU" height="80"></a>';
    } else {
        echo $field_value;
    }
    ?></dd>
    <?php endforeach; ?>
</dl>
<?php endforeach; ?>

<p>
    <a href="<?php echo JRoute::_('index.php?option=com_researchprojects'); ?>">Back</a>
</p>
