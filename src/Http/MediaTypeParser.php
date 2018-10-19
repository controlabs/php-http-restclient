<?php

namespace Controlabs\Http;

class MediaTypeParser extends \ArrayObject
{
    public function offsetSet($mediaType, $callable)
    {
        if (!$callable instanceof \Closure) {
            throw new \Exception('Invalid media type parser to ' . $mediaType);
        }

        $callable->bindTo($this);

        parent::offsetSet($mediaType, $callable);
    }

    public function loadDefaults()
    {
        $this['application/json'] = function ($input) {
            $result = json_decode($input, true);

            if (!is_array($result)) {
                return null;
            }

            return $result;
        };

        $this['text/xml'] = $this['application/xml'] = function ($input) {
            $backup = libxml_disable_entity_loader(true);
            $backupErrors = libxml_use_internal_errors(true);
            $result = simplexml_load_string($input);

            libxml_disable_entity_loader($backup);
            libxml_clear_errors();
            libxml_use_internal_errors($backupErrors);

            if ($result === false) {
                return null;
            }

            return $result;
        };

        $this['text/html'] = function ($input) {
            $document = new \DOMDocument();

            @$document->loadHTML($input);

            return $document;
        };
    }

    public function parse($format, $content)
    {
        if (empty($this[$format])) {
            return null;
        }

        return $this[$format]($content);
    }
}
