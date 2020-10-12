<?php

namespace App\Controller;

use App\Entity\ShortLink;
use App\Form\Type\LinkCreateType;
use App\Form\Type\LinkStatsType;
use App\Repository\ShortLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ShortLinkController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(LinkCreateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $linkData = $form->getData();
            $shortLink = new ShortLink();

            $uuid = Uuid::v6();
            $shortLink->setUrl($linkData['url']);
            $shortLink->setHash(substr(md5($uuid . $linkData['url']), 0, ShortLink::HASH_LENGTH));
            $shortLink->setExpiresAt(new \DateTime("+" . $linkData['lifetime'] . " hours"));

            $entityManager->persist($shortLink);
            $entityManager->flush();

            return $this->redirectToRoute('show_link', ['hash' => $shortLink->getHash()]);
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/clicks", name="link_clicks")
     */
    public function clicks(Request $request, ShortLinkRepository $shortLinkRepository)
    {
        $form = $this->createForm(LinkStatsType::class);
        $shortLink = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->getData()['short_url'];
            $hash = substr($url, -ShortLink::HASH_LENGTH);
            $shortLink = $shortLinkRepository->findOneBy(['hash' => $hash]);
        }

        return $this->render('clicks.html.twig', [
            'link_url' => $shortLink ? $shortLink->getUrl() : null,
            'link_clicks' => $shortLink ? $shortLink->getClicks() : null,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{hash}", name="handle_link",  requirements={"hash"=".{6}"})
     */
    public function handle(Request $request, ShortLinkRepository $shortLinkRepository, EntityManagerInterface $entityManager, string $hash)
    {
        $shortLink = $shortLinkRepository->findActiveByHash($hash);
        if (!$shortLink) {
            throw new NotFoundHttpException("Link with hash `$hash` not found");
        }

        $shortLink->setClicks($shortLink->getClicks() + 1);

        $entityManager->persist($shortLink);
        $entityManager->flush();

        return $this->redirect($shortLink->getUrl());
    }

    /**
     * @Route("/show/{hash}", name="show_link")
     */
    public function show(Request $request, ShortLinkRepository $shortLinkRepository, string $hash)
    {
        $shortLink = $shortLinkRepository->findOneBy(['hash' => $hash]);
        if (!$shortLink) {
            throw new NotFoundHttpException("Link with hash `$hash` not found");
        }

        return $this->render('details.html.twig', [
            'link_hash' => $shortLink->getHash(),
            'link_url' => $shortLink->getUrl()
        ]);
    }
}
