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

use LogicException;
use Sidus\DataGridBundle\Form\Type\LinkType;
use Sidus\FilterBundle\Query\Handler\QueryHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use UnexpectedValueException;

/**
 * Handle a datagrid configuration
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
#[AutoconfigureTag('sidus.datagrid')]
class DataGrid
{
    use CommonTrait;

    protected ?QueryHandlerInterface $queryHandler = null;

    protected ?string $formTheme = null;

    /** @var Column[] */
    protected array $columns = [];

    protected ?FormInterface $form = null;

    protected array $formOptions = [];

    protected ?FormView $formView = null;

    protected array $actions = [];

    protected array $submitButton = [];

    protected array $resetButton = [];

    public function __construct(string $code, array $configuration)
    {
        $this->code = $code;
        /** @var array $columns */
        $columns = $configuration['columns'];
        unset($configuration['columns']);

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($configuration as $key => $option) {
            $accessor->setValue($this, $key, $option);
        }

        foreach ($columns as $key => $columnConfiguration) {
            $this->createColumn($key, $columnConfiguration);
        }
    }

    public function getQueryHandler(): QueryHandlerInterface
    {
        return $this->queryHandler;
    }

    public function setQueryHandler(QueryHandlerInterface $queryHandler): void
    {
        $this->queryHandler = $queryHandler;
    }

    public function getFormTheme(): ?string
    {
        return $this->formTheme;
    }

    public function setFormTheme(string $formTheme = null): void
    {
        $this->formTheme = $formTheme;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function addColumn(Column $column, int $index = null): void
    {
        if (null === $index) {
            $this->columns[] = $column;
        } else {
            array_splice($this->columns, $index, 0, [$column]);
        }
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getAction(string $action): array
    {
        if (!$this->hasAction($action)) {
            throw new UnexpectedValueException("No action with code: '{$action}'");
        }

        return $this->actions[$action];
    }

    public function hasAction(string $action): bool
    {
        return array_key_exists($action, $this->actions);
    }

    public function setAction(string $action, array $configuration): void
    {
        $this->actions[$action] = $configuration;
    }

    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }

    public function getSubmitButton(): array
    {
        return $this->submitButton;
    }

    public function setSubmitButton(array $submitButton): void
    {
        $this->submitButton = $submitButton;
    }

    public function getResetButton(): array
    {
        return $this->resetButton;
    }

    public function setResetButton(array $resetButton): void
    {
        $this->resetButton = $resetButton;
    }

    public function getForm(): FormInterface
    {
        if (!$this->form) {
            throw new LogicException('You must first call buildForm()');
        }

        return $this->form;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function setFormOptions(array $formOptions): void
    {
        $this->formOptions = $formOptions;
    }

    public function getFormView(): FormView
    {
        if (!$this->formView) {
            $this->formView = $this->getForm()->createView();
        }

        return $this->formView;
    }

    public function buildForm(FormBuilderInterface $builder): FormInterface
    {
        $this->buildFilterActions($builder);
        $this->buildDataGridActions($builder);

        $this->form = $this->getQueryHandler()->buildForm($builder);

        return $this->form;
    }

    public function handleRequest(Request $request): void
    {
        $this->queryHandler->handleRequest($request);
    }

    public function handleArray(array $data): void
    {
        $this->queryHandler->handleArray($data);
    }

    public function getPager(): iterable
    {
        return $this->getQueryHandler()->getPager();
    }

    public function setActionParameters(string $action, array $parameters): void
    {
        if ('submit_button' === $action) {
            $this->setSubmitButton(
                array_merge(
                    $this->getSubmitButton(),
                    [
                        'route_parameters' => $parameters,
                    ]
                )
            );

            return;
        }
        if ('reset_button' === $action) {
            $this->setResetButton(
                array_merge(
                    $this->getResetButton(),
                    [
                        'route_parameters' => $parameters,
                    ]
                )
            );

            return;
        }
        $this->setAction(
            $action,
            array_merge(
                $this->getAction($action),
                [
                    'route_parameters' => $parameters,
                ]
            )
        );
    }

    protected function buildFilterActions(FormBuilderInterface $builder): void
    {
        $visibleFilterCount = 0;
        foreach ($this->getQueryHandler()->getConfiguration()->getFilters() as $filter) {
            $filter->getOption('hidden') ?: ++$visibleFilterCount;
        }
        if ($visibleFilterCount > 0) {
            $this->buildResetAction($builder);
            $this->buildSubmitAction($builder);
        }
    }

    protected function buildResetAction(FormBuilderInterface $builder): void
    {
        $action = $builder->getOption('action');
        $defaults = [
            'form_type' => LinkType::class,
            'label' => 'sidus.datagrid.reset.label',
            'uri' => $action ?: '?',
        ];
        $options = array_merge($defaults, $this->getResetButton());
        $type = $options['form_type'];
        unset($options['form_type']);
        $builder->add('filterResetButton', $type, $options);
    }

    protected function buildSubmitAction(FormBuilderInterface $builder): void
    {
        $defaults = [
            'form_type' => SubmitType::class,
            'label' => 'sidus.datagrid.submit.label',
            'attr' => [
                'class' => 'btn-primary',
            ],
        ];
        $options = array_merge($defaults, $this->getSubmitButton());
        $type = $options['form_type'];
        unset($options['form_type']);
        $builder->add('filterSubmitButton', $type, $options);
    }

    protected function buildDataGridActions(FormBuilderInterface $builder): void
    {
        $actionsBuilder = $builder->create(
            'actions',
            FormType::class,
            [
                'label' => false,
            ]
        );
        foreach ($this->getActions() as $code => $options) {
            $type = empty($options['form_type']) ? LinkType::class : $options['form_type'];
            unset($options['form_type']);
            $actionsBuilder->add($code, $type, $options);
        }
        $builder->add($actionsBuilder);
    }

    protected function createColumn(string $key, array $columnConfiguration): void
    {
        $this->columns[] = new Column($key, $this, $columnConfiguration);
    }
}
