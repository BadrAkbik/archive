<?php

namespace App\Imports;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FileImport implements ToCollection, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {
            $data = [
                'user_id' => request()->user()->id,
                'registeration_number'          => is_numeric($row['registeration_number']) ? (int) $row['registeration_number'] : null,
                'description'         => $row['description'] ?? null,
                'date' => $this->formatDate($row['date'] ?? null),
                'creditor_amount' =>  $this->formatNumber($row['creditor_amount'] ?? null),
                'debtor_amount' => $this->formatNumber($row['debtor_amount'] ?? null),
                'path' => is_numeric($row['registeration_number']) ? 'pdf_files/' . $row['registeration_number'] . '.pdf' : null,
            ];

            try{
                File::create($data);

            }catch (\Exception $error){
                throw ValidationException::withMessages(['File' => 'يوجد مشكلة في ملف الاكسل']);
            }
        }
    }
    /**
     * Format date to Y-m-d if valid, otherwise return null
     *
     * @param string|null $date
     * @return string|null
     */
    private function formatDate($date)
    {
        try {
            return $date ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return null; // or handle the error as needed
        }
    }

    /**
     * Format number to ensure it's valid, otherwise return null
     *
     * @param mixed $number
     * @return float|null
     */
    private function formatNumber($number)
    {
        return is_numeric($number) ? (float) $number : null;
    }
}
