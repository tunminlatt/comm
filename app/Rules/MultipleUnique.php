<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DB;

class MultipleUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table, $column, $except = false, $extraColumns = [])
    {
        $this->table = $table;
        $this->column = $column;
        $this->except = $except;
        $this->extraColumns = $extraColumns;
    }

    protected function columnFormat ($column) {
        $columnArray = explode('->', $column);

        if (count($columnArray) > 1) { // json
            return $columnArray[0] .'->"$.'. $columnArray[1] .'"';
        } else { // normal
            return $column;
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // main query
        $query = 'SELECT * FROM '. $this->table .' WHERE '. $this->columnFormat($this->column) .' = "'. $value .'"';

        // id to ignore
        if ($this->except !== false) {
            $query .= ' AND id <> "'. $this->except .'"';
        }

        // extra columns to filter
        foreach ($this->extraColumns as $column => $value) {
            $query .= ' AND '. $this->columnFormat($column) .' = "'. $value .'"';
        }

        // execute query
        $result = DB::select($query);

        return count($result) > 0 ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
