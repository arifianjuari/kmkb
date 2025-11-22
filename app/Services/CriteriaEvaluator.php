<?php

namespace App\Services;

use App\Models\PatientCase;

/**
 * Simple criteria evaluator for pathway steps.
 *
 * Syntax (semicolon-separated AND conditions):
 * - ina_cbg_code==CODE
 * - ina_cbg_code!=CODE
 * - diagnosis~=substring   (case-insensitive contains)
 * - primary_diagnosis~=substring (alias of diagnosis~=)
 * - los>=N  (length of stay in days)
 * - los<=N
 * - los>N, los<N, los==N
 * - cost_over_tariff==true|false
 *
 * Empty or null criteria => applies (true).
 */
class CriteriaEvaluator
{
    public function applies(?string $criteria, PatientCase $case): bool
    {
        $criteria = is_string($criteria) ? trim($criteria) : '';
        if ($criteria === '') {
            return true;
        }

        $conditions = array_filter(array_map('trim', explode(';', $criteria)), fn($c) => $c !== '');
        foreach ($conditions as $cond) {
            if (!$this->evaluateCondition($cond, $case)) {
                return false; // AND semantics
            }
        }
        return true;
    }

    private function evaluateCondition(string $cond, PatientCase $case): bool
    {
        // Supported operators ordered by length to avoid partial matches
        $operators = ['>=', '<=', '==', '!=', '>', '<', '~='];
        $op = null;
        foreach ($operators as $candidate) {
            $pos = strpos($cond, $candidate);
            if ($pos !== false) {
                $op = $candidate;
                break;
            }
        }
        if ($op === null) {
            return true; // unknown condition -> ignore (non-blocking)
        }

        [$left, $right] = array_map('trim', explode($op, $cond, 2));
        $left = strtolower($left);
        $right = trim($right, " \"'\t\n\r\0\x0B");

        // Resolve left value from case
        $leftVal = null;
        switch ($left) {
            case 'ina_cbg_code':
                $leftVal = (string)($case->ina_cbg_code ?? '');
                break;
            case 'diagnosis':
            case 'primary_diagnosis':
                $leftVal = (string)($case->primary_diagnosis ?? '');
                break;
            case 'los':
                $leftVal = $this->lengthOfStayDays($case);
                break;
            case 'cost_over_tariff':
                $leftVal = ((float)($case->actual_total_cost ?? 0)) > ((float)($case->ina_cbg_tariff ?? 0));
                break;
            default:
                // Unknown field: ignore condition
                return true;
        }

        return $this->compare($leftVal, $op, $right);
    }

    private function lengthOfStayDays(PatientCase $case): int
    {
        if ($case->admission_date && $case->discharge_date) {
            return $case->admission_date->diffInDays($case->discharge_date) ?: 0;
        }
        return 0;
    }

    private function compare($leftVal, string $op, string $right): bool
    {
        switch ($op) {
            case '==':
                if (is_bool($leftVal)) {
                    return $leftVal === $this->toBool($right);
                }
                return (string)$leftVal === $right;
            case '!=':
                if (is_bool($leftVal)) {
                    return $leftVal !== $this->toBool($right);
                }
                return (string)$leftVal !== $right;
            case '~=':
                return stripos((string)$leftVal, $right) !== false;
            case '>':
            case '<':
            case '>=':
            case '<=':
                $l = is_numeric($leftVal) ? (float)$leftVal : 0.0;
                $r = is_numeric($right) ? (float)$right : 0.0;
                if ($op === '>') return $l > $r;
                if ($op === '<') return $l < $r;
                if ($op === '>=') return $l >= $r;
                if ($op === '<=') return $l <= $r;
                return false;
            default:
                return true;
        }
    }

    private function toBool(string $val): bool
    {
        $v = strtolower($val);
        return in_array($v, ['1', 'true', 'yes', 'y'], true);
    }
}
