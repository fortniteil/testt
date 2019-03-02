<?php

namespace app\plugins;

use app\components\ConsoleRunner;
use app\events\PluginEvent;
use app\helpers\Migrations;
use Composer\Autoload\ClassLoader;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\plugins
 */
class PluginManager extends Component implements BootstrapInterface
{
    const EVENT_BEFORE_PLUGIN_ENABLE = 'onBeforePluginEnable';
    const EVENT_AFTER_PLUGIN_ENABLE = 'onAfterPluginEnable';
    const EVENT_BEFORE_PLUGIN_DISABLE = 'onBeforePluginDisable';
    const EVENT_AFTER_PLUGIN_DISABLE = 'onAfterPluginDisable';

    /**
     * @var string
     */
    public $pluginsDirectory = '@webroot/content/plugins';
    /**
     * @var string
     */
    public $pluginsRegistryFile = '@content/params/plugins.json';
    /**
     * @var Plugin[]
     */
    private $enabledPlugins = [];
    /**
     * @var Plugin[]
     */
    private $availablePlugins;
    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @param \yii\base\Application $app
     * @return bool
     * @throws Exception
     */
    public function bootstrap($app)
    {
        $pluginsDirectory = Yii::getAlias($this->pluginsDirectory);
        if (!is_dir($pluginsDirectory)) {
            FileHelper::createDirectory($pluginsDirectory);
            return false;
        }

        $plugins = $this->getEnabledPlugins();
        foreach ($plugins as $plugin) {
            $this->enabledPlugins[$plugin->getPluginId()] = $plugin;
            if ($plugin instanceof BootstrapInterface) {
                $plugin->bootstrap($app);
            }
        }

        return true;
    }

    /**
     * @param $pluginId
     * @return bool
     */
    public function isEnabled($pluginId)
    {
        return isset($this->enabledPlugins[$pluginId]);
    }

    /**
     * @param $pluginId
     * @return bool
     */
    public function isInstalled($pluginId)
    {
        $plugins = $this->getAvailablePlugins();
        return isset($plugins[$pluginId]);
    }

    /**
     * @param $pluginId
     * @return bool
     * @throws Exception
     */
    public function enablePlugin($pluginId)
    {
        if (isset($this->enabledPlugins[$pluginId])) {
            return false;
        }

        $plugin = $this->getInstalledPlugin($pluginId);

        try {
            $pluginEvent = new PluginEvent(['plugin' => $plugin]);
            $this->trigger(self::EVENT_BEFORE_PLUGIN_ENABLE, $pluginEvent);
            if (!$pluginEvent->isValid) {
                return false;
            }
            $plugin->enable();
            $this->enabledPlugins[$pluginId] = $plugin;
            $this->writePluginsRegistry();
            Migrations::run('up', Yii::getAlias("@content/plugins/$pluginId/migrations"));
            $this->trigger(self::EVENT_AFTER_PLUGIN_ENABLE, $pluginEvent);
        } catch (\Exception $exception) {
            Yii::error($exception->getMessage());
        }

        return true;
    }

    /**
     * @param $pluginId
     * @return bool
     */
    public function disablePlugin($pluginId)
    {
        if (!isset($this->enabledPlugins[$pluginId])) {
            return false;
        }

        $plugin = $this->enabledPlugins[$pluginId];
        $pluginEvent = new PluginEvent(['plugin' => $plugin]);
        $this->trigger(self::EVENT_BEFORE_PLUGIN_DISABLE, $pluginEvent);

        try {
            if (!$pluginEvent->isValid) {
                return false;
            }
            $plugin->disable();
            unset($this->enabledPlugins[$pluginId]);
            $this->writePluginsRegistry();
            Migrations::run('down', Yii::getAlias("@content/plugins/$pluginId/migrations"));
            $this->trigger(self::EVENT_AFTER_PLUGIN_DISABLE, $pluginEvent);
        } catch (\Exception $exception) {
            Yii::error($exception->getMessage());
        }

        return true;
    }

    /**
     * @return Plugin[]
     */
    public function getAvailablePlugins()
    {
        if (isset($this->availablePlugins)) {
            return $this->availablePlugins;
        }

        $subDirectories = FileHelper::findDirectories(Yii::getAlias($this->pluginsDirectory), ['recursive' => false]);
        $this->availablePlugins = [];

        foreach ($subDirectories as $directory) {
            $pluginId = basename($directory);
            $plugin = $this->getPlugin($pluginId, $this->getPluginInfo($pluginId, $directory . '/plugin.json'));
            if ($plugin !== null) {
                $plugin->setIsEnabled(isset($this->enabledPlugins[$pluginId]));
                $this->availablePlugins[$pluginId] = $plugin;
            }
        }

        return $this->availablePlugins;
    }

    /**
     * @param $pluginId
     * @return Plugin
     * @throws Exception
     */
    public function getInstalledPlugin($pluginId)
    {
        $availablePlugins = $this->getAvailablePlugins();

        if (!isset($availablePlugins[$pluginId])) {
            throw new Exception("Plugin '$pluginId' not found");
        }

        return $availablePlugins[$pluginId];
    }

    /**
     * @return Plugin[]
     */
    public function getEnabledPlugins()
    {
        $pluginsRegistryFile = Yii::getAlias($this->pluginsRegistryFile);
        if (!is_file($pluginsRegistryFile)) {
            return [];
        }

        try {
            $pluginsRegistry = file_get_contents($pluginsRegistryFile);
            $pluginsRegistry = json_decode($pluginsRegistry);
        } catch (\Exception $exception) {
            Yii::warning('Could not process plugins registry file');
            return [];
        }

        $plugins = [];
        foreach ($pluginsRegistry as $pluginId => $pluginRegistry) {
            $plugin = $this->getPlugin($pluginId, $this->getPluginInfo($pluginId, $this->getPluginPath($pluginId) . '/plugin.json'));
            if ($plugin !== null) {
                $plugin->setIsEnabled(true);
                $plugins[$pluginId] = $plugin;
            }
        }

        return $plugins;
    }

    /**
     * @param $pluginId
     * @return string|null
     */
    public function getPluginPath($pluginId)
    {
        $path = Yii::getAlias($this->pluginsDirectory . '/' . $pluginId);
        return is_dir($path) ? $path : null;
    }

    private function writePluginsRegistry()
    {
        $pluginsRegistryFile = Yii::getAlias($this->pluginsRegistryFile);
        $paramsDirectory = dirname($pluginsRegistryFile);

        if (!is_dir($paramsDirectory)) {
            FileHelper::createDirectory($paramsDirectory);
        }

        $registry = [];
        foreach ($this->enabledPlugins as $pluginId => $plugin) {
            $registry[$pluginId] = [
                'installedVersion' => $plugin->getVersion(),
                'namespace' => $plugin->getNamespace(),
            ];
        }

        file_put_contents($pluginsRegistryFile, json_encode($registry, JSON_PRETTY_PRINT));
    }

    /**
     * @param string $pluginId
     * @param PluginInfo $pluginInfo
     * @return Plugin|null
     */
    private function getPlugin($pluginId, $pluginInfo)
    {
        $pluginNamespace = $pluginInfo['namespace'] ?? false;
        if (!$pluginNamespace) {
            Yii::warning("'namespace' must be set in plugin.json");
            return null;
        }
        if (substr($pluginNamespace, -2) !== '\\') {
            $pluginNamespace .= '\\';
        }
        $this->getClassLoader()->addPsr4($pluginNamespace, $this->getPluginPath($pluginId));

        $pluginClass = $pluginInfo->className ?? false;
        if (!$pluginClass) {
            Yii::warning("'class' must be set in plugin.json");
            return null;
        }

        if (!class_exists($pluginClass)) {
            Yii::warning("Class $pluginClass doesn't exist");
            return null;
        }

        /** @var Plugin $plugin */
        $plugin = new $pluginClass;
        $plugin->setPluginId($pluginId);
        $plugin->setPluginInfo($pluginInfo);
        $plugin->setPath($this->getPluginPath($pluginId));

        return $plugin;
    }

    /**
     * @param $pluginId
     * @param $pluginInfoFile
     * @return array|mixed
     */
    private function getPluginInfo($pluginId, $pluginInfoFile)
    {
        if (is_file($pluginInfoFile)) {
            try {
                $pluginJson = file_get_contents($pluginInfoFile);
                $pluginJsonData = json_decode($pluginJson, true);
                $pluginInfo = new PluginInfo();
                $pluginInfo->setAttributes($pluginJsonData);
                $pluginInfo->alias = $pluginId;
                $pluginInfo->className = $pluginJsonData['class'];

                return $pluginInfo;
            } catch (\Exception $exception) {
                Yii::warning('Could not process plugin info - ' . $pluginInfoFile);
            }
        }

        return null;
    }

    /**
     * @return ClassLoader
     */
    private function getClassLoader()
    {
        if (!isset($this->classLoader)) {
            $this->classLoader = require (Yii::getAlias('@app/vendor/autoload.php'));
        }

        return $this->classLoader;
    }
}
