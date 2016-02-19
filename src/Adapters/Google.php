<?php

namespace Embed\Adapters;

use Embed\Request;
use Embed\Utils;
use Embed\Providers\Api;

/**
 * Adapter provider more information from google maps and google drive.
 */
class Google extends Webpage implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function check(Request $request)
    {
        return $request->isValid() && $request->match([
            'https://maps.google.*',
            'https://www.google.*/maps*',
            'https://drive.google.com/file/*/view',
            'https://plus.google.com/*/posts/*',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        if ($this->request->match('*/maps/*')) {
            $this->addProvider('google', new Api\GoogleMaps());
        }

        parent::run();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        $this->width = null;
        $this->height = null;

        if ($this->request->getHost() === 'plus.google.com') {
            return '<script src="https://apis.google.com/js/plusone.js" type="text/javascript"></script>'
                .'<div class="g-post" data-href="'.$this->request->getUrl().'"></div>';
        }

        if (($google = $this->getProvider('google'))) {
            return $google->getCode();
        }

        $url = $this->request->createUrl()
            ->withDirectoryPosition(3, 'preview')
            ->withQueryParameters([]);

        return Utils::iframe($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesUrls()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
        if ($this->request->getHost() === 'plus.google.com') {
            return 'Google Plus';
        }

        return parent::getProviderName();
    }
}
