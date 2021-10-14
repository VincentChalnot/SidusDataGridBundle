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

namespace Sidus\DataGridBundle\Twig;

use Sidus\DataGridBundle\Model\DataGrid;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Render values inside the Twig engine
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class RendererExtension extends AbstractExtension
{
    /** @var Environment */
    protected $twig;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_datagrid',
                [$this, 'renderDataGrid'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'get_filter_columns',
                [$this, 'getFilterColumns'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'page_path',
                [$this, 'getPagePath']
            ),
        ];
    }

    public function getPagePath(int $page): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return "?page={$page}";
        }
        $parameters = array_merge($request->attributes->get('_route_params'), $request->query->all());
        $parameters['page'] = $page;

        return $this->urlGenerator->generate($request->attributes->get('_route'), $parameters);
    }

    /**
     * @param DataGrid $dataGrid
     * @param array    $viewParameters
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return string
     */
    public function renderDataGrid(DataGrid $dataGrid, array $viewParameters = []): string
    {
        $viewParameters = array_merge($dataGrid->getTemplateVars(), $viewParameters);
        $viewParameters['datagrid'] = $dataGrid;

        return $this->twig->render($dataGrid->getTemplate(), $viewParameters);
    }

    /**
     * Simple function to split form widgets in as many columns as wanted
     *
     * @param FormView $formView
     * @param int      $numColumns
     *
     * @return array
     */
    public function getFilterColumns(FormView $formView, int $numColumns = 3): array
    {
        $columns = [];
        $i = 0;
        foreach ($formView as $formItem) {
            $columns[$i % $numColumns][] = $formItem;
            ++$i;
        }

        return $columns;
    }
}
