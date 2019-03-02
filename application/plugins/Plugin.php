<?php

namespace app\plugins;

use app\helpers\Url;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\plugins
 *
 * @property $isEnabled bool
 */
class Plugin extends BaseObject
{
    /**
     * @var PluginInfo
     */
    private $pluginInfo;
    /**
     * @var string
     */
    private $pluginId;
    /**
     * @var bool
     */
    private $isEnabled;
    /**
     * @var string
     */
    private $path;

    /**
     * @param PluginInfo $pluginInfo
     */
    public function setPluginInfo($pluginInfo)
    {
        $this->pluginInfo = $pluginInfo;
    }

    /**
     * @param $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return string
     */
    public function getPluginId()
    {
        return $this->pluginId;
    }

    /**
     * @param $id
     */
    public function setPluginId($id)
    {
        $this->pluginId = $id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getInfoFile()
    {
        return $this->path . '/plugin.json';
    }

    /**
     * @return mixed|null
     */
    public function getImage()
    {
        return Url::to(['@web/content/plugins/' . $this->pluginId . '/plugin.png']);
    }

    /**
     * @return mixed|null
     */
    public function getNamespace()
    {
        return $this->pluginInfo->namespace;
    }

    /**
     * @return mixed|null
     */
    public function getTitle()
    {
        return $this->pluginInfo->title;
    }

    /**
     * @return mixed|null
     */
    public function getDescription()
    {
        return $this->pluginInfo->description;
    }

    /**
     * @return mixed|null
     */
    public function getAuthor()
    {
        return $this->pluginInfo->author;
    }

    /**
     * @return mixed|null
     */
    public function getWebsite()
    {
        return $this->pluginInfo->website;
    }

    /**
     * @return mixed|null
     */
    public function getVersion()
    {
        return $this->pluginInfo->version;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    public function getSetting($key, $default = null)
    {
        return Yii::$app->settings->get("plugin.{$this->pluginId}", $key, $default);
    }

    /**
     * Turn on plugin
     */
    public function enable()
    {
    }

    /**
     * Turn off plugin
     */
    public function disable()
    {
    }
}
