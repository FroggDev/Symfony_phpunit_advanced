<?php
namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlEncoder
 * @package App\Serializer
 */
class YamlEncoder implements EncoderInterface, DecoderInterface
{
    /**
     * @param mixed $data
     * @param string $format
     * @param array $context
     * @return bool|float|int|string
     */
    public function encode($data, $format, array $context = array())
    {
        return Yaml::dump($data);
    }

    /**
     * @param string $format
     * @return bool
     */
    public function supportsEncoding($format)
    {
        return ('yaml' === $format || 'yml' === $format);
    }

    /**
     * @param string $data
     * @param string $format
     * @param array $context
     * @return mixed
     */
    public function decode($data, $format, array $context = array())
    {
        return Yaml::parse($data);
    }

    /**
     * @param string $format
     * @return bool
     */
    public function supportsDecoding($format)
    {
        return ('yaml' === $format || 'yml' === $format);
    }
}