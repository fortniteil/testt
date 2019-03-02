<?php

namespace app\components;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\components
 */
class PhpMessageSource extends \yii\i18n\PhpMessageSource
{
    /**
     * @param string $category
     * @param string $fallbackLanguage
     * @param array $messages
     * @param string $originalMessageFile
     * @return array|null
     */
    protected function loadFallbackMessages($category, $fallbackLanguage, $messages, $originalMessageFile)
    {
        $fallbackMessageFile = $this->getMessageFilePath($category, $fallbackLanguage);
        $fallbackMessages = $this->loadMessagesFromFile($fallbackMessageFile);

        if (empty($messages)) {
            return $fallbackMessages;
        } elseif (!empty($fallbackMessages)) {
            foreach ($fallbackMessages as $key => $value) {
                if (!empty($value) && empty($messages[$key])) {
                    $messages[$key] = $fallbackMessages[$key];
                }
            }
        }

        return (array) $messages;
    }
}
