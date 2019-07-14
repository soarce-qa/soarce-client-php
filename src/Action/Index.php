<?php

namespace Soarce\Action;

use Soarce\Action;

class Index extends Action
{
    /**
     * @return string
     */
    public function run(): string
    {
        $preconditionsUrl = self::url('preconditions');

        return <<<HTML
<html lang="en">
    <head>
        <title>SOARCE</title>
    </head>
    <body>
        <h1>SOARCE</h1>
        <h2>Menu</h2>
        <ul>
            <li><a href="{$preconditionsUrl}">check preconditions</a></li>        
        </ul>
    </body>
</html>
HTML;
    }
}
