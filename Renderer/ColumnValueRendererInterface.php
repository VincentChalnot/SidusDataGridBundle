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

namespace Sidus\DataGridBundle\Renderer;

/**
 * Allows an object to be rendered in a templating engine
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
interface ColumnValueRendererInterface
{
    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string
     */
    public function renderValue($value, array $options = []): string;
}
