<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class MarksRowsImport implements ToArray
{
    public function array(array $array): void
    {
        // The controller applies the same header mapping to CSV and XLSX rows.
    }
}
