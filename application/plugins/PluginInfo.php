<?php

namespace app\plugins;

use yii\base\Model;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\plugins
 */
class PluginInfo extends Model
{
    /**
     * @var string
     */
    public $alias;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $version;
    /**
     * @var string
     */
    public $author;
    /**
     * @var string
     */
    public $website;
    /**
     * @var string
     */
    public $namespace;
    /**
     * @var string
     */
    public $className;
    /**
     * @var string
     */
    public $imageFileUrl;
    /**
     * @var string
     */
    public $zipFileUrl;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['alias', 'title', 'version', 'author', 'description', 'website', 'namespace', 'className', 'imageFileUrl', 'zipFileUrl'], 'string', 'max' => 255],
        ];
    }
}
