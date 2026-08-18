<?php

namespace App\Imports;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BeneficiariesImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected int $importedCount = 0;

    protected int $skippedCount = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            try {
                $payload = $this->normalizeRow($row);

                if (empty($payload['name'])) {
                    continue;
                }

                if ($this->isDuplicate($payload)) {
                    $this->skippedCount++;
                    \App\Models\BeneficiaryDuplicate::create($payload);
                    continue;
                }

                Beneficiary::create($payload);
                $this->importedCount++;
            } catch (\Throwable $e) {
                // Log the error and skip this row
                \Log::error('Error importing beneficiary row: ' . $e->getMessage(), [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage(),
                ]);
                $this->skippedCount++;
                continue;
            }
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function normalizeRow(Collection $row): array
    {
        $name = $this->getValue($row, ['full_name', 'name', 'beneficiary_name']);
        $birthdate = $this->getValue($row, ['birthdate', 'date_of_birth', 'dob']);
        $idNumber = $this->getValue($row, ['id_number', 'identification_number', 'national_id']);
        $email = $this->getValue($row, ['email', 'email_address']);
        $employmentStatus = $this->getValue($row, ['employment_status', 'gov_employee', 'government_employee']);
        $studentStatus = $this->getValue($row, ['student_status', 'student']);
        $pwdStatus = $this->getValue($row, ['pwd_status', 'pwd', 'person_with_disability']);
        
        // Parse birthdate properly
        $parsedBirthdate = null;
        if (!empty($birthdate)) {
            $parsedBirthdate = $this->parseDate($birthdate);
        }
        
        $age = $this->calculateAge($parsedBirthdate);
        $eligible = $age >= 18;
        $eligibilityStatus = $eligible ? 'Eligible' : 'Ineligible due to underage';
        $tags = [];

        if ($age >= 60) {
            $tags[] = 'Senior Citizen';
        }

        if ($this->isYesLike($employmentStatus)) {
            $employmentStatus = 'Gov Employee';
        } elseif (!empty($employmentStatus)) {
            $employmentStatus = $this->normalizeStatus($employmentStatus);
        }

        if ($this->isYesLike($studentStatus)) {
            $studentStatus = 'Yes';
        } elseif (!empty($studentStatus)) {
            $studentStatus = 'No';
        }

        if ($this->isYesLike($pwdStatus)) {
            $pwdStatus = 'Yes';
        } elseif (!empty($pwdStatus)) {
            $pwdStatus = 'No';
        }

        return [
            'name' => $name,
            'age' => $age ?: null,
            'id_number' => $idNumber ?: null,
            'email' => $email ?: null,
            'birthdate' => $parsedBirthdate,
            'employment_status' => $employmentStatus ?: null,
            'student_status' => $studentStatus ?: null,
            'pwd_status' => $pwdStatus ?: null,
            'eligibility_status' => $eligibilityStatus,
            'is_eligible' => $eligible,
            'tags' => !empty($tags) ? implode(', ', $tags) : null,
        ];
    }

    protected function isDuplicate(array $payload): bool
    {
        if (!empty($payload['id_number'])) {
            return Beneficiary::where('id_number', $payload['id_number'])->exists();
        }

        if (!empty($payload['email'])) {
            return Beneficiary::where('email', $payload['email'])->exists();
        }

        if (!empty($payload['name']) && !empty($payload['birthdate'])) {
            return Beneficiary::where('name', $payload['name'])
                ->whereDate('birthdate', $payload['birthdate'])
                ->exists();
        }

        return false;
    }

    protected function calculateAge($birthdate): int
    {
        if (empty($birthdate)) {
            return 0;
        }

        try {
            $birth = new \DateTime($birthdate);
            $today = new \DateTime('today');

            return (int) $birth->diff($today)->y;
        } catch (\Throwable) {
            return 0;
        }
    }

    protected function parseDate(?string $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }

        $dateStr = trim((string) $dateStr);
        
        // Try parsing with various common formats
        $formats = [
            'Y-m-d',      // 2005-05-27
            'm/d/Y',      // 05/27/2005
            'd/m/Y',      // 27/05/2005
            'Y/m/d',      // 2005/05/27
            'F d, Y',     // May 27, 2005 (with space after comma)
            'F d,Y',      // May 27,2005 (no space after comma)
            'F j, Y',     // May 27, 2005 (no leading zero)
            'F j,Y',      // May 27,2005 (no leading zero, no space)
            'd F Y',      // 27 May 2005
            'j M Y',      // 27 May 2005
            'j-M-Y',      // 27-May-2005
            'd-m-Y',      // 27-05-2005
            'm-d-Y',      // 05-27-2005
            'd/m/y',      // 27/05/05
            'm/d/y',      // 05/27/05
        ];

        // First, try DateTime::createFromFormat with each format
        foreach ($formats as $format) {
            try {
                $date = \DateTime::createFromFormat($format, $dateStr);
                // If successful, return the formatted date
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                // Continue to next format
                continue;
            }
        }

        // If DateTime formats fail, try normalizing and using strtotime
        try {
            // Normalize month names (capitalize them)
            $normalized = $this->normalizeDateString($dateStr);
            
            $timestamp = strtotime($normalized);
            if ($timestamp !== false && $timestamp > 0) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Throwable) {
            // Fall through to return null
        }

        // If nothing works, return null (will cause empty/invalid date in database)
        return null;
    }

    protected function normalizeDateString(string $dateStr): string
    {
        // Convert common month variations to full month names
        $months = [
            'jan' => 'January', 'january' => 'January',
            'feb' => 'February', 'february' => 'February',
            'mar' => 'March', 'march' => 'March',
            'apr' => 'April', 'april' => 'April',
            'may' => 'May',
            'jun' => 'June', 'june' => 'June',
            'jul' => 'July', 'july' => 'July',
            'aug' => 'August', 'august' => 'August',
            'sep' => 'September', 'september' => 'September',
            'oct' => 'October', 'october' => 'October',
            'nov' => 'November', 'november' => 'November',
            'dec' => 'December', 'december' => 'December',
        ];

        // Case-insensitive replacement
        foreach ($months as $short => $full) {
            $dateStr = preg_replace("/\\b{$short}\\b/i", $full, $dateStr);
        }

        return $dateStr;
    }

    protected function isYesLike(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['yes', 'y', 'true', '1', 'student', 'gov employee', 'government employee', 'pwd', 'person with disability'], true);
    }

    protected function normalizeStatus(string $value): string
    {
        $normalized = strtolower(trim($value));

        if (str_contains($normalized, 'gov') || str_contains($normalized, 'government') || str_contains($normalized, 'employee')) {
            return 'Gov Employee';
        }

        return ucfirst($value);
    }

    protected function getValue(Collection $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if ($row->has($key)) {
                return trim((string) $row->get($key));
            }
        }

        return null;
    }
}
