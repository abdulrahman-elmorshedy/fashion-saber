<?php
namespace InnoShop\Common\Traits;

trait PatchRequestTrait
{
    /**
     * Apply 'sometimes' to all rules for PATCH requests.
     * This allows partial updates where only provided fields are validated.
     *
     * @param  array  $rules
     * @return array
     */
    protected function applySometimesToRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            $rules[$key] = 'sometimes|'.$rule;
        }

        return $rules;
    }
}
