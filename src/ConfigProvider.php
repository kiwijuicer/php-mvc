<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc;

/**
 * Config Provider
 *
 * @package KiwiJuicer\Mvc
 * @author Norbert Hanauer <norbert.hanauer@check24.de>
 * @copyright CHECK24 Vergleichsportal GmbH
 */
class ConfigProvider
{
    /**
     * Merges given config files
     *
     * @param array $paths
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function merge(array $paths): array
    {
        $configs = [];

        foreach ($paths as $path) {

            if (!file_exists($path)) {
                throw new \InvalidArgumentException('Given config path does not resolve to a file');
            }

            $configs[] = include $path;
        }

        return array_merge(...$configs);
    }
}
