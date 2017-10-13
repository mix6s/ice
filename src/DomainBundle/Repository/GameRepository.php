<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:30
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\Game;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\GameRepositoryInterface;
use DomainBundle\CacheTrait;
use DomainBundle\Identity\GameIdentity;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

/**
 * Class GameRepository
 * @package DomainBundle\Repository
 */
class GameRepository extends EntityRepository implements GameRepositoryInterface
{
	use CacheTrait;

	const DEFAULT_LIMIT = 20;

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		$identity = new GameIdentity();
		$this->getEntityManager()->persist($identity);
		$this->getEntityManager()->flush($identity);
		return $identity->getId();
	}


	/**
	 * @param int $id
	 * @return Game
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Game
	{
		/** @var Game $game */
		$game = $this->find($id);
		if (empty($game)) {
			throw new EntityNotFoundException(sprintf('Game with id %d not found', $id));
		}
		return $game;
	}

	/**
	 * @param Game $game
	 */
	public function save(Game $game)
	{
		$this->getCache()->invalidateTags([
			'game.' . $game->getId(),
			'season.' . $game->getSeason()->getId(),
			'seasonteam.' . $game->getSeasonTeamA()->getId(),
			'seasonteam.' . $game->getSeasonTeamB()->getId(),
		]);
		$this->getEntityManager()->persist($game);
	}

	/**
	 * @param Game $game
	 */
	public function remove(Game $game)
	{
		$this->getEntityManager()->remove($game);
	}

	/**
	 * @return Game[]
	 */
	public function findBySeasonTeam(SeasonTeam $seasonTeam)
	{
		return $this->getEntityManager()->createQueryBuilder()
			->select('g')
			->from('Domain:Game', 'g')
			->where('g.seasonTeamA = :team')
			->orWhere('g.seasonTeamB = :team')
			->setParameter('team', $seasonTeam)
			->getQuery()
			->getResult();
	}

	/**
	 * @return Game[]
	 */
	public function findBySeason(Season $season)
	{
		return $this->getEntityManager()->createQueryBuilder()
			->select('g')
			->from('Domain:Game', 'g')
			->where('g.season = :season')
			->setParameter('season', $season)
			->getQuery()
			->getResult();
	}
}