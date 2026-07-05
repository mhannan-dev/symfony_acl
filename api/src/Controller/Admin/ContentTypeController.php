<?php

namespace App\Controller\Admin;

use App\Entity\ContentType;
use App\Repository\ContentTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/content-types')]
class ContentTypeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'app_admin_content_types')]
    public function index(InertiaInterface $inertia, ContentTypeRepository $repo)
    {
        return $inertia->render('ContentTypes/Index', [
            'contentTypes' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_content_types_new')]
    public function new(InertiaInterface $inertia)
    {
        return $inertia->render('ContentTypes/Form', [
            'contentType' => null,
        ]);
    }

    #[Route('/save', name: 'app_admin_content_types_save', methods: ['POST'])]
    public function save(Request $request)
    {
        $data = $request->request->all();

        $ct = $data['id'] ? $this->em->getRepository(ContentType::class)->find($data['id']) : new ContentType();
        $ct->setAppLabel($data['appLabel']);
        $ct->setModel($data['model']);

        $this->em->persist($ct);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_content_types');
    }

    #[Route('/{id}/edit', name: 'app_admin_content_types_edit')]
    public function edit(InertiaInterface $inertia, ContentType $contentType)
    {
        return $inertia->render('ContentTypes/Form', [
            'contentType' => $contentType,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_content_types_delete', methods: ['POST'])]
    public function delete(ContentType $contentType)
    {
        $this->em->remove($contentType);
        $this->em->flush();
        return $this->redirectToRoute('app_admin_content_types');
    }
}
