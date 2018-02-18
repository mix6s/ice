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
use DomainBundle\DTO\GamesFilter;
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
	private $bySeasonTeams = [];

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
		$this->getEntityManager()->persist($game);
		$this->getCache()->invalidateTags([
			'game.' . $game->getId(),
			'games',
			'season.' . $game->getSeason()->getId(),
			'seasonteam.' . $game->getSeasonTeamA()->getId(),
			'seasonteam.' . $game->getSeasonTeamB()->getId(),
		]);
		$this->getCache()->clear();
	}

	/**
	 * @param Game $game
	 */
	public function remove(Game $game)
	{
		$this->getEntityManager()->remove($game);
		$this->getCache()->clear();
	}

	/**
	 * @param SeasonTeam $seasonTeam
	 * @return Game[]
	 */
	public function findBySeasonTeam(SeasonTeam $seasonTeam)
	{
		if (array_key_exists($seasonTeam->getId(), $this->bySeasonTeams)) {
			return $this->bySeasonTeams[$seasonTeam->getId()];
		}
		$this->bySeasonTeams[$seasonTeam->getId()] = $this->getEntityManager()->createQueryBuilder()
			->select('g')
			->from('Domain:Game', 'g')
			->where('g.seasonTeamA = :team')
			->orWhere('g.seasonTeamB = :team')
			->setParameter('team', $seasonTeam)
			->getQuery()
			->getResult();
		return $this->bySeasonTeams[$seasonTeam->getId()];
	}

	/**
	 * @return Game[]
	 */
	public function findAllGames()
	{
		return $this->getEntityManager()->createQueryBuilder()
			->from('Domain:Game', 'g')
			->select('g')
			->orderBy('g.datetime', 'DESC')
			->getQuery()
			->getResult();
	}


	/**
	 * @return Game[]
	 */
	public function findGamesByFilter(GamesFilter $filter)
	{
		$builder = $this->getEntityManager()->createQueryBuilder()
			->from('Domain:Game', 'g')
			->select('g')
			->orderBy('g.datetime', 'DESC');

		if ($filter->hasStatus()) {
			$builder->andWhere('g.state = :state')->setParameter('state', $filter->getStatus());
		}

		if ($filter->hasFromDt()) {
			$builder->andWhere('g.datetime >= :fromDt')->setParameter('fromDt', $filter->getFromDt());
		}

		if ($filter->hasToDt()) {
			$builder->andWhere('g.datetime <= :toDt')->setParameter('toDt', $filter->getToDt());
		}

		return $builder->getQuery()
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