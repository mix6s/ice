<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 29.08.2017
 * Time: 21:56
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\League;
use Domain\Entity\Player;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Entity\SeasonTeamMember;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\SeasonTeamMemberRepositoryInterface;
use DomainBundle\Identity\SeasonTeamMemberIdentity;

/**
 * Class SeasonTeamMemberRepository
 * @package DomainBundle\Repository
 */
class SeasonTeamMemberRepository extends EntityRepository implements SeasonTeamMemberRepositoryInterface
{

	/**
	 * @param SeasonTeam $seasonTeam
	 * @return SeasonTeamMember[]
	 */
	public function findBySeasonTeam(SeasonTeam $seasonTeam): array
	{
		return $this->createQueryBuilder('stm')
			->select('stm', 'p', 'pm')
			->where('stm.seasonTeam = :seasonTeam')
			->join('stm.player', 'p')
			->join('p.metadata', 'pm')
			->setParameters(['seasonTeam' => $seasonTeam])
			->getQuery()
			->getResult();
	}

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new SeasonTeamMemberIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}

	/**
	 * @param Player $player
	 * @param Season $season
	 * @return SeasonTeamMember[]
	 * @throws EntityNotFoundException
	 */
	public function findByPlayerAndSeason(Player $player, Season $season): array
	{
		return $this->createQueryBuilder('stm')
			->join('stm.seasonTeam', 'st')
			->where('st.season = :season')
			->andWhere('stm.player = :player')
			->setParameters(['player' => $player, 'season' => $season])
			->getQuery()
			->getResult();
	}

	/**
	 * @param Season $season
	 * @return SeasonTeamMember[]
	 */
	public function findBySeason(Season $season): array
	{
		return $this->createQueryBuilder('stm')
			->join('stm.seasonTeam', 'st')
			->where('st.season = :season')
			->setParameters(['season' => $season])
			->getQuery()
			->getResult();
	}

	/**
	 * @param Player $player
	 * @return SeasonTeamMember[]
	 */
	public function findByPlayer(Player $player): array
	{
		return $this->createQueryBuilder('stm')
			->join('stm.seasonTeam', 'st')
			->join('st.season', 's')
			->join('st.league', 'l')
			->andWhere('stm.player = :player')
			->setParameters(['player' => $player])
			->orderBy('s.year', 'DESC')
			->getQuery()
			->getResult();
	}

	/**
	 * @param SeasonTeamMember $member
	 */
	public function save(SeasonTeamMember $member)
	{
		$this->getEntityManager()->persist($member);
	}

	/**
	 * @param SeasonTeamMember $member
	 */
	public function remove(SeasonTeamMember $member)
	{
		$this->getEntityManager()->remove($member);
	}

	/**
	 * @param int $id
	 * @return SeasonTeamMember
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): SeasonTeamMember
	{
		$member = $this->createQueryBuilder('stm')
			->join('stm.player', 'p')
			->join('p.metadata', 'pm')
			->where('stm.id = :id')
			->setParameters(['id' => $id])
			->getQuery()
			->getOneOrNullResult();
		if (empty($member)) {
			throw new EntityNotFoundException('SeasonTeamMember not found');
		}
		return $member;
	}

	/**
	 * @param Player $player
	 * @param Season $season
	 * @param League $league
	 * @return SeasonTeamMember
	 * @throws EntityNotFoundException
	 */
	public function findByPlayerLeagueAndSeason(Player $player, Season $season, League $league): SeasonTeamMember
	{
		$member = $this->createQueryBuilder('stm')
			->join('stm.seasonTeam', 'st')
			->where('st.season = :season')
			->andWhere('stm.player = :player')
			->andWhere('st.league = :league')
			->setParameters(['player' => $player, 'season' => $season, 'league' => $league])
			->getQuery()
			->getOneOrNullResult();
		if (empty($member)) {
			throw new EntityNotFoundException('SeasonTeamMember not found');
		}
		return $member;
	}
}