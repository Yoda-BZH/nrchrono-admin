<?php

namespace App\Test\Controller;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/team/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Team::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Team index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'team[name]' => 'Testing',
            'team[nbHeurePause]' => 'Testing',
            'team[nbPerson]' => 'Testing',
            'team[idReference]' => 'Testing',
            'team[color]' => 'Testing',
            'team[guest]' => 'Testing',
            'team[race]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Team();
        $fixture->setName('My Title');
        $fixture->setNbHeurePause('My Title');
        $fixture->setNbPerson('My Title');
        $fixture->setIdReference('My Title');
        $fixture->setColor('My Title');
        $fixture->setGuest('My Title');
        $fixture->setRace('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Team');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Team();
        $fixture->setName('Value');
        $fixture->setNbHeurePause('Value');
        $fixture->setNbPerson('Value');
        $fixture->setIdReference('Value');
        $fixture->setColor('Value');
        $fixture->setGuest('Value');
        $fixture->setRace('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'team[name]' => 'Something New',
            'team[nbHeurePause]' => 'Something New',
            'team[nbPerson]' => 'Something New',
            'team[idReference]' => 'Something New',
            'team[color]' => 'Something New',
            'team[guest]' => 'Something New',
            'team[race]' => 'Something New',
        ]);

        self::assertResponseRedirects('/team/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getNbHeurePause());
        self::assertSame('Something New', $fixture[0]->getNbPerson());
        self::assertSame('Something New', $fixture[0]->getIdReference());
        self::assertSame('Something New', $fixture[0]->getColor());
        self::assertSame('Something New', $fixture[0]->getGuest());
        self::assertSame('Something New', $fixture[0]->getRace());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Team();
        $fixture->setName('Value');
        $fixture->setNbHeurePause('Value');
        $fixture->setNbPerson('Value');
        $fixture->setIdReference('Value');
        $fixture->setColor('Value');
        $fixture->setGuest('Value');
        $fixture->setRace('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/team/');
        self::assertSame(0, $this->repository->count([]));
    }
}
