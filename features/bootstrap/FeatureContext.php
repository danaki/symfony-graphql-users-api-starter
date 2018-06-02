<?php

declare(strict_types=1);

/*
 * This file is part of the CyclePath project.
 *
 * (c) Guillaume Loulier <contact@guillaumeloulier.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class FeatureContext
 *
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
class FeatureContext extends MinkContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * FeatureContext constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $path
     *
     * @throws Exception
     *
     * @When a demo scenario sends a request to :path
     */
    public function aDemoScenarioSendsARequestTo(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived()
    {
        if ($this->response === null) {
            throw new \RuntimeException(
                \sprintf(
                    'No response received'
            ));
        }
    }

    /**
     * @param int $statusCode
     *
     * @throws \InvalidArgumentException
     *
     * @Then the status code equals :statusCode
     */
    public function theStatusCodeEquals(int $statusCode)
    {
        if ($this->response->getStatusCode() !== $statusCode) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Incorrect status code given ! Found %d',
                    $this->response->getStatusCode()
                )
            );
        }
    }
}
