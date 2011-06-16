<?php

namespace Sirel
{
    use Sirel\Node\SqlLiteral;

    const VERSION = "0.1.0alpha1";

    /**
     * Factory for SQL Literals
     *
     * @param string $rawSql String of raw SQL
     * @return SqlLiteral
     */
    function sql($rawSql)
    {
        return new SqlLiteral($rawSql);
    }

    /**
     * Returns the '*' quantifier wrapped as SQL Literal
     * for use in projections
     *
     * @return SqlLiteral
     */
    function star()
    {
        return sql('*');
    }
}
