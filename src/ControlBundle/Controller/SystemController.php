<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 16.12.2017
 * Time: 16:13
 */

namespace ControlBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SystemController
 * @package ControlBundle\Controller
 *
 * @Route("/system")
 */
class SystemController extends Controller
{
	/**
	 * @Route("", name="control.system.index")
	 */
	public function indexAction(Request $request)
	{
		return $this->render('@Control/system/index.html.twig');
	}

	/**
	 * @Route("/clear_cache", name="control.system.clear_cache")
	 */
	public function clearCacheAction(Request $request)
	{
		$status = $this->get('app.cache')->clear();
		return $this->redirectToRoute('control.system.index');
	}
}