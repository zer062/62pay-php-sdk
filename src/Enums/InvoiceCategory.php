<?php
declare(strict_types=1);

namespace Sixtytwopay\Enums;

final class InvoiceCategory
{
    public const ENROLLMENT = 'ENROLLMENT';
    public const REENROLLMENT = 'REENROLLMENT';
    public const TUITION = 'TUITION';
    public const FEE = 'FEE';
    public const OTHER = 'OTHER';

    public const ALL = [
        self::ENROLLMENT,
        self::REENROLLMENT,
        self::TUITION,
        self::FEE,
        self::OTHER,
    ];

    private function __construct()
    {
    }
}
