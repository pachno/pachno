<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities\common\Timeable;
    use pachno\core\entities\tables\Issues;
    use pachno\core\entities\tables\IssueSpentTimes;
    use pachno\core\framework\cli\Command;
    use pachno\core\framework\Context;

    /**
     * CLI command class, main -> fix_times_spent
     *
     * @package pachno
     * @subpackage core
     */
    class FixTimesSpent extends Command
    {

        public function do_execute()
        {
            if (Context::isInstallmode()) {
                $this->cliEcho("Pachno is not installed\n", 'red');
            } else {
                $this->cliEcho("Finding times to fix\n", 'white', 'bold');
                $issuetimes = IssueSpentTimes::getTable()->getAllSpentTimesForFixing();
                $error_issues = [];
                foreach ($issuetimes as $issue_id => $times) {
                    if (count($times) > 1) {
                        $this->cliEcho("Fixing times spent for issue ID {$issue_id}, " . count($times) . " entries\n");
                        $prev_times = Timeable::getZeroedUnitsWithPoints();
                        foreach ($times as $k => $row) {
                            if ($row[IssueSpentTimes::SPENT_POINTS] < $prev_times['points'] ||
                                $row[IssueSpentTimes::SPENT_HOURS] < $prev_times['minutes'] ||
                                $row[IssueSpentTimes::SPENT_HOURS] < $prev_times['hours'] ||
                                $row[IssueSpentTimes::SPENT_DAYS] < $prev_times['days'] ||
                                $row[IssueSpentTimes::SPENT_WEEKS] < $prev_times['weeks'] ||
                                $row[IssueSpentTimes::SPENT_MONTHS] < $prev_times['months']) {
                                $error_issues[] = $issue_id;
                            } else {
                                IssueSpentTimes::getTable()->fixRow($row, $prev_times);
                                $prev_times['points'] += $row[IssueSpentTimes::SPENT_POINTS];
                                $prev_times['minutes'] += $row[IssueSpentTimes::SPENT_MINUTES];
                                $prev_times['hours'] += $row[IssueSpentTimes::SPENT_HOURS];
                                $prev_times['days'] += $row[IssueSpentTimes::SPENT_DAYS];
                                $prev_times['weeks'] += $row[IssueSpentTimes::SPENT_WEEKS];
                                $prev_times['months'] += $row[IssueSpentTimes::SPENT_MONTHS];
                            }
                        }
                    }

                }
                foreach (IssueSpentTimes::getTable()->getAllSpentTimesForFixing() as $issue_id => $times) {
                    foreach ($times as $row) {
                        IssueSpentTimes::getTable()->fixHours($row);
                    }
                    Issues::getTable()->fixHours($issue_id);
                }
                if (count($error_issues) > 0) {
                    $this->cliEcho("\n");
                    $this->cliEcho("All spent times have been attempted fixed, but there were some issues that could not be fixed automatically!\n");
                    $this->cliEcho("This happens if there has been adjustments in time spent, lowering the value for spent points, minutes, hours, days, weeks or months.\n\n");
                    $this->cliEcho("You should fix the issues manually (issue ids corresponding to issue_ids in the timesspent table): ");
                    $this->cliEcho(join(', ', $error_issues) . "\n\n");
                    $this->cliEcho("Spent times fixed!\n\n", 'green');
                } else {
                    $this->cliEcho("All spent times fixed successfully!\n\n", 'green');
                }
                $this->cliEcho("IMPORTANT: Don't run this task again!\n", 'white', 'bold');
            }
        }

        protected function _setup()
        {
            $this->_command_name = 'fix_times_spent';
            $this->_description = "Fixes times spent on upgrade from 3.2 -> 3.3";
        }

    }
