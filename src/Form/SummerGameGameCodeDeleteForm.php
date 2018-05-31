<?php

/**
 * @file
 * Contains \Drupal\accountfix\Form\SummerGameGameCodeDeleteForm
 */

namespace Drupal\summergame\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SummerGameGameCodeDeleteForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'summergame_game_code_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $code_id = 0) {
    $code_id = (int) $code_id;
    $db = \Drupal::database();
    $form = [];

    $game_code = $db->query("SELECT * FROM sg_game_codes WHERE code_id = $code_id")->fetchAssoc();

    if ($game_code['code_id']) {
      $form['code_id'] = [
        '#type' => 'value',
        '#value' => $game_code['code_id'],
      ];

      $form['warning'] = [
        '#markup' => '<h2>Are you sure you want to delete the game code ' . $game_code['text'] . '?</h2>' .
                     '<p>This action cannot be undone.</p>',
      ];

      $form['inline'] = [
        '#prefix' => '<div class="container-inline">',
        '#suffix' => '</div>',
      ];
      $form['inline']['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Delete'),
      );
      $form['inline']['cancel'] = [
        '#type' => 'link',
        '#title' => $this->t('Cancel'),
        '#url' => \Drupal\Core\Url::fromRoute('summergame.admin'),
      ];
    }
    else {
      drupal_set_message('Invalid Game Code ID', 'error');
      return $this->redirect('summergame.admin');
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $db = \Drupal::database();
    $db->delete('sg_game_codes')->condition('code_id', $form_state->getValue('code_id'))->execute();

    drupal_set_message('Game Code has been deleted.');

    $form_state->setRedirect('summergame.admin');

    return;
  }
}