<?php

namespace app\plugins;

use app\components\AppException;
use app\components\ConsoleRunner;
use app\events\PluginEvent;
use app\helpers\Migrations;
use app\settings\LazySettingsValue;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;
use yii\httpclient\Client;
use yii\httpclient\Response;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\plugins
 */
class PluginInstaller extends Component
{
    const EVENT_BEFORE_PLUGIN_INSTALL = 'onBeforePluginInstall';
    const EVENT_AFTER_PLUGIN_INSTALL = 'onAfterPluginInstall';
    const EVENT_BEFORE_PLUGIN_UNINSTALL = 'onBeforePluginUninstall';
    const EVENT_AFTER_PLUGIN_UNINSTALL = 'onAfterPluginUninstall';
    const EVENT_BEFORE_PLUGIN_UPDATE = 'onBeforePluginUpdate';
    const EVENT_AFTER_PLUGIN_UPDATE = 'onAfterPluginUpdate';

    /**
     * @var string
     */
    public $baseUrl;
    /**
     * @var Client
     */
    public $httpClient;
    /**
     * @var string
     */
    public $licenseKey;
    /**
     * @var string
     */
    public $authHeader = 'X-License-Key';
    /**
     * @var array
     */
    private $tempFiles = [];

    public function init()
    {
        parent::init();
        $this->httpClient = new Client();
        if ($this->licenseKey instanceof LazySettingsValue) {
            $this->licenseKey = $this->licenseKey->getValue();
        }
    }

    /**
     * @param $pluginId
     * @throws AppException
     * @throws \yii\base\InvalidConfigException
     */
    public function installPlugin($pluginId)
    {
        $pluginEvent = new PluginEvent(['pluginId' => $pluginId]);
        $this->trigger(self::EVENT_BEFORE_PLUGIN_INSTALL, $pluginEvent);
        if (!$pluginEvent->isValid) {
            return;
        }

        $pluginData = $this->fetchPluginData($pluginId);

        if (Yii::$app->pluginManager->isEnabled($pluginId)) {
            throw new AppException(Yii::t('app', 'Plugin "{0}" is already enabled', $pluginId), AppException::LEVEL_WARNING);
        }
        if (Yii::$app->pluginManager->isInstalled($pluginId)) {
            throw new AppException(Yii::t('app', 'Plugin "{0}" is already installed', $pluginId), AppException::LEVEL_WARNING);
        }

        try {
            $zipFile = Yii::getAlias('@app/runtime/tmp-zip-') . time() . rand(1111, 9999) . '.zip';
            $this->download($pluginData['zipFileUrl'], $zipFile);
        } catch (\Exception $exception) {
            Yii::error($exception->getMessage());
            throw new AppException(Yii::t('app', 'Could not download plugin "{0}": {1}', $pluginId, $exception->getMessage()));
        }

        if (!$this->unzip($zipFile, Yii::getAlias('@content/plugins/' . $pluginId))) {
            throw new AppException(Yii::t('app', 'Could not unzip plugin "{0}"', $pluginId));
        }

        $this->trigger(self::EVENT_AFTER_PLUGIN_INSTALL, $pluginEvent);
    }

    /**
     * @param $pluginId
     * @throws AppException
     * @throws Exception
     * @throws \yii\base\ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function updatePlugin($pluginId)
    {
        $pluginEvent = new PluginEvent(['pluginId' => $pluginId]);
        $this->trigger(self::EVENT_BEFORE_PLUGIN_UPDATE, $pluginEvent);
        if (!$pluginEvent->isValid) {
            return;
        }

        $pluginData = $this->fetchPluginData($pluginId);
        $installedPlugin = Yii::$app->pluginManager->getInstalledPlugin($pluginId);

        if (version_compare($installedPlugin->getVersion(), $pluginData['version']) >= 0) {
            throw new AppException(Yii::t('app', 'Plugin "{0}" is up to date', $pluginId), AppException::LEVEL_INFO);
        }

        try {
            $zipFile = Yii::getAlias('@app/runtime/tmp-zip-') . time() . rand(1111, 9999) . '.zip';
            $this->download($pluginData['zipFileUrl'], $zipFile);
        } catch (\Exception $exception) {
            Yii::error($exception->getMessage());
            throw new AppException(Yii::t('app', 'Could not download plugin "{0}": {1}', $pluginId, $exception->getMessage()));
        }

        $path = Yii::getAlias("@content/plugins/$pluginId");

        FileHelper::removeDirectory($path);
        if (!$this->unzip($zipFile, $path)) {
            throw new AppException(Yii::t('app', 'Could not unzip plugin "{0}"', $pluginId));
        }

        Migrations::run('up', Yii::getAlias("@content/plugins/$pluginId/migrations"));

        $this->trigger(self::EVENT_AFTER_PLUGIN_UPDATE, $pluginEvent);
    }

    /**
     * @param $pluginId
     * @throws AppException
     * @throws \yii\base\ErrorException
     */
    public function unInstallPlugin($pluginId)
    {
        $pluginManager = Yii::$app->pluginManager;

        $pluginEvent = new PluginEvent(['pluginId' => $pluginId]);
        $this->trigger(self::EVENT_BEFORE_PLUGIN_UNINSTALL, $pluginEvent);
        if (!$pluginEvent->isValid) {
            return;
        }

        if ($pluginManager->isEnabled($pluginId)) {
            $status = $pluginManager->disablePlugin($pluginId);
            if ($status == false) {
                throw new AppException("Could not disable plugin '$pluginId' before uninstalling");
            }
        }

        $pluginPath = $pluginManager->getPluginPath($pluginId);
        if ($pluginPath) {
            FileHelper::removeDirectory($pluginPath);
        }

        $this->trigger(self::EVENT_AFTER_PLUGIN_UNINSTALL, $pluginEvent);
    }

    /**
     * @param $searchQuery
     * @return ArrayDataProvider
     * @throws AppException
     * @throws \yii\base\InvalidConfigException
     */
    public function getPluginsProvider($searchQuery)
    {
        $models = $this->sendRequest('plugins', ['searchQuery' => $searchQuery]);

        return new ArrayDataProvider([
            'key' => 'alias',
            'allModels' => $models,
            'modelClass' => PluginInfo::class,
        ]);
    }

    /**
     * @param $pluginId
     * @return array|bool|mixed
     * @throws AppException
     * @throws \yii\base\InvalidConfigException
     */
    public function fetchPluginData($pluginId)
    {
        $pluginData = $this->sendRequest("plugins/$pluginId");

        if (!isset($pluginData['zipFileUrl'])) {
            throw new AppException(Yii::t('app', 'Invalid plugin format'));
        }

        return $pluginData;
    }

    /**
     * @param $url
     * @param array $data
     * @return array|mixed
     * @throws AppException
     * @throws \yii\base\InvalidConfigException
     */
    protected function sendRequest($url, $data = [])
    {
        /** @var Response $response */
        $response = $this->httpClient->createRequest()
            ->setHeaders([$this->authHeader => $this->licenseKey])
            ->setUrl($this->makeUrl($url))
            ->setData($data)
            ->send();

        if ($response->isOk) {
            return $response->getData();
        } elseif ($response->statusCode == 401 || $response->statusCode == 403) {
            $message = Yii::t('app', 'App License Key (Item Purchase Code) is invalid or empty');
            Yii::warning($message);
            throw new AppException($message);
        } else {
            throw new AppException(Yii::t('app', 'Plugin server is temporary unavailable'));
        }
    }

    /**
     * @param $url
     * @return string
     */
    protected function makeUrl($url)
    {
        return rtrim($this->baseUrl, '\//') . '/' . $url;
    }

    /**
     * @param $fileUrl
     * @param $fileTarget
     */
    protected function download($fileUrl, $fileTarget)
    {
        $options = ['http' => ['header' => "$this->authHeader: $this->licenseKey\r\n"]];
        $context = stream_context_create($options);

        $this->tempFiles[] = $fileTarget;

        file_put_contents($fileTarget, file_get_contents($fileUrl, false, $context));
    }

    /**
     * @param $zipFile
     * @param $targetDirectory
     * @return bool
     */
    protected function unzip($zipFile, $targetDirectory)
    {
        $zip = new \ZipArchive();
        if (!$zip->open($zipFile)) {
            return false;
        }

        $status = $zip->extractTo($targetDirectory);
        $zip->close();

        return $status;
    }

    /**
     * Remove temp files
     */
    public function cleanUp()
    {
        foreach ($this->tempFiles as $file) {
            FileHelper::unlink($file);
        }
    }
}
