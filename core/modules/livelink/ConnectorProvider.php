<?php

namespace pachno\core\modules\livelink;

use pachno\core\entities\Branch;
use pachno\core\entities\Commit;
use pachno\core\entities\Project;
use pachno\core\entities\User;
use pachno\core\framework\Request;

interface ConnectorProvider
{

    public function getName();

    /**
     * @return BaseConnector
     */
    public function getConnector();

    public function postConnectorSettings(Request $request);

    public function removeConnectorSettings(Request $request);

    public function getInputOptionsForProjectEdit(Request $request);

    public function getRepositoryDisplayNameForProject(Project $project);

    public function getImportDisplayNameForProjectEdit(Request $request);

    public function getImportProjectNameForProjectEdit(Request $request);

    public function saveProjectConnectorSettings(Request $request, Project $project, $secret);

    public function removeProjectConnectorSettings(Project $project, $secret);

    public function webhook(Request $request, Project $project);

    public function importProject(Project $project, User $user);

    public function importSingleCommit(Project $project, Commit $commit);

    public function getCommitUrl(Commit $commit);

}