<?php

namespace app\modules\config\models;

use app\modules\config\helpers\ApplicationConfigWriter;
use Yii;
use yii\base\Model;
use yii\helpers\StringHelper;

/**
 * Abstract class for configurable models of configurable modules.
 * @package app\models
 */
abstract class BaseConfigurableModel extends Model
{
    /**
     * @var string module name, filled by Configurable
     */
    private $module = '';

    /**
     * Setter for $module
     * @param string $module module name
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * Get module name
     * @param $module
     * @return string
     */
    public function getModule($module)
    {
        return $this->module;
    }

    /**
     * Fills model attributes with default values
     * @return void
     */
    abstract public function defaultValues();

    /**
     * Returns array of module configuration that should be stored in application config.
     * Array should be ready to merge in app config.
     * Used both for web only.
     *
     * @return array
     */
    abstract public function webApplicationAttributes();

    /**
     * Returns array of module configuration that should be stored in application config.
     * Array should be ready to merge in app config.
     * Used both for console only.
     *
     * @return array
     */
    abstract public function consoleApplicationAttributes();

    /**
     * Returns array of module configuration that should be stored in application config.
     * Array should be ready to merge in app config.
     * Used both for web and console.
     *
     * @return array
     */
    abstract public function commonApplicationAttributes();

    /**
     * Returns array of key=>values for configuration.
     *
     * @return mixed
     */
    abstract public function keyValueAttributes();

    /**
     * The name of event that is triggered when this configuration is being saved.
     * The event will be triggered before model validation proceeds and after model is loaded with user-input.
     *
     * @return string Configuration save event name
     */
    public function configurationSaveEvent()
    {
        return StringHelper::basename(get_class($this)) . 'ConfigurationSaveEvent';
    }

    /**
     * Loads state from file
     * @return bool result
     */
    public function loadState()
    {
        $this->defaultValues();
        $filename = Yii::getAlias('@app/config/configurables-state/' . $this->module . '.php');
        if (is_readable($filename) === true) {
            $this->setAttributes(
                include($filename),
                false
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Saves state to file
     * @return bool
     */
    public function saveState()
    {
        $filename = Yii::getAlias('@app/config/configurables-state/' . $this->module . '.php');

        $writer = new ApplicationConfigWriter([
            'filename' => $filename,
        ]);
        $writer->configuration = $this->getAttributes();
        return $writer->commit();


    }
}