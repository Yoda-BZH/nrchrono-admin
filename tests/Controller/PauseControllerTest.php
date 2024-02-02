<?php

namespace App\Test\Controller;

use App\Entity\Pause;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PauseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/pause/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Pause::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Pause index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'pause[porder]' => 'Testing',
            'pause[hourStart]' => 'Testing',
            'pause[hourStop]' => 'Testing',
            'pause[team]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Pause();
        $fixture->setPorder('My Title');
        $fixture->setHourStart('My Title');
        $fixture->setHourStop('My Title');
        $fixture->setTeam('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Pause');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Pause();
        $fixture->setPorder('Value');
        $fixture->setHourStart('Value');
        $fixture->setHourStop('Value');
        $fixture->setTeam('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'pause[porder]' => 'Something New',
            'pause[hourStart]' => 'Something New',
            'pause[hourStop]' => 'Something New',
            'pause[team]' => 'Something New',
        ]);

        self::assertResponseRedirects('/pause/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPorder());
        self::assertSame('Something New', $fixture[0]->getHourStart());
        self::assertSame('Something New', $fixture[0]->getHourStop());
        self::assertSame('Something New', $fixture[0]->getTeam());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Pause();
        $fixture->setPorder('Value');
        $fixture->setHourStart('Value');
        $fixture->setHourStop('Value');
        $fixture->setTeam('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/pause/');
        self::assertSame(0, $this->repository->count([]));
    }
}
