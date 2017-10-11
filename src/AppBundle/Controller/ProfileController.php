<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 22.07.2017
 * Time: 14:23
 */

namespace AppBundle\Controller;

use Domain\Entity\Player;
use Domain\Exception\EntityNotFoundException;
use DomainBundle\Entity\PlayerMetadata;
use AppBundle\Entity\User;
use AppBundle\Form\Type\PlayerProfileFormType;
use Liip\ImagineBundle\Model\Binary;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/profile")
 * @Security("has_role('ROLE_USER')")
 */
class ProfileController extends Controller
{
	/**
	 * @Route("", name="profile.current")
	 */
	public function currentAction()
	{
		/** @var User $user */
		$user = $this->getUser();
		if ($user->isPlayer()) {
			/** @var PlayerMetadata $profile */
			$profile = $user->getPlayerProfile();
			if ($profile) {
				return $this->forward("AppBundle:Profile:index", ['id' => $user->getPlayer()->getId()]);
			}
			return $this->redirectToRoute('profile.player.edit');
		}
	}

	/**
	 * @Route("/edit", name="profile.player.edit")
	 * @Security("has_role('ROLE_PLAYER')")
	 */
	public function editPlayerProfileAction(Request $request)
	{
		/** @var User $user */
		$user = $this->getUser();
		$profile = $user->getPlayerProfile();
		$form = $this->createForm(PlayerProfileFormType::class, $profile);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var PlayerMetadata $profile */
			$profile = $form->getData();
			$user->setPlayerProfile($profile);
			$this->get('doctrine.orm.entity_manager')->persist($user);
			$this->get('doctrine.orm.entity_manager')->flush();
			return $this->redirectToRoute('profile.current');
		}
		return $this->render('profile-empty.twig', [
			'form' => $form->createView(),
			'profile' => $profile,
		]);
	}

	/**
	 * @Route("/{id}", name="profile.index")
	 */
	public function indexAction($id)
	{
		/** @var Player|null $player */
		$player = $this->get('domain.repository.player')->find($id);
		if (!$player) {
			throw $this->createNotFoundException();
		}
		try {
			$season = $this->get('domain.repository.season')->findById($this->get('settings.manager')->getCurrentSeasonId());
			$seasonTeamMember = $this->get('domain.repository.seasonteammember')->findByPlayerAndSeason($player, $season);
		} catch (EntityNotFoundException $e) {
			$seasonTeamMember = null;
		}

		$seasonTeams = $this->get('domain.repository.seasonteam')->findByPlayer($player);
		$members = $this->get('domain.repository.seasonteammember')->findByPlayer($player);
		$isCurrentPlayer = false;
		/** @var User $user */
		$user = $this->getUser();
		if ($user) {
			$currentPlayer = $user->getPlayer();
			if ($currentPlayer) {
				$isCurrentPlayer = $player->getId() === $currentPlayer->getId();
			}
		}
		return $this->render('profile.twig', [
			'profile' => $player->getMetadata(),
			'member' => $seasonTeamMember,
			'isCurrentPlayer' => $isCurrentPlayer,
			'members' => $members
		]);
	}

	/**
	 * @Route("/avatar/upload", name="profile.player.avatar.upload")
	 * @Security("has_role('ROLE_PLAYER')")
	 */
	public function uploadPlayerProfileAvatarAction(Request $request)
	{
		$base64Image = $request->request->get('image');
		$contents = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
		$binary = new Binary($contents, 'image/png', 'png');
		$filename = uniqid('avatar_', true) . '.png';

		$response = $this->get('liip_imagine.filter.manager')->applyFilter($binary, 'avatar_mini');
		$f = fopen($this->getParameter('web_dir') . '/avatar/mini/' . $filename, 'w');
		fwrite($f, $response->getContent());
		fclose($f);

		$response = $this->get('liip_imagine.filter.manager')->applyFilter($binary, 'avatar_normal');
		$f = fopen($this->getParameter('web_dir') . '/avatar/' . $filename, 'w');
		fwrite($f, $response->getContent());
		fclose($f);
		/** @var User $user */
		$user = $this->getUser();
		$profile = $user->getPlayerProfile();
		if (!$profile) {
			$profile = new PlayerMetadata();
		}
		$profile->setImage($filename);
		$user->setPlayerProfile($profile);
		$this->get('doctrine.orm.entity_manager')->persist($user);
		$this->get('doctrine.orm.entity_manager')->flush();
		return $this->json($filename);
	}
}