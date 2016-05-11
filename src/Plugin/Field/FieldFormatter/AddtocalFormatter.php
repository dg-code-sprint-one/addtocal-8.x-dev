<?php

/**
 * @file
 * Contains \Drupal\addtocal\Plugin\Field\FieldFormatter\AddtocalFormatter.
 */

namespace Drupal\addtocal\Plugin\Field\FieldFormatter;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Plugin implementation of the 'Add to Cal' formatter.
 *
 * @FieldFormatter(
 *   id = "addtocal",
 *   label = @Translation("Add to Cal"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class AddtocalFormatter extends FormatterBase {
/**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'location_field' => '',
      'description_field' => '',
    ) + parent::defaultSettings();
  }


  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
   
  //$bundle= $form['#bundle'];
  $element = parent::settingsForm($form, $form_state);
  $dec_option = array();
  $loc_option = array();

  $entity_type_id = 'node';
  $bundle = $form['#bundle'];
  foreach (\Drupal::entityManager()->getFieldDefinitions($entity_type_id, $bundle) as $field_name => $field_definition) {
    if (!empty($field_definition->getTargetBundle())) {
      $bundleFields[$entity_type_id][$field_name]['type'] = $field_definition->getType();
      $bundleFields[$entity_type_id][$field_name]['label'] = $field_definition->getLabel();
      if($bundleFields[$entity_type_id][$field_name]['type'] == 'text_with_summary'){
        //array_push($dec_option, $bundleFields[$entity_type_id][$field_name]['label']);
        $label = $bundleFields[$entity_type_id][$field_name]['label'];
        $dec_option[$label] = $label;
       
      }
      if($bundleFields[$entity_type_id][$field_name]['type'] == 'string'){
        //array_push($loc_option, $bundleFields[$entity_type_id][$field_name]['label']);
        $label = $bundleFields[$entity_type_id][$field_name]['label'];
        $loc_option[$label] = $label;
        
      }

    }
  }
  
  $element['location_field'] = array(
    '#title' => t('Location Field:'),
    '#type' => 'select',
    '#options' => $loc_option,
    '#default_value' => $this->getSetting('location_field'),
    '#empty_option' => t('-None-'),
    '#description' => 'A field to use as the location for calendar events.',
    '#weight' => 0,
  );

  $element['description_field'] = array(
    '#title' => t('Description Field:'),
    '#type' => 'select',
    '#options' => $dec_option,
    '#default_value' => $this->getSetting('description_field'),
    '#empty_option' => t('-None-'),
    '#description' => 'A field to use as the description for calendar events.<br />The contents used from this field will be truncated to 1024 characters.',
    '#weight' => 1,
  );

  $element['past_events'] = array(
    '#title' => t('Show Add to Cal widget for Past Events'),
    '#type' => 'checkbox',
    '#default_value' => $this->getSetting('past_events'),
    '#description' => 'Show the widget for past events.',
    '#weight' => 2,
  );

    return $element;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

  $loc_value=$this->getSetting('location_field');
  $dec_value=$this->getSetting('description_field');
  $node_id=$items->getEntity()->id();
    foreach ($items as $delta => $item) {  
      // Render each element as markup.
      $element[$delta] = array(
        '#type' => 'markup',
        '#markup' => $item->value,
        //'#theme' => 'addtocalformatter',
      );
      $element[$delta][$addtocaltheme] = array(
      //$element[$delta] = array(
        '#theme' => 'addtocalformatter',
        '#loclab' => $loc_value,
        '#nid'   => $node_id,
        '#declab' => $dec_value,

      );
    }


    return $element;
  }



 /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
 $summary = parent::settingsSummary();

 $f1 = $this->getSetting('location_field');


    $summary = array();
    $settings = $this->getSettings();

    $summary[] = t('Displays the add to cal:@effect', array('@effect' => $this->getSetting('location_field')));

    return $summary;
  }

/**
   * Return value to controller.
   */

public function selectedvalue() {
  $loc_value=$this->getSetting('location_field');
  $dec_value=$this->getSetting('description_field');
  $select=array(
    '#loc_value' =>$loc_value,
    '#dec_value' =>$dec_value,
  );

  return $select; 

}

}



