#!/bin/sh

php exc.php
vendor/bin/phpunit --printer=Codedungeon\\PHPUnitPrettyResultPrinter\\Printer
