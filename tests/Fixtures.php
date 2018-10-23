<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Tests;

/**
 * Class Fixtures
 * @package SwipeStripe\Accounts\Tests
 */
final class Fixtures
{
    const BASE_PATH = __DIR__ . '/fixtures';

    const BASE_COMMERCE_PAGES = self::BASE_PATH . '/BaseCommercePages.yml';
    const CUSTOMERS = self::BASE_PATH . '/Customers.yml';

    /**
     * Fixtures constructor.
     */
    private function __construct()
    {
    }
}
