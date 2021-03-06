<?php
/**
 * Advertisement controller.
 */

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdvertisementType;
use App\Repository\AdvertisementRepository;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AdvertisementController.
 *
 * @Route("/Advertisement")
 */
class AdvertisementController extends AbstractController
{

    /**
     * Index action.
     *
     * @param Request $request        HTTP request
     * @param AdvertisementRepository $AdvertisementRepository Advertisement repository
     * @param PaginatorInterface $paginator      Paginator
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="Advertisement_index",
     * )
     */
    public function index(Request $request, AdvertisementRepository $AdvertisementRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $AdvertisementRepository->queryAll(),
            $request->query->getInt('page', 1),
            AdvertisementRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'Advertisement/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param Advertisement $Advertisement Advertisement entity
     *
     * @return Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="Advertisement_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     * IsGranted("ROLE_ADMIN")
     *
     */

    public function show(Advertisement $Advertisement): Response
    {
        return $this->render(
            'Advertisement/show.html.twig',
            ['Advertisement' => $Advertisement]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request        HTTP request
     * @param AdvertisementRepository $AdvertisementRepository Advertisement repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="Advertisement_create",
     * )
     */
    public function create(Request $request, AdvertisementRepository $AdvertisementRepository): Response
    {
        $Advertisement = new Advertisement();
        $form = $this->createForm(AdvertisementType::class, $Advertisement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Advertisement->setCreatedAt(new DateTime());
            $Advertisement->setUpdatedAt(new DateTime());
            $AdvertisementRepository->save($Advertisement);
            $this->addFlash('success', 'action_successful');

            return $this->redirectToRoute('Advertisement_index');
        }

        return $this->render(
            'Advertisement/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request        HTTP request
     * @param Advertisement $Advertisement           Advertisement entity
     * @param AdvertisementRepository $AdvertisementRepository Advertisement repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="Advertisement_edit",
     * )
     */
    public function edit(Request $request, Advertisement $Advertisement, AdvertisementRepository $AdvertisementRepository): Response
    {
        $form = $this->createForm(AdvertisementType::class, $Advertisement, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $AdvertisementRepository->save($Advertisement);
            $this->addFlash('success', 'action_successful');

            return $this->redirectToRoute('Advertisement_index');
        }

        return $this->render(
            'Advertisement/edit.html.twig',
            [
                'form' => $form->createView(),
                'Advertisement' => $Advertisement,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request        HTTP request
     * @param Advertisement $Advertisement           Advertisement entity
     * @param AdvertisementRepository $AdvertisementRepository Advertisement repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="Advertisement_delete",
     * )
     */
    public function delete(Request $request, Advertisement $Advertisement, AdvertisementRepository $AdvertisementRepository): Response
    {
        $form = $this->createForm(FormType::class, $Advertisement, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $AdvertisementRepository->delete($Advertisement);
            $this->addFlash('success', 'action_successful');

            return $this->redirectToRoute('Advertisement_index');
        }

        return $this->render(
            'Advertisement/delete.html.twig',
            [
                'form' => $form->createView(),
                'Advertisement' => $Advertisement,
            ]
        );
    }
}