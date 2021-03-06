<?php
/*
 * Copyright (C) 2007-2013 Alibaba Group Holding Limited
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 */
 
include_once('BaseParser.php');
class NoseTestsParser extends BaseParser
{
    protected function parseCaseAmount()
    {
        foreach($this->parserInfo->cases as $case)
        {
            if($case->result == CaseInfo::RESULT_PASSED)
                $this->parserInfo->case_passed_amount++;
            else if($case->result == CaseInfo::RESULT_FAILED)
                $this->parserInfo->case_failed_amount++;
            else if($case->result == CaseInfo::RESULT_SKIPPED)
                $this->parserInfo->case_skipped_amount++;
            else if($case->result == CaseInfo::RESULT_BLOCKED)
                $this->parserInfo->case_blocked_amount++;
        }
        $this->parserInfo->case_total_amount = $this->parserInfo->case_passed_amount +
                $this->parserInfo->case_failed_amount + $this->parserInfo->case_skipped_amount +
                $this->parserInfo->case_blocked_amount;
    }
    
    protected function parseCases()
    {
        $casePattern = "/(.*?) \.\.\. .*?(ok|FAIL)/is";
        $result = array('ok' => CaseInfo::RESULT_PASSED, 
            'FAIL' => CaseInfo::RESULT_FAILED);
        
        preg_match_all($casePattern, $this->output, $matches);
        foreach ($matches[0] as $key => $value)
        {
            $caseInfo = new CaseInfo();
            $caseInfo->name = trim($matches[1][$key]);
            $caseInfo->result = $result[trim($matches[2][$key])];
            if(mb_detect_encoding($caseInfo->name,"UTF-8, GBK") != "UTF-8")
                    $caseInfo->name = iconv("GBK", "UTF-8", $caseInfo->name); 
            $this->parserInfo->cases[] = $caseInfo;
        }
    }
}
?>