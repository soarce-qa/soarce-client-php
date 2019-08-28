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
        return <<<HTML
<html lang="en">
    <head>
        <title>SOARCE</title>
    </head>
    <body>
        <h1>SOARCE</h1>
        <h2>Hello World!</h2>
        <p>If you see this page the basic functionality of SOARCE is enabled in your service/application. Please use the main application (soarce/application) to remotely access,
        check and control the SOARCE features.</p>
    </body>
</html>
HTML;
    }
}
