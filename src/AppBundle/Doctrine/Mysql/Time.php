<?php

/**
 * Source: https://gist.github.com/ohadwkn/5945575
 */
 
namespace AppBundle\Doctrine\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
/**
 * DateFunction ::= "TIME" "(" ArithmeticPrimary ")"
 * @author Florent Viel <fviel@wanadev.fr>
 */
class Time extends FunctionNode
{
    public $dateExpression;
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'TIME(' .
            $this->dateExpression->dispatch($sqlWalker) .
        ')'; 
    }
} 
