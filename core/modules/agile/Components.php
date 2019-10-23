<?php

    namespace pachno\core\modules\agile;

    use pachno\core\entities\BoardColumn;
    use pachno\core\entities\Category;
    use pachno\core\entities\CustomDatatype;
    use pachno\core\entities\Milestone;
    use pachno\core\entities\Priority;
    use pachno\core\entities\SavedSearch;
    use pachno\core\entities\Severity;
    use pachno\core\entities\Status;
    use pachno\core\entities\tables\SavedSearches;
    use pachno\core\framework;

    /**
     * action components for the agile module
     */
    class Components extends framework\ActionComponent
    {

        public function componentEditAgileBoard()
        {
            $i18n = framework\Context::getI18n();
            $this->autosearches = [
                SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES => $i18n->__('Project open issues (recommended)'),
                SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project open issues (including subprojects)'),
                SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES => $i18n->__('Project closed issues'),
                SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project closed issues (including subprojects)'),
                SavedSearch::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH => $i18n->__('Project issues reported last month'),
                SavedSearch::PREDEFINED_SEARCH_PROJECT_WISHLIST => $i18n->__('Project wishlist')
            ];
            $this->savedsearches = SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID(), $this->board->getProject()->getID());
            $this->issuetypes = $this->board->getProject()->getIssuetypeScheme()->getIssuetypes();
            $this->issuefields = CustomDatatype::getByFieldTypes([CustomDatatype::DATE_PICKER, CustomDatatype::DATETIME_PICKER]);
            $this->swimlane_groups = [
                'priority' => $i18n->__('Issue priority'),
                'severity' => $i18n->__('Issue severity'),
                'category' => $i18n->__('Issue category'),
            ];
            $this->priorities = Priority::getAll();
            $this->severities = Severity::getAll();
            $this->categories = Category::getAll();
            $fakecolumn = new BoardColumn();
            $fakecolumn->setBoard($this->board);
            $this->fakecolumn = $fakecolumn;
        }

        public function componentEditBoardColumn()
        {
            $this->statuses = Status::getAll();
        }

        public function componentMilestoneBox()
        {
            $this->include_counts = (isset($this->include_counts)) ? $this->include_counts : false;
            $this->include_buttons = (isset($this->include_buttons)) ? $this->include_buttons : true;
        }

        public function componentBoardSwimlane()
        {
            $this->issues = $this->swimlane->getIssues();
        }

        public function componentBoardColumnheader()
        {
            $this->statuses = Status::getAll();
        }

        public function componentWhiteboardTransitionSelector()
        {
            foreach ($this->board->getColumns() as $column) {
                if ($column->hasIssue($this->issue)) {
                    $this->current_column = $column;
                    break;
                }
            }

            $transition_ids = [];
            $same_transition_statuses = [];

            foreach ($this->transitions as $status_id => $transitions) {
                if (!in_array($status_id, $this->statuses)) continue;

                foreach ($transitions as $transition) {
                    if (in_array($transition->getID(), $transition_ids)) {
                        $same_transition_statuses[] = $status_id;
                    } else {
                        $transition_ids[] = $transition->getID();
                    }
                }
            }

            $this->same_transition_statuses = $same_transition_statuses;
            $this->statuses_occurred = array_fill_keys($this->statuses, 0);
        }

        public function componentColorpicker()
        {
            $this->colors = ['#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF'];
        }

        public function componentMilestone()
        {
            if (!isset($this->milestone)) {
                $this->milestone = new Milestone();
                $this->milestone->setProject($this->project);
            }
        }

    }

