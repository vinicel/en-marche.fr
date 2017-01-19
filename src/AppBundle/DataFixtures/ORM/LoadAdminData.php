<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Admin\AdministratorFactory;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadAdminData implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager)
    {
        $factory = $this->getAdministratorFactory();

        $superAdmin = $factory->createFromArray([
            'email' => 'titouan.galopin@en-marche.fr',
            'password' => 'secret!12345',
            'role' => 'ROLE_SUPER_ADMIN',
        ]);

        $admin = $factory->createFromArray([
            'email' => 'jean.dupond@en-marche.fr',
            'password' => 'secret!12345',
            'role' => 'ROLE_ADMIN',
        ]);

        $writer = $factory->createFromArray([
            'email' => 'martin.pierre@en-marche.fr',
            'password' => 'secret!12345',
            'role' => 'ROLE_WRITER',
        ]);

        $manager->persist($superAdmin);
        $manager->persist($admin);
        $manager->persist($writer);
        $manager->flush();
    }

    private function getAdministratorFactory(): AdministratorFactory
    {
        return $this->container->get('app.admin.administrator_factory');
    }
}
