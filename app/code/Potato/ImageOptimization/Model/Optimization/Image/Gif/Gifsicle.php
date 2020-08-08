<?php

namespace Potato\ImageOptimization\Model\Optimization\Image\Gif;

use Potato\ImageOptimization\Model\Image;
use Potato\ImageOptimization\Model\Source\Image\Status as StatusSource;
use Potato\ImageOptimization\Model\Source\Optimization\Error as ErrorSource;
use Potato\ImageOptimization\Model\Optimization\Image\AbstractUtility;

class Gifsicle extends AbstractUtility
{
    const LIB_PATH = 'gifsicle';
    const DEFAULT_OPTIONS = '-b -O3';

    /**
     * @param string $imagePath
     * @return void
     * @throws \Exception
     */
    public function optimize($imagePath)
    {
        $command = $this->getLibPath(self::LIB_PATH) . ' ' . self::DEFAULT_OPTIONS . ' "' . $imagePath . '" 2>&1';
        exec(
            $command,
            $result,
            $error
        );
        $stringResult = implode(' ', $result);

        if ($error != 0 && strpos($stringResult, 'gifsicle:   trailing garbage after GIF ignored') === false) {
            throw new \Exception(__('Application for GIF files optimization returns the error. Error code: %1 %2. Current script owner: %3. Command: %4',
                $error, $stringResult, get_current_user(), $command), ErrorSource::APPLICATION);
        }
    }

    /**
     * @return array
     */
    public function isAvailable()
    {
        $command = $this->getLibPath(self::LIB_PATH) . ' --version 2>&1';
        exec(
            $command,
            $result,
            $error
        );
        $result = true;
        if ($error != 0) {
            $result = false;
        }
        return [self::LIB_PATH => $result];
    }
}
