<?php

namespace App\Test\Controller;

use App\Entity\RacerPause;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RacerPauseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/racer/pause/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(RacerPause::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RacerPause index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'racer_pause[porder]' => 'Testing',
            'racer_pause[racer]' => 'Testing',
            'racer_pause[pause]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new RacerPause();
        $fixture->setPorder('My Title');
        $fixture->setRacer('My Title');
        $fixture->setPause('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RacerPause');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new RacerPause();
        $fixture->setPorder('Value');
        $fixture->setRacer('Value');
        $fixture->setPause('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'racer_pause[porder]' => 'Something New',
            'racer_pause[racer]' => 'Something New',
            'racer_pause[pause]' => 'Something New',
        ]);

        self::assertResponseRedirects('/racer/pause/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPorder());
        self::assertSame('Something New', $fixture[0]->getRacer());
        self::assertSame('Something New', $fixture[0]->getPause());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new RacerPause();
        $fixture->setPorder('Value');
        $fixture->setRacer('Value');
        $fixture->setPause('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/racer/pause/');
        self::assertSame(0, $this->repository->count([]));
    }
}
