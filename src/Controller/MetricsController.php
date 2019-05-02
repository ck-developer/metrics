<?php

namespace PrismaMedia\MetricsBundle\Controller;

use PrismaMedia\MetricsBundle\MetricProvider;
use PrismaMedia\MetricsBundle\MetricRenderer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MetricsController
{
    /**
     * @var MetricProvider
     */
    protected $metrics;

    /**
     * @var MetricRenderer
     */
    private $metricRenderer;

    public function __construct(MetricProvider $metrics, MetricRenderer $metricRenderer)
    {
        $this->metrics = $metrics;
        $this->metricRenderer = $metricRenderer;
    }

    /**
     * @return Response
     */
    public function __invoke()
    {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/plain; charset=utf-8');

        // No cache
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-cache');
        $response->headers->addCacheControlDirective('no-store');
        $response->headers->addCacheControlDirective('must-revalidated');

        $response->setCallback(function () {
            foreach ($this->metrics->getMetrics() as $metric) {
                echo $this->metricRenderer->render($metric);
            }
        });

        return $response;
    }
}