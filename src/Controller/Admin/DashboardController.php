<?php

namespace App\Controller\Admin;

use App\Controller\ReservationController;
use App\Entity\Apartment;
use App\Entity\Client;
use App\Entity\Configuration;
use App\Entity\Reservation;
use App\Entity\ReservationState;
use App\Repository\ApartmentRepository;
use App\Repository\ReservationRepository;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private ApartmentRepository $apartmentRepository,
        private ReservationRepository $reservationRepository,
        private ReservationController $reservationController,
        private AdminUrlGenerator $adminUrlGenerator
    )
    {
        
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Loc Manager')
            ->setTranslationDomain('admin')
            ;
    }

    public function configureAssets(): Assets
    {
        $assets = parent::configureAssets();
        $assets->addWebpackEncoreEntry('admin');

        return $assets;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Réservations', 'fas fa-calendar', Reservation::class);
        yield MenuItem::linkToCrud('Appartements', 'fas fa-hotel', Apartment::class);
        yield MenuItem::linkToCrud('Clients', 'fas fa-user', Client::class);
        yield MenuItem::linkToCrud('Configurations', 'fas fa-gears', Configuration::class);
        yield MenuItem::linkToCrud('Etats de réservation', 'fas fa-list', ReservationState::class);
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Get data
        $number_of_reservations = $this->reservationRepository->getTotalReservations();
        $total_sales = $this->reservationRepository->getTotalReservationsSales();
        $apartments = $this->apartmentRepository->findAll();
        $reservations = $this->reservationRepository->findAll();

        $newReservations = [];
        // Format reservations data
        foreach ($reservations as $reservation) {
            $redirectUrl = $this->adminUrlGenerator
                ->setController(ReservationCrudController::class)
                ->setAction(Crud::PAGE_DETAIL)
                ->setEntityId($reservation->getId())
                ->generateUrl();
    
            $newReservations[] = [
                'id' => $reservation->getId(),
                'start' => $reservation->getStartAt()->format('Y-m-d H:i'),
                'end' => $reservation->getEndAt()->format('Y-m-d H:i'),
                'title' => $reservation->getClient()->getFullName(),
                'time' => false,
                'backgroundColor' => $reservation->getApartment()->getColor(),
                'textColor' => 'white',
                'url' => $redirectUrl,
            ];
        }

        // Encode reservations data
        $reservationsData = json_encode($newReservations);
        
        // Construct chart for year sales per month
        $months = ['Janvier' => '1', 'Février' => '2', 'Mars' => '3', 'Avril' => '4', 'Mai' => '5', 'Juin' => '6', 'Juillet' => '7', 'Août' => '8', 'Septembre' => '9', 'Octobre' => '10', 'Novembre' => '11', 'Décembre' => '12'];
        $now = new DateTime('now');
        $year = $now->format('Y');
        $datasets = [];
        $i = 0;
        foreach ($apartments as $apartment) {
            $monthData = [];
            foreach ($months as $k => $v) {
            $monthData[] = $this->reservationRepository->getTotalSalesByYearAndMonth($year, $v, $apartment);
            }
            $datasets[] = [
            'stack' => 'Stack '.$i,
            'label' => $apartment->getName(),
            'backgroundColor' => $apartment->getColor(),
            'data' => $monthData,
            ];
            $i = $i+1;
        }

        // Create chart
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'datasets' => $datasets,
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ]);

        return $this->render('admin/dashboard.html.twig', [
            'nbReservations' => $number_of_reservations,
            'totalSales' => $total_sales,
            'reservations' => $reservationsData,
            'apartments' => $apartments,
            'chart' => $chart
        ]);
    }
}
