<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 09.09.2017
 * Time: 22:26
 */

namespace AppBundle;


use Domain\DTO\Request\CreateTeamRequest;
use Domain\Entity\Team;
use DomainBundle\Entity\TeamMetadata;
use DomainBundle\Repository\TeamRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TeamManager
 * @package AppBundle
 */
class TeamManager implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * @param array $teamRequestData
	 * @return Team
	 */
	public function saveTeam(array $teamRequestData): Team
	{
		$id = $teamRequestData['id'] ?? null;
		$meta = $teamRequestData['metadata'];
		if (empty($id)) {
			$team = $this->get('domain.use_case.create_team_use_case')->execute(new CreateTeamRequest(new TeamMetadata()))->getTeam();
		} else {
			/** @var TeamRepository $repository */
			$repository = $this->get('domain.repository.team');
			$team = $repository->findById($id);
		}
		/** @var TeamMetadata $metadata */
		$metadata = $team->getMetadata();
		$metadata->updateFromData($meta);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $team;
	}

	/**
	 * @param $id
	 * @return object
	 */
	public function get($id)
	{
		return $this->container->get($id);
	}
}