<?php

namespace App\Test\Controller;

use App\Entity\Racer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RacerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/racer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Racer::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Racer index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'racer[firstname]' => 'Testing',
            'racer[lastname]' => 'Testing',
            'racer[nickname]' => 'Testing',
            'racer[timingMin]' => 'Testing',
            'racer[timingMax]' => 'Testing',
            'racer[timingAvg]' => 'Testing',
            'racer[position]' => 'Testing',
            'racer[paused]' => 'Testing',
            'racer[team]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Racer();
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setNickname('My Title');
        $fixture->setTimingMin('My Title');
        $fixture->setTimingMax('My Title');
        $fixture->setTimingAvg('My Title');
        $fixture->setPosition('My Title');
        $fixture->setPaused('My Title');
        $fixture->setTeam('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Racer');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Racer();
        $fixture->setFirstname('Value');
        $fixture->setLastname('Value');
        $fixture->setNickname('Value');
        $fixture->setTimingMin('Value');
        $fixture->setTimingMax('Value');
        $fixture->setTimingAvg('Value');
        $fixture->setPosition('Value');
        $fixture->setPaused('Value');
        $fixture->setTeam('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'racer[firstname]' => 'Something New',
            'racer[lastname]' => 'Something New',
            'racer[nickname]' => 'Something New',
            'racer[timingMin]' => 'Something New',
            'racer[timingMax]' => 'Something New',
            'racer[timingAvg]' => 'Something New',
            'racer[position]' => 'Something New',
            'racer[paused]' => 'Something New',
            'racer[team]' => 'Something New',
        ]);

        self::assertResponseRedirects('/racer/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getFirstname());
        self::assertSame('Something New', $fixture[0]->getLastname());
        self::assertSame('Something New', $fixture[0]->getNickname());
        self::assertSame('Something New', $fixture[0]->getTimingMin());
        self::assertSame('Something New', $fixture[0]->getTimingMax());
        self::assertSame('Something New', $fixture[0]->getTimingAvg());
        self::assertSame('Something New', $fixture[0]->getPosition());
        self::assertSame('Something New', $fixture[0]->getPaused());
        self::assertSame('Something New', $fixture[0]->getTeam());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Racer();
        $fixture->setFirstname('Value');
        $fixture->setLastname('Value');
        $fixture->setNickname('Value');
        $fixture->setTimingMin('Value');
        $fixture->setTimingMax('Value');
        $fixture->setTimingAvg('Value');
        $fixture->setPosition('Value');
        $fixture->setPaused('Value');
        $fixture->setTeam('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/racer/');
        self::assertSame(0, $this->repository->count([]));
    }
}
