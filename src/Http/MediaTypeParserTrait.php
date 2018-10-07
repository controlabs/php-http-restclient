<?php

namespace Controlabs\Http;

trait MediaTypeParserTrait
{
    protected $mediaTypeParsers = [];

    public function registerMediaTypeParser($mediaType, callable $callable)
    {
        if ($callable instanceof Closure) {
            $callable = $callable->bindTo($this);
        }

        $this->mediaTypeParsers[(string)$mediaType] = $callable;
    }

    public function registerDefaultMediaTypeParser()
    {
        $forJson = function ($input) {
            $result = json_decode($input, true);

            if (!is_array($result)) {
                return null;
            }

            return $result;
        };

        $forXml = function ($input) {
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

        $this->registerMediaTypeParser('application/json', $forJson);
        $this->registerMediaTypeParser('application/xml', $forXml);
        $this->registerMediaTypeParser('text/xml', $forXml);
    }
}
