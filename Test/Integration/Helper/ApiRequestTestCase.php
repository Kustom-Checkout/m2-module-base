<?php
/**
 * Copyright 2025 Kustom AB (Originally developed by Klarna Bank AB)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Magento\Checkout\Model\Session as MagentoSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\ObjectManagerInterface;

/**
 * @internal
 */
class ApiRequestTestCase extends TestCase
{
    /**
     * @var MagentoSession
     */
    protected $session;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var ConfigInterface
     */
    private $configWriter;
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    /**
     * @var Manager
     */
    private $cacheManager;
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->session = $this->objectManager->get(MagentoSession::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $this->configWriter = $this->objectManager->get(ConfigInterface::class);
        $this->encryptor = $this->objectManager->get(EncryptorInterface::class);
        $this->cacheManager = $this->objectManager->get(Manager::class);
    }

    /**
     * Encrypting and saving the password
     *
     * @param $store
     * @param string $market
     */
    protected function configureKlarnaCredentials($store, string $market): void
    {
        $file = file_get_contents('/var/www/src/_Klarna/klarna_api_credentials.json');
        $credentials = json_decode($file, true);
        $market = strtolower($market);

        $paths = [
            'klarna/api/region' => $market,
            'klarna/api_' . $market . '/api_mode' => 1,
            'klarna/api_' . $market . '/username_playground' => $credentials[$market]["username"],
            'klarna/api_' . $market . '/password_playground' => $this->encryptor->encrypt($credentials[$market]["password"]),
        ];

        foreach ($paths as $path => $value) {
            $this->configWriter->saveConfig(
                $path,
                $value,
                $store->getCode(),
                $store->getStoreId()
            );
        }
        $this->clearCache();
    }

    /**
     * Clearing the cache
     */
    private function clearCache(): void
    {
        $this->cacheManager->clean(['config']);
        $this->cacheManager->flush(['config']);
    }
}
