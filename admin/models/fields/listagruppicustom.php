<?php
/**
 * Created by IntelliJ IDEA.
 * User: Salma
 * Date: 30/01/2023
 * Time: 14:14
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

class JFormFieldlistagruppicustom extends JFormFieldList {

    /**
     * A flexible category list that respects access controls
     *
     * @var		string
     * @since	1.6
     */
    public $type = 'listagruppicustom';

    /**
     * Method to get a list of categories that respects access controls and can be used for
     * either category assignment or parent category assignment in edit screens.
     * Use the parent element to indicate that the field will be used for assigning parent categories.
     *
     * @return	array	The field option objects.
     * @since	1.6
     */
    protected function getOptions() {
        // Initialise variables.

        $options = new stdClass();
        $options->value="0";
        $options->text="CAMPO NON DISPONIBILE";

        try {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);


            $query->select('id as value, title as text');
            $query->from('#__usergroups as e');
            $query->where('id in (121,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143)');
            $query->order('id');

            // Get the options.
            $db->setQuery($query);

            $options = $db->loadObjectList();


        }catch (Exception $e )
        {
            return $options;
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}

