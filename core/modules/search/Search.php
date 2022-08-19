<?php

    namespace pachno\core\modules\search;

    use pachno\core\entities\Issue;
    use pachno\core\entities\Project;
    use pachno\core\framework;

    class Search extends framework\CoreModule
    {
    
        /**
         * @param Project $project
         * @return array
         */
        public static function getQuicksearchJsonFromProject(Project $project)
        {
            return [
                'icon_url' => $project->getIconName(),
                'name' => $project->getName(),
                'type' => 'navigate',
                'url' => framework\Context::getRouting()->generate('project_dashboard', ['project_key' => $project->getKey()])
            ];
        }

        /**
         * @param Issue $issue
         * @return array
         */
        public static function getQuicksearchJsonFromIssue(Issue $issue)
        {
            return [
                'icon' => $issue->getIssueType()->toJSON(false),
                'name' => $issue->getFormattedTitle(true),
                'type' => 'navigate',
                'url' => $issue->getUrl()
            ];
        }

        /**
         * Header "Publish" page names
         *
         * @Listener(module="core", identifier="project/templates/projectheader::pagename")
         *
         * @param framework\Event $event
         */
        public function dashboardProjectHeaderPagename(framework\Event $event)
        {
            switch (framework\Context::getRouting()->getCurrentRoute()->getModuleName()) {
                case 'search':
                    $event->setReturnValue(framework\Context::getI18n()->__('Issues'));
                    $event->setProcessed(true);
                    break;
            }
        }

    }
