<?php

declare(strict_types=1);

namespace App\Handler;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationHandler
{
    public static function getJoinedViolationMessages(ConstraintViolationListInterface $violationList): string
    {
        $message = '';
        /** @var ConstraintViolationInterface $violation */
        foreach ($violationList as $violation) {
            $message .= $violation->getMessage();
        }

        return $message;
    }
}
