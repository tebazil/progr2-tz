<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Entity\House;
use App\Repository\HouseRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/house/list")
     * @param HouseRepository $hr
     * @return Response
     */
    public function houseList(HouseRepository $hr): Response
    {
        $houses = $hr->findAll();

        return $this->render('main/house-list.html.twig', [
//            'houses' => [$houses[0], $houses[1]],
            'houses' => $houses,
//            'houses' => (array)$houses,
        ]);
    }

    /**
     * @Route("/house/add")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws \Exception
     */
    public function houseAdd(Request $request, EntityManagerInterface $em): Response
    {

        $postInputBag = $request->request;
        if($request->isMethod('POST') && $this->isCsrfTokenValid('token', $postInputBag->get('token'))) {
            
            $streetName = $postInputBag->get('streetName');
            $number = $postInputBag->get('number');

            if(!$streetName) {
                throw new \Exception("Название улицы обязательно");
            }
            if(!$number) {
                throw new \Exception("Номер дома обязателен");
            }

            $house = new House();
            $house->setStreetName($streetName);
            $house->setNumber($number);
            $em->persist($house);
            try {
                $em->flush();
            }
            catch(UniqueConstraintViolationException $e) {
                if(str_contains($e->getMessage(), 'Duplicate entry')) {
                    throw new \Exception("Такой дом уже есть");
                }
            }
            return $this->redirect('/house/list');
        }
        return $this->render('main/house-form.html.twig',       [
            'street_name' => '',
            'number' => '',
        ]);
    }

    /**
     * @Route("/house/{houseId}/flat-list", requirements={"houseId"="\d+"})
     * @param int $houseId
     * @param HouseRepository $hr
     * @return Response
     */
    public function flatList(int $houseId, HouseRepository $hr): Response
    {
        $house = $hr->find($houseId);
        if(!$house) {
            throw new \Exception("Дом не найден");
        }


        return $this->render('main/flat-list.html.twig', [
            'houseId' => $house->getId(),
            'flats' => $house->getFlats(),
        ]);
    }

    /**
     * @Route("/house/{houseId}/flat-add", requirements={"houseId"="\d+"})
     * @param int $houseId
     * @param HouseRepository $hr
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function flatAdd(int $houseId, HouseRepository $hr, EntityManagerInterface $em, Request $request): Response
    {
        $house = $hr->find($houseId);
        if(!$house) {
            throw new \Exception("Дом не найден");
        }

        $postInputBag = $request->request;
        if($request->isMethod('POST') && $this->isCsrfTokenValid('token', $postInputBag->get('token'))) {

            $number = $postInputBag->get('number');
            
            if(!$number) {
                throw new \Exception("Номер квартиры обязателен");
            }

            $flat = new Flat();
            $flat->setHouse($house);
            $flat->setNumber($number);
            $em->persist($flat);
            try {
                $em->flush();
            }
            catch(UniqueConstraintViolationException $e) {
                if(str_contains($e->getMessage(), 'Duplicate entry')) {
                    throw new \Exception("Такая квартира уже есть");
                }
            }
            return $this->redirect('/house/list');
//            return $this->redirect('/house/'.$house->getId().'/flat-list');
        }
        
        
        return $this->render('main/flat-form.html.twig', [
            'number' => '',
        ]);
    }
}
