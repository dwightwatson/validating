<?php

namespace Watson\Validating\Injectors;

trait UniqueInjector
{
    /**
     * Prepare a unique rule, adding the table name, column and model identifier
     * if required.
     *
     * @param  array  $parameters
     * @param  string $field
     * @return string
     */
    protected function prepareUniqueRule($parameters, $field)
    {
        // If the table name isn't set, infer it.
        if (empty($parameters[0])) {
            $parameters[0] = $this->getModel()->getTable();
        }

        // If the connection name isn't set but exists, infer it.
        if ((strpos($parameters[0], '.') === false) && (($connectionName = $this->getModel()->getConnectionName()) !== null)) {
            $parameters[0] = $connectionName.'.'.$parameters[0];
        }

        // If the field name isn't get, infer it.
        if (! isset($parameters[1])) {
            $parameters[1] = $field;
        }

        if ($this->exists) {
            // If the identifier isn't set, infer it.
            if (! isset($parameters[2]) || strtolower($parameters[2]) === 'null') {
                $parameters[2] = $this->getModel()->getKey();
            }

            // If the primary key isn't set, infer it.
            if (! isset($parameters[3])) {
                $parameters[3] = $this->getModel()->getKeyName();
            }

            // If the additional where clause isn't set, infer it.
            // Example: unique:users,email,123,id,username,NULL
            foreach ($parameters as $key => $parameter) {
                if (strtolower((string) $parameter) === 'null') {
                    // Maintain NULL as string in case the model returns a null value
                    $value = $this->getModel()->{$parameters[$key - 1]};
                    $parameters[$key] = is_null($value) ? 'NULL' : $value;
                }
            }
        }

        return 'unique:' . implode(',', $parameters);
    }
}
