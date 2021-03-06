<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Domain\Entity\League;
use Domain\Entity\Player;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Entity\Team;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\SeasonTeamRepositoryInterface;
use DomainBundle\CacheTrait;
use DomainBundle\Identity\SeasonTeamIdentity;

/**
 * Class SeasonTeamRepository
 * @package DomainBundle\Repository
 */
class SeasonTeamRepository extends EntityRepository implements SeasonTeamRepositoryInterface
{
	use CacheTrait;

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
			->select('st', 'l', 'lm', 't', 'tm')
			->join('st.league', 'l')
			->join('l.metadata', 'lm')
			->join('st.team', 't')
			->join('t.metadata', 'tm')
			->where('st.season = :season')
			->setParameters(['season' => $season])
			->orderBy('lm.title')
			->addOrderBy('tm.title')
			->getQuery()
			->getResult();
	}

	/**
	 * @param Player $player
	 * @return SeasonTeam[]
	 */
	public function findByPlayer(Player $player): array
	{
		return $this->createQueryBuilder('st')
			->join('Domain\Entity\SeasonTeamMember', 'm', Join::WITH, 'm.seasonTeam = st.id')
			->where('m.player = :player')
			->setParameters(['player' => $player])
			->getQuery()
			->getResult();
	}

	/**
	 * @param League $league
	 * @return SeasonTeam[]
	 */
	public function findByLeague(League $league): array
	{
		return $this->createQueryBuilder('st')
			->where('st.league = :league')
			->setParameters(['league' => $league])
			->getQuery()
			->getResult();
	}

	/**
	 * @param Team $team
	 * @return SeasonTeam[]
	 */
	public function findByTeam(Team $team): array
	{
		return $this->createQueryBuilder('st')
			->where('st.team = :team')
			->setParameters(['team' => $team])
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
			->select('st', 'c', 'cm', 'l', 'lm', 't', 'tm', 's')
			->join('st.coach', 'c')
			->join('c.metadata', 'cm')
			->join('st.team', 't')
			->join('t.metadata', 'tm')
			->join('st.league', 'l')
			->join('l.metadata', 'lm')
			->join('st.season', 's')
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