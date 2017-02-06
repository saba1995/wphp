<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Firm;
use FeedbackBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Firm controller.
 *
 * @Route("/firm")
 */
class FirmController extends Controller
{
    /**
     * Lists all Firm entities.
     *
     * @Route("/", name="firm_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Firm e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $firms = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'firms' => $firms,
        );
    }

    /**
     * Full text search for Firm entities.
	 *
     * @Route("/fulltext", name="firm_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Firm');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$firms = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$firms = array();
		}

        return array(
            'firms' => $firms,
			'q' => $q,
        );
    }

    /**
     * Finds and displays a Firm entity.
     *
     * @Route("/{id}", name="firm_show")
     * @Method({"GET","POST"})
     * @Template()
	 * @param Firm $firm
     */
    public function showAction(Request $request, Firm $firm)
    {
        $comment = new Comment();
        $form = $this->createForm('FeedbackBundle\Form\CommentType', $comment);
        $form->handleRequest($request);
		$service = $this->get('feedback.comment');
        if ($form->isSubmitted() && $form->isValid()) {
            $service->addComment($firm, $comment);
            $this->addFlash('success', 'Thank you for your suggestion.');
            return $this->redirect($this->generateUrl('firm_show', array('id' => $firm->getId())));
        }

        $comments = $service->findComments($firm);
		return array(
            'form' => $form->createView(),
            'firm' => $firm,
			'comments' => $comments,
			'service' => $service,
        );
    }
}
