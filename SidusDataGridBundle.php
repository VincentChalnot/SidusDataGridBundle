<?php
/*
 * This file is part of the Sidus/DataGridBundle package.
 *
 * Copyright (c) 2015-2021 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sidus\DataGridBundle;

use Sidus\BaseBundle\DependencyInjection\Compiler\GenericCompilerPass;
use Sidus\DataGridBundle\Registry\DataGridRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class SidusDataGridBundle extends Bundle
{
    /**
     * Adding compiler passes to inject services into configuration handlers
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GenericCompilerPass(
            DataGridRegistry::class,
            'sidus.datagrid',
            'addDataGrid'
        ));
    }
}
