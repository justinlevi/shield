<?php

/**
 * Contains Drupal\shield\Form\ShieldForm
 */

namespace Drupal\shield\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ShieldForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'shield_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        // Form constructor
        $form = parent::buildForm($form, $form_state);
        // Default settings
        $config = $this->config('shield.settings');

        $form['description'] = array(
            '#type' => 'item',
            '#title' => t('Shield settings'),
            '#description' => t('Set up credentials for an authenticated user. You can also decide whether you want to print out the credentials or not.'),
        );

        $form['general'] = array(
            '#type' => 'fieldset',
            '#title' => t('General settings'),
        );

        $form['general']['shield_allow_cli'] = array(
            '#type' => 'checkbox',
            '#title' => t('Allow command line access'),
            '#description' => t('When the site is accessed from command line (e.g. from Drush, cron), the shield should not work.'),
            '#default_value' => $config->get('shield.allow_cli', 1),
        );

        $form['credentials'] = array(
            '#type' => 'fieldset',
            '#title' => t('Credentials'),
        );

        $form['credentials']['shield_user'] = array(
            '#type' => 'textfield',
            '#title' => t('User'),
            '#default_value' => $config->get('shield.user', ''),
            '#description' => t('Live it blank to disable authentication.')
        );

        $form['credentials']['shield_pass'] = array(
            '#type' => 'textfield',
            '#title' => t('Password'),
            '#default_value' => $config->get('shield.pass', ''),
        );

        $form['shield_print'] = array(
            '#type' => 'textfield',
            '#title' => t('Authentication message'),
            '#description' => t("The message to print in the authentication request popup. You can use [user] and [pass] to print the user and the password respectively. You can leave it empty, if you don't want to print out any special message to the users."),
            '#default_value' => $config->get('shield.print', 'Hello, user: [user], pass: [pass]!'),
        );

        return $form;
    }

    /**
     * {@inheritdoc}.
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {

    }

    /**
     * {@inheritdoc}.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('shield.settings');
        $config->set('shield.allow_cli', $form_state->getValue('allow_cli'));
        $config->set('shield.user', $form_state->getValue('user'));
        $config->set('shield.pass', $form_state->getValue('pass'));
        $config->set('shield.print', $form_state->getValue('print'));
        $config->save();
        return parent::submitForm($form, $form_state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getEditableConfigNames()
    {
        return [
            'shield.settings',
        ];
    }
}