<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusBlacklistPlugin\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Tests\BitBag\SyliusBlacklistPlugin\Behat\Behaviour\ContainsEmptyListTrait;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    use ContainsEmptyListTrait;

    public function getCustomerFraudStatus(string $email): string
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $updatedRow = $tableAccessor->getRowWithFields($table, ['email' => $email]);

        return $tableAccessor->getFieldFromRow($table, $updatedRow, 'fraudStatus')->getText();
    }
}
