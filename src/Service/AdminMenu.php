<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatOfficialAccountCustomServiceBundle\Controller\Admin\KfAccountCrudController;

final class AdminMenu implements MenuProviderInterface
{
    /**
     * @return iterable<MenuItemInterface>
     */
    public function getMenuItems(): iterable
    {
        yield MenuItem::section('微信客服管理', 'fas fa-headset');

        yield MenuItem::linkToCrud('客服账号管理', 'fas fa-user-headset', KfAccountCrudController::getEntityFqcn())
            ->setController(KfAccountCrudController::class)
        ;
    }

    public function __invoke(mixed $item): void
    {
        // Implementation required by MenuProviderInterface
    }
}
