<?php
/*
 * This file is part of the Sidus/DataGridBundle package.
 *
 * Copyright (c) 2015-2023 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sidus\DataGridBundle\Renderer;

use Sidus\DataGridBundle\Model\Column;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Render values inside the Twig engine
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class DefaultColumnLabelRenderer implements ColumnLabelRendererInterface
{
    public function __construct(
        protected TranslatorInterface $translator,
    ) {
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public function renderColumnLabel(Column $column): string
    {
        $label = $column->getLabel();
        if (!$label) {
            $key = "datagrid.{$column->getDataGrid()->getCode()}.{$column->getCode()}";
            if ($this->translator instanceof TranslatorBagInterface
                && $this->translator->getCatalogue()
                && $this->translator->getCatalogue()->has($key)
            ) {
                $label = $key;
            } else {
                $label = ucfirst(
                    strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $column->getCode())))
                );
            }
        }

        return $this->translator->trans($label);
    }
}
