<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 09.09.2017
 * Time: 22:26
 */

namespace AppBundle;


use Domain\DTO\Request\CreateTeamRequest;
use Domain\DTO\Request\CreateLeagueRequest;
use Domain\Entity\Team;
use Domain\Entity\League;
use DomainBundle\Entity\LeagueMetadata;
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
	 * @param array $leagueRequestData
	 * @return League
	 */
	public function saveLeague(array $leagueRequestData): League
	{
		$id = $leagueRequestData['id'] ?? null;
		$meta = $leagueRequestData['metadata'];
		if (empty($id)) {
			$league = $this->get('domain.use_case.create_league_use_case')->execute(new CreateLeagueRequest(new LeagueMetadata()))->getLeague();
		} else {
			/** @var LeagueRepository $repository */
			$repository = $this->get('domain.repository.league');
			$league = $repository->findById($id);
		}
		/** @var LeagueMetadata $metadata */
		$metadata = $league->getMetadata();
		$metadata->updateFromData($meta);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $league;
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