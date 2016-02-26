<?php

namespace Joli\Jane\OpenApi\Generator;

use Joli\Jane\Jane;
use Joli\Jane\OpenApi\Naming\ChainOperationNaming;
use Joli\Jane\OpenApi\Naming\OperationUrlNaming;
use Joli\Jane\Reference\Resolver;
use Joli\Jane\OpenApi\Generator\Parameter\BodyParameterGenerator;
use Joli\Jane\OpenApi\Generator\Parameter\FormDataParameterGenerator;
use Joli\Jane\OpenApi\Generator\Parameter\HeaderParameterGenerator;
use Joli\Jane\OpenApi\Generator\Parameter\PathParameterGenerator;
use Joli\Jane\OpenApi\Generator\Parameter\QueryParameterGenerator;
use Joli\Jane\OpenApi\Naming\OperationIdNaming;
use Joli\Jane\OpenApi\Operation\OperationManager;
use PhpParser\Lexer;
use PhpParser\Parser;

class GeneratorFactory
{
    static public function build()
    {
        $parser = new Parser(new Lexer());

        $resolver          = new Resolver(Jane::buildSerializer());
        $bodyParameter     = new BodyParameterGenerator($parser, $resolver);
        $pathParameter     = new PathParameterGenerator($parser);
        $formDataParameter = new FormDataParameterGenerator($parser);
        $headerParameter   = new HeaderParameterGenerator($parser);
        $queryParameter    = new QueryParameterGenerator($parser);

        $operation = new OperationGenerator($resolver, $bodyParameter, $formDataParameter, $headerParameter, $pathParameter, $queryParameter);
        $operationManager = new OperationManager();
        $operationNaming = new ChainOperationNaming([
            new OperationIdNaming(),
            new OperationUrlNaming(),
        ]);
        $client = new ClientGenerator($operationManager, $operation, $operationNaming);

        return $client;
    }
}
