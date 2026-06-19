<?php

namespace App\Constants;

class LetterTagConfig
{
    // input type settings (create/edit tag page)
    public static function inputTypes()
    {
        return [
            'short_text' => 'Short Text (Auto-Fill Supported)',
            'long_text' => 'Long Text (Paragraph)',
            'number' => 'Number',
            'date' => 'Date (Auto-Fill Supported)',
            'time' => 'Time',
            'dropdown' => 'Dropdown (Dynamic Choices)',
            // add new html input types here
        ];
    }

    // auto-fill settings (for text & date)
    // labels shown in the tag form dropdown
    public static function autoFillOptions()
    {
        return [
            'user_name' => 'Employee Name (Letter Creator)',
            'user_position' => 'Position (Letter Creator)',
            'user_department' => 'Department (Letter Creator)',
            'today_date' => 'Today\'s Date',
            'today_day' => 'Today\'s Day',
            'today_date_full' => 'Today\'s Full Date',
            // add new auto-fill options here (e.g., user_nik)
        ];
    }

    // auto-fill data fetcher engine (for employee page)
    public static function getAutoFillValues($user)
    {
        return [
            'user_name' => $user->name ?? '-',
            'user_position' => $user->employee->position->position_name ?? '-',
            'user_department' => $user->employee->department->name ?? '-',
            'today_date' => now()->format('Y-m-d'),
            'today_day' => now()->locale('id')->translatedFormat('l'),
            'today_date_full' => now()->locale('id')->translatedFormat('l, d F Y'),
            // adjust the data fetching logic here
        ];
    }

    // database relation settings (for dropdown)
    // labels shown in the tag form dropdown
    public static function dropdownModels()
    {
        return [
            'OfficeLocation' => 'Office Location Data (OfficeLocation)',
            'Employee' => 'Active Employee Data (Employee)',
            'EmployeeNIK' => 'Employee NIK Data (Employee)',
            'Position' => 'Position Data (Position)',
            // add new db tables for dropdown here
        ];
    }

    // dropdown data fetcher engine (for ajax controller)
    public static function getDropdownData($modelName)
    {
        switch ($modelName) {
            case 'OfficeLocation':
                return \App\Models\OfficeLocation::pluck('name', 'id')->toArray();

            case 'Employee':
                return \App\Models\User::has('employee')->pluck('name', 'id')->toArray();

            case 'EmployeeNIK':
                return \App\Models\Employee::whereNotNull('nik')
                    ->get()
                    ->mapWithKeys(function ($emp) {
                        return [$emp->nik => $emp->fullname . ' - ' . $emp->nik];
                    })->toArray();

            case 'Position':
                return \App\Models\Position::pluck('position_name', 'position_id')->toArray();

            default:
                return [];
        }
    }
}
