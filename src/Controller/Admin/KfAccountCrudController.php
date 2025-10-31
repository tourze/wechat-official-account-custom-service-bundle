<?php

declare(strict_types=1);

namespace WechatOfficialAccountCustomServiceBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;
use WechatOfficialAccountCustomServiceBundle\Service\FieldFactoryInterface;

/**
 * 客服账号管理控制器
 *
 * @extends AbstractCrudController<KfAccount>
 */
#[AdminCrud(routePath: '/wechat-official-account-custom-service/kf-account', routeName: 'wechat_official_account_custom_service_kf_account')]
final class KfAccountCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly FieldFactoryInterface $fieldService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return KfAccount::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield AssociationField::new('account', '所属公众号')
            ->setRequired(true)
        ;

        yield TextField::new('kfAccount', '客服账号')
            ->setRequired(true)
            ->setHelp('客服账号，最多100个字符')
        ;

        yield TextField::new('nickname', '客服昵称')
            ->setRequired(true)
            ->setHelp('客服昵称，最多100个字符')
        ;

        yield TextField::new('password', '客服密码')
            ->setRequired(false)
            ->setHelp('客服密码，最多32个字符，可选')
        ;

        yield UrlField::new('avatar', '客服头像')
            ->setRequired(false)
            ->setHelp('客服头像URL，可选')
        ;

        /** @phpstan-ignore-next-line */
        yield $this->fieldService->createEnumField('status', '状态', KfAccountStatus::cases())
            ->setRequired(true)
        ;

        yield TextField::new('kfId', '客服ID')
            ->setRequired(false)
            ->setHelp('微信平台分配的客服ID，最多32个字符')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnIndex()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnIndex()
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('客服账号')
            ->setEntityLabelInPlural('客服账号')
            ->setSearchFields(['kfAccount', 'nickname', 'kfId'])
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('account', '所属公众号'))
            ->add(TextFilter::new('kfAccount', '客服账号'))
            ->add(TextFilter::new('nickname', '客服昵称'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
