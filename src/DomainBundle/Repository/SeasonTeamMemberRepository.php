<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 29.08.2017
 * Time: 21:56
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
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
			->where('stm.seasonTeam = :seasonTeam')
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
	 * @return SeasonTeamMember
	 * @throws EntityNotFoundException
	 */
	public function findByPlayerAndSeason(Player $player, Season $season): SeasonTeamMember
	{
		$member = $this->createQueryBuilder('stm')
			->join('stm.seasonTeam', 'st')
			->where('st.season = :season')
			->andWhere('stm.player = :player')
			->setParameters(['player' => $player, 'season' => $season])
			->getQuery()
			->getOneOrNullResult();
		if (empty($member)) {
			throw new EntityNotFoundException('SeasonTeamMember not found');
		}
		return $member;
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
			->where('stm.id = :id')
			->setParameters(['id' => $id])
			->getQuery()
			->getOneOrNullResult();
		if (empty($member)) {
			throw new EntityNotFoundException('SeasonTeamMember not found');
		}
		return $member;
	}
}