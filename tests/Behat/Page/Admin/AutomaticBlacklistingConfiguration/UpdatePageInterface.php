<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusBlacklistPlugin\Behat\Page\Admin\AutomaticBlacklistingConfiguration;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Tests\BitBag\SyliusBlacklistPlugin\Behat\Behaviour\ChecksCodeImmutabilityInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface, ChecksCodeImmutabilityInterface
{
    public function fillName(string $name): void;

    public function disable(): void;

    public function update(): void;

    public function fillRuleOption(string $option, string $value): void;

    public function uncheckField(string $locator): void;
}
