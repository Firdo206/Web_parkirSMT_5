<?php

namespace App\Exceptions;

use RuntimeException;

/** Dilempar saat pendaftaran melebihi kapasitas slot. Pesannya aman ditampilkan ke admin. */
class SlotPenuhException extends RuntimeException
{
}