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

namespace Sidus\DataGridBundle\Model;

use Sidus\DataGridBundle\Renderer\ColumnLabelRendererInterface;
use Sidus\DataGridBundle\Renderer\ColumnValueRendererInterface;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Represents a column configuration for a datagrid
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class Column
{
    use CommonTrait;

    protected DataGrid $dataGrid;

    protected ?string $sortColumn = null;

    protected ?string $propertyPath = null;

    protected array $formattingOptions = [];

    protected ?string $label = null;

    public function __construct(string $code, DataGrid $dataGrid, array $options = [])
    {
        $this->code = $code;
        $this->dataGrid = $dataGrid;
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($options as $key => $option) {
            $accessor->setValue($this, $key, $option);
        }
    }

    public function getDataGrid(): DataGrid
    {
        return $this->dataGrid;
    }


    public function getSortColumn(): string
    {
        if (!$this->sortColumn) {
            return $this->getCode();
        }

        return $this->sortColumn;
    }

    public function setSortColumn(string $sortColumn): void
    {
        $this->sortColumn = $sortColumn;
    }

    public function getPropertyPath(): string
    {
        if (!$this->propertyPath) {
            return $this->getCode();
        }

        return $this->propertyPath;
    }

    public function setPropertyPath(string $propertyPath): void
    {
        $this->propertyPath = $propertyPath;
    }

    public function getFormattingOptions(): array
    {
        return $this->formattingOptions;
    }

    public function setFormattingOptions(array $formattingOptions): void
    {
        $this->formattingOptions = $formattingOptions;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * Get column value for a given object
     */
    public function getValue(object $object): mixed
    {
        return PropertyAccess::createPropertyAccessor()->getValue($object, $this->getPropertyPath());
    }

    /**
     * Render column for a given result
     */
    public function renderValue(object $object, array $options = []): string
    {
        try {
            $value = $this->getValue($object);
        } catch (UnexpectedTypeException) {
            return '';
        }

        return $this->getColumnValueRenderer()->renderValue(
            $value,
            array_merge(
                ['column' => $this, 'object' => $object],
                $this->getFormattingOptions(),
                $options
            )
        );
    }

    public function renderLabel(): string
    {
        return ucfirst($this->getColumnLabelRenderer()->renderColumnLabel($this));
    }
}
