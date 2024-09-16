<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FileImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;



    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new File([
            'user_id' => request()->user()->id,
            'registeration_number' => $row['registeration_number'],
            'category_id' => Category::where('name', $row['category'])->first()?->id,
            'description' => $row['description'] ?? null,
            'date' => $this->formatDate($row['date'] ?? null),
            'creditor_amount' => $row['creditor_amount'] ? str_replace(',', '', $row['creditor_amount']) : null,
            'debtor_amount' => $row['debtor_amount'] ? str_replace(',', '', $row['debtor_amount']) : null,
            'path' => $row['registeration_number'] ? 'pdf_files/' . $row['registeration_number'] . '.pdf' : null,
        ]);
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


    public function rules(): array
    {
        return [
            'registeration_number' => ['required', 'string'],
            'description' => ['string', 'nullable'],
            'category' => ['required', 'exists:categories,name'],
            'creditor_amount' => ['regex:/^\d{1,3}(,\d{3})*(\.\d{2})?$/', 'nullable'],
            'debtor_amount' => ['regex:/^\d{1,3}(,\d{3})*(\.\d{2})?$/', 'nullable'],
            'date' => ['date_format:d/m/Y']
        ];
    }
}
