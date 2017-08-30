<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Entity\Team;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\SeasonTeamRepositoryInterface;
use DomainBundle\Identity\SeasonTeamIdentity;

/**
 * Class SeasonTeamRepository
 * @package DomainBundle\Repository
 */
class SeasonTeamRepository extends EntityRepository implements SeasonTeamRepositoryInterface
{

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new SeasonTeamIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param int $id
	 * @return SeasonTeam
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): SeasonTeam
	{
		/** @var SeasonTeam $seasonTeam */
		$seasonTeam = $this->find($id);
		if (empty($seasonTeam)) {
			throw new EntityNotFoundException(sprintf('SeasonTeam with id %d not found', $id));
		}
		return $seasonTeam;
	}

	/**
	 * @param Season $season
	 * @return SeasonTeam[]
	 */
	public function findBySeason(Season $season): array
	{
		return $this->createQueryBuilder('st')
			->where('st.season = :season')
			->setParameters(['season' => $season])
			->getQuery()
			->getResult();
	}

	/**
	 * @param SeasonTeam $seasonTeam
	 */
	public function remove(SeasonTeam $seasonTeam)
	{
		$this->getEntityManager()->remove($seasonTeam);
	}

	/**
	 * @param SeasonTeam $seasonTeam
	 */
	public function save(SeasonTeam $seasonTeam)
	{
		$this->getEntityManager()->persist($seasonTeam);
	}

	/**
	 * @param Team $team
	 * @param Season $season
	 * @return SeasonTeam
	 * @throws EntityNotFoundException
	 */
	public function findByTeamAndSeason(Team $team, Season $season): SeasonTeam
	{
		$seasonteam = $this->createQueryBuilder('st')
			->where('st.team = :team')
			->andWhere('st.season = :season')
			->setParameters(['team' => $team, 'season' => $season])
			->getQuery()
			->getOneOrNullResult();
		if (empty($seasonteam)) {
			throw new EntityNotFoundException('SeasonTeam not found');
		}
		return $seasonteam;
	}
}