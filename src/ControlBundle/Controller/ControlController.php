<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 16:37
 */

namespace ControlBundle\Controller;


use Domain\Entity\League;
use Domain\Entity\Player;
use Domain\Entity\Season;
use Domain\Entity\SeasonTeam;
use Domain\Entity\Team;
use DomainBundle\Entity\LeagueMetadata;
use DomainBundle\Entity\PlayerMetadata;
use DomainBundle\Entity\TeamMetadata;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ControlController
 * @package ControlBundle\Controller
 */
class ControlController extends Controller
{
	/**
	 * @Route("", name="control.index")
	 */
	public function indexAction()
	{
		return $this->redirectToRoute('control.games.list');
	}


	/**
	 * @Route("/typeahead", name="control.typeahead")
	 */
	public function typeaheadAction(Request $request)
	{
		$options = $request->get('options', []);
		$seasonQuery = $request->get('season');
		if (!empty($seasonQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$seasons = $qb
				->from('Domain:Season', 's')
				->select('s')
				->where('s.year like :query ')
				->setParameter('query', '%' . $seasonQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var Season $season */
			foreach ($seasons as $season) {
				$result[] = [
					'name' => ($season->getYear() - 1) . '/' . $season->getYear(),
					'season' => $season
				];
			}
			return $this->json($result);
		}

		$seasonteamQuery = $request->get('seasonteam');
		if (!empty($seasonteamQuery)) {



			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$qb
				->from('Domain:SeasonTeam', 'st')
				->join('Domain:Team', 't', 'WITH', 't.id = st.team')
				->join('t.metadata', 'm')
				->select('st')
				->where('m.title like :query ')
				->setParameter('query', '%' . $seasonteamQuery . '%')
				;

			if (!empty($options['seasonId'])) {
				$qb->andWhere('st.season = :seasonId')->setParameter('seasonId', $options['seasonId']);
			}

			$teams = $qb
				->getQuery()
				->getResult();
			$result = [];
			/** @var SeasonTeam $seasonteam */
			foreach ($teams as $seasonteam) {
				/** @var TeamMetadata $metadata */
				$metadata = $seasonteam->getTeam()->getMetadata();
				$result[] = [
					'name' => $metadata->getTitle(),
					'seasonteam' => $seasonteam
				];
			}
			return $this->json($result);
		}

		$teamQuery = $request->get('team');
		if (!empty($teamQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$teams = $qb
				->from('Domain:Team', 't')
				->join('t.metadata', 'm')
				->select('t')
				->where('m.title like :query ')
				->setParameter('query', '%' . $teamQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var Team $team */
			foreach ($teams as $team) {
				/** @var TeamMetadata $meta */
				$meta = $team->getMetadata();

				$result[] = [
					'name' => $meta->getTitle(),
					'team' => $team
				];
			}
			return $this->json($result);
		}

		$coachQuery = $request->get('coach');
		if (!empty($coachQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$players = $qb
				->from('Domain:Player', 'p')
				->join('p.metadata', 'm')
				->select('p')
				->where('m.surname like :query')
				->orWhere('m.firstName like :query')
				->orWhere('m.secondName like :query')
				->setParameter('query', '%' . $coachQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var Player $player */
			foreach ($players as $player) {
				/** @var PlayerMetadata $meta */
				$meta = $player->getMetadata();

				$result[] = [
					'name' => $meta->getFullName(),
					'coach' => $player
				];
			}
			return $this->json($result);
		}

		$playerQuery = $request->get('player');
		if (!empty($playerQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$players = $qb
				->from('Domain:Player', 'p')
				->join('p.metadata', 'm')
				->select('p')
				->where('m.surname like :query')
				->orWhere('m.firstName like :query')
				->orWhere('m.secondName like :query')
				->setParameter('query', '%' . $playerQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var Player $player */
			foreach ($players as $player) {
				/** @var PlayerMetadata $meta */
				$meta = $player->getMetadata();

				$result[] = [
					'name' => $meta->getFullName(),
					'player' => $player
				];
			}
			return $this->json($result);
		}

		$leagueQuery = $request->get('league');
		if (!empty($leagueQuery)) {
			$em = $this->get('doctrine.orm.entity_manager');
			$qb = $em->createQueryBuilder();
			$leagues = $qb
				->from('Domain:League', 'l')
				->join('l.metadata', 'm')
				->select('l')
				->where('m.title like :query')
				->setParameter('query', '%' . $leagueQuery . '%')
				->getQuery()
				->getResult();
			$result = [];
			/** @var League $league */
			foreach ($leagues as $league) {
				/** @var LeagueMetadata $meta */
				$meta = $league->getMetadata();

				$result[] = [
					'name' => $meta->getTitle(),
					'league' => $league
				];
			}
			return $this->json($result);
		}
	}


	/**
	 * @Route("/avatar/upload", name="control.team.avatar.upload")
	 */
	public function uploadTeamAvatarAction(Request $request)
	{
		/** @var UploadedFile $file */
		$file = $request->files->get('file');
		$tmpfilename = uniqid();
		$file->move($this->getParameter('web_dir') . '/backend/img/upload/', $tmpfilename);
		$binary = new Binary(file_get_contents($this->getParameter('web_dir') . '/backend/img/upload/' . $tmpfilename), 'image/png', 'png');
		$filename = uniqid('t_avatar_', true) . '.png';

		$response = $this->get('liip_imagine.filter.manager')->applyFilter($binary, 'avatar_mini');
		$f = fopen($this->getParameter('web_dir') . '/avatar/mini/' . $filename, 'w');
		fwrite($f, $response->getContent());
		fclose($f);

        $response = $this->get('liip_imagine.filter.manager')->applyFilter($binary, 'avatar_normal');
        $f = fopen($this->getParameter('web_dir') . '/avatar/' . $filename, 'w');
        fwrite($f, $response->getContent());
        fclose($f);
        return $this->json($filename);
    }


}