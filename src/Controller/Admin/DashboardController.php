<?php

namespace App\Controller\Admin;

use App\Controller\Admin\ConsultationCrudController;
use App\Controller\Admin\MedecinCrudController;
use App\Controller\Admin\PatientCrudController;
use App\Controller\Admin\RendezVousCrudController;
use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(MedecinCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<h2 class="mt-3 fw-bold text-white text-center">🏥 Clinique</h2>')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(MedecinCrudController::class, 'Médecins', 'fas fa-user-md');
        yield MenuItem::linkTo(PatientCrudController::class, 'Patients', 'fas fa-procedures');
        yield MenuItem::linkTo(RendezVousCrudController::class, 'Rendez-vous', 'fas fa-calendar');
        yield MenuItem::linkTo(ConsultationCrudController::class, 'Consultations', 'fas fa-stethoscope');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users');
    }
}