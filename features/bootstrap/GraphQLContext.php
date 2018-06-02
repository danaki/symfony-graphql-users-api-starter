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

use Behatch\HttpCall\Request;
use Behat\Behat\Context\Context;
use Behatch\Context\RestContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Symfony\Component\HttpFoundation\Request as HTTPFoundationRequest;

/**
 * Class GraphQLContext
 * 
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
class GraphQLContext implements Context
{
    /**
     * @var RestContext
     */
    private $restContext;

    /**
     * @var array
     */
    private $graphqlRequest;

    /**
     * @var int
     */
    private $graphqlLine;

    /**
     * @var Request
     */
    private $request;

    /**
     * GraphQLContext constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gives access to the Behatch context.
     *
     * @param BeforeScenarioScope $scope
     *
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        $this->restContext = $environment->getContext(RestContext::class);
    }

    /**
     * @param PyStringNode $request
     *
     * @When I have the following GraphQL request:
     */
    public function IHaveTheFollowingGraphqlRequest(PyStringNode $request)
    {
        $this->graphqlRequest = ['query' => $request->getRaw()];
        $this->graphqlLine = $request->getLine();
    }

    /**
     * @param PyStringNode $request
     *
     * @When I send the following GraphQL request:
     */
    public function ISendTheFollowingGraphqlRequest(PyStringNode $request)
    {
        $this->IHaveTheFollowingGraphqlRequest($request);
        $this->sendGraphqlRequest();
    }
    /**
     * @param PyStringNode $variables
     *
     * @When I send the GraphQL request with variables:
     */
    public function ISendTheGraphqlRequestWithVariables(PyStringNode $variables)
    {
        $this->graphqlRequest['variables'] = $variables->getRaw();
        $this->sendGraphqlRequest();
    }

    /**
     * @param string $operation
     *
     * @When I send the GraphQL request with operation :operation
     */
    public function ISendTheGraphqlRequestWithOperation(string $operation)
    {
        $this->graphqlRequest['operation'] = $operation;
        $this->sendGraphqlRequest();
    }

    private function sendGraphqlRequest()
    {
        $this->request->setHttpHeader('Accept', null);
        $this->restContext->iSendARequestTo(HTTPFoundationRequest::METHOD_GET, '/api/?'.http_build_query($this->graphqlRequest));
    }
}
