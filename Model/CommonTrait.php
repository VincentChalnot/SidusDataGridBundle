<?php
declare(strict_types=1);

namespace Sidus\DataGridBundle\Model;

use Sidus\DataGridBundle\Renderer\ColumnLabelRendererInterface;
use Sidus\DataGridBundle\Renderer\ColumnValueRendererInterface;

trait CommonTrait
{
    protected string $code;

    protected ?string $template = null;

    protected array $templateVars = [];

    protected array $attributes = [];

    protected ?ColumnValueRendererInterface $columnValueRenderer = null;

    protected ?ColumnLabelRendererInterface $columnLabelRenderer = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    public function setTemplateVars(array $templateVars): void
    {
        $this->templateVars = $templateVars;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function unsetAttribute(string $key): void
    {
        unset($this->attributes[$key]);
    }

    public function getColumnValueRenderer(): ColumnValueRendererInterface
    {
        if (!$this->columnValueRenderer) {
            return $this->getDataGrid()->getColumnValueRenderer();
        }

        return $this->columnValueRenderer;
    }

    public function setColumnValueRenderer(ColumnValueRendererInterface $columnValueRenderer): void
    {
        $this->columnValueRenderer = $columnValueRenderer;
    }

    public function getColumnLabelRenderer(): ColumnLabelRendererInterface
    {
        return $this->columnLabelRenderer ?? $this->getDataGrid()->getColumnLabelRenderer();
    }

    public function setColumnLabelRenderer(ColumnLabelRendererInterface $columnLabelRenderer): void
    {
        $this->columnLabelRenderer = $columnLabelRenderer;
    }
}
